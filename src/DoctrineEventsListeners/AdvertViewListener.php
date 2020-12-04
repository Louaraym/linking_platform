<?php


namespace App\DoctrineEventsListeners;


use App\Entity\Advert;
use App\Repository\ViewRepository;
use App\Service\AddView;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\RequestStack;

class AdvertViewListener
{
    private $viewRepository;
    private $requestStack;
    private $addView;

    public function __construct(ViewRepository $viewRepository, RequestStack $requestStack, AddView $addView)
    {
        $this->viewRepository = $viewRepository;
        $this->requestStack = $requestStack;
        $this->addView = $addView;
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     * @throws NonUniqueResultException
     */
    public function postLoad(LifecycleEventArgs $eventArgs): void
    {
        $clientIp = $this->requestStack->getCurrentRequest()->getClientIp();
        $entity = $eventArgs->getObject();

        if (!$entity instanceof Advert){
            return;
        }

        if ($this->viewRepository->isFlood($clientIp, 15)){
           return;
        }

        $this->addView->add($entity, $clientIp);

    }

}