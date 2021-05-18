<?php

namespace App\Filter;

use App\Form\DateSemaineFilterType;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Filter\FilterInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FilterDataDto;
use EasyCorp\Bundle\EasyAdminBundle\Filter\FilterTrait;

class DateSemaineFilter implements FilterInterface
{
    use FilterTrait;

    public static function new(string $propertyName, $label = null): self
    {
        return (new self())
            ->setFilterFqcn(__CLASS__)
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setFormType(DateSemaineFilterType::class)
            ->setFormTypeOption('data', 0);
    }

    public function apply(QueryBuilder $queryBuilder, FilterDataDto $filterDataDto, ?FieldDto $fieldDto, EntityDto $entityDto): void
    {
        if ($filterDataDto->getValue() == 0) {
            // à publier
            $queryBuilder->andWhere(sprintf('%s.%s >= :thisweek', $filterDataDto->getEntityAlias(), $filterDataDto->getProperty()))
                ->setParameter('thisweek', (new \DateTime('last monday'))->format('Y-m-d'));
        }
        if ($filterDataDto->getValue() == 1) {
            // publié
            $queryBuilder->andWhere(sprintf('%s.%s <= :nextweek', $filterDataDto->getEntityAlias(), $filterDataDto->getProperty()))
                ->setParameter('nextweek', (new \DateTime('next monday'))->format('Y-m-d'));
        }
        // else: toutes les publications => pas de filtre

    }
}