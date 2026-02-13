<?php

namespace App\Service;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;

class TaskService
{
    public function __construct(
        private TaskRepository $taskRepository,
        private EntityManagerInterface $em
    ) {}

    public function addTask(Task $task): void
    {
        try {
            if ($task->getCreatedAt() === null) {
                $task->setCreatedAt(new \DateTimeImmutable());
            }

            if ($task->isStatus() === null) {
                $task->setStatus(false);
            }

            $this->em->persist($task);
            $this->em->flush();
        } catch (\Throwable $e) {
            throw new \Exception('Erreur lors de l’ajout de la tâche');
        }
    }

    /**
     * @return Task[]
     */
    public function getAllTasks(): array
    {
        try {
            return $this->taskRepository->findBy([], ['id' => 'DESC']);
        } catch (\Throwable $e) {
            throw new \Exception('Erreur lors de la récupération des tâches');
        }
    }
}
