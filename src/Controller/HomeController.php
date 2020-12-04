<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class HomeController
 * @package App\Controller
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param Environment $environment
     * @return Response
     * @throws LoaderError When the template cannot be found
     * @throws RuntimeError When an error occurred during rendering
     * @throws SyntaxError When an error occurred during compilation
     */
    public function index(Environment  $environment): Response
    {
        $content = $environment->render('home/index.html.twig', [

        ]);

        return new Response($content);

//        return $this->render('home/index.html.twig');
    }
}
