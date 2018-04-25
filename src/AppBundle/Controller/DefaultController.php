<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\TimeTracking;
use AppBundle\Form\EmployeeTimeTrackingType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;

use NotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:Project');
        $nbProjetR=$em->nombreProjetRealisation();
        $nbProjetL=$em->nombreProjetLivree();
        $nbProjetCapex=$em->nombreProjetCapex();
        $nbProjetOpex=$em->nombreProjetOpex();
        $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:Employee');
        $nombreEmployee=$em->nombreEmployee();
        $em= $this->getDoctrine()->getManager()->getRepository('AppBundle:TimeTracking');
        $tempsProduction=$em->findAll();
        $nombreJour= $em->nombreJourProduction();
        $classement = $em->classementEmployee();
        $project=$em->coutTotalProjet();

        return $this->render('index.html.twig',
            array(
                'nombreProjetR' => $nbProjetR[0]['nombre'],
                'nombreProjetL' => $nbProjetL[0]['nombre'],
                'nombreProjetCapex' => $nbProjetCapex[0]['nombre'],
                'nombreProjetOpex' => $nbProjetOpex[0]['nombre'],
                'nombreEmployee' => $nombreEmployee[0]['nombre'],
                'listeProject' => $project,
                'nombreJour' => $nombreJour[0]['nombre'],
                'topEmployee' => $classement[0],
                'tempsProduction' => $tempsProduction
            ));
    }


    /**
     * @Route("/delete/timeTracking/{id}", name="delete_timeTracking")
     */
    public function deleteTimeTracking(Request $request, $id){


    }
}
