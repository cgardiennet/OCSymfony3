<?php

namespace OC\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                ['label' => 'Title', 'attr' => ['class' => 'form-control']]
            )
            ->add(
                'author',
                TextType::class,
                ['label' => 'Author', 'attr' => ['class' => 'form-control']]
            )
            ->add(
                'content',
                TextareaType::class,
                ['label' => 'Content', 'attr' => ['class' => 'form-control', 'rows' => 10]]
            )
            ->add(
                'save',
                SubmitType::class,
                ['label' => 'Save', 'attr' => ['class' => 'btn btn-primary']]
            )
            ->add(
                'cancel',
                ResetType::class,
                ['label' => 'Cancel', 'attr' => ['class' => 'btn btn-cancel']]
            )
        ;

    }

    public function configureOptions (OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OC\PlatformBundle\Entity\Advert'
        ));
    }
}