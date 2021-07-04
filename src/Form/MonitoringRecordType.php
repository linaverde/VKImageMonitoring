<?php

namespace App\Form;

use App\Entity\MonitoringRecord;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class MonitoringRecordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $now = new \DateTime('@'.strtotime('now'));
        $builder
            ->add('Time', DateTimeType::class, array(
                'data' => $now,
                'view_timezone'     => 'Europe/Moscow',
                'label' => 'Время мониторинга (Московское)',
                ))
            ->add('Name', null,
            ['label' => 'Название сканирования',
                'data' => 'Сканирование ' . $now->format('Y/m/d')
                ])
            ->add('GroupLink', null,
            ['label' => 'Группа'])
            ->add('Status', HiddenType::class, [
                'data' => 0])
            ->add('User', null, array('attr' => array('style' => 'display:none;',),
                'label' => false,));
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MonitoringRecord::class,
        ]);
    }
}
