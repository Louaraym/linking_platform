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
        // (Je mets ici du CSS en ligne, mais il faudrait utiliser un fichier CSS bien sûr !)
        $html = '<div style="position: fixed; top: 55px; background: orange; width: 100%; text-align: center; padding: 0.5em;">
                    Beta J-'.(int) $remainingDays.' ! 
                </div>';

        // Insertion du code dans la page, au début du <body>
        $content = str_replace(
            '<body>',
            '<body> '.$html,
            $content
        );

        // Modification du contenu dans la réponse
        $response->setContent($content);

        return $response;
    }
}