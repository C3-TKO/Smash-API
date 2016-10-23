<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', IntegerType::class, ['disabled' => $options['is_edit']])
            ->add('id_round', EntityType::class, ['class' => 'AppBundle:Round', 'property_path' => 'round'])
            ->add('id_team_a', EntityType::class, ['class' => 'AppBundle:Team', 'property_path' => 'team_a'])
            ->add('id_team_b', EntityType::class, ['class' => 'AppBundle:Team', 'property_path' => 'team_b'])
            ->add('team_a_score', IntegerType::class)
            ->add('team_a_score', IntegerType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Game',
            'is_edit' => false,
            // Setting the csrf protection globally to false as this api will not store the user within session cookies
            // Keep in mind that the protection should be turned on again if his form type will be used within an ui
            // form as well!
            'csrf_protection' => false
        ]);
    }

    public function getName()
    {
        return 'game';
    }
}
