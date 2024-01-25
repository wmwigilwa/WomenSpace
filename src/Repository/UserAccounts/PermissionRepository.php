<?php

namespace App\Repository\UserAccounts;

use App\Entity\Member;
use App\Entity\UserAccounts\Permission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class PermissionRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Permission::class);
    }

    /**
     * @param $object
     * @param $userRoles
     * @return array|QueryBuilder
     * @throws Exception
     */
    public function getCurrentUserACLs($object,$userRoles): array|QueryBuilder
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $results = $queryBuilder->select('actions')
            ->from('user_roles_permissions', 'd');
        
        $counter = 0;

        foreach ($userRoles as $userRole)
        {
            $results->orWhere("role_id=:role_id_$counter")
                ->setParameter("role_id_$counter", $userRole);

            $counter++;
        }

        $results->andWhere('object = :object')
            ->setParameter('object',$object);

        $results = $results->executeQuery();

        $results = $results->fetchAssociative();

        if (isset($results['actions']))
        {
            $ACLs = json_decode($results['actions']);
        }
        else
        {
            $ACLs = [];
        }

        return $ACLs;
    }

    /**
     * @param $object
     * @param $roleId
     * @param $actions
     * @return bool
     * @throws Exception
     */
    public function recordPermission($object,$roleId,$actions): bool
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $queryBuilder
            ->insert('user_roles_permissions')
            ->setValue('object',':object')
            ->setValue('role_id',':roleId')
            ->setValue('actions',':actions')
            ->setParameter('object',$object)
            ->setParameter('roleId',$roleId)
            ->setParameter('actions',$actions)
            ->executeStatement();

        return true;
    }

    /**
     * @param $roleId
     * @return bool|string
     * @throws Exception
     */
    public function clearPermissionByRoleId($roleId): bool|string
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

         $queryBuilder
             ->delete('user_roles_permissions')
             ->where('role_id = :roleId')
             ->setParameter('roleId',$roleId)
             ->executeStatement();

         return true;
    }

}
