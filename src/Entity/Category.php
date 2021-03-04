<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
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
     * @ORM\OneToMany(targetEntity=TaskTemplate::class, mappedBy="category")
     */
    private $taskTemplates;

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="category")
     */
    private $tasks;

    public function __construct()
    {
        $this->taskTemplates = new ArrayCollection();
        $this->tasks = new ArrayCollection();
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
     * @return Collection|TaskTemplate[]
     */
    public function getTaskTemplates(): Collection
    {
        return $this->taskTemplates;
    }

    public function addTaskTemplate(TaskTemplate $taskTemplate): self
    {
        if (!$this->taskTemplates->contains($taskTemplate)) {
            $this->taskTemplates[] = $taskTemplate;
            $taskTemplate->setCategory($this);
        }

        return $this;
    }

    public function removeTaskTemplate(TaskTemplate $taskTemplate): self
    {
        if ($this->taskTemplates->removeElement($taskTemplate)) {
            // set the owning side to null (unless already changed)
            if ($taskTemplate->getCategory() === $this) {
                $taskTemplate->setCategory(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->label;
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
            $task->setCategory($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getCategory() === $this) {
                $task->setCategory(null);
            }
        }

        return $this;
    }
}
