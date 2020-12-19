<?php


namespace App\Service;


use Symfony\Component\HttpFoundation\Response;

class BetaHtmlAdder
{
    /**
     * Méthode pour ajouter le « bêta » à une réponse
     * @param Response $response
     * @param $remainingDays
     * @return Response
     */
    public function addBeta(Response $response, $remainingDays): Response
    {
        $content = $response->getContent();

        // Code à rajouter
        $html = '<div class="beta-html-adder">Beta J - ' .$remainingDays. ' !</div>';

        // Insertion du code dans la page, au début du <body>
        $content = str_replace('<body>','<body> '.$html, $content);

        // Modification du contenu dans la réponse
        $response->setContent($content);

        return $response;
    }
}