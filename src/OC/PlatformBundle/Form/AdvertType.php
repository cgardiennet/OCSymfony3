<?php

namespace OC\PlatformBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvertType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('date', DateType::class, array('html5' => false, 'format' => 'ddMMMMyyyy', 'label' => 'Date'))
            ->add('title', TextType::class, array('label' => 'Title'))
            ->add('author', TextType::class, array('label' => 'Author'))
            ->add('email', EmailType::class, array('label' => 'Email'))
            ->add('content', TextareaType::class, array('label' => 'Content'))
            ->add('published', CheckboxType::class, array('label' => 'Published'))
            ->add('image', ImageType::class, array('required' => false, 'label' => 'Image'))
            ->add(
                'categories',
                EntityType::class,
                array(
                    'class' => 'OCPlatformBundle:Category',
                    'choice_label' => 'name',
                    'multiple' => true,
                    'expanded' => true,
                    'required' => false,
                    'label' => 'Categories'
                )
            )
            ->add('save', SubmitType::class, array('label' => 'Save'))
            ->add('cancel', ResetType::class, array('label' => 'Reset form'))
        ;

    }

    public function configureOptions (OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => 'OC\PlatformBundle\Entity\Advert'
            ))
        ;
    }
}