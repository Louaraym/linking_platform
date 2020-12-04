<?php


namespace App\Event;


use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

class MessagePostEvent extends Event
{
    protected $message;
    protected $user;

    public function __construct($message, UserInterface $user)
    {
        $this->message = $message;
        $this->user    = $user;
    }

    /**
     * Le listener doit avoir accès au message
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Le listener doit pouvoir modifier le message
     * @param $message
     * @return mixed
     */
    public function setMessage($message)
    {
        return $this->message = $message;
    }

    /**
     * Le listener doit avoir accès à l'utilisateur
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }

    // Pas de setUser, les listeners ne peuvent pas modifier l'auteur du message !
}