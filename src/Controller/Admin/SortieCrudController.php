<?php

namespace App\Controller\Admin;

use App\Entity\Sortie;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SortieCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Sortie::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('nom'),
            DateTimeField::new('dateHeureDebut'),
            IntegerField::new('duree'),
            DateTimeField::new('dateLimiteInscription'),
            IntegerField::new('nbInscriptionsMax'),
            TextField::new('infosSortie'),
            AssociationField::new('lieu'),
            AssociationField::new('etatSortie'),
            AssociationField::new('site'),
            AssociationField::new('participants'),
            AssociationField::new('organisateur'),
            ImageField::new('imageFile')->setUploadDir('/public/img/uploads/')
        ];
    }
}
