<?php

namespace App\Controller\Admin;

use App\Entity\Subscription;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{
    TextField,
    DateTimeField,
    BooleanField,
    AssociationField
};
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class SubscriptionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Subscription::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        // Nom / contrat
        yield TextField::new('name');

        // ✅ relations indispensables
        yield AssociationField::new('user')
            ->setHelp('Utilisateur concerné par l’abonnement');

        yield AssociationField::new('subscriptionplan')
            ->setHelp('Plan tarifaire associé');

        // Dates
        yield DateTimeField::new('startedAt');
        yield DateTimeField::new('endedAt')->setRequired(false);

        // Etat
        yield BooleanField::new('etat');
    }
}
