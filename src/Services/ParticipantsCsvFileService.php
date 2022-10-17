<?php

namespace App\Services;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ParticipantsCsvFileService
{
    private EntityManagerInterface $entityManager;
    private string $dataDirectory;
    private ParticipantRepository $participantRepository;
    private SiteRepository $siteRepository;
    private UserPasswordHasherInterface $passwordHasher;


    public function __construct(
        EntityManagerInterface $entityManager,
        string $dataDirectory,
        ParticipantRepository $participantRepository,
        UserPasswordHasherInterface $passwordHasher,
        SiteRepository $siteRepository
    ){
        //parent::__construct();
        $this->dataDirectory = $dataDirectory;
        $this->entityManager = $entityManager;
        $this->participantRepository = $participantRepository;
        $this->passwordHasher = $passwordHasher;
        $this->siteRepository = $siteRepository;
    }

    public function getDataFromFile(string $fileName):array{
        $file = $this->dataDirectory.$fileName;
        $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
        $normalizers = [new ObjectNormalizer()];
        $encoders = [new CsvEncoder()];
        $serializer = new Serializer($normalizers, $encoders);
        /** @var $fileString */
        $fileString = file_get_contents($file);
        $data = $serializer->decode($fileString, $fileExtension);

        //TENIR COMPTE STRUCTURE TABLEAU
        if (array_key_exists('results', $data)){
            return $data['results'];
        }
        return $data;
    }

    public function createParticipants(
        string $fileName
    ) : void {
        $participantsCrees = 0 ;
        $data = $this->getDataFromFile($fileName);
        foreach ($data as $row) {
            if (array_key_exists('email', $row) && !empty($row['email'])) {
                $participant = $this->participantRepository->findOneBy([
                    'email' => $row['email']
                ]);
                if (!$participant){
                    $site = $this->siteRepository->findOneBy(['id'=>$row['site_id']]);
;                   $participant = new Participant();
                    $stringRole1 = trim($row['roles'], '[');
                    $stringRole2 = trim($stringRole1, ']');
                    $stringRole3 = trim($stringRole2, '"');
                    $participant->setEmail($row['email'])
                                ->setRoles(array($stringRole3))
                                ->setPassword($this->passwordHasher->hashPassword($participant, $row['password']))
                                ->setNom($row['nom'])
                                ->setPrenom($row['prenom'])
                                ->setTelephone($row['telephone'])
                                ->setAdministrateur($row['administrateur'])
                                ->setActif($row['actif'])
                                ->setSite($site);
                    $this->entityManager->persist($participant);
                    $participantsCrees ++;
                }
            }
        }
        $this->entityManager->flush();
    }
}