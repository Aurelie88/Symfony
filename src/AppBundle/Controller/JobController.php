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
class JobController extends Controller
{

    /**
     * @Route("/job", name="job")
     */
    public function jobAction(Request $request)
    {
        $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:Job');
        $job=$em->findAll();

        $paginator= $this->get('knp_paginator');
        $job=$paginator->paginate($job, $request->query->get('page', 1),10);

        return $this->render('job/index.html.twig', array('job' => $job));
    }


    /**
     * @Route("/job/add", name="job_add")
     */
    public function jobAddAction(Request $request)
    {
        $job= new Job();
        $form = $this->createForm(JobType::class, $job);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() ){
            $em= $this->getDoctrine()->getManager();

            $em->persist($job);
            $em->flush();

            return $this->redirect($this->generateUrl('job'));
        }

        return $this->render('job/edit.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/job/edit/{id}", name="job_edit")
     */
    public function jobEditAction(Request $request, $id)
    {
        $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:Job');
        $job=$em->find($id);
        if ($job==null){
            throw new \Exception('Le métier demandé est introuvable');
        }
        $form = $this->createForm(JobType::class, $job);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() ){
            $em=$this->getDoctrine()->getManager();
            $em->persist($job);
            $em->flush();

            return $this->redirect($this->generateUrl('job'));
        }
        return $this->render('job/edit.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/job/delete/{id}", name="job_delete", requirements={"id"="\d+"})
     * @throws \Exception
     * @throws \Exception
     */
    public function jobDeleteAction(Request $request, $id)
    {
        $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:Job');
        /*$result=$em->nombreMetier();
        die($result[0]['nombre']);*/
        $job=$em->find($id);

        if ($job==null){
            throw new \Exception("le métier existe pas");
        }

        if(isset($_POST['del'])){
            if($_POST['del']=='Oui'){
                try{
                    $em= $this->getDoctrine()->getManager();
                    $job=$em->getRepository('AppBundle:Job')->find($id);

                    $em->remove($job);
                    $em->flush();
                } catch (\Doctrine\DBAL\DBALException $e){
                    throw new \Exception('Vous ne pouvez pas supprimez un métier utilisé par des employées');
                }
                return $this->redirect($this->generateUrl('job'));
            }
            else{
                return $this->redirect($this->generateUrl('job'));
            }
        }
        return $this->render('job/delete.html.twig', array('job' => $job));
    }

}
