<?php


namespace App\Bigbrother;


use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\User\UserInterface;

class MessageNotificator
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param $message
     * @param UserInterface $user
     */
    public function notifyByEmail($message, UserInterface $user): void
    {
        $email = (new Email())
            ->from('admin@gmail.com')
            ->to('admin@gmail.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject("Nouveau message d'un utilisateur surveillé !")
            ->text("L'utilisateur surveillé " .$user->getUsername(). " a posté le message suivant : " .$message)
//            ->html('<p>See Twig integration for better HTML integration!</p>')
        ;

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }

    }

}