<?php

namespace App\Service;

use App\Entity\Task;
use App\Entity\TaskTemplate;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TaskTemplateRepository;

class TaskGenerator
{
    private $minDate;
    private $maxDate;
    private $interval;
    private $templateRepository;
    private $taskRepository;
    private $today;

    public function __construct(TaskTemplateRepository $templateRepository, TaskRepository $taskRepository, EntityManagerInterface $manager)
    {
        // @TODO: récupérer la période du calendrier
        // Définir le début de la période à aujourd'hui (1ère heure !)
        $this->minDate = new \DateTime();
        $this->minDate->settime(0,0);

        $this->maxDate = (clone $this->minDate)->modify('+1 month');
        $this->templateRepository = $templateRepository; 
        $this->taskRepository = $taskRepository; 
        $this->manager = $manager;
        // NOW
        $this->today = new \DateTime();
    }

    /**
     * Get the value of interval
     */ 
    public function getInterval()
    {
        // Créer une période type (+ 1 mois à partir d'aujourd'hui)
        // et renvoyer la periode entre ces deux dates
        $this->maxDate = $this->maxDate->modify( '+1 day' ); 
        $this->interval = new \DateInterval('P1D');
      
        return new \DatePeriod($this->minDate, $this->interval, $this->maxDate);
    }

    /**
     * Create Tasks from TaskTemplate
     */
    public function createTasks(TaskTemplate $template)
    {
        // Récupérer la période créée auparavant
        // et intialiser le compteur
        $period = $this->getInterval();
        $count = 0;

        // Ignorer les templates dont la récurrence n'est pas activée
        if ($template->getIsRecurrent()) {
            // boucler sur la période créée auparavant
            // pour récupérer un datetime à chaque itération
            foreach ($period as $dt) {      
                // si un template est trouvé pour $dt (jour de la semaine)
                // et si $dt est bien postérieur à 'now'
                if ($template->getDayOfWeek() == $dt->format('N') && $dt > $this->today) {                     
                    // préparer les variables
                    $exist = false;
                    $start = clone $dt;
                    $end = clone $dt;
                    
                    // mettre à jour les horaires pour les comparer aux horaires des tâches existantes
                    $start->setTime($template->getStartAt()->format('H'),$template->getStartAt()->format('i'));
                    $end->setTime($template->getEndAt()->format('H'),$template->getEndAt()->format('i'));

                    // si la tâche a déjà été générée
                    foreach ($template->getTasks() as $generatedTask) {
                        if ($generatedTask->getEventStart() == $start && $generatedTask->getEventEnd() == $end) {
                            $exist = true;
                        }
                    }

                    if (!$exist) {
                        // créer un nouvel objet Task
                        $task = new Task();
                            
                        // et hydrater l'objet avec les propriétés du template
                        $task->setCategory($template->getCategory());
                        $task->setEventStart($start);
                        $task->setEventEnd($end);
                        $task->setMembersMin($template->getMembersMin());
                        $task->setMembersMax($template->getMembersMax());    
                        $task->setTaskTemplate($template);

                        $this->manager->persist($task);
                        $this->manager->flush();

                        // une fois la tâche enregistrée, incrémenter le compteur
                        $count++;
                    }
                }
            }
        }
        return $count;
    }

    /**
     * Update Tasks from TaskTemplate
     */
    public function updateTasks(TaskTemplate $template) {
        $count = 0;

        // boucler sur la liste des tâches du template
        foreach ($template->getTasks() as $generatedTask) {

            // si le début de la tâche est postérieur à 'now'
            if ($generatedTask->getEventStart() > $this->today) {
                // hydrater et flush
                $generatedTask->setMembersMin($template->getMembersMin());
                $generatedTask->setMembersMax($template->getMembersMax());
                $this->manager->flush();

                // et incrémenter le compteur
                $count++;
            }
        }
        return $count;
    }
}