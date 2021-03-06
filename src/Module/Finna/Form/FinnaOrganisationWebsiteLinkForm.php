<?php

namespace App\Module\Finna\Form;

use App\Form\WebsiteLinkForm;
use App\Module\Finna\Entity\FinnaOrganisationWebsiteLink;
use App\Module\Finna\WebsiteLinkCategories;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FinnaOrganisationWebsiteLinkForm extends WebsiteLinkForm
{
    public function configureOptions(OptionsResolver $options) : void
    {
        parent::configureOptions($options);

        $options->setDefaults([
            'data_class' => FinnaOrganisationWebsiteLink::class,
        ]);
    }

    public function form(FormBuilderInterface $builder, array $options) : void
    {
        parent::form($builder, $options);

        $builder->add('category', ChoiceType::class, [
            'placeholder' => '-- Select --',
            'choices' => new WebsiteLinkCategories(),
        ]);
    }
}
