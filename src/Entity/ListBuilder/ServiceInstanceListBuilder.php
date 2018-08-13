<?php

namespace App\Entity\ListBuilder;

use Doctrine\ORM\QueryBuilder;
use App\Util\ServiceTypes;

class ServiceInstanceListBuilder extends EntityListBuilder
{
    protected function createQueryBuilder() : QueryBuilder
    {
        $builder = parent::createQueryBuilder()
            ->addSelect('t')
            ->addSelect('td')
            ->join('e.template', 't')
            ->join('t.translations', 'td', 'WITH', 'td.langcode = :langcode')
            ->andWhere('COALESCE(IDENTITY(e.library), 0) = :library')
            ->setParameter('library', 0)
            ;

        $search = $this->getSearch();
        $search['shared'] = true;

        if (isset($search['shared'])) {
            $builder->andWhere('e.shared = :shared');
            $builder->setParameter('shared', $search['shared']);
        }

        if (isset($search['name'])) {
            $builder->andWhere('LOWER(d.name) LIKE LOWER(:name)');
            $builder->setParameter('name', '%' . $search['name'] . '%');
        }

        if (isset($search['type'])) {
            $builder->andWhere('t.type = :type');
            $builder->setParameter('type', $search['type']);
        }

        return $builder;
    }

    public function build(iterable $entities) : iterable
    {
        $types = new ServiceTypes;
        $table = parent::build($entities)
            ->setColumns([
                'standard_name' => ['mapping' => ['td.name']],
                'name' => ['mapping' => ['d.name']],
                'type',
                'description',
                'owner'
            ])
            ->setSortable('standard_name')
            ->setSortable('name')
            ->useAsTemplate('standard_name')
            ->transform('standard_name', function() {
                return '<a href="{{ path("entity.service_instance.edit", {service_instance: row.id}) }}">{{ row.standardName }}</a>';
            })
            ->transform('type', function($s) use ($types) {
                return $types->search($s->getType());
            });

        return $table;
    }
}