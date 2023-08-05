<?php

namespace App\Controller\Admin;

use App\Entity\Book;
use App\Entity\Author;
use Doctrine\ORM\Query\Expr\Select;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use App\Enum\BookStatus;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class BookCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Book::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
//            IdField::new('id'),
            ChoiceField::new('status')->setChoices(BookStatus::cases()),
            TextField::new('isbn'),
            TextField::new('title'),
            AssociationField::new('authors')
                ->setFormTypeOptionIfNotSet('by_reference', false),
            DateField::new('published'),

            AssociationField::new('categories')
                ->setFormTypeOptionIfNotSet('by_reference', false),
//            CollectionField::new('categories')->setEntryType(EntityType::class),
//            CollectionField::new('categories')
//                ->setFormTypeOptions([
//                    'delete_empty' => true,
//                    'by_reference' => false,
//                ])
//                ->setEntryIsComplex(false)
//                ->setCustomOptions([
//                    'allowAdd' => true,
//                    'allowDelete' => true,
//                    'entryType' => 'App\Form\CategoryType',
//                    'showEntryLabel' => false,
//                ]),

            IntegerField::new('pages'),
            TextField::new('cover_image'), // TODO: https://symfony.com/bundles/EasyAdminBundle/current/fields/ImageField.html#setuploaddir
            TextareaField::new('short_description')->setRequired(false),
            TextEditorField::new('long_description')->setRequired(false),
        ];
    }
}
