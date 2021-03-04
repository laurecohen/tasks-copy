<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity=TaskTemplate::class, inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $taskTemplate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $eventStart;

    /**
     * @ORM\Column(type="datetime")
     */
    private $eventEnd;

    /**
     * @ORM\Column(type="integer")
     */
    private $membersMin;

    /**
     * @ORM\Column(type="integer")
     */
    private $membersMax;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isClosed;

    /**
     * @ORM\Column(type="boolean")
     */
    private $mutePassAlert;

    /**
     * @ORM\Column(type="boolean")
     */
    private $muteMembersAlert;

    /**
     * @ORM\OneToMany(targetEntity=RegistrationTask::class, mappedBy="task", cascade="persist")
     */
    private $registrationTasks;


    public function __construct()
    {
        $this->isClosed = false;
        $this->mutePassAlert = false;
        $this->muteMembersAlert = false;
        $this->registrationTasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTaskTemplate(): ?TaskTemplate
    {
        return $this->taskTemplate;
    }

    public function setTaskTemplate(?TaskTemplate $taskTemplate): self
    {
        $this->taskTemplate = $taskTemplate;

        return $this;
    }

    public function getEventStart(): ?\DateTimeInterface
    {
        return $this->eventStart;
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

    public function setEventStart(\DateTimeInterface $eventStart): self
    {
        $this->eventStart = $eventStart;

        return $this;
    }

    public function getEventEnd(): ?\DateTimeInterface
    {
        return $this->eventEnd;
    }

    public function setEventEnd(\DateTimeInterface $eventEnd): self
    {
        $this->eventEnd = $eventEnd;

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

    public function getIsClosed(): ?bool
    {
        return $this->isClosed;
    }

    public function setIsClosed(bool $isClosed): self
    {
        $this->isClosed = $isClosed;

        return $this;
    }

    public function getMutePassAlert(): ?bool
    {
        return $this->mutePassAlert;
    }

    public function setMutePassAlert(bool $mutePassAlert): self
    {
        $this->mutePassAlert = $mutePassAlert;

        return $this;
    }

    public function getMuteMembersAlert(): ?bool
    {
        return $this->muteMembersAlert;
    }

    public function setMuteMembersAlert(bool $muteMembersAlert): self
    {
        $this->muteMembersAlert = $muteMembersAlert;

        return $this;
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
            $registrationTask->setTask($this);
        }

        return $this;
    }

    public function removeRegistrationTask(RegistrationTask $registrationTask): self
    {
        if ($this->registrationTasks->removeElement($registrationTask)) {
            // set the owning side to null (unless already changed)
            if ($registrationTask->getTask() === $this) {
                $registrationTask->setTask(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->category->getLabel();
    }
}
