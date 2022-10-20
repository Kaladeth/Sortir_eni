<?php

namespace App\Controller\Admin;

use App\Entity\Participant;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ParticipantCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Participant::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            EmailField::new('email', 'Email'),
            TextField::new('password', 'Mot de passe')->hideOnIndex(),
            TextField::new('nom', 'Nom'),
            TextField::new('prenom', 'Prenom'),
            NumberField::new('telephone', 'Tél.'),
            ArrayField::new('roles', 'Rôle')->hideOnIndex(),
            BooleanField::new('administrateur', 'Statut admin.'),
            BooleanField::new('actif', 'En activité'),
            AssociationField::new('site', 'Site'),
            AssociationField::new('sorties', 'Sorties'),
            AssociationField::new('sortiesOrganisees', 'Sorties Organisées'),
            ImageField::new('imageName')
                ->setBasePath('img/uploads/')
                ->setUploadDir('public/img/uploads/')
                ->hideOnForm(),
            TextareaField::new('imageFile')->setFormType(VichImageType::class)->hideOnIndex()
        ];
    }
}
