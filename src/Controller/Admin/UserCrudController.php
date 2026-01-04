<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield EmailField::new('email', 'Identifiant Agent');
        yield ArrayField::new('roles', 'Permissions');
        yield BooleanField::new('isVerified', 'Statut Vérifié')->renderAsSwitch(true);

        // On n'affiche le mot de passe QUE sur la page de création (NEW)
        // Sur la page d'édition (EDIT), on le cache pour éviter d'écraser le hash
        yield TextField::new('password', 'Clé Accès')
        ->onlyOnForms()
        ->onlyWhenCreating()
        ->setRequired(true);
}
}
