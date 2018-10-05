<?php

namespace App\Form;

use DateTime;
use App\Entity\Department;
use App\Entity\Period;
use App\Util\FormData;
use App\Util\PeriodSections;
use App\Form\Type\PeriodDayCollectionType;
use App\Form\Type\PeriodDayType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class PeriodForm extends EntityFormType
{
    public function form(FormBuilderInterface $builder, array $options) : void
    {
        $builder
            ->add('valid_from', DateType::class, [
                'widget' => 'single_text',
                'format' => 'YYYY-MM-dd'
            ])
            ->add('valid_until', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'format' => 'YYYY-MM-dd'
            ])
            ->add('days', PeriodDayCollectionType::class, [
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('translations', I18n\EntityDataCollectionType::class, [
                'entry_type' => EntityData\PeriodDataType::class
            ])
            ;

        // Periods are marked non-legacy when they are saved as the data will be converted.
        $builder->add('is_legacy_format', CheckboxType::class, [
            'data' => false
        ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            $form = $event->getForm();
            $period = $event->getData();

            if ($period instanceof FormData) {
                $organisation = $period->getValues()['library'] ?? null;
                $event->setData(null);
            } else {
                $this->fixLegacyFormatDayTranslations($period);
                $organisation = $period->getParent();
            }

            if ($organisation) {
                $form->add('department', EntityType::class, [
                    'required' => false,
                    'class' => Department::class,
                    'choices' => $organisation->getDepartments(),
                    'placeholder' => $organisation->getName(),
                    'help' => 'Attach contact info to a department',
                ]);
            }
        });

        $builder->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) {
            $from = $event->getForm()->get('valid_from');

            if (!$from->getData()) {
                $from->setData(new DateTime);
            }
        });

        $builder->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) {
            $days = $event->getForm()->get('days');

            if (!$days->getData()) {
                $days->setData(array_fill(0, 7, []));
            }
        });

        $builder->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();
            $langcodes = $data ? $data->getTranslations()->getKeys() : ['fi'];

            foreach ($form->get('days') as $day) {
                $info = $day->get('info');
                $values = [];

                if (!$info->getData()) {
                    foreach ($langcodes as $langcode) {
                        $info->add($langcode, TextType::class, [
                            'langcode' => $langcode,
                        ]);

                        $info->get($langcode)->setData(null);
                        $values[$langcode] = null;
                    }

                    $info->setData($values);
                }
            }
        });
    }

    private function fixLegacyFormatDayTranslations(Period $period) : void
    {
        /*
         * In previous Kirkanta 'info' was the Finnish translation for day info; now it must be
         * an array of all translations.
         */
         
        $days = $period->getDays();
        foreach ($days as &$day) {
            if (is_string($day['info'])) {
                $day['info'] = ['fi' => $day['info']];
            }
        }

        $period->setDays($days);
    }
}
