<?php

namespace App\Entity;

use App\Repository\MemberRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MemberRepository::class)
 */
class Member
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
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

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
    private $hasValidAdhesion;

    /**
     * @ORM\Column(type="datetime")
     */
    private $firstAdhesion;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $leftOn;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastAdhesion;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasPass;

    /**
     * @ORM\Column(type="json")
     */
    private $teams = [];

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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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

    public function getHasValidAdhesion(): ?bool
    {
        return $this->hasValidAdhesion;
    }

    public function setHasValidAdhesion(bool $hasValidAdhesion): self
    {
        $this->hasValidAdhesion = $hasValidAdhesion;

        return $this;
    }

    public function getFirstAdhesion(): ?\DateTimeInterface
    {
        return $this->firstAdhesion;
    }

    public function setFirstAdhesion(\DateTimeInterface $firstAdhesion): self
    {
        $this->firstAdhesion = $firstAdhesion;

        return $this;
    }

    public function getLeftOn(): ?\DateTimeInterface
    {
        return $this->leftOn;
    }

    public function setLeftOn(?\DateTimeInterface $leftOn): self
    {
        $this->leftOn = $leftOn;

        return $this;
    }

    public function getLastAdhesion(): ?\DateTimeInterface
    {
        return $this->lastAdhesion;
    }

    public function setLastAdhesion(\DateTimeInterface $lastAdhesion): self
    {
        $this->lastAdhesion = $lastAdhesion;

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

    public function getTeams(): ?array
    {
        return $this->teams;
    }

    public function setTeams(array $teams): self
    {
        $this->teams = $teams;

        return $this;
    }
}
