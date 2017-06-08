<?php

namespace CalendarBundle\Form;

use CalendarBundle\Entity\Dayoff;
use CalendarBundle\Form\DataTransformer\DateToStringTransformer;
use CalendarBundle\Form\Object\DayOffRangeObject;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;

class DayOffRangeType extends AbstractType
{

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('comment', Type\TextType::class, [
                'required' => false,
                'attr'     => [
                    'class' => 'form-control',
                ]
            ])

            ->add('type', Type\ChoiceType::class, [
                'choices'     => Dayoff::getDayoffTypes(),
                'empty_value' => 'Choose someone',
                'attr'        => [
                    'class' => 'form-control'
                ]
            ])

            ->add('startDate', Type\TextType::class, [
                'label'    => 'Select Dates',
                'attr'     => [
                    'class'       => 'form-control',
                    'placeholder' => 'Select Dates'
                ],
            ])

            ->add('endDate', Type\TextType::class, [
                'label'    => false,
                'attr'     => [
                    'class'       => 'form-control',
                    'placeholder' => 'Select Dates'
                ],
            ])
        ;

        $builder->get('startDate')
            ->addModelTransformer(new DateToStringTransformer());

        $builder->get('endDate')
            ->addModelTransformer(new DateToStringTransformer());

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DayOffRangeObject::class
        ]);
    }

}