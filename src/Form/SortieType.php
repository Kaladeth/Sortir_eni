<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\GreaterThan;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null,
                [
                ])
            ->add('dateHeureDebut', null,
                [
                    "attr"=>["class"=>"dhd_ajout"],

                    "label"=>"Date et heure de la sortie"
                ])
            ->add('duree', null,
                [
                    "attr"=>["placeholder"=>"en minutes"],
                    "label"=>"Durée"
                ])
            ->add('dateLimiteInscription', null,
                [
                    "attr"=>["class"=>"dli_ajout"],
                    "label"=>"Date limite d'inscription"
                ])
            ->add('nbInscriptionsMax',null,
                [
                    "label"=>"Nombre maximum d'inscrits"
                ])
            ->add('infosSortie', TextareaType::class,
                [
                    "attr"=>["class"=>"motifAnnulation"],
                    "label"=>"Description de la sortie"
                ])
            ->add('imageFile',VichImageType::class,
                [
                    "label"=>"Photo"
                ])
            ->add('lieu', EntityType::class,
                [
                    'class'=>Lieu::class,
                    "choice_label"=>strtoupper("nom"),
                    "attr"=>["class"=>"lieu_ajout"]
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
