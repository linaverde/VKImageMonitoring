<?php

namespace App\Form;

use App\Entity\GroupMonitoringSettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupMonitoringSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('DaysCount', null,
                ['label' => 'Количество дней',
                ])
            ->add('PostsCount', null,
                ['label' => 'Количество постов',
                ])
            ->add('GroupInfo', null, array('attr' => array('style' => 'display:none;',),
                'label' => false,));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GroupMonitoringSettings::class,
        ]);
    }
}
