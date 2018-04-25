<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use AppBundle\AppBundle;
use AppBundle\Entity\TimeTracking;
use AppBundle\Form\EmployeeTimeTrackingType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;

use NotFoundException;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\EmployeeType;
use AppBundle\Form\JobType;
use AppBundle\Form\ProjectType;
use AppBundle\Entity\Employee;
use AppBundle\Entity\Job;
use AppBundle\Entity\Project;

use Symfony\Component\Validator\Tests\Fixtures\Entity;

class EmployeeController extends Controller
{
    /**
     * @Route("/employee", name="employee")
     */
    public function employeeAction(Request $request)
    {
        $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:Employee');
        $employee=$em->findAll();

        $paginator= $this->get('knp_paginator');
        $employee=$paginator->paginate($employee, $request->query->get('page', 1),10);

        return $this->render('employee/index.html.twig', array('employee' => $employee));
    }

    /**
     * @Route("/employee/detail/{id}", name="employee_detail")
     */
    public function employeeDetailAction(Request $request,$id)
    {
        $timeTracking= new TimeTracking();
        $form = $this->createForm(EmployeeTimeTrackingType::class, $timeTracking);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() ){
            $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:Employee');
            $employee=$em->find($id);
            if ($employee->getArchive()==true){
                throw new \Exception("Vous ne pouvez pas ajouter des temps de travail pour un employee qui a été archivé");
            }
            if($timeTracking->getIdProject()->getDelivery()==true){
                throw new \Exception('Vous ne pouvez plus ajouter de temps de travail sur un projet déja livrer');
            }
            $timeTracking->setIdEmployee($employee);
            $em= $this->getDoctrine()->getManager();
            $em->persist($timeTracking);
            $em->flush();
            return $this->redirect($this->generateUrl('employee_detail', array('id' => $id)));
        }

        $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:Employee');
        $employee=$em->find($id);
        if ($employee==null){
            throw new \Exception("L'employee n'existe pas");
        }

        $temps= $this->getDoctrine()->getManager()->getRepository('AppBundle:TimeTracking')
            ->findBy(array('idEmployee' => $employee));
        $paginator= $this->get('knp_paginator');
        $temps=$paginator->paginate($temps, $request->query->get('page', 1),10);

        return $this->render('employee/details.html.twig', array(
            'employee' => $employee,
            'temps' => $temps,
            'form' => $form->createView()));
    }

    /**
     * @Route("/employee/add", name="employee_add")
     */
    public function employeeAddAction(Request $request)
    {
        $employee= new Employee();
        $form = $this->createForm(EmployeeType::class, $employee);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() ){
            $em= $this->getDoctrine()->getManager();
            $employee->setArchive(false);
            $em->persist($employee);
            $em->flush();
            return $this->redirect($this->generateUrl('employee'));
        }
        return $this->render('employee/edit.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/employee/edit/{id}", name="employee_edit")
     */
    public function employeeEditAction(Request $request, $id)
    {
        $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:Employee');
        $employee= $em->find($id);
        if ($employee==null){
            throw new \Exception("Impossible de trouver l'employer");
        }
        $form = $this->createForm(EmployeeType::class, $employee);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() ){
            $em= $this->getDoctrine()->getManager();

            $em->persist($employee);
            $em->flush();

            return $this->redirect($this->generateUrl('employee'));
        }
        return $this->render('employee/edit.html.twig', array('form' => $form->createView()));

    }

    /**
     * @Route("/employee/delete/{id}", name="employee_delete")
     */
    public function employeeDeleteAction(Request $request, $id)
    {
        $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:Employee');
        $employee=$em->find($id);
        if ($employee==null){
            throw new \Exception("Impossible de trouver l'employer");
        }
        if(isset($_POST['del'])){
            if($_POST['del']=='Oui'){
                $em= $this->getDoctrine()->getManager();
                $employee=$em->getRepository('AppBundle:Employee')->find($id);
                $employee->setArchive(1);
                $em->persist($employee);
                $em->flush();

                return $this->redirect($this->generateUrl('employee'));
            }
            else{
                return $this->redirect($this->generateUrl('employee'));
            }
        }
        return $this->render('employee/delete.html.twig', array('employee' => $employee));

    }
}
