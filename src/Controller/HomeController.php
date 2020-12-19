<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
     *
     * @param Environment $environment
     * @param Request $request
     * @return Response
     * @throws LoaderError When the template cannot be found
     * @throws RuntimeError When an error occurred during rendering
     * @throws SyntaxError When an error occurred during compilation
     */
    public function index(Environment  $environment, Request $request): Response
    {
        $content = $environment->render('home/index.html.twig', [

        ]);

        //$url = $this->generateUrl('home', [], UrlGeneratorInterface::ABSOLUTE_URL);

       // dd($request->);

        return new Response($content);

        //return $this->render('home/index.html.twig');
    }
}
