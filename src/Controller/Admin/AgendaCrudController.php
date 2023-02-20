<?php

namespace App\Controller\Admin;

use App\Entity\Agenda;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AgendaCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Agenda::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setPaginatorPageSize(35);
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);
        $actions->remove(Crud::PAGE_INDEX, Action::BATCH_DELETE);
        $actions->remove(Crud::PAGE_INDEX, Action::DELETE);

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        yield BooleanField::new('isEnabled');
        yield TextField::new('name');

        yield TextField::new('slug')
            ->onlyWhenUpdating()
            ->setDisabled();

        yield TextField::new('timezone')->hideOnForm();
    }
}
