<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParticipantsCsvFileController extends AbstractController
{
    #[Route('/admin/participants/csv', name: 'app_participants_csv_file')]
    public function index(): Response
    {


        return $this->render('admin/participants_csv.html.twig');
    }
}
