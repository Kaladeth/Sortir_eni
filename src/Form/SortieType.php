<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut', null,
            [
                "attr"=>["class"=>"dhd_ajout"]
            ]
            )
            ->add('duree')
            ->add('dateLimiteInscription', null,
                [
                    "attr"=>["class"=>"dli_ajout"]
                ])
            ->add('nbInscriptionsMax')
            ->add('infosSortie')
            ->add('image')
            ->add('lieu', EntityType::class,
            [
                'class'=>Lieu::class,
                "choice_label"=>"nom",
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
