<?php

namespace App\Repository\UserAccounts;

use App\Entity\UserAccounts\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class RoleRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }

    /**
     * @param array $options
     * @return QueryBuilder
     */
    public function findAllRoles($options = []): QueryBuilder
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->select('id,
                               role_name')
            ->from('user_defined_roles', 'u');
        $queryBuilder = $this->setFilterOptions($options, $queryBuilder);
        return $this->setSortOptions($options, $queryBuilder);
    }

    public function countAllRoles(QueryBuilder $queryBuilder): \Closure
    {
        return function ($queryBuilder) {
            $queryBuilder->select('COUNT(DISTINCT id) AS total_results')
                ->setMaxResults(1)
                ->resetQueryPart('orderBy')
                ->resetQueryPart('groupBy');
        };
    }

    public function setFilterOptions($options, QueryBuilder $queryBuilder): QueryBuilder
    {

        if (!empty($options['name']))
        {
            return $queryBuilder->andwhere('role_name LIKE :role_name')
                ->setParameter('role_name', '%' . $options['name'] . '%');
        }

        return $queryBuilder;
    }

    public function setSortOptions($options, QueryBuilder $queryBuilder): QueryBuilder
    {

        $options['sortType'] == 'desc' ? $sortType = 'desc' : $sortType = 'asc';

         if ($options['sortBy'] === 'name')
         {
             return $queryBuilder->addOrderBy('role_name', $sortType);
         }

        return $queryBuilder->addOrderBy('id', 'desc');
    }

    public function add(Role $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }



}
