<?php

namespace App\Controller\Admin;

use App\Entity\Sortie;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;


class SortieCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Sortie::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // set this option if you prefer the page content to span the entire
            // browser width, instead of the default design which sets a max width
            //->renderContentMaximized()

            // set this option if you prefer the sidebar (which contains the main menu)
            // to be displayed as a narrow column instead of the default expanded design
            ->renderSidebarMinimized()
            ->setDateTimeFormat('d-M-Y à H:m')
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nom', 'Nom de la sortie'),
            TextField::new('infosSortie', 'Informations'),
            AssociationField::new('organisateur', 'Organisateur'),
            AssociationField::new('lieu', 'Lieu'),
            DateTimeField::new('dateHeureDebut', 'Date et heure de début')->hideOnIndex(),
            IntegerField::new('duree', 'Durée (en minutes)')->hideOnIndex(),
            DateTimeField::new('dateLimiteInscription', 'Date limite d\'inscription')->hideOnIndex(),
            IntegerField::new('nbInscriptionsMax', 'Nombre max d\'inscrits')->hideOnIndex(),
            AssociationField::new('etatSortie', 'Etat'),
            AssociationField::new('site', 'Site')->hideOnIndex(),
            AssociationField::new('participants', 'Participants'),
            ImageField::new('imageName')
                ->setBasePath('img/uploads/')
                ->setUploadDir('public/img/uploads/')
                ->hideOnForm(),
            TextareaField::new('imageFile')->setFormType(VichImageType::class)->hideOnIndex()
        ];
    }
}
