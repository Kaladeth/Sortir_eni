<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie')]
class SortieController extends AbstractController
{
    #[Route('/', name: 'app_sortie_index', methods: ['GET'])]
    public function index(SortieRepository $sortieRepository,
                          EntityManagerInterface $entityManager,
                          EtatRepository $etatRepository
    ): Response
    {
        $etatArchive = $etatRepository->findOneBy(
            [
                'id'=>7
            ]
        );

        date_default_timezone_set('Europe/Paris');
        $dateNow = new \DateTime("now");
        $sorties = $sortieRepository->findAll();

        foreach ($sorties as $sort)
        {
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
            'sorties' => $sortieRepository->findAll(),
        ]);
    }

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

        return $this->render('sortie/index.html.twig', [
            'sorties' => $sortieRepository->findAll(),
        ]);
    }

    #[Route('/', name: 'app_sortie_index_filtre', methods: ['POST'])]
    public function indexFiltre(SortieRepository $sortieRepository): Response
    {

        return $this->render('sortie/index.html.twig', [
            'sorties' => $sortieRepository->findBy(
                ['site' => $_POST['site_filter']]
            ),
        ]);
    }

    #[Route('/new', name: 'app_sortie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SortieRepository $sortieRepository, EtatRepository $etatRepository): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $etat = $etatRepository->findOneBy([
                'id'=>1

            ]);
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

    #[Route('/details/{id}', name: 'app_sortie_show', methods: ['GET'])]
    public function show(Sortie $sortie): Response
    {
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    //METHODE POUR MODIFIER UNE SORTIE
    #[Route('/{id}/edit', name: 'app_sortie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortieRepository->save($sortie, true);

            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    #[Route('/annuler/{id}', name: 'app_sortie_cancel', methods: ['GET','POST'])]
    public function cancel(Request $request, Sortie $sortie, SortieRepository $sortieRepository,
                           EtatRepository $etatRepository): Response
    {
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $etat = $etatRepository->findOneBy(
                [
                    'id'=> 6
                ]
            );
            $sortie->setEtatSortie($etat);

            $sortieRepository->save($sortie,true);
            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/cancel.html.twig', [
            'sortie' => $sortie,
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
