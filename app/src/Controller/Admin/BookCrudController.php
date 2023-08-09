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
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
            ChoiceField::new('status')->setChoices(BookStatus::cases()),
            TextField::new('isbn'),
            TextField::new('title'),
            AssociationField::new('authors')
                ->setFormTypeOptionIfNotSet('by_reference', false),
            DateField::new('published'),

            AssociationField::new('categories')
                ->setFormTypeOptionIfNotSet('by_reference', false),

            ImageField::new('cover_image')
                ->setBasePath('uploads/images/')
                ->setUploadDir('public/uploads/images/')
                ->setUploadedFileNamePattern(
                    fn (UploadedFile $file): string => sprintf('%s.%s', md5(random_int(1, 999) . $file->getFilename() . $file->guessExtension()), $file->guessExtension())
                ),

            IntegerField::new('pages'),
//            TextField::new('cover_image'), // TODO: https://symfony.com/bundles/EasyAdminBundle/current/fields/ImageField.html#setuploaddir
            TextareaField::new('short_description')->setRequired(false),
            TextEditorField::new('long_description')->setRequired(false),
        ];
    }
}
