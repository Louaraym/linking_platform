<?php


namespace App\DoctrineEventsListeners;


use App\Entity\Application;
use App\Service\ApplicationMailer;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\RequestStack;

class ApplicationCreationListener
{
    private $applicationMailer;
    private $requestStack;

    public function __construct(ApplicationMailer $applicationMailer, RequestStack $requestStack)
    {
        $this->applicationMailer = $applicationMailer;
        $this->requestStack = $requestStack;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $ip = $request->getClientIp();

        $entity = $args->getObject();

        if (!$entity instanceof Application) {return;}

        $entity->setIp($ip);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Application) {
            return;
        }

        $this->applicationMailer->sendNewNotification($entity);
    }

}