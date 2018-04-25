<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use AppBundle\Entity\Project;

class EmployeeTimeTrackingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('idProject',EntityType::class, array(
            'class' => 'AppBundle:Project',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->where('u.delivery=false');
            },
            'label' => 'Attribuer un projet',
        ));
        $builder->add('nbJour', IntegerType::class, array('label' => 'Nombre de jour'));
    }

    public function getName(){
        return 'employeetimetracking_form';
    }
}
