<?php

namespace App\Controller\Admin;

use App\Form\CsvUploaderType;
use App\Services\ParticipantsCsvFileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParticipantsCsvFileController extends AbstractController
{
    #[Route('/admin/participants/csv', name: 'app_participants_csv_file')]
    public function index(
        ParticipantsCsvFileService $participantCsv,
        Request $request
    ): Response
    {

        //FORM AVEC UN CHAMP POUR LE FICHIER
        $form = $this->createForm(CsvUploaderType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $csvfile = $form->get('csvfile')->getData();
            if ($csvfile){
                $participantCsv->createParticipants($csvfile);
                return $this->redirectToRoute('admin') ;
            }
        }

        return $this->render('admin/participants_csv.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
