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
    // liste des employees
    /**
     * @Route("/employee", name="employee")
     */
    public function employeeAction(Request $request)
    {
        //recuperation de tout les employee presents en BDD
        $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:Employee');
        $employee=$em->findAll();

        //organise les employee a l'aide d'une pagination de 10 employee par page
        $paginator= $this->get('knp_paginator');
        $employee=$paginator->paginate($employee, $request->query->get('page', 1),10);

        //retoune la liste des employée vec les actions possible pour chaque employée
        return $this->render('employee/index.html.twig', array('employee' => $employee));
    }

    //feuille des temps de production propre a l'employee sélectionné
    /**
     * @Route("/employee/detail/{id}", requirements={"id" = "\d+"}, name="employee_detail")
     */
    public function employeeDetailAction(Request $request,$id)
    {
        //création du fromulaire d'ajout un temps de travail
        $timeTracking= new TimeTracking();
        $form = $this->createForm(EmployeeTimeTrackingType::class, $timeTracking);

        $form->handleRequest($request);


        //si le formulaire est envoyée et valide
        if($form->isSubmitted() && $form->isValid() ){

            //recuperation de l'employee
            $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:Employee');
            $employee=$em->find($id);

            //si l'employee n'est pas present en BDD on lance une page d'erreur
            if ($employee==null){
                throw new \Exception("L'employee n'existe pas");
            }

            //si l'employé est archivé on affiche un message d'erreur
            if ($employee->getArchive()==true){
                throw new \Exception("Vous ne pouvez pas ajouter des temps de travail pour un employee qui a été archivé");
            }

            //si le projet selectionnée est deja livré  on empeche l'ajout de temps de production dessus
            if($timeTracking->getIdProject()->getDelivery()==true){
                throw new \Exception('Vous ne pouvez plus ajouter de temps de travail sur un projet déja livrer');
            }

            //on lie l'employee au temps de production
            $timeTracking->setIdEmployee($employee);
            //on ajoute le temps en BDD
            $em= $this->getDoctrine()->getManager();
            $em->persist($timeTracking);
            $em->flush();

            //on retourne la meme page avec un formulaire vide
            return $this->redirect($this->generateUrl('employee_detail', array('id' => $id)));
        }

        //recuperation de l'employee
        $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:Employee');
        $employee=$em->find($id);

        //si l'employee n'est pas present en BDD on lance une page d'erreur
        if ($employee==null){
            throw new \Exception("L'employee n'existe pas");
        }

        //recuperation des tesmps de productions propre a l'employée
        $temps= $this->getDoctrine()->getManager()->getRepository('AppBundle:TimeTracking')
            ->findBy(array('idEmployee' => $employee));

        //affichage des tesmp de production a l'aide d'une pagiantion (10 temps par page)
        $paginator= $this->get('knp_paginator');
        $temps=$paginator->paginate($temps, $request->query->get('page', 1),10);

        //retourne la vue avec le formulaire d'ajout de temps et les information lie a l'employee avec ses temps de prod
        return $this->render('employee/details.html.twig', array(
            'employee' => $employee,
            'temps' => $temps,
            'form' => $form->createView()));
    }

    //page d'ajout d'un employee
    /**
     * @Route("/employee/add", name="employee_add")
     */
    public function employeeAddAction(Request $request)
    {
        //creation du formulaire d'ajout d'un employee
        $employee= new Employee();
        $form = $this->createForm(EmployeeType::class, $employee);

        $form->handleRequest($request);

        //si envoie du formulaire et qu'il est valide
        if($form->isSubmitted() && $form->isValid() ){
            $em= $this->getDoctrine()->getManager();
            //on met a false pour dire qu'a sa création l'employe n'est pas encore archivé
            $employee->setArchive(false);

            if($employee->getImage()==null){
                $employee->setImage('default.jpg');
            }
            //ajout de l'employee en bdd
            $em->persist($employee);
            $em->flush();
            //redirection vers la liste des employeees
            return $this->redirect($this->generateUrl('employee'));
        }
        //retoune la vue avec le formulaire d'ajout
        return $this->render('employee/edit.html.twig', array('form' => $form->createView()));
    }

    //page permettant de modifier un employee existant
    /**
     * @Route("/employee/edit/{id}", requirements={"id" = "\d+"}, name="employee_edit")
     */
    public function employeeEditAction(Request $request, $id)
    {
        //recuperation de l'employee
        $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:Employee');
        $employee= $em->find($id);
        $employee->setImage(null);
        //si on ne trouve pas l'employee demandé on lance une erreur
        if ($employee==null){
            throw new \Exception("Impossible de trouver l'employée");
        }
        //creation du formulaire preremplis
        $form = $this->createForm(EmployeeType::class, $employee);

        $form->handleRequest($request);

        //si le formulaire est soumis et valide
        if($form->isSubmitted() && $form->isValid() ){

            $em= $this->getDoctrine()->getManager();
            //si il l'utilisateur n'a pas selectionné d'image
            if($employee->getImage()==null){
                //on met une image par defaut (deja presente dans les asssets)
                $employee->setImage('default.jpg');
            }
            //on enregistre la modification en bdd
            $em->persist($employee);
            $em->flush();

            //redirection vers la liste des employees
            return $this->redirect($this->generateUrl('employee'));
        }
        //retourne la page avec le formulaire pres remplis
        return $this->render('employee/edit.html.twig', array('form' => $form->createView()));

    }


    //page de confirmation de suppression (ou plutot archivage)
    /**
     * @Route("/employee/delete/{id}", requirements={"id" = "\d+"}, name="employee_delete")
     */
    public function employeeDeleteAction(Request $request, $id)
    {
        //on recupère l'employee que l'on souhaite supprimer
        $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:Employee');
        $employee=$em->find($id);
        //si on ne trouve pas l'employee on lance une exception
        if ($employee==null){
            throw new \Exception("Impossible de trouver l'employée");
        }
        //si l'employee est deja archivé on lance une exception pour informer l'utilisateur
        if ($employee->getArchive()==true){
            throw new \Exception("L'employé est déjà archiver ");
        }


        if(isset($_POST['del'])){//si on repon d a la question
            if($_POST['del']=='Oui'){//par oui
                //on recupere l'employeee
                $em= $this->getDoctrine()->getManager();
                $employee=$em->getRepository('AppBundle:Employee')->find($id);
                //si on ne trouve pas l'employee on lance une exception
                if ($employee==null){
                    throw new \Exception("Impossible de trouver l'employée");
                }
                //on met la variable archive a true pour dire que l'employee est archivé
                $employee->setArchive(1);
                //enregistre la modification en bdd
                $em->persist($employee);
                $em->flush();
                //retoune sur la liste des employees
                return $this->redirect($this->generateUrl('employee'));
            }
            else{//si on ne souhaire paa supprimer l'employee retour direct vers la liste des employees
                return $this->redirect($this->generateUrl('employee'));
            }
        }
        //retourne la vue avec les information sur l'employee
        return $this->render('employee/delete.html.twig', array('employee' => $employee));
    }
}
