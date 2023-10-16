<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

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

    public function findByFilters($nom, $etat, $dateDebut, $dateFin, $sortieOrganisateur, $participant, $sortiePassee): ?array {
        $qb = $this->createQueryBuilder('s');
        $maintenant = new \DateTime();

        if ($nom !== null){
            $qb->andWhere('s.nom LIKE :nom')
                ->setParameter('nom', '%'.$nom.'%');
        } elseif ($etat !== null) {
            $qb->andWhere('s.etat = :etat')
                ->setParameter('etat', $etat);
        } elseif (($dateDebut !== null) and ($dateFin !== null)){
            $qb->andWhere('s.dateHeureDebut BETWEEN :dateDebut AND :dateFin')
                ->setParameter('dateDebut', $dateDebut)
                ->setParameter('dateFin', $dateFin);
        } elseif ($sortieOrganisateur and ($participant !== null)) {
            $qb->andWhere('s.organisateur = :organisateur')
                ->setParameter('organisateur', $participant);
        } elseif ($sortiePassee) {
            $qb->andWhere('s.dateHeureDebut < :dateNow')
                ->setParameter('dateNow', $maintenant->format('Y-m-d H:i:s'));
        }
        $result = $qb->getQuery()->getResult();
        return $result;
    }

    public function getSortieAfterOneMonth() : \Doctrine\ORM\QueryBuilder
    {
        return $this->createQueryBuilder('s')
            ->andWhere("s.dateHeureDebut < :current_date")
            ->setParameter('current_date', new \DateTime(-1 . ' month'));
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countSortieAfterOneMonth() : int {
        return $this->getSortieAfterOneMonth()->select("COUNT(s.id)")->getQuery()->getSingleScalarResult();
    }

    public function deleteSortieAfterOneMonth() : int {
        return $this->getSortieAfterOneMonth()->delete()->getQuery()->execute();
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
