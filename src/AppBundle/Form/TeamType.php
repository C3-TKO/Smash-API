<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use AppBundle\Form\PlayerType;

class TeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', IntegerType::class, ['disabled' => $options['is_edit']])
            ->add('id_player_a', EntityType::class, ['class' => 'AppBundle:Player'])
            ->add('id_player_b', EntityType::class, ['class' => 'AppBundle:Player'])
            ->add('name', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Team',
            'is_edit' => false,
            // Setting the csrf protection globally to false as this api will not store the user within session cookies
            // Keep in mind that the protection should be turned on again if his form type will be used within an ui
            // form as well!
            'csrf_protection' => false
        ]);
    }

    public function getName()
    {
        return 'team';
    }
}
