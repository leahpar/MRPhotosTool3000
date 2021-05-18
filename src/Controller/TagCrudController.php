<?php

namespace App\Controller;

use App\Entity\Tag;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field;

class TagCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tag::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            'nom',
            'tags',
            Field\TextareaField::new('tags')
            ->setCustomOption(Field\TextareaField::OPTION_MAX_LENGTH, 1024)
        ];
    }

}
