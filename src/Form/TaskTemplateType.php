<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\TaskTemplate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

class TaskTemplateType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'label' => "Catégorie",
                'attr' => [
                    'class' => 'custom-select',
                ],
            ])
            ->add('dayOfWeek', ChoiceType::class, [
                'choices' => [
                    'lundi'    => 1,
                    'mardi'    => 2,
                    'mercredi' => 3,
                    'jeudi'    => 4,
                    'vendredi' => 5,
                    'samedi'   => 6,
                    'dimanche' => 7
                ],
                'label' => "Jour de la semaine",
                'attr' => [
                    'class' => 'custom-select',
                ],
            ])
            ->add('startAt', TimeType::class, [
                'widget' => 'single_text',
                'label' => "Début",
                'attr' => [
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Vous devez renseigner un horaire de début."
                    ]),
                ]
            ])
            ->add('endAt', TimeType::class, [
                'widget' => 'single_text',
                'label' => "Fin",
                'attr' => [
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Vous devez renseigner un horaire de fin."
                    ]),
                    new GreaterThan([
                        'propertyPath' => 'parent.all[startAt].data',
                        'message' => "L'horaire de fin ne peut pas être antérieur à l'horaire de début."
                    ])
                ]
            ])
            ->add('membersMin', IntegerType::class, [
                'label' => "Minimum",
                'constraints' => [
                    new NotBlank([
                        'message' => "Vous devez renseigner un nombre de participants."
                    ]),
                    new Positive([
                        'message' => "Le nombre de participants doit être supérieur à zéro."
                    ]),
                    new LessThanOrEqual([
                        'propertyPath' => 'parent.all[membersMax].data',
                        'message' => "Le nombre de participants total doit être inférieur ou égal à {{ compared_value }}.",
                    ])
                ],
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add('membersMax', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => "Maximum",
                'constraints' => [
                    new NotBlank([
                        'message' => "Vous devez renseigner un nombre de participants."
                    ]),
                    new GreaterThanOrEqual([
                        'propertyPath' => 'parent.all[membersMin].data',
                        'message' => "Le nombre de participants total doit être supérieur ou égal à {{ compared_value }}.",
                    ])
                ],
            ])
            ->add('isRecurrent', CheckboxType::class, [
                'attr' => [
                    'class' => 'form-check',
                ],
                'label' => "Activer la répétition du modèle",
                'help' => "Cocher cette case pour répéter le créneau chaque semaine.",
                'required' => false,
            ])
        ;

        $builder
            ->add('templateSkills', CollectionType::class, [
                'entry_type' => TemplateSkillType::class,
                'entry_options' => [
                    'label' => true
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
                'label' => "Compétences",
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TaskTemplate::class,
        ]);
    }
}
