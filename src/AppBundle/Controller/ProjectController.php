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
    //action appeler par la barre de recherche
    /**
     * @Route("/project/rechercher", name="recherche")
     */
    public function projectSearchAction(Request $request)
    {
        //si il y'a quelque chose d'écrit dans la barre de recherche on le recupère
        if(!empty($_REQUEST['search'])){
            $recherche=$_REQUEST['search'];
        }else{//sinon on retourne la liste des projets complete
            return $this->redirect($this->generateUrl('project'));
        }

        $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:Project');
        // requete permettant de selectionné uniquement les projet qui contient la valeur rechercher dans leur titre
        $project=$em->rechercheTitre($recherche);

        //permet de remplir les projet trouvé a l'aide d'une pagination (10 par page)
        $paginator= $this->get('knp_paginator');
        $project=$paginator->paginate($project, $request->query->get('page', 1),10);

        //retourne la meme vue que la liste de projet
        return $this->render('project/index.html.twig', array("project" => $project));
    }



    //page : liste des projets
    /**
     * @Route("/project", name="project")
     */
    public function projectAction(Request $request)
    {
        //recupération de tous les projets
        $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:Project');
        $project=$em->findAll();

        //pagination des projets 10 projet par page
        $paginator= $this->get('knp_paginator');
        $project=$paginator->paginate($project, $request->query->get('page', 1),10);

        //retourne la vue avec les projets a afficher
        return $this->render('project/index.html.twig', array("project" => $project));
    }


    //page : fiche d'heure depuis un projet (accessible depuis la page liste des projets)
    /**
     * @Route("/project/detail/{id}", requirements={"id" = "\d+"}, name="project_detail")
     */
    public function projectDetailAction(Request $request, $id)
    {
        //Création du formulaire d'ajout d'un temps de travail
        $timeTracking= new TimeTracking();
        $form = $this->createForm(ProjectTimeTrackingType::class, $timeTracking);

        $form->handleRequest($request);

        //si le formulaire est envoyé
        if($form->isSubmitted() && $form->isValid() ){
            //on recupere le projet correspond a l'id de l'url
            $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:Project');
            $project=$em->find($id);
            //si l'id n'est pas present en BDD
            if ($project==null){
                //lance une exception
                throw new \Exception('Impossible de trouver le projet que vous avez demandé');
            }

            if ($timeTracking->getIdEmployee()->getArchive()==true){
                //lance une exception pour empecher d'ajouter un temps a partir d'un employée archivé
                throw new \Exception("Vous ne pouvez pas ajouter des temps de travail pour un employee qui a été archivé");
            }

            if($project->getDelivery()==true){
                //lance une exception pour empecher d'ajouter un temps sur un projet deja livré
                throw new \Exception('Vous ne pouvez plus ajouter de temps de travail sur un projet déja livré');
            }

            //on lie le projet au temps de travail
            $timeTracking->setIdProject($project);
            $em= $this->getDoctrine()->getManager();
            //ajout en base de donnée
            $em->persist($timeTracking);
            $em->flush();

            //retoune la vue avec le formualaire vide
            return $this->redirect($this->generateUrl('project_detail', array('id' => $id)));
        }
        //récupération du projet correspondan à l'id de l'url
        $em=$this->getDoctrine()->getManager()->getRepository('AppBundle:Project');
        $project=$em->find($id);

        //si on ne trouve pas le projet on retourne une page d'erreur
        if($project==null){
            throw new \Exception('Impossible de trouver le projet que vous avez demandé');
        }


        $em=$this->getDoctrine()->getManager()->getRepository('AppBundle:TimeTracking');
        //fonction qui permet de calculer le coup du projet
        $cout=$em->coutProjet($project);

        //recupere les temps qui contiennent le projet sélectionné
        $temps= $this->getDoctrine()->getManager()->getRepository('AppBundle:TimeTracking')
            ->findBy(array('idProject' => $project));

        //liste des temps de production afficher a l'aide d'une pagination 10 par page
        $paginator= $this->get('knp_paginator');
        $temps=$paginator->paginate($temps, $request->query->get('page', 1),10);

        //retoune la vue avec les differentes données a afficher dans la page
        return $this->render('project/details.html.twig',array(
            'project' => $project,
            'temps' => $temps,
            'form' => $form->createView(),//permet la création du formulaire
            'cout' => $cout[0]['coutTotal']
        ));
    }

    //page d'edition d'un projet la modificaion est autorisé uniquement si le projet n'est pas encore livré
    /**
     * @Route("/project/edit/{id}", requirements={"id" = "\d+"}, name="project_edit")
     */
    public function projectEditAction(Request $request, $id)
    {
        //recuperation du projet correspondant à l'id dans l'url
        $em=$this->getDoctrine()->getManager()->getRepository('AppBundle:Project');
        $project=$em->find($id);
        //si le projet n'a pas été trouvé on lnce une exception
        if ($project==null){
            throw new \Exception('Le projet demandé est introuvable');
        }else if($project->getDelivery()){// si est deja livré on lnce également une exception pour empecher de modifier le projet
            throw new \Exception("Vous n'etes pas autorisé à modifer ce projet");
        }
        //creation du formulaire
        $form = $this->createForm(ProjectType::class, $project);

        $form->handleRequest($request);

        //si envoie du formulaire et a reussi a psser les point de valisation
        if($form->isSubmitted() && $form->isValid() ){
            //on envoie le projet en bdd
            $em= $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();
            //retour a la liste des projets
            return $this->redirect($this->generateUrl('project'));
        }
        //retoune le template avec la vue qui permet la création du formulaire
        return $this->render('project/edit.html.twig', array('form' => $form->createView()));
    }

    //page permetant d'ajouter un projet
    /**
     * @Route("/project/add", name="project_add")
     */
    public function projectAddAction(Request $request)
    {
        //creation du formulaire
        $project= new Project();
        $form = $this->createForm(ProjectType::class, $project);

        $form->handleRequest($request);

        //si envoie du formulaire et est valide
        if($form->isSubmitted() && $form->isValid() ){

            $em= $this->getDoctrine()->getManager();
            //lors de la création le projet est obligatoirement non livré
            $project->setDelivery(false);
            //on insert le projet en bdd
            $em->persist($project);
            $em->flush();

            //retour a la liste des projets
            return $this->redirect($this->generateUrl('project'));
        }
        return $this->render('project/edit.html.twig', array('form' => $form->createView()));
    }


    //page permettant la suppression d'un projet
    /**
     * @Route("/project/delete/{id}", requirements={"id" = "\d+"}, name="project_delete")
     */
    public function projectDeleteAction(Request $request,$id)
    {
        // on recupere le projet correspondant a l'id passé dans l'url
        $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:Project');
        $project=$em->find($id);
        //si le projet n'est pas retrouvé on retourne une erreur
        if ($project==null){
            throw new \Exception('le projet est introuvable');
        }
        else if($project->getDelivery()){//empeche la suppression d'un projet déja livré
            throw new \Exception('projet livré ne peut etre supprimé');
        }
        if(isset($_POST['del'])){// si on a repondu a la question pour la suppression
            //si on veux supprimé
            if($_POST['del']=='Oui'){
                //recupere le projet
                $em= $this->getDoctrine()->getManager();
                $project=$em->getRepository('AppBundle:Project')->find($id);
                //verifie si il est deja livré envoie d'une exception si c'est le cas
                if ($project->getDelivery()){
                    throw new \Exception('projet livré ne peut etre supprimé');
                }
                else{//si tout est ok!

                    //recupere tout les temps lié au projet
                    $temps= $this->getDoctrine()->getManager()->getRepository('AppBundle:TimeTracking')
                        ->findBy(array('idProject' => $project));

                    //on recupere le prix total du projet
                    $em=$this->getDoctrine()->getManager()->getRepository('AppBundle:TimeTracking');
                    $cout=$em->coutProjet($project);

                    //décommentez pour voir un aperçu du mail
                    /*return $this->render('sauvegarde.html.twig', array(
                        'project' => $project,
                        'temps' => $temps,
                        'cout' => $cout[0]['coutTotal']
                        ));*/


                    //envoie du mail de sauvegarde

                    $message = \Swift_Message::newInstance()
                        ->SetSubject('Sauvegarde du projet supprimé')
                        ->SetFrom('administrateur@gmail.com')
                        ->SetTo('contact@procost.fr')
                        ->setContentType("text/html")
                        ->SetBody($this->render('sauvegarde.html.twig', array(
                            'project' => $project,
                            'temps' => $temps,
                            'cout' => $cout[0]['coutTotal']
                        )));
                    $this->get('mailer')->send($message);

                    //supprime tout les temps
                    foreach ($temps as $time){
                        $em=$this->getDoctrine()->getManager();
                        $tempsProd= $em->getRepository('AppBundle:TimeTracking')->find($time);
                        //die(var_dump($tempsProd));
                        $em->remove($tempsProd);
                    }

                    //on peut alors supprimer le projet
                    $em=$this->getDoctrine()->getManager();
                    $em->remove($project);
                    $em->flush();
                }
                //retour sur la liste des projets
                return $this->redirect($this->generateUrl('project'));
            }
            else{//si on ne veux pas supprimer retour directe a la liste des projets
                return $this->redirect($this->generateUrl('project'));
            }
        }
        //retoune la vue avec les données du projet a supprimé
        return $this->render('project/delete.html.twig', array('project' => $project));
    }


    // action livrer le projet : accessible depuis la liste des projets ET la feuille d'heure projet
    /**
     * @Route("project/delivery/{id}", requirements={"id" = "\d+"}, name="delivery")
     * @throws \Exception
     */
    public function projectDeliveryAction(Request $request, $id)
    {
        //recupération de l'objet projet correspodant à l'id
        $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:Project');
        $project=$em->find($id);

        //si on ne trouve pas le projet on lance une exception
        if ($project==null){
            throw new \Exception('Le projet est introuvable');
        }
        else if($project->getDelivery()){//si le projet est deja livré on lance une erreur
            throw new \Exception('Le projet a déjà été livrer');
        }
        //on met la variable delivery a true pour indique que le projet est bien livré
        $project->setDelivery(1);
        //on enregistre la modification en BDD
        $em= $this->getDoctrine()->getManager();
        $em->persist($project);
        $em->flush();
        //retour a la liste des projets
        return $this->redirect($this->generateUrl('project'));
    }
}
