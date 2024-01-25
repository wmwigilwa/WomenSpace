<?php

namespace App\Repository\UserAccounts;

use App\Entity\UserAccounts\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param array $options
     * @return QueryBuilder
     */
    public function getAll(array $options = []): QueryBuilder
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->select("u.id,email,
        u.first_name,u.middle_name,u.last_name,
        string_agg(role_name, ',') AS roles,
        account_status AS status")
            ->from('tbl_user_accounts', 'u')
            ->leftJoin('u','user_role','ur','ur.user_id=u.id')
            ->leftJoin('ur','user_defined_roles','r','r.id=ur.role_id')
            ->groupBy('u.id');
        $queryBuilder = $this->setFilterOptions($options, $queryBuilder);
        return $this->setSortOptions($options, $queryBuilder);
    }

    public function setFilterOptions($options, QueryBuilder $queryBuilder): QueryBuilder
    {

        return $queryBuilder;
    }

    public function setSortOptions($options, QueryBuilder $queryBuilder): QueryBuilder
    {

        $options['sortType'] == 'desc' ? $sortType = 'desc' : $sortType = 'asc';

        if ($options['sortBy'] === 'name')
        {
            return $queryBuilder->addOrderBy('first_name', $sortType);
        }

        return $queryBuilder->addOrderBy('u.id', 'desc');

    }

    public function countAll(QueryBuilder $queryBuilder): \Closure
    {
        return function ($queryBuilder) {
            $queryBuilder->select('COUNT(DISTINCT u.id) AS total_results')
                ->setMaxResults(1)
                ->resetQueryPart('orderBy')
                ->resetQueryPart('groupBy');
        };
    }

    public function add(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws Exception
     */
    public function recordUserRole($roleId, $userId)
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->insert('user_roles')
            ->setValue('role_id','?')
            ->setValue('user_id','?')
            ->setParameter(0,$roleId)
            ->setParameter(1,$userId);
        $queryBuilder->executeStatement();
    }

    /**
     * @throws Exception
     */
    public function deleteStaffRole($Id)
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->delete('user_roles')
            ->andWhere('user_id=?')
            ->setParameter(0,$Id);
        $queryBuilder->executeStatement();
    }

    /**
     * @throws Exception
     */
    public function findCurrentUserHash($Id)
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $results = $queryBuilder->select('password')
            ->from('user_accounts', 'u')
            ->where('u.id = :Id')
            ->setParameter('Id', $Id)
            ->fetchOne();

        return $results['password'];
    }

    /**
     * @throws Exception
     */
    public function getTotals(){
        $conn = $this->getEntityManager()->getConnection();
        $queryBuilder = new QueryBuilder($conn);
        $data = $queryBuilder
            ->select('COUNT(u.id)')
            ->from('tbl_user_accounts', 'u')
            ->fetchNumeric();
        return ($data[0]);
    }

}
