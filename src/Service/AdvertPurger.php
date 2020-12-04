<?php


namespace App\Service;


use Datetime;
use Exception;
use App\Repository\AdvertRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class AdvertPurger
{
    private $advertRepository;
    private $entityManager;
    private $requestStack;

    public function __construct(RequestStack $requestStack,AdvertRepository $advertRepository, EntityManagerInterface $entityManager)
    {
        $this->advertRepository = $advertRepository;
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
    }

    /**
     * @param $days
     * @throws Exception
     */
    public function purge($days): void
    {
        // date d'il y a $days jours
        $date = new Datetime($days.' days ago');
        $listAdverts = $this->advertRepository->findAdvertsbefore($date);

        if (count($listAdverts)>0){

            foreach ($listAdverts as $advert){
                $this->entityManager->remove($advert);
            }

            $this->entityManager->flush();
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()
                ->add('info', 'Les annonces plus vieilles que '.$days.' jours ont été purgées.');
        }else{
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()
                ->add('info', "Il n'y a pas d'annonces plus vieilles que ".$days." jours.");
        }

    }

}