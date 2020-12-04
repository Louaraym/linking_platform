<?php


namespace App\Service;


use App\Entity\Application;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ApplicationMailer
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param Application $application
     */
    public function sendNewNotification(Application $application): void
    {
        $email = (new Email())
            ->from('admin@gmail.com')
            ->to($application->getAdvert()->getAuthor()->getEmail())
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Vous avez reçu une nouvelle candidature pour l\'annonce : '. ' '. $application->getAdvert()->getTitle() )
//            ->html('<p>See Twig integration for better HTML integration!</p>')
        ;

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }

    }

}