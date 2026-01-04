<?php

namespace App\Controller\Admin;

use App\Entity\SubscriptionPlan;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{TextField, MoneyField, BooleanField, TextareaField, AssociationField};
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class SubscriptionPlanCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SubscriptionPlan::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name');
        yield MoneyField::new('price')->setCurrency('EUR');
        yield TextareaField::new('description')->hideOnIndex();
        yield BooleanField::new('enligne');

        // ✅ LE CHAMP QUI TE MANQUE :
        yield AssociationField::new('features')
            ->setHelp('Features incluses dans ce plan (ex: Newsletter IA hebdomadaire).')
            ->setFormTypeOption('by_reference', false);
    }
}
