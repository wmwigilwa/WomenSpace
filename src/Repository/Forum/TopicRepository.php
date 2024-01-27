<?php

namespace App\Repository\Forum;

use App\Entity\Configuration\Space;
use App\Entity\Forum\Topic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Topic>
 *
 * @method Topic|null find($id, $lockMode = null, $lockVersion = null)
 * @method Topic|null findOneBy(array $criteria, array $orderBy = null)
 * @method Topic[]    findAll()
 * @method Topic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TopicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Topic::class);
    }

  /**
     * @return Topic[] Returns an array of Topic objects
     */
   public function findBySpace(Space $space): array
   {
       return $this->createQueryBuilder('t')
            ->andWhere('t.space = :val')
            ->setParameter('val', $space)
            ->orderBy('t.id', 'DESC')
            ->getQuery()
            ->getResult();
   }

//    public function findOneBySomeField($value): ?Topic
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
