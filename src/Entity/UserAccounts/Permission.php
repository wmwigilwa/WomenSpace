<?php
namespace App\Entity\UserAccounts;

use App\Repository\UserAccounts\PermissionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PermissionRepository::class)]
#[ORM\Table(name: 'user_roles_permissions')]
class Permission
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: "integer")]
    private mixed $id;

    #[ORM\Column(type: "string", nullable: true)]
    private mixed $object;

    #[ORM\ManyToOne(targetEntity: Role::class, inversedBy: "permissions")]
    #[ORM\JoinColumn(name: "role_id", referencedColumnName: "id", nullable: false)]
    private mixed $role;

    #[ORM\Column(type: "json")]
    private mixed $actions;

    /**
     * @return mixed
     */
    public function getActions(): mixed
    {
        return $this->actions;
    }

    /**
     * @param mixed $actions
     */
    public function setActions(mixed $actions): void
    {
        $this->actions = $actions;
    }

    /**
     * @return mixed
     */
    public function getRole(): mixed
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole(mixed $role): void
    {
        $this->role = $role;
    }

    /**
     * @return mixed
     */
    public function getObject(): mixed
    {
        return $this->object;
    }

    /**
     * @param mixed $object
     */
    public function setObject(mixed $object): void
    {
        $this->object = $object;
    }

    /**
     * @return mixed
     */
    public function getId(): mixed
    {
        return $this->id;
    }

}