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

class ProjectTimeTrackingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('idEmployee',EntityType::class, array(
            'class' => 'AppBundle:Employee',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->where('u.archive=false');
            },
            'label' => 'Attribuer un employee',
        ));
        $builder->add('nbJour', IntegerType::class, array('label' => 'Nombre de jour'));
    }

    public function getName(){
        return 'projecttimetracking_form';
    }
}
