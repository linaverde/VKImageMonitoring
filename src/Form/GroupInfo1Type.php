<?php

namespace App\Form;

use App\Entity\GroupInfo;
use Cassandra\Collection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class GroupInfo1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Name', null, array(
                'label' => 'Название',
            ))
            ->add('Link', null, array(
                'label' => 'Ссылка на группу',
            ))
            ->add('Country', null, array(
                'label' => 'Страна проживания целевой аудитории',
            ))
            ->add('City', null, array(
                'label' => 'Город проживания целевой аудитории',
            ))
            ->add('Gender', ChoiceType::class, [
                'choices' => [
                    '' => null,
                    'Мужчины' => 'M',
                    'Женщины' => 'F',
                ],
                'multiple' => false,
                'label' => 'Пол целевой аудитории',
            ])

//        ->add('Gender', null, array(
//                'label' => 'Пол целевой аудитории',
//            ));
            ->add('MinAge', null, array(
                'label' => 'Минимальный возраст целевой аудитории',
            ))
            ->add('MaxAge', null, array(
                'label' => 'Максимальный возраст целевой аудитории',
            ))
            ->add('User', null, array('attr' => array('style' => 'display:none;',),
                'label' => false,));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GroupInfo::class,
        ]);
    }
}
