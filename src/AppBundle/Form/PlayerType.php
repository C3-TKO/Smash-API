<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class PlayerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', IntegerType::class, ['disabled' => $options['is_edit']])
            ->add('name', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Player',
            'is_edit' => false,
            // Setting the csrf protection globally to false as this api will not store the user within session cookies
            // Keep in mind that the protection should be turned on again if his form type will be used within an ui
            // form as well!
            'csrf_protection' => false
        ]);
    }

    public function getName()
    {
        return 'player';
    }
}
