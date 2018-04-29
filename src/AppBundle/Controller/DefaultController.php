<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\TimeTracking;
use AppBundle\Form\EmployeeTimeTrackingType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;

use NotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse\Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\EmployeeType;
use AppBundle\Form\JobType;
use AppBundle\Form\ProjectType;
use AppBundle\Entity\Employee;
use AppBundle\Entity\Job;
use AppBundle\Entity\Project;

use Symfony\Component\Validator\Tests\Fixtures\Entity;

class DefaultController extends Controller
{
    // page d'acceuil / Tableau de bord
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        //recuperation des valeur pour remplir les graph et tableau
        $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:Project');
        //requetes speciale ecrite dans ProjectRepository

        $nbProjetR=$em->nombreProjetRealisation();
        $nbProjetL=$em->nombreProjetLivree();
        $nbProjetCapex=$em->nombreProjetCapex();
        $nbProjetOpex=$em->nombreProjetOpex();

        $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:Employee');
        //requetes speciale ecrite dans EmployeeRepository
        $nombreEmployee=$em->nombreEmployee();
        $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:TimeTracking');
        $tempsProduction=$em->findAll();
        //requetes speciale ecrite dans TimeTrackingRepository
        $nombreJour= $em->nombreJourProduction();
        $topEmployee = $em->classementEmployee();
        $project=$em->coutTotalProjet();

        //retourne la vue avec toute les données utile aux tableau de bord
        return $this->render('index.html.twig',
            array(

                'nombreProjetR' => $nbProjetR[0]['nombre'],
                'nombreProjetL' => $nbProjetL[0]['nombre'],
                'nombreProjetCapex' => $nbProjetCapex[0]['nombre'],
                'nombreProjetOpex' => $nbProjetOpex[0]['nombre'],
                'nombreEmployee' => $nombreEmployee[0]['nombre'],
                'listeProject' => $project,
                'nombreJour' => $nombreJour[0]['nombre'],
                'topEmployee' => $topEmployee[0],
                'tempsProduction' => $tempsProduction
            ));
    }


    //action suppression d'un temps de production depuis la page employée
    /**
     * @Route("/delete/timeTrackingEmployee/{id}", requirements={"id" = "\d+"}, name="employee_delete_time")
     */
    public function deleteEmployeeTimeTracking(Request $request, $id){
        //récuperation du temps de travail
        $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:TimeTracking');
        $temps=$em->find($id);
        //SI le temps de travail existe pas on lance un exception
        if($temps==null){
            throw new \Exception('impossible de trouver le temps de travail demandé');
        }
        if(isset($_POST['del'])){//si envoie du formulaire
            if($_POST['del']=='Oui'){//si on veux supprimer le temps
                //récuperation du temps de travail
                $em= $this->getDoctrine()->getManager();
                $temps=$em->getRepository('AppBundle:TimeTracking')->find($id);
                //on teste si le projet est deja livré ?
                if ($temps->GetIdProject()->getDelivery()==true){
                    throw new \Exception('impossible de supprimez un temps de travail sur un projet déja livré');
                }
                //on test si l'employee n'est pas archivé pour pouvoir supprimer le temps de travail
                if ($temps->getIdEmployee()->getArchive()==true){
                    throw new \Exception("impossible de supprimer un temps de travail d'un employee deja archivé");
                }
                //si tout est ok on supprime
                $em->remove($temps);
                $em->flush();

                //retourne a la page de temps de production de l'employée
                return $this->redirect($this->generateUrl('employee_detail',
                    array('id'=> $temps->getIdEmployee()->getId()
                    )));
            }
            else{// si on ne souhaite pas supprimer
                //renvoie a la page de detail de l'employé qui permet l'ajout des heures
                return $this->redirect($this->generateUrl('employee_detail',
                    array('id'=> $temps->getIdEmployee()->getId())));
            }
        }
        //retourne la vue avec lees informations sur le temps de production que l'on souhaite supprimé
        return $this->render('deleteTime.html.twig', array(
            'temps' => $temps
        ));
    }

    //
    /**
     * @Route("/delete/timeTrackingProject/{id}", requirements={"id" = "\d+"}, name="project_delete_time")
     */
    public function deleteProjectTimeTracking(Request $request, $id){
        //récuperation du temps de travail
        $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:TimeTracking');
        $temps=$em->find($id);
        //SI le temps de travail existe pas on lance un exception
        if($temps==null){
            throw new \Exception('impossible de trouver le temps de travail demandé');
        }
        if(isset($_POST['del'])){//si envoie du formulaire
            if($_POST['del']=='Oui'){//si on veux supprimer le temps
                //récuperation du temps de travail
                $em= $this->getDoctrine()->getManager();
                $temps=$em->getRepository('AppBundle:TimeTracking')->find($id);
                //on teste si le projet est deja livré ?
                if ($temps->GetIdProject()->getDelivery()==true){
                    throw new \Exception('impossible de supprimez un temps de travail sur un projet déja livré');
                }
                //on test si l'employee n'est pas archivé pour pouvoir supprimer le temps de travail
                if ($temps->getIdEmployee()->getArchive()==true){
                    throw new \Exception("impossible de supprimer un temps de travail d'un employee deja archivé");
                }
                //si tout est ok on supprime
                $em->remove($temps);
                $em->flush();

                return $this->redirect($this->generateUrl('project_detail',
                    array('id'=> $temps->getIdProject()->getId()
                    )));
            }
            else{// si on ne souhaite pas supprimer
                //renvoie a la page de detail de l'employer qui permet l'ajout des heures
                return $this->redirect($this->generateUrl('project_detail',
                    array('id'=> $temps->getIdProject()->getId())));
            }
        }
        // retourne la vue qui demande la confirmation de supprimer
        return $this->render('deleteTime.html.twig', array(
            'temps' => $temps
        ));
    }

}
