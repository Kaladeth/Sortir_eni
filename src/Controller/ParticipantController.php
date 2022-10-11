<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParticipantController extends AbstractController
{
    #[Route('/participant/modifier_profil/{id}',
        name: 'app_participant',
        requirements:['id'=>'\d+']
    )]
    public function modifierProfil(
        int                   $id,
        ParticipantRepository $participantRepository
    ): Response
    {
        $user = $participantRepository->findOneBy(
            ['id'=>$id]
        );
        return $this->render('participant/modifier_profil.html.twig',
            [
                'user' => $user,
            ]);
    }
}