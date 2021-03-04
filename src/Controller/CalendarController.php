<?php

namespace App\Controller;

use App\Entity\Task;
use App\Service\TaskGenerator;
use App\Entity\RegistrationTask;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @IsGranted("ROLE_USER")
 * @Route("calendar")
 */
class CalendarController extends AbstractController
{
    private $taskGenerator;
    private $manager;

    public function __construct(TaskGenerator $taskGenerator, EntityManagerInterface $manager)
    {
        $this->taskGenerator = $taskGenerator;
        $this->manager = $manager;
    }

    /**
     * @Route("/", name="calendar")
     */
    public function index(): Response
    {
        $tasks = $this->getDoctrine()
            ->getRepository(Task::class)
            ->getAll(); 

        return $this->render('calendar/index.html.twig', [
            'tasks' => $tasks,
            'task' => null,
            'period' => $this->taskGenerator->getInterval()
        ]);
    }

    /**
     * @Route("/{id}", name="task_show")
     */
    public function taskShow(Task $task = null): Response
    {
        // Préparer la réponse :
        // Stocker la sous-vue et son contenu dans une variable
        $sidebar = $this->renderView('calendar/_task-info.html.twig', [
            'task' => $task, 
            'session_user' => $this->getUser(),
            'period' => $this->taskGenerator->getInterval()
        ]);

        // et retourner la sous-vue et son contenu
        return new Response($sidebar);
    }

    /**
     * @Route("/{id}/task/register", name="tasks_register")
     */
    public function registerToTask(Task $task): Response
    {
        // Préparer les variables pour les tests
        $registered = false;
        $today = new \DateTime();

        // chercher l'utilisateur connecté parmi les inscrits,
        // s'il est trouvé, mettre à jour la variable $registered
        foreach ($task->getRegistrationTasks() as $taskRegistrationTask) {
            if ($taskRegistrationTask->getUser() == $this->getUser()) $registered = true;
        }

        // Si la date de la tâche est postérieure à aujourd'hui, s'il reste de la place, et si l'adhérent n'est pas déjà inscrit...
        if ($task->getEventStart() > $today && ($task->getMembersMax() - count($task->getRegistrationTasks())) > 0 && !$registered) {
            
            // créer un nouvel objet RegistrationTask
            $registrationTask = new RegistrationTask();
            $registrationTask->setTask($task);
            $registrationTask->setUser($this->getUser());

            // puis préparer l'enregistrement de l'inscription dans l'objet tâche
            $task->addRegistrationTask($registrationTask);
            $this->manager->persist($task);
 
            $this->manager->flush();
        }

        // mettre à jour la tâche renvoyée à la sous-vue
        $task = $this->manager->getRepository(Task::class)->findOneBy(['id' => $task->getId()]);

        // Préparer la réponse :
        // Stocker la sous-vue et son contenu dans une variable
        $sidebar = $this->renderView('calendar/_task-info.html.twig', [
            'task' => $task, 
            'session_user' => $this->getUser(),
            'period' => $this->taskGenerator->getInterval()
        ]);

        // et retourner la sous-vue et son contenu
        return new Response($sidebar);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}/unregister", name="task_registration_delete")
     */
    public function unregisterFromTask(RegistrationTask $registrationTask) {
        $task = $registrationTask->getTask();
        $this->manager->remove($registrationTask);
        $this->manager->flush();

        return $this->redirectToRoute('task_more', ['id' => $task->getId()]);
    }
}