<?php

namespace App\Controller\Admin;

use App\Entity\Settings;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SettingsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Settings::class;
    }

    protected function getRedirectResponseAfterSave(AdminContext $context, string $action): RedirectResponse
    {
//        /** @var Tag $tag */
//        $tag = $context->getEntity()->getInstance();
        $submitButtonName = $context->getRequest()->request->all()['ea']['newForm']['btn'];
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        if (Action::SAVE_AND_RETURN === $submitButtonName) {
            $url = $context->getReferrer()
                ?? $this->container
                    ->get(AdminUrlGenerator::class)
                    ->setAction(Action::INDEX)
                    ->setEntityId(1)
                    ->generateUrl();

            return $this->redirect($url);
        }
//        if ($tag->isPublic() && $tag->isEditable()) {
//            $url = $adminUrlGenerator
//                ->setAction(Action::EDIT)
//                ->setEntityId(1)
//                ->generateUrl()
//            ;
//
//            return $this->redirect($url);
//        }

        return parent::getRedirectResponseAfterSave($context, $action);
    }

    #[Route('/admin/settings/index', name: 'admin_settings_index')]
    public function index(AdminContext $context)
    {
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
        $url = $routeBuilder
            ->setController(SettingsCrudController::class)
            ->setAction(Action::EDIT)
            ->setEntityId(1)
            ->generateUrl();
        return $this->redirect($url);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setPageTitle('edit', 'Site settings');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IntegerField::new('count_book_element')
                ->setLabel('Amount of elements on the page:'),
            TextField::new('support_email')
                ->setLabel('Support e-mail'),
            TextField::new('parse_url')
                ->setLabel('URL source'),
        ];
    }
}
