<?php

namespace App\Security;

use App\Repository\UserAccounts\PermissionRepository;
use Doctrine\DBAL\Exception;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ACLSecurityProvider
{

    /**
     * @var PermissionRepository
     */
    private PermissionRepository $em;
    /**
     * @var TokenStorageInterface
     */
    private TokenStorageInterface $storageInterface;

    /**
     * ACLSecurityProvider constructor.
     * @param PermissionRepository $em
     * @param TokenStorageInterface $storageInterface
     */
    public function __construct(PermissionRepository $em, TokenStorageInterface $storageInterface)
    {
        $this->storageInterface = $storageInterface;
        $this->em = $em;
    }


    /**
     * @throws Exception
     */
    public function getCurrentACLs($subject): array|\Doctrine\DBAL\Query\QueryBuilder
    {
        $user = $this->storageInterface->getToken()->getUser();
        
        $userRoles = $user->getRoleIds();

        if(!empty($userRoles)) {
            return $this->em
                ->getCurrentUserACLs($subject, $userRoles);
        }

        return [];
    }

    public function hasPermission($action,$ACLs): bool
    {
        if(is_array($ACLs)) {

            if (in_array($action, $ACLs))
                return true;
        }

        throw new AccessDeniedException('You have no permission to perform this action');
    }

}