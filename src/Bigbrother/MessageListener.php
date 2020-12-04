<?php


namespace App\Bigbrother;


use App\Event\MessagePostEvent;

class MessageListener
{
    protected $notificator;
    protected $listUsers = array();

    public function __construct($listUsers, MessageNotificator $notificator)
    {
        $this->notificator = $notificator;
        $this->listUsers   = $listUsers;
    }

    /**
     * On active la surveillance si l'auteur du message est dans la liste
     *
     * si oui, On envoie un e-mail à l'administrateur
     *
     * @param MessagePostEvent $event
     */
    public function onLinkingPlatformPostMessage(MessagePostEvent $event): void
    {
        if (in_array($event->getUser()->getUsername(), $this->listUsers, true)) {

            $this->notificator->notifyByEmail($event->getMessage(), $event->getUser());
        }
    }
}