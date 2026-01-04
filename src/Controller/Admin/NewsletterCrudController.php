<?php
// src/Controller/Admin/NewsletterCrudController.php
namespace App\Controller\Admin;

use App\Entity\Newsletter;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{TextField, TextareaField, BooleanField, DateTimeField, CollectionField};

class NewsletterCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Newsletter::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('title');
        yield TextField::new('slug')->setHelp('Unique, ex: veille-ia-2026-01-04');
        yield BooleanField::new('isPublished');
        yield DateTimeField::new('publishedAt')->setRequired(false);

        yield TextareaField::new('excerpt')
            ->setHelp('Affiché aux non-abonnés (≈ 30% du contenu).')
            ->renderAsHtml(false);

        // Si tu veux du HTML, on passera à un WYSIWYG ensuite (CKEditor / Trix)
        yield TextareaField::new('content')
            ->setHelp('Contenu complet réservé aux abonnés.')
            ->renderAsHtml(false);

        yield CollectionField::new('sources')
            ->setEntryIsComplex(true)
            ->setHelp('Ajoute des sources + liens (CNIL, décret, articles, etc.)');
    }
}
