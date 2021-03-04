<?php

namespace App\Controller\Admin;

use App\Service\TaskGenerator;
use App\Repository\TaskRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin")
 */
class DashboardController extends AbstractController
{
    private $taskRepository;

    public function __construct(TaskRepository $taskRepository, TaskGenerator $taskGenerator)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * @Route("/", name="dashboard")
     */
    public function index(): Response
    {
        return $this->render('dashboard/layout.html.twig', [
            'tasks' => $this->taskRepository->findAll(),
        ]);
    }
}
