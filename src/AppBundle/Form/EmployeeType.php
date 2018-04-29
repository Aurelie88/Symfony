<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AppBundle\Entity\Job;
use Symfony\Component\Form\Extension\Core\Type\EmailType;



class EmployeeType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('prenom', TextType::class, array('label' => 'Prénom'));
        $builder->add('nom',TextType::class, array('label' => 'Nom'));
        $builder->add('email', EmailType::class, array('label' => 'Email'));
        //$builder->add('metier', Job::class, array('label' => 'Métier'));
        $builder->add('metier', EntityType::class, array(
            'class' => 'AppBundle:Job',
            'label' => 'Attribuer un métier à l\'utilisateur',
        ));
        $builder->add('cout',IntegerType::class, array('label' => 'Coût journalier'));
        $builder->add('embauche', DateType::class, array('label' => 'Date d\'embauche'));
        $builder->add('image', FileType::class, array('label' => 'image (PNG)'));
    }

    public function getName(){
        return 'employee_form';
    }

}
