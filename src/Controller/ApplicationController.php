<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Entity\Application;
use App\Form\ApplicationType;
use App\Service\Antispam;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApplicationController extends AbstractController
{
    /**
     * @Route("/application", name="application")
     */
    public function index(): Response
    {
        return $this->render('application/index.html.twig', [
            'controller_name' => 'ApplicationController',
        ]);
    }

    /**
     * @Route("/Application/advert/{id}", name="application_new")
     * @param Advert $advert
     * @param Request $request
     * @param Antispam $antispam
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function new(Advert $advert, Request $request, Antispam $antispam, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ApplicationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            /** @var Application $application */
            $application = $form->getData();

            if ($antispam->isSpam($application->getContent())){
                throw new \RuntimeException('Le contenu de votre candidature a été détectée comme un spam');
            }

            $application->setAuthor($this->getUser());
            $application->setAdvert($advert);

            $entityManager->persist($application);
            $entityManager->flush();

            $this->addFlash('success', 'Votre candidature a été envoyée avec succès !');
            return $this->redirectToRoute(
                'advert_show',
                ['id' => $advert->getId(), 'slug' => $advert->getSlug()]
            );
        }

        return $this->render('application/new.html.twig', [
            'advert' => $advert,
            'form' => $form->createView(),
        ]);
    }
}
