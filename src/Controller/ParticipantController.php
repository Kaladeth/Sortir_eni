<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/participant')]
class ParticipantController extends AbstractController
{
    //AFFICHER TOUS LES PARTICIPANTS
    #[Route('/', name: 'app_participant_index', methods: ['GET'])]
    public function index(ParticipantRepository $participantRepository): Response
    {
        return $this->render('participant/index.html.twig', [
            'participants' => $participantRepository->findAll(),
        ]);
    }

    //CREER UN NOUVEAU PARTICIPANT
    #[Route('/new', name: 'app_participant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ParticipantRepository $participantRepository): Response
    {
        $participant = new Participant();
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $participantRepository->save($participant, true);

            return $this->redirectToRoute('app_participant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('participant/new.html.twig', [
            'participant' => $participant,
            'form' => $form,
        ]);
    }

    //AFFICHER LE DETAIL D'UN PARTICIPANT
    #[Route('/detail/{id}', name: 'app_participant_show', methods: ['GET'])]
    public function show(Participant $participant): Response
    {

        return $this->render('participant/show.html.twig', [
            'participant' => $participant,
        ]);
    }

    //MODIFIER UN PARTICIPANT
    #[Route('/{id}/edit', name: 'app_participant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Participant $participant, ParticipantRepository $participantRepository): Response
    {
        if($this->getUser() === $participant) {
            $form = $this->createForm(ParticipantType::class, $participant);
            $form->handleRequest($request);
        }
        else {
            $this->addFlash(
                'notice',
                'Vous ne pouvez pas modifier la page d\'un autre utilisateur !!'
            );
            return $this->redirectToRoute('accueil_main', [], Response::HTTP_SEE_OTHER);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $participantRepository->save($participant, true);
            $participant->setImageFile(null);
            return $this->redirectToRoute('app_participant_edit',['id' => $participant->getId()], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('participant/edit.html.twig', [
            'form' => $form,
            'participant' => $participant
        ]);
    }

    //SUPPRIMER UN PARTICIPANT
    #[Route('/{id}', name: 'app_participant_delete', methods: ['POST'])]
    public function delete(Request $request, Participant $participant, ParticipantRepository $participantRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$participant->getId(), $request->request->get('_token'))) {
            $participantRepository->remove($participant, true);
        }

        return $this->redirectToRoute('app_participant_index', [], Response::HTTP_SEE_OTHER);
    }

    //PARTICIPANT S'INSCRIT A UNE SORTIE
    #[Route('/ajouter/sortie/{id}', name: 'app_participant_add_sortie')]
    public function addParticipant(
        Request $request,
        int $id,
        EntityManagerInterface $entityManager,
        SortieRepository $sortieRepository,
        ParticipantRepository $participantRepository
    ): Response
    {
        $participant = $participantRepository->findOneBy([
            "email" => $this->getUser()->getUserIdentifier()
        ]);

        $sortie = $sortieRepository->findOneBy([
            'id' => $id
        ]);

        $datenow = new \DateTime("now");
        if (count($sortie->getParticipants()) <= $sortie->getNbInscriptionsMax() && $datenow<$sortie->getDateLimiteInscription())
        {
            $sortie->addParticipant($participant);
            $participant->addSortie($sortie);
            $entityManager->persist($sortie);
            $entityManager->persist($participant);
            $entityManager->flush();
            $this->addFlash(
                'gestionInscriptions',
                'Bravo, vous êtes inscrit à la sortie !'
            );
            return $this->redirectToRoute('app_sortie_index');

        }

        $this->addFlash(
            'gestionInscriptions',
            'Impossible de vous inscrire à la sortie !'
        );

        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }

    //PARTICIPANT SE DESINSCRIT D'UNE SORTIE
    #[Route('/suppr/sortie/{id}', name: 'app_participant_del_sortie')]
    public function delParticipant(
        Request $request,
        int $id,
        EntityManagerInterface $entityManager,
        SortieRepository $sortieRepository,
        ParticipantRepository $participantRepository
    ): Response
    {
        $participant = $participantRepository->findOneBy([
            "email" => $this->getUser()->getUserIdentifier()
        ]);

        $sortie = $sortieRepository->findOneBy([
            'id' => $id
        ]);

        $datenow = new \DateTime("now");

        if ($datenow<$sortie->getDateHeureDebut())
        {
            $sortie->removeParticipant($participant);
            $participant->removeSortie($sortie) ;
            $entityManager->persist($sortie);
            $entityManager->persist($participant);
            $entityManager->flush();
            $this->addFlash(
                'gestionInscriptions',
                'Vous êtes désinscrit de la sortie !'
            );
            return $this->redirectToRoute('app_sortie_index');

        }

        $this->addFlash(
            'gestionInscriptions',
            'Impossible de vous désinscrire !'
        );

        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }
}