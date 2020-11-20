<?php

namespace App\Form;

use App\Doctrine\EntityRepository;
use App\Entity\Service;
use App\Entity\ServiceInstance;
use App\Util\ServiceTypes;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceInstanceForm extends EntityFormType
{
    public function configureOptions(OptionsResolver $options) : void
    {
        parent::configureOptions($options);

        $options->setDefaults([
            'data_class' => ServiceInstance::class
        ]);
    }
    
    public function form(FormBuilderInterface $builder, array $options) : void
    {
        parent::form($builder, $options);

        $types = new ServiceTypes();

        $builder
            ->add('template', EntityType::class, [
                'class' => Service::class,
                'placeholder' => '-- Select --',
                'choice_label' => function ($service) use ($types) {
                    return $types->search($service->getType()) . ' -- '. $service->getName();
                },
                'group_by' => 'type',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('s')
                        ->where('t.langcode = :langcode')
                        ->setParameter('langcode', $options['current_langcode'])
                        ->leftJoin('s.translations', 't')
                        ->orderBy('t.name', 'ASC');
                },
            ])
            ->add('for_loan', CheckboxType::class, [
                'required' => false,
                'label' => 'Available for loan',
            ])
            ->add('email', EmailType::class, [
                'required' => false,
            ])
            ->add('phone_number', TelType::class, [
                'required' => false
            ])
            ->add('translations', I18n\EntityDataCollectionType::class, [
                'entry_type' => EntityData\ServiceInstanceDataType::class
            ])
            ;

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) use ($builder) {
            // Template field should be enabled only when creating new service instances.
            if ($event->getForm()->get('template')->getData()) {
                $event->getForm()->remove('template');
            }
        });
    }
}
