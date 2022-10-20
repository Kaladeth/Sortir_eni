<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function save(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findMain()
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder
            ->andWhere('p.etatSortie!=3')
            ->andWhere('p.etatSortie!=6')
            ->andWhere('p.etatSortie!=7');
        $queryBuilder->setMaxResults(3);
        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }

    public function findWithFilters(
        string $nomSite = NULL,
        string $rechercheTexte = NULL,
        string $dateDebut = NULL,
        string $dateFin = NULL,
        bool   $suisOrganisateur = NULL,
        bool   $suisInscrit = NULL,
        bool   $suisPasInscrit = NULL,
        bool   $sortiesPassees = NULL,
        int    $userId
    )
    {
        $query = $this
            ->createQueryBuilder('so')
            ->select('so');

        if (!empty($nomSite)) {
            $query = $query
                ->join('so.site', 'si')
                ->andWhere('si.nom IN (:site)')
                ->setParameter('site', $nomSite);
        }
        if (!empty($rechercheTexte)) {
            $query = $query
                ->andWhere('so.nom LIKE :recherche')
                ->setParameter('recherche', "%{$rechercheTexte}%");
        }
        if (!empty($dateDebut)) {
            $query = $query
                ->andWhere('so.dateHeureDebut > :dateDebutRech')
                ->setParameter('dateDebutRech', $dateDebut);
        }
        if (!empty($dateFin)) {
            $query = $query
                ->andWhere('so.dateHeureDebut < :dateFinRech')
                ->setParameter('dateFinRech', $dateFin);
        }
        if ($suisOrganisateur) {
            $query = $query
                ->andWhere('so.organisateur = :idUser')
                ->setParameter('idUser', $userId);
        }
        if ($suisInscrit) {
            $query = $query
                ->leftjoin('so.participants', 'pa')
                ->andWhere('pa.id IN (:userId)')
                ->setParameter('userId', $userId);
        }
        if ($suisPasInscrit) {
            $tempsQuery = $this->createQueryBuilder('sortie')
                ->select('sortie.id')
                ->leftjoin('sortie.participants', 'participant')
                ->andWhere('participant.id = :userId');

            $query = $query
                ->andWhere('so.id NOT IN (' . $tempsQuery->getDQL() . ')')
                ->setParameter('userId', $userId);
        }
        if ($sortiesPassees) {
            $query = $query
                ->andWhere('so.etatSortie = :etatId')
                ->setParameter('etatId', 5);
        }
        return $query->getQuery()->getResult();
    }

//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
