<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\AppBundle;
use AppBundle\Entity\TimeTracking;
use AppBundle\Form\EmployeeTimeTrackingType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;

use NotFoundException;

use AppBundle\Form\ProjectTimeTrackingType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\EmployeeType;
use AppBundle\Form\JobType;
use AppBundle\Form\ProjectType;
use AppBundle\Entity\Employee;
use AppBundle\Entity\Job;
use AppBundle\Entity\Project;

use Symfony\Component\Validator\Tests\Fixtures\Entity;

class ProjectController extends Controller
{
    /**
     * @Route("/project/rechercher", name="recherche")
     */
    public function projectSearchAction(Request $request)
    {
        if(!empty($_REQUEST['search'])){
            $recherche=$_REQUEST['search'];
        }else{
            return $this->redirect($this->generateUrl('project'));
        }
        $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:Project');
        $project=$em->rechercheTitre($recherche);
        $paginator= $this->get('knp_paginator');
        $project=$paginator->paginate($project, $request->query->get('page', 1),10);
        return $this->render('project/index.html.twig', array("project" => $project));
    }

    /**
     * @Route("/project", name="project")
     */
    public function projectAction(Request $request)
    {
        $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:Project');
        $project=$em->findAll();

        $paginator= $this->get('knp_paginator');
        $project=$paginator->paginate($project, $request->query->get('page', 1),10);

        return $this->render('project/index.html.twig', array("project" => $project));
    }

    /**
     * @Route("/project/detail/{id}", name="project_detail")
     */
    public function projectDetailAction(Request $request, $id)
    {

        $timeTracking= new TimeTracking();
        $form = $this->createForm(ProjectTimeTrackingType::class, $timeTracking);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() ){
            $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:Project');
            $project=$em->find($id);
            if ($timeTracking->getIdEmployee()->getArchive()==true){
                throw new \Exception("Vous ne pouvez pas ajouter des temps de travail pour un employee qui a été archivé");
            }
            if($project->getDelivery()==true){
                throw new \Exception('Vous ne pouvez plus ajouter de temps de travail sur un projet déja livrer');
            }
            $timeTracking->setIdProject($project);
            $em= $this->getDoctrine()->getManager();
            $em->persist($timeTracking);
            $em->flush();
            return $this->redirect($this->generateUrl('project_detail', array('id' => $id)));
        }

        $em=$this->getDoctrine()->getManager()->getRepository('AppBundle:Project');
        $project=$em->find($id);
        if($project==null){
            throw new \Exception('Impossible de trouver le projet que vous avez demandé');
        }
        $em=$this->getDoctrine()->getManager()->getRepository('AppBundle:TimeTracking');
        $cout=$em->coutProjet($project);
        //die(var_dump($cout));
        $temps= $this->getDoctrine()->getManager()->getRepository('AppBundle:TimeTracking')
            ->findBy(array('idProject' => $project));
        $paginator= $this->get('knp_paginator');
        $temps=$paginator->paginate($temps, $request->query->get('page', 1),10);

        return $this->render('project/details.html.twig',array(
            'project' => $project,
            'temps' => $temps,
            'form' => $form->createView(),
            'cout' => $cout[0]['coutTotal']
        ));
    }

    /**
     * @Route("/project/edit/{id}", name="project_edit")
     */
    public function projectEditAction(Request $request, $id)
    {
        $em=$this->getDoctrine()->getManager()->getRepository('AppBundle:Project');
        $project=$em->find($id);
        if ($project==null){
            throw new \Exception('Le projet demandé est introuvable');
        }else if($project->getDelivery()){
            throw new \Exception("Vous n'etes pas autorisé à modifer ce projet");
        }
        $form = $this->createForm(ProjectType::class, $project);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() ){
            $em= $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();

            return $this->redirect($this->generateUrl('project'));
        }
        return $this->render('project/edit.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/project/add", name="project_add")
     */
    public function projectAddAction(Request $request)
    {
        $project= new Project();
        $form = $this->createForm(ProjectType::class, $project);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() ){
            $em= $this->getDoctrine()->getManager();
            $project->setDelivery(false);
            $em->persist($project);
            $em->flush();

            return $this->redirect($this->generateUrl('project'));
        }
        return $this->render('project/edit.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/project/delete/{id}", name="project_delete")
     */
    public function projectDeleteAction(Request $request,$id)
    {

        $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:Project');
        $project=$em->find($id);
        if ($project==null){
            throw new \Exception('le projet est introuvable');
        }
        else if($project->getDelivery()){
            throw new \Exception('projet livré ne peut etre supprimé');
        }
        if(isset($_POST['del'])){
            if($_POST['del']=='Oui'){
                //die("suppr");
                $em= $this->getDoctrine()->getManager();
                $project=$em->getRepository('AppBundle:Project')->find($id);
                if ($project->getDelivery()){
                    throw new \Exception('projet livré ne peut etre supprimé');
                }
                else{
                    $em->remove($project);
                    $em->flush();
                }
                return $this->redirect($this->generateUrl('project'));
            }
            else{
                return $this->redirect($this->generateUrl('project'));
            }
        }
        return $this->render('project/delete.html.twig', array('project' => $project));
    }

    /**
     * @Route("project/delivery/{id}", name="delivery")
     * @throws \Exception
     */
    public function projectDeliveryAction(Request $request, $id)
    {
        $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:Project');
        $project=$em->find($id);
        if ($project==null){
            //die('projet introuvable');
            throw new \Exception('Le projet est introuvable');
        }
        else if($project->getDelivery()){
            //die('projet deja livré');
            throw new \Exception('Le projet a déjà été livrer');
        }
        $project->setDelivery(1);
        $em= $this->getDoctrine()->getManager();
        $em->persist($project);
        $em->flush();
        return $this->redirect($this->generateUrl('project'));
    }
}
