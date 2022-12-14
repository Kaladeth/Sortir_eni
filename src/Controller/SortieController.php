<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\SortieAnnuleeType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\ParticipantsCsvFileService;

#[Route('/sortie')]
class SortieController extends AbstractController
{
    //METHODE D'AFFICHAGE DE LA PAGE
    #[Route('/', name: 'app_sortie_index', methods: ['GET'])]
    public function index(SortieRepository $sortieRepository,
                          SiteRepository $siteRepository,
                          EtatRepository $etatRepository,
                          EntityManagerInterface $entityManager
    ): Response
    {

        //ARCHIVAGE DES SORTIES TERMINEES DEPUIS +1 MOIS
        $etatCloture = $etatRepository->findOneBy(['id'=>3]);
        $etatEnCours = $etatRepository->findOneBy(['id'=>4]);
        $etatPasse = $etatRepository->findOneBy(['id'=>5]);
        $etatArchive = $etatRepository->findOneBy(['id'=>7]);
        date_default_timezone_set('Europe/Paris');
        $dateNow = new \DateTime("now");
        $sorties = $sortieRepository->findAll();
        foreach ($sorties as $sort)
        {
            //etat cloture
            $dateLimite = $sort->getDateLimiteInscription();
            if ($dateLimite<$dateNow){
                $sort->setEtatSortie($etatCloture);
                $entityManager->persist($sort);
                $entityManager->flush();
            }

            //etat en cours
            $dureeEnCours = $sort->getDuree();
            $sortieEnCoursDebut = clone $sort->getDateHeureDebut();
            $sortieEnCoursFin = clone $sortieEnCoursDebut;
            $sortieEnCoursFin =  $sortieEnCoursFin->modify('+'.$dureeEnCours.' minutes');
            if ($sortieEnCoursDebut < $dateNow && $sortieEnCoursFin > $dateNow){
                $sort->setEtatSortie($etatEnCours);
                $entityManager->persist($sort);
                $entityManager->flush();
            }

            //etat pass??
            $duree = $sort->getDuree();
            $sortiePassee = clone $sort->getDateHeureDebut();
            $sortiePassee->modify('+'. $duree . ' minutes' );
            if ($sortiePassee<$dateNow){
                $sort->setEtatSortie($etatPasse);
                $entityManager->persist($sort);
                $entityManager->flush();
            }

            //etat archive
            $finDeSortie = clone $sort->getDateHeureDebut();
            $finDeSortie->add(new \DateInterval('PT745H'));
            $finDeSortie->add(new \DateInterval('PT'.$sort->getDuree().'M'));
            if ($finDeSortie<$dateNow){
                $sort->setEtatSortie($etatArchive);
                $entityManager->persist($sort);
                $entityManager->flush();
            }

        }
        return $this->render('sortie/index.html.twig', [
            'sorties' => $sorties,
            'sites' => $siteRepository->findAll()
        ]);
    }

    //METHODE POUR APPLICATION DES FILTRES
    #[Route('/filtres', name: 'app_sortie_index_filtre', methods: ['POST'])]
    public function indexFiltre(SortieRepository           $sortieRepository,
                                ParticipantsCsvFileService $sortieFiltres,
                                SiteRepository             $siteRepository,
                                ParticipantRepository      $participantRepository,
                                Request                    $request
    ): Response
    {
        //GESTION DES FILTRES
        $siteSelection = $request->request->get('site_filter');
        $rechercheTexte = $request->request->get('word_filter');
        $dateDebut = $request->request->get('dateDebut_filter');
        $dateFin = $request->request->get('dateFin_filter');
        $suisOrganisateur = $request->request->get('suisOrganisateur');
        $suisInscrit = $request->request->get('suisInscrit');
        $suisPasInscrit = $request->request->get('suisPasInscrit');
        $sortiesPassees = $request->request->get('sortiesPassees');

        $user = $participantRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]) ;
        $userId = $user->getId();

        $sorties = $sortieRepository->findWithFilters($siteSelection,
                                    $rechercheTexte,
                                    $dateDebut,
                                    $dateFin,
                                    $suisOrganisateur,
                                    $suisInscrit,
                                    $suisPasInscrit,
                                    $sortiesPassees,
                                    $userId);

        return $this->render('sortie/index.html.twig', [
            'sorties' => $sorties,
            'sites' => $siteRepository->findAll()
        ]);
    }

    //METHODE POUR PASSER UNE SORTIE EN PUBLIEE
    #[Route('/{id}', name: 'app_sortie_index_pub', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function indexPublier(SortieRepository $sortieRepository, EtatRepository $etatRepository, int $id): Response
    {
        $sortie =$sortieRepository->findOneBy(
            ['id' => $id]
        );
        $etat = $etatRepository->findOneBy([
            'id'=>2
        ]);
        $sortie->setEtatSortie($etat);
        $sortieRepository->save($sortie, true);
        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);

    }

    //METHODE DE CREATION D'UNE NOUVELLE SORTIE
    #[Route('/new', name: 'app_sortie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SortieRepository $sortieRepository, EtatRepository $etatRepository): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);
//        $sortieDebut = $sortie->getDateHeureDebut()->format('Y-m-d H:i:s');
//        $sortieLimite = $sortie->getDateLimiteInscription()->format('Y-m-d H:i:s');

        if ($form->isSubmitted() && $form->isValid()) {
            $etat = $etatRepository->findOneBy(['id'=>1]);
            $sortie->setEtatSortie($etat);
            $sortie->setOrganisateur($this->getUser());
            $sortie->setSite($this->getUser()->getSite());
            $sortieRepository->save($sortie, true);

            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    //METHODE POUR AFFICHER LES DETAILS D'UNE SORTIE
    #[Route('/details/{id}', name: 'app_sortie_show', methods: ['GET'])]
    public function show(Sortie $sortie): Response
    {
        if ($sortie->getEtatSortie()->getId() === 7) {
            return $this->redirectToRoute('accueil_main');
        }

        $participant = $sortie->getParticipants();
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
            'participant'=>$participant

        ]);
    }

    //METHODE POUR MODIFIER UNE SORTIE
    #[Route('/{id}/edit', name: 'app_sortie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {

        if($this->getUser() === $sortie->getOrganisateur()){
            $form = $this->createForm(SortieType::class, $sortie);
            $form->handleRequest($request);
        }else {
            $this->addFlash(
                'notice',
                'Vous ne pouvez pas modifier la sortie d\'un autre utilisateur !!'
            );
            return $this->redirectToRoute('accueil_main', [], Response::HTTP_SEE_OTHER);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $sortieRepository->save($sortie, true);

            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form
        ]);
    }

    //METHODE POUR ANNULER UNE SORTIE EN TANT QU'ORGANISATEUR
    #[Route('/annuler/{id}', name: 'app_sortie_cancel', methods: ['POST', 'GET'])]
    public function cancel(Request $request, Sortie $sortieAnulee,
                           EtatRepository $etatRepository,
                           SortieRepository $sortieRepository): Response
    {
        $form = $this->createForm(SortieAnnuleeType::class, $sortieAnulee);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $etat = $etatRepository->findOneBy(['id'=> 6]);
            $sortieAnulee->setEtatSortie($etat);
            $sortieRepository->save($sortieAnulee,true);
            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/cancel.html.twig', [
            'sortie' => $sortieAnulee,
            'form'=>$form,
        ]);
    }

    #[Route('/{id}', name: 'app_sortie_delete', methods: ['POST'])]
    public function delete(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sortie->getId(), $request->request->get('_token'))) {
            $sortieRepository->remove($sortie, true);
        }

        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }
}
