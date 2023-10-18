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

    public function findByFilters($nom, $siteSelect, $participantsSite, $etat, $dateDebut, $dateFin, $sortieOrganisateur, $inscrit, $pasInscrit, $sortiePassee, UserInterface $user): ?array {

        if(!$sortieOrganisateur AND !$inscrit AND !$pasInscrit AND !$sortiePassee ) {
            return [];
        }

        $qb = $this->createQueryBuilder('s');
        $maintenant = new \DateTime();

        //Première partie de filtres
        if ($nom !== null){
            $qb->andWhere('s.nom LIKE :nom')
                ->setParameter('nom', '%'.$nom.'%');
        }

        if ($siteSelect !== null) {
            $qb->andWhere('s.organisateur IN (:participantsSite)')
                ->setParameter('participantsSite', $participantsSite);
        }

        if ($etat !== null) {
            $qb->andWhere('s.etat = :etat')
                ->setParameter('etat', $etat);
        }

        if (($dateDebut !== null) and ($dateFin !== null)) {
            $qb->andWhere('s.dateHeureDebut BETWEEN :dateDebut AND :dateFin')
                ->setParameter('dateDebut', $dateDebut)
                ->setParameter('dateFin', $dateFin);
        }

        //Partie cases à cocher !
        $Ou_X = $qb->expr()->orX();

        if($sortieOrganisateur) {
            $Ou_X->add($qb->expr()->eq('s.organisateur', ':user'));
        }

        if ($inscrit) {
            $Ou_X->add($qb->expr()->isMemberOf(':user', 's.participants'));
        }

        if ($pasInscrit) {
            $Ou_X->add($qb->expr()->not($qb->expr()->isMemberOf(':user', 's.participants')));
        }

        if ($sortiePassee) {
            $condition = $qb->expr()->lt('s.dateHeureDebut', ':dateNow');
            $Ou_X->add($condition);
        }

        $qb->andWhere($Ou_X);

        if($sortieOrganisateur OR $inscrit OR $pasInscrit) {
            $qb->setParameter('user', $user);
        }

        if ($sortiePassee) {
            $qb->setParameter('dateNow', $maintenant->format('Y-m-d H:i:s'));
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
