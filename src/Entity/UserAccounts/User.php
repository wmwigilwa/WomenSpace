<?php

namespace App\Entity\UserAccounts;

use App\Entity\Data\Quiz;
use App\Entity\Forum\Topic;
use App\Repository\UserAccounts\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'tbl_user_accounts')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $middleName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 1)]
    private ?string $accountStatus = null;

    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'users')]
    private Collection $userRoles;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $token = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Topic::class)]
    private Collection $topics;


    public function __construct()
    {
        $this->userRoles = new ArrayCollection();
        $this->topics = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * The public representation of the user (e.g. a username, an email address, etc.)
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = array();

        $rolesArray = $this->userRoles->toArray();

        foreach ($rolesArray as $userRole)
        {
            $roles[] = 'ROLE_' . strtoupper(str_replace(' ', '_', $userRole->getRoleName()));
        }

        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function getRoleIds(): array
    {
        $roleIds = [];

        $rolesArray = $this->userRoles->toArray();

        foreach ($rolesArray as $userRole)
        {
            $roleIds[] = $userRole->getId();
        }

        return $roleIds;
    }

    public function getReadableRoles(): string
    {
        $roles = $this->userRoles->toArray();
        $rolesDescription = '';

        foreach ($roles as $userRole)
        {

            $rolesDescription .= $userRole->getRoleName();
        }

        return $rolesDescription;
    }
    
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(string $middleName): static
    {
        $this->middleName = $middleName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getAccountStatus(): ?string
    {
        return $this->accountStatus;
    }

    public function setAccountStatus(string $accountStatus): static
    {
        $this->accountStatus = $accountStatus;

        return $this;
    }

    /**
     * Returning a salt is only needed if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    public function addRole(Role $role): static
    {
        if (!$this->userRoles->contains($role)) {
            $this->userRoles->add($role);
        }

        return $this;
    }

    public function removeRole(Role $role): static
    {
        $this->userRoles->removeElement($role);

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): static
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return Collection<int, Topic>
     */
    public function getTopics(): Collection
    {
        return $this->topics;
    }

    public function addTopic(Topic $topic): static
    {
        if (!$this->topics->contains($topic)) {
            $this->topics->add($topic);
            $topic->setOwner($this);
        }

        return $this;
    }

    public function removeTopic(Topic $topic): static
    {
        if ($this->topics->removeElement($topic)) {
            // set the owning side to null (unless already changed)
            if ($topic->getOwner() === $this) {
                $topic->setOwner(null);
            }
        }

        return $this;
    }

    public function getFullName(): string
    {
        return sprintf("%s %s",$this->firstName, $this->lastName);
    }

}
