<?php

namespace App\Controller\Admin;

use App\Entity\Feedback;
use App\Enum\FeedbackStatus;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;

class FeedbackCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Feedback::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            DateTimeField::new('sent_at')
                ->setLabel('time')
//                ->formatValue('')
                ->setFormat('ago')
                ->setSortable(true),
            TextField::new('name'),
            ChoiceField::new('status')->setChoices(FeedbackStatus::cases())
                ->setSortable(true),
            TextField::new('email'),
            TextField::new('topic'),
            TextEditorField::new('text'),
        ];
    }

}
