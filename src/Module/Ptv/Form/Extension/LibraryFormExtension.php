<?php

namespace App\Module\Ptv\Form\Extension;

use App\EntityTypeManager;
use App\Entity\Library;
use App\Entity\Feature\GroupOwnership;
use App\Form\LibraryForm;
use App\Module\Ptv\Entity\PtvData;
use App\Module\Ptv\Form\PtvDataType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Handles processing of PTV configuration for Library entities.
 */
class LibraryFormExtension extends AbstractTypeExtension
{
    const REQUIRED_ROLE = 'ROLE_PTV';

    private $types;

    public function __construct(EntityTypeManager $types) {
        $this->types = $types;
    }

    public function getExtendedType() : string
    {
        return LibraryForm::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) use($builder) {
            $library = $event->getData();
            $form = $event->getForm();

            if (($library instanceof Library) && $this->isPtvAllowed($library)) {
                $data = $this->types->getRepository('ptv_data')->findOneBy([
                    'entity_type' => 'library',
                    'entity_id' => $library->getId(),
                ]);

                if (!$data) {
                    $data = new PtvData('library', $library->getId());
                }

                $form->add('ptv', PtvDataType::class, [
                    'required' => false,
                    'mapped' => false,
                    'label' => 'PTV Info',
                    'data' => $data,
                ]);
            }
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) {
            $form = $event->getForm();

            if ($form->isValid() && $form->has('ptv')) {
                $data = $event->getForm()->get('ptv')->getData();
                $this->types->getEntityManager()->persist($data);
                $this->types->getEntityManager()->flush($data);
            }
        });
    }

    protected function isPtvAllowed(GroupOwnership $entity)
    {
        $roles = $entity->getOwner()->getRoles();
        return in_array(self::REQUIRED_ROLE, $roles, true);
    }
}