<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TaskController extends AbstractController
{
    public function __construct(
        private TaskService $taskService
    ) {}

    #[Route('/task/add', name: 'app_task_add', methods: ['GET', 'POST'])]
    public function add(Request $request): Response
    {
        $task = new Task();

        $task->setCreatedAt(new \DateTimeImmutable());

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $this->taskService->addTask($task);
                    $this->addFlash('success', 'La tâche a été ajoutée');
                    return $this->redirectToRoute('app_task_add');
                } catch (\Exception $e) {
                    $this->addFlash('error', $e->getMessage());
                }
            } else {
                $this->addFlash('error', 'Formulaire invalide');
            }
        }

        return $this->render('task/add_task.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/task', name: 'app_task_list', methods: ['GET'])]
    public function list(): Response
    {
        try {
            $tasks = $this->taskService->getAllTasks();
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            $tasks = [];
        }

        return $this->render('task/list_task.html.twig', [
            'tasks' => $tasks,
        ]);
    }
}
