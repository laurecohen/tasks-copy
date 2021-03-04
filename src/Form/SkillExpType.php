<?php

namespace App\Form;

use App\Entity\UserSkill;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SkillExpType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('exp', ChoiceType::class, [
                'choices' => [
                    'habitué.e' => 'habitué.e',
                    'peu à l\'aise' => 'peu à l\'aise',
                ],
                'attr' => [
                    'class' => 'custom-select',
                ],
                'label' => false
            ])
            ->add('skill', null, [
                'disabled' => true,
                'attr' => [
                    'class' => 'custom-select',
                    'readonly' => true
                ],
                'label' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserSkill::class,
        ]);
    }
}
