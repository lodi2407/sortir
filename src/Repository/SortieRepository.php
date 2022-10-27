<?php

namespace App\Repository;

use App\Entity\FiltersSorties;
use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
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
    public const PAGINATOR_PER_PAGE = 8;

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

    /**
     * @return Sortie[] Returns an array of Sortie objects
     */
    public function findCurrentSorties(): array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.dateHeureDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param FiltersSorties $filters
     * @param Participant $user
     * @param integer $offset
     * @return Paginator
     */
    public function findFilteredSorties(FiltersSorties $filters, Participant $user, int $offset): Paginator {

        $query = $this
            ->createQueryBuilder('s')
            ->orderBy('s.dateHeureDebut', 'ASC');

        if($filters->getCampus()) {
            $query = $query 
                ->andWhere('s.campus = :campus')
                ->setParameter('campus', $filters->getCampus()->getId());
        }

        if($filters->getNomSortie()) {
            $query = $query
                ->andWhere($query->expr()->like('s.nom', ':name'))
                ->setParameter(':name', '%' . $filters->getNomSortie() . '%');
                
        }

        if($filters->getDateDebut() && $filters->getDateFin()) {
            $query = $query
            ->andWhere('s.dateHeureDebut BETWEEN :debut AND :fin')
            ->setParameter(':debut', $filters->getDateDebut())
            ->setParameter(':fin', $filters->getDateFin());
        }

        if($filters->isOrganisateur()) {
            $query = $query
                ->andWhere('s.organisateur = :id')
                ->setParameter(':id', $user->getId());
        }

        if($filters->isInscrit()) {
            $query = $query
                ->innerJoin('s.participant', 'participant')
                ->andWhere('participant.id = :id')
                ->setParameter(':id', $user->getId());
                
        }

        if($filters->isPasInscrit()) {
            $query = $query
                ->innerJoin('s.participant', 'participant')
                ->addSelect('participant')
                ->andWhere('participant.id != :id')
                ->setParameter(':id', $user->getId())
                ;
        }

        if($filters->isPassees()) {
            $query = $query
                ->andWhere('s.dateHeureDebut < :now')
                ->setParameter(':now', date('Y-m-d'));
        } else {
            $query = $query
                ->andWhere('s.dateHeureDebut > ' . "'" . date("Y-m-d") . "'");
        }

        $query
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery();
        
        return new Paginator($query);

    }

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
