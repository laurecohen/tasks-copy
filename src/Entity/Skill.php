<?php

namespace App\Entity;

use App\Repository\SkillRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SkillRepository::class)
 */
class Skill
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $label;

    /**
     * @ORM\OneToMany(targetEntity=UserSkill::class, mappedBy="skill")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity=TaskTemplateSkill::class, mappedBy="skill")
     */
    private $templateSkills;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->templateSkills = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return Collection|UserSkill[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(UserSkill $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setSkill($this);
        }

        return $this;
    }

    public function removeUser(UserSkill $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getSkill() === $this) {
                $user->setSkill(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->label;
    }

    /**
     * @return Collection|TaskTemplateSkill[]
     */
    public function getTemplateSkills(): Collection
    {
        return $this->templateSkills;
    }

    public function addTemplateSkill(TaskTemplateSkill $templateSkill): self
    {
        if (!$this->templateSkills->contains($templateSkill)) {
            $this->templateSkills[] = $templateSkill;
            $templateSkill->setSkill($this);
        }

        return $this;
    }

    public function removeTemplateSkill(TaskTemplateSkill $templateSkill): self
    {
        if ($this->templateSkills->removeElement($templateSkill)) {
            // set the owning side to null (unless already changed)
            if ($templateSkill->getSkill() === $this) {
                $templateSkill->setSkill(null);
            }
        }

        return $this;
    }
}
