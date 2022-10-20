<?php

namespace App\Command;

use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:etat_update',
    description: 'Add a short description for your command',
)]
class EtatUpdateCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    public function __construct(SortieRepository $sortieRepository,
                                EtatRepository $etatRepository,
                                EntityManagerInterface $entityManager)
    {
        $this->sortieRepository = $sortieRepository;
        $this->etatRepository = $etatRepository;
        $this->entityManagerInterface = $entityManager;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {


        $output->writeln([
            'Execution de la commande'
        ]);

//
//        //ARCHIVAGE DES SORTIES TERMINEES DEPUIS +1 MOIS
//        $etatCloture = $this->etatRepository->findOneBy(['id'=>3]);
//        $etatEnCours = $this->etatRepository->findOneBy(['id'=>4]);
//        $etatPasse = $this->etatRepository->findOneBy(['id'=>5]);
//        $etatArchive = $this->etatRepository->findOneBy(['id'=>7]);
//        date_default_timezone_set('Europe/Paris');
//        $dateNow = new \DateTime("now");
//        $sorties = $this->sortieRepository->findAll();
//        foreach ($sorties as $sort)
//        {
//            //etat cloture
//            $dateLimite = $sort->getDateLimiteInscription();
//            if ($dateLimite<$dateNow){
//                $sort->setEtatSortie($etatCloture);
//                $this->entityManagerInterface->persist($sort);
//                $this->entityManagerInterface->flush();
//            }
//
//            //etat en cours
//            $dureeEnCours = $sort->getDuree();
//            $sortieEnCoursDebut = clone $sort->getDateHeureDebut();
//            $sortieEnCoursFin = clone $sortieEnCoursDebut;
//            $sortieEnCoursFin =  $sortieEnCoursFin->modify('+'.$dureeEnCours.' minutes');
//            if ($sortieEnCoursDebut < $dateNow && $sortieEnCoursFin > $dateNow){
//                $sort->setEtatSortie($etatEnCours);
//                $this->entityManagerInterface->persist($sort);
//                $this->entityManagerInterface->flush();
//            }
//
//            //etat passé
//            $duree = $sort->getDuree();
//            $sortiePassee = clone $sort->getDateHeureDebut();
//            $sortiePassee->modify('+'. $duree . ' minutes' );
//            if ($sortiePassee<$dateNow){
//                $sort->setEtatSortie($etatPasse);
//                $this->entityManagerInterface->persist($sort);
//                $this->entityManagerInterface->flush();
//            }
//
//            //etat archive
//            $finDeSortie = clone $sort->getDateHeureDebut();
//            $finDeSortie->add(new \DateInterval('PT745H'));
//            $finDeSortie->add(new \DateInterval('PT'.$sort->getDuree().'M'));
//            if ($finDeSortie<$dateNow){
//                $sort->setEtatSortie($etatArchive);
//                $this->entityManagerInterface->persist($sort);
//                $this->entityManagerInterface->flush();
//            }
//
//        }

        return Command::SUCCESS;
    }
}
