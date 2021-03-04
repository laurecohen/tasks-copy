<?php

namespace App\Entity;

use App\Repository\TaskTemplateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TaskTemplateRepository::class)
 */
class TaskTemplate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="time")
     */
    private $startAt;

    /**
     * @ORM\Column(type="time")
     */
    private $endAt;

    /**
     * @ORM\Column(type="integer")
     */
    private $dayOfWeek;

    /**
     * @ORM\Column(type="integer")
     */
    private $membersMin;

    /**
     * @ORM\Column(type="integer")
     */
    private $membersMax;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isRecurrent;

    private $formattedDayOfWeek;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="taskTemplates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="taskTemplate")
     */
    private $tasks;

    /**
     * @ORM\OneToMany(targetEntity=TaskTemplateSkill::class, mappedBy="taskTemplate", cascade="persist", orphanRemoval=true)
     */
    private $templateSkills;
    
    public function __construct()
    {
        $this->tasks = new ArrayCollection();
        $this->isRecurrent = false;
        $this->templateSkills = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartAt(): ?\DateTimeInterface
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeInterface $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeInterface $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getDayOfWeek(): ?int
    {
        setlocale(LC_ALL, 'fr_FR');
        return $this->dayOfWeek;
    }

    public function setDayOfWeek(int $dayOfWeek): self
    {
        $this->dayOfWeek = $dayOfWeek;

        return $this;
    }

    public function getMembersMin(): ?int
    {
        return $this->membersMin;
    }

    public function setMembersMin(int $membersMin): self
    {
        $this->membersMin = $membersMin;

        return $this;
    }

    public function getMembersMax(): ?int
    {
        return $this->membersMax;
    }

    public function setMembersMax(int $membersMax): self
    {
        $this->membersMax = $membersMax;

        return $this;
    }

    public function getIsRecurrent(): ?bool
    {
        return $this->isRecurrent;
    }

    public function setIsRecurrent(bool $isRecurrent): self
    {
        $this->isRecurrent = $isRecurrent;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get the value of formattedDayOfWeek
     */ 
    public function getFormattedDayOfWeek()
    {
        switch ($this->dayOfWeek) {
            case 1:
                $this->formattedDayOfWeek = "lundi";
                break;
            case 2:
                $this->formattedDayOfWeek = "mardi";
                break;
            case 3:
                $this->formattedDayOfWeek = "mercredi";
                break;
            case 4:
                $this->formattedDayOfWeek = "jeudi";
                break;
            case 5:
                $this->formattedDayOfWeek = "vendredi";
                break;
            case 6:
                $this->formattedDayOfWeek = "samedi";
                break;
            case 7:
                $this->formattedDayOfWeek = "dimanche";
                break;
        }
        return $this->formattedDayOfWeek;
    }

    /**
     * Set the value of formattedDayOfWeek
     *
     * @return  self
     */ 
    public function setFormattedDayOfWeek($formattedDayOfWeek)
    {
        $this->formattedDayOfWeek = $formattedDayOfWeek;

        return $this;
    }

    /**
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setTaskTemplate($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getTaskTemplate() === $this) {
                $task->setTaskTemplate(null);
            }
        }

        return $this;
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
            $templateSkill->setTaskTemplate($this);
        }

        return $this;
    }

    public function removeTemplateSkill(TaskTemplateSkill $templateSkill): self
    {
        if ($this->templateSkills->removeElement($templateSkill)) {
            // set the owning side to null (unless already changed)
            if ($templateSkill->getTaskTemplate() === $this) {
                $templateSkill->setTaskTemplate(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->category->getLabel();
    }

}
