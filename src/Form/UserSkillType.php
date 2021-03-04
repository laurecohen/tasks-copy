<?php

namespace App\Form;

use App\Entity\Skill;
use App\Entity\UserSkill;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserSkillType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('skill', EntityType::class, [
                'class' => Skill::class,
                'query_builder' => function (EntityRepository $er) 
                    {
                        return $er->createQueryBuilder('s')
                            ->orderBy('s.label', 'ASC')
                        ;
                    },
                'attr' => [
                    'class' => 'custom-select'
                ],
                'label' => false,
                'placeholder' => 'Choisir un élément',
                'constraints' => [
                    new NotBlank()
                ]
            ])
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
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserSkill::class,
        ]);
    }
}
