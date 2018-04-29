<?php
namespace AppBundle\DataFixtures;

use AppBundle\Entity\Employee;
use AppBundle\Entity\Job;
use AppBundle\Entity\Project;
use AppBundle\Entity\TimeTracking;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    function randomDateInRange(DateTime $start, DateTime $end) {
        $randomTimestamp = mt_rand($start->getTimestamp(), $end->getTimestamp());
        $randomDate = new DateTime();
        $randomDate->setTimestamp($randomTimestamp);
        return $randomDate;
    }

	public function load(ObjectManager $manager)
    {
        //ajout de metiers
        $metier = array("développeur web", "web designer", "graphiste", "integrateur", "SEO Manager", "chef de projet");
        for ($i = 0; $i < count($metier); $i++) {
            $job = new Job();
            $job->setNomMetier($metier[$i]);
            $manager->persist($job);
        }
        $manager->flush();


        //ajout d'employées
        $personne[]= array( 'nom' => "cuny", 'prenom' => "aurelie");
        $personne[]= array( 'nom' => "gaiga", 'prenom' => "damien");
        $personne[]= array( 'nom' => "marin", 'prenom' => "mathieu");
        $personne[]= array( 'nom' => "husson", 'prenom' => "arnaud");

        for ($i=0 ; $i<count($personne); $i++){
            $employee= new Employee();
            $employee->setNom($personne[$i]['nom']);
            $employee->setPrenom($personne[$i]['prenom']);
            $metier=$manager->getRepository('AppBundle:Job')->find(rand(1,6));
            $employee->setMetier($metier);
            $employee->setEmail($personne[$i]['nom'].".".$personne[$i]['prenom']."@gmail.com");
            $employee->setEmbauche(new \DateTime());
            $employee->setArchive(rand(0,1));
            $employee->setCout(rand(150,300));
            $employee->setImage('default.jpg');
            $manager->persist($employee);
        }
        $manager->flush();

        $type=array("Capex", "Opex");
        // ajout de projet
        for ($i=0 ; $i<5; $i++){
            $project= new Project();
            $project->setTitre("projet n°".$i);
            $project->setDescription("description du projet n°".$i);
            $project->setDateCreation(new \DateTime());
            $project->setDelivery(rand(0,1));
            $project->setTypeProjet($type[rand(0,1)]);
            $manager->persist($project);
        }
        $manager->flush();

        //ajout des temps de production

        for ($i=0; $i<20 ; $i++){
            $timeTracking = new TimeTracking();
            $project=$manager->getRepository('AppBundle:Project')->find(rand(1,5));
            $timeTracking ->setIdProject($project);
            $employee=$manager->getRepository('AppBundle:Employee')->find(rand(1,4));
            $timeTracking ->setIdEmployee($employee);
            $timeTracking->setNbJour(rand(1,10));
            $manager->persist($timeTracking);
        }
        $manager->flush();
    }
}
?>