<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $login;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $teams = [];

    /**
     * @ORM\OneToMany(targetEntity=UserSkill::class, mappedBy="user", cascade="persist", orphanRemoval=true)
     */
    private $userSkills;

    private $isAdmin;

    /**
     * @ORM\OneToMany(targetEntity=RegistrationTask::class, mappedBy="user")
     */
    private $registrationTasks;

    /**
     * @ORM\Column(type="string", length=70)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=70)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $street;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $zip;

    /**
     * @ORM\Column(type="string", length=35)
     */
    private $town;

    /**
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $yearOfBirth;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasPass;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasValidAdhesion;

    public function __construct()
    {
        $this->userSkills = new ArrayCollection();
        $this->registrationTasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->login;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getTeams(): ?array
    {
        return array_unique($this->teams);
    }

    public function setTeams(?array $teams): self
    {
        $this->teams = $teams;

        return $this;
    }
    
    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|UserSkill[]
     */
    public function getUserSkills(): Collection
    {
        return $this->userSkills;
    }

    public function addUserSkill(UserSkill $userSkill): self
    {
        if (!$this->userSkills->contains($userSkill)) {
            $this->userSkills[] = $userSkill;
            $userSkill->setUser($this);
        }

        return $this;
    }

    public function removeUserSkill(UserSkill $userSkill): self
    {
        if ($this->userSkills->removeElement($userSkill)) {
            // set the owning side to null (unless already changed)
            if ($userSkill->getUser() === $this) {
                $userSkill->setUser(null);
            }
        }

        return $this;
    }
    
    /**
     * Get the value of isAdmin
     */ 
    public function getIsAdmin()
    {
        return in_array('ROLE_ADMIN', $this->roles);
    }
    
    /**
     * Set the value of isAdmin
     *
     * @return  self
     */ 
    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
        
        return $this;
    }
    
    public function getRoleName()
    {

        if ($this->getIsAdmin()){
            return 'Administrateur';
        }
        
        return 'Utilisateur';
    }

    public function __toString()
    {
        return $this->getUsername();
    }

    /**
     * @return Collection|RegistrationTask[]
     */
    public function getRegistrationTasks(): Collection
    {
        return $this->registrationTasks;
    }

    public function addRegistrationTask(RegistrationTask $registrationTask): self
    {
        if (!$this->registrationTasks->contains($registrationTask)) {
            $this->registrationTasks[] = $registrationTask;
            $registrationTask->setUser($this);
        }

        return $this;
    }

    public function removeRegistrationTask(RegistrationTask $registrationTask): self
    {
        if ($this->registrationTasks->removeElement($registrationTask)) {
            // set the owning side to null (unless already changed)
            if ($registrationTask->getUser() === $this) {
                $registrationTask->setUser(null);
            }
        }

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getZip(): ?string
    {
        return $this->zip;
    }

    public function setZip(string $zip): self
    {
        $this->zip = $zip;

        return $this;
    }

    public function getTown(): ?string
    {
        return $this->town;
    }

    public function setTown(string $town): self
    {
        $this->town = $town;

        return $this;
    }

    public function getYearOfBirth(): ?string
    {
        return $this->yearOfBirth;
    }

    public function setYearOfBirth(?string $yearOfBirth): self
    {
        $this->yearOfBirth = $yearOfBirth;

        return $this;
    }

    public function getHasPass(): ?bool
    {
        return $this->hasPass;
    }

    public function setHasPass(bool $hasPass): self
    {
        $this->hasPass = $hasPass;

        return $this;
    }

    public function getHasValidAdhesion(): ?bool
    {
        return $this->hasValidAdhesion;
    }

    public function setHasValidAdhesion(bool $hasValidAdhesion): self
    {
        $this->hasValidAdhesion = $hasValidAdhesion;

        return $this;
    }
}
