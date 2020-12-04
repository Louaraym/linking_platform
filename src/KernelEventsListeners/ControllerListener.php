<?php


namespace App\KernelEventsListeners;


use App\Repository\AdvertRepository;
use App\Repository\ViewRepository;
use App\Service\AddView;
use Exception;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

class ControllerListener
{
    private $advertRepository;
    private $entityManager;
    private $addView;
    private $viewRepository;

    public function __construct(AdvertRepository $advertRepository, AddView $addView, ViewRepository $viewRepository)
    {
        $this->addView = $addView;
        $this->viewRepository = $viewRepository;
        $this->advertRepository = $advertRepository;
    }

    /**
     * Add a view object at advert object
     * @param ControllerEvent $event
     * @throws Exception
     */
    public function onKernelController(ControllerEvent $event): void
    {
        $request = $event->getRequest();
        $clientIp = $request->getClientIp();

        if ($request->attributes->get('_route') !== 'advert_show'){
            return;
        }

        if ($this->viewRepository->isFlood($clientIp, 15)){
            return;
        }

        $id = $request->attributes->get('id');
        $advert = $this->advertRepository->find($id);

        $this->addView->add($advert, $clientIp);

    }
}