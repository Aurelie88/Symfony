<?php
namespace AppBundle\DataFixtures;

use AppBundle\Entity\Employee;
use AppBundle\Entity\Job;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
	public function load(ObjectManager $manager)
	{
		for ($i=1; $i <15; $i++) { 
			 $employee=new Employee();
        $employee->setNom("nom".$i);
        $employee->setPrenom("prenom".$i);
        $employee->setEmail($i."@gmail.com");
        $employee->setCout(200);
        $employee->setEmbauche(new \DateTime());
        $employee->setMetier(1);       
        $manager->persist($employee);
		}
		$manager->flush();

		$job = new Job();
		$job->setNomMetier("dev");
		$manager->persist($job);

		$job = new Job();
		$job->setNomMetier("manager");
		$manager->persist($job);
		
		$job = new Job();
		$job->setNomMetier("designer");
		$manager->persist($job);
		
		$job = new Job();
		$job->setNomMetier("integrateur");
		$manager->persist($job);
		$manager->flush();
		}

}
?>