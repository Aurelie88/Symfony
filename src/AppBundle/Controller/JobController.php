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
        //recupere tout les metiers
        $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:Job');
        $job=$em->findAll();

        //créé des pages de metier 10 par page
        $paginator= $this->get('knp_paginator');
        $job=$paginator->paginate($job, $request->query->get('page', 1),10);

        // retoune la liste des metiers
        return $this->render('job/index.html.twig', array('job' => $job));
    }


    /**
     * @Route("/job/add", name="job_add")
     */
    public function jobAddAction(Request $request)
    {
        //creation d'un formualire vide
        $job= new Job();
        $form = $this->createForm(JobType::class, $job);

        $form->handleRequest($request);

        //si le formulaire est soumis et valide
        if($form->isSubmitted() && $form->isValid() ){
            $em= $this->getDoctrine()->getManager();
            //ajout du metier en BDD
            $em->persist($job);
            $em->flush();
            //redirection vers la liste des métiers
            return $this->redirect($this->generateUrl('job'));
        }
        //retoune la vue avec un formulaire vide pour l'ajout d'un metier
        return $this->render('job/edit.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/job/edit/{id}", name="job_edit")
     */
    public function jobEditAction(Request $request, $id)
    {
        //recuperation du metier
        $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:Job');
        $job=$em->find($id);

        //si le metier n'est pas trouvé on lance une erreur
        if ($job==null){
            throw new \Exception('Le métier demandé est introuvable');
        }
        //création du formulaire lié au métier selectionné
        $form = $this->createForm(JobType::class, $job);

        $form->handleRequest($request);

        //si le formulaire est valide et envoyé
        if($form->isSubmitted() && $form->isValid() ){
            // on enregistre les changement en Bdd
            $em=$this->getDoctrine()->getManager();
            $em->persist($job);
            $em->flush();

            //retour a la liste des métiers
            return $this->redirect($this->generateUrl('job'));
        }
        //retourne la vue avec un formulaire pré remplis
        return $this->render('job/edit.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/job/delete/{id}", name="job_delete", requirements={"id"="\d+"})
     * @throws \Exception
     * @throws \Exception
     */
    public function jobDeleteAction(Request $request, $id)
    {
        //on recupere le metier que l'on souhaite supprimé
        $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:Job');
        $job=$em->find($id);

        //si on nentrouve pas métier, on lance une exception
        if ($job==null){
            throw new \Exception("Impossible de trouver le métier existe pas");
        }

        //si on repond a la confirmation
        if(isset($_POST['del'])){
            if($_POST['del']=='Oui'){//par oui
                try{// alors on essaye de supprimer le metier
                    $em= $this->getDoctrine()->getManager();
                    $job=$em->getRepository('AppBundle:Job')->find($id);

                    $em->remove($job);
                    $em->flush();
                } catch (\Doctrine\DBAL\DBALException $e){//si on ne peux pas supprimé le metier c'est qu'il y a des employee lié a ce metier alors on lance un message d'erreur
                    throw new \Exception('Vous ne pouvez pas supprimez un métier utilisé par des employées');
                }
                //retour a la liste des métiers
                return $this->redirect($this->generateUrl('job'));
            }
            else{// si on ne souhaite pas supprimer on retourne directement a la liste des metiers
                return $this->redirect($this->generateUrl('job'));
            }
        }
        //on retourne la vue avec les données du métier
        return $this->render('job/delete.html.twig', array('job' => $job));
    }

}
