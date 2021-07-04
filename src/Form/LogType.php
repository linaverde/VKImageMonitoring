<?php

namespace App\Form;

use App\Entity\Log;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ip')
            ->add('uri')
            ->add('user')
            ->add('method')
            ->add('request_time')
            ->add('zone')
            ->add('code')
            ->add('referral')
            ->add('agent')
            ->add('bytes')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Log::class,
        ]);
    }
}
