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
//                    'constraints'=>
//                    [
//                        new DateTime()
//                    ]

                ])
            ->add('dateLimiteInscription', null,
                [
                    "attr"=>["class"=>"dli_ajout"]
                ])
            ->add('nbInscriptionsMax',null,
                [
                ])
            ->add('infosSortie', TextareaType::class,
                [
                    "attr"=>["class"=>"motifAnnulation"]
                ])
            ->add('imageFile',VichImageType::class,
                [
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
