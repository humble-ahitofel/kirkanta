<?php

namespace App\Module\ApiCache\EventListener;

use App\Entity\EntityDataBase;
use App\Module\ApiCache\DocumentManager;
use App\Module\ApiCache\Entity\Feature\ApiCacheable;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class CacheEntity
{
    private $manager;

    public function __construct(DocumentManager $manager)
    {
        $this->manager = $manager;
    }

    public function preUpdate(PreUpdateEventArgs $event) : void
    {
    }

    public function postUpdate(LifecycleEventArgs $event) : void
    {
        if ($entity = $this->getParentEntity($event->getEntity())) {
            $this->manager->write($entity);
        }
    }

    private function getParentEntity($entity)
    {
        /*
         * NOTE: Don't try to optimize by just checking if getFoo() exists and returning its value
         * straight; some entities might have many of the methods and only some of them might
         * return the parent.
         */

        if ($entity instanceof ApiCacheable) {
            return $entity;
        }

        if ($entity instanceof EntityDataBase) {
            return $this->getParentEntity($entity->getEntity());
        }

        if (method_exists($entity, 'getParent')) {
            if ($parent = $entity->getParent()) {
                return $parent;
            }
        }

        if (method_exists($entity, 'getLibrary')) {
            if ($parent = $entity->getLibrary()) {
                return $parent;
            }
        }

        if (method_exists($entity, 'getFinnaOrganisation')) {
            if ($parent = $entity->getFinnaOrganisation()) {
                return $parent;
            }
        }

        if (method_exists($entity, 'getConsortium')) {
            if ($parent = $entity->getConsortium()) {
                return $parent;
            }
        }
    }
}
