<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieAnnuleeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
//            ->add('nom')
//            ->add('dateHeureDebut')
//            ->add('duree')
//            ->add('dateLimiteInscription')
//            ->add('nbInscriptionsMax')
            ->add('infosSortie')
//            ->add('image')
//            ->add('lieu', EntityType::class,
//                [
//                    'class'=>Lieu::class,
//                    "choice_label"=>"nom",
//                ])
//            ->add('etatSortie')
//            ->add('site')
//            ->add('participants')
//            ->add('organisateur')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}