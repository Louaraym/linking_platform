<?php


namespace App\KernelEventsListeners;


use App\Service\BetaHtmlAdder;
use DateTime;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class BetaListener
{
    /**
     * Notre processeur
     * @var BetaHtmlAdder
     */
    private $betaHtmlAdder;

    /**
     * La date de fin de la version bêta :
     * Avant cette date, on affichera un compte à rebours (J-3 par exemple)
     * Après cette date, on n'affichera plus le « bêta »
     *
     * @var DateTime
     */
    private $endDate;

    public function __construct($endDate, BetaHtmlAdder $betaHtmlAdder)
    {
        $this->betaHtmlAdder = $betaHtmlAdder;
        $this->endDate = new \DateTime($endDate);
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $origin = new DateTime('Today');
        $target = $this->endDate;
        $remainingDays = $origin->diff($target)->days;

        // Si la date est dépassée, on ne fait rien
        if ($origin >= $target) { return; }

        // On teste si la requête est bien la requête principale (et non une sous-requête)
        if (!$event->isMasterRequest()) { return; }

        // On récupère la réponse que le gestionnaire a insérée dans l'évènement
        $response = $event->getResponse();

        // Ici on modifie comme on veut la réponse…
        $newResponse = $this->betaHtmlAdder->addBeta($response, $remainingDays);

        // Puis on insère la réponse modifiée dans l'évènement
        $event->setResponse($newResponse);

        /* On stop la propagation de l'évènement en cours, ( ici kernel.response )
            cela empêche les listeners de priorité inférieure au stopeur de s'exécuter*/
        //$event->stopPropagation();
    }
}