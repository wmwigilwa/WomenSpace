<?php
namespace App\Entity\UserAccounts;

use App\Repository\UserAccounts\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[ORM\Table(name: 'user_defined_roles')]
class Role
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private mixed $id;

    #[ORM\Column(type: "string", nullable: true)]
    private mixed $roleName;

    #[ORM\OneToMany(mappedBy: "role", targetEntity: Permission::class)]
    private $permissions;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'userRoles')]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    /**
     * @return array|ArrayCollection|PersistentCollection
     */
    public function getPermissions(): array|ArrayCollection|PersistentCollection
    {
        return $this->permissions;
    }


    /**
     * @return mixed
     */
    public function getRoleName(): mixed
    {
        return $this->roleName;
    }

    /**
     * @param mixed $roleName
     */
    public function setRoleName(mixed $roleName): void
    {
        $this->roleName = $roleName;
    }

    /**
     * @return mixed
     */
    public function getId(): mixed
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId(mixed $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getRoleSystemName(): string
    {
        return 'ROLE_' . strtoupper(str_replace(' ', '_', $this->roleName));
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addRole($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeRole($this);
        }

        return $this;
    }

}