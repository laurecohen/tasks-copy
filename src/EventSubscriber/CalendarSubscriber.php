<?php

namespace App\EventSubscriber;

use App\Repository\TaskRepository;
use App\Repository\TaskTemplateRepository;
use App\Service\TaskGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CalendarSubscriber implements EventSubscriberInterface
{
    private $taskRepository;
    private $templateRepository;
    private $router;
    private $taskGenerator;

    public function __construct(
        TaskRepository $taskRepository, 
        TaskTemplateRepository $templateRepository, 
        TaskGenerator $taskGenerator, 
        UrlGeneratorInterface $router
    ) {
        $this->taskRepository = $taskRepository;
        $this->templateRepository = $templateRepository;
        $this->router = $router;
        $this->taskGenerator = $taskGenerator;
    }

    public static function getSubscribedEvents()
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    public function onCalendarSetData(CalendarEvent $calendar)
    {
        // Générer les tâches
        foreach ($this->templateRepository->findAll() as $template) {
            $this->taskGenerator->createTasks($template);
        }    

        $start = $calendar->getStart();
        $end = $calendar->getEnd();
        // @TODO: à définir
        $filters = $calendar->getFilters();    

        // Récupérer les tâches
        $tasks = $this->taskRepository
            ->createQueryBuilder('t')
            ->where('t.eventStart BETWEEN :start and :end OR t.eventEnd BETWEEN :start and :end')
            ->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->setParameter('end', $end->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getResult()
        ;

        foreach ($tasks as $task) {
            // this create the events with your data (here booking data) to fill calendar
            $taskEvent = new Event(
                $task->getCategory(),
                $task->getEventStart(),
                $task->getEventEnd() // If the end date is null or not defined, a all day event is created.
            );

            $taskEvent->addOption(
                'url',
                $this->router->generate('task_show', [
                    'id' => $task->getId(),
                ])
            );         

            $taskEvent->addOption(
                'classNames', ["event-{$task->getCategory()}"]
            );

            // $capacity = count($task->getRegistrationTasks()) ==  $task->getMembersMax() ? true : false;

            // $taskEvent->addOption(
            //     'extendedProps', ["isFull" => $capacity]
            // );

            // finally, add the event to the CalendarEvent to fill the calendar
            $calendar->addEvent($taskEvent);
        }
    }
}
