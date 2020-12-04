<?php


namespace App\Service;


use App\Entity\View;
use Doctrine\ORM\EntityManagerInterface;

class AddView
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $object
     * @param $ip
     */
    public function add($object, $ip): void
    {
           $view = new View();

           $view->setAdvert($object)
               ->setClientIp($ip);

           $this->entityManager->persist($view);
           $this->entityManager->flush();
    }
}