<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Entity\View;
use App\Event\MessagePostEvent;
use App\Event\PlatformEvents;
use App\Form\AdvertType;
use App\Repository\AdvertRepository;
use App\Repository\ViewRepository;
use App\Service\AddView;
use App\Service\AdvertPurger;
use App\Service\Antispam;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/advert")
 */
class AdvertController extends AbstractController
{
    /**
     * @Route("/traduction/{name}", name="platform_traduction")
     * @param $name
     * @param TranslatorInterface $translator
     * @param AdvertRepository $advertRepository
     * @return Response
     */
    public function translation($name, TranslatorInterface $translator, AdvertRepository $advertRepository): Response
    {
        $translated = $translator->trans('All about translation with symfony');

        return $this->render('advert/translation.html.twig', [
            'name' => $name,
            'translated' => $translated,
            'hello' => $translator->trans('hello', ['%name%' => $name]),
            'number' => count($advertRepository->findAllAdverts()),
        ]);
    }

    /**
     * @Route("/purge/{days}", name="advert_purge")
     * @param AdvertPurger $advertPurger
     * @param $days
     * @return Response
     * @throws Exception
     */
    public function purgeAdvert(AdvertPurger $advertPurger, $days): Response
    {
        $advertPurger->purge($days);

        return $this->redirectToRoute('advert_index');
    }

    /**
     * @Route("/for/last/{days}/days", name="advert_last_days")
     * @param $days
     * @param AdvertRepository $advertRepository
     * @return Response
     * @throws Exception
     */
    public function advertsAfter($days, AdvertRepository $advertRepository): Response
    {
        $date = new DateTime($days.' days ago');
        $lastDaysAdverts = $advertRepository->findAdvertsAfter($date);

        return $this->render('advert/last_days.html.twig', [
            'lastDaysAdverts' => $lastDaysAdverts,
            'days' => $days,
        ]);
    }

    /**
     * @param AdvertRepository $advertRepository
     * @param $limit
     * @return Response
     */
    public function recentAdverts(AdvertRepository $advertRepository, $limit): Response
    {
        return $this->render('advert/_recent_adverts.html.twig', [
            'recentAdverts' => $advertRepository->findBy(['published' => true], ['id' => 'desc'], 5)
        ]);
    }

    /**
     * @Route("/view/{id}", name="json_response")
     * @param $id
     * @return JsonResponse
     */
    public function view($id): JsonResponse
    {
         /*$response = new  Response(json_encode(['id' => $id]));
         $response->headers->set('content-type', 'application/json');

         return $response;*/

        return new JsonResponse(['id'=>$id]);
    }

    /**
     * @Route("/", name="advert_index", methods={"GET"})
     * @param AdvertRepository $advertRepository
     * @return Response
     * @throws Exception
     */
    public function index(AdvertRepository $advertRepository): Response
    {
        $days = 100;
        $date = new Datetime($days.' days ago');
       //dd($advertRepository->findAdvertsBefore($date));

        $adverts = $advertRepository->findAllAdverts();

        return $this->render('advert/index.html.twig', [
            'adverts' => $adverts,
        ]);
    }

    /**
     * @Route("/new", name="advert_new", methods={"GET","POST"})
     * @param EventDispatcherInterface $eventDispatcher
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Antispam $antispam
     * @return Response
     */
    public function new(EventDispatcherInterface $eventDispatcher, Request $request, EntityManagerInterface $entityManager, Antispam $antispam): Response
    {
        $advert = new Advert();
        $form = $this->createForm(AdvertType::class, $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($antispam->isSpam($advert->getContent())){
                throw new \RuntimeException('Le contenu de votre annoce a été détectée comme un spam');
            }

            $advert->setAuthor($this->getUser());

            // On déclenche l'évènement à dispatcher aux différents listeners en l'instanciant
            $event = new MessagePostEvent($advert->getContent(), $advert->getAuthor());

            // Le gestionnaire dévènement dispatche aux listeners enrégistrés
            $eventDispatcher->dispatch($event, PlatformEvents::POST_MESSAGE);

            // On récupère ce qui a été modifié par le ou les listeners, ici le message pour exemple
            $advert->setContent($event->getMessage());

            $entityManager->persist($advert);
            $entityManager->flush();

            $this->addFlash('success', 'Votre annonce a été ajoutée avec succès !');
            return $this->redirectToRoute('advert_show', [
                'id' => $advert->getId(),
                'slug' => $advert->getSlug(),
            ]);
        }

        return $this->render('advert/new.html.twig', [
            'advert' => $advert,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}-{id}", name="advert_show", methods={"GET"}, requirements={"slug": "[a-z0-9\-]*"})
     * @param AddView $addView
     * @param Request $request
     * @param int $id
     * @param string $slug
     * @param Advert $advert
     * @param ViewRepository $viewRepository
     * @return Response
     * @throws NonUniqueResultException
     */
    public function show(AddView $addView, Request $request,int $id, string $slug, Advert $advert, ViewRepository $viewRepository): Response
    {
        if ($advert->getSlug() !== $slug){
            return $this->redirectToRoute('advert_show', [
                'id' => $advert->getId(),
                'slug' => $advert->getSlug(),
            ], 301);
        }

        /*$clientIp = $request->getClientIp();

        if (!$viewRepository->isFlood($clientIp, 15)){
            $addView->add($advert, $clientIp);
        }*/

        return $this->render('advert/show.html.twig', [
            'advert' => $advert,
            'nbViews' => count($advert->getViews()),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="advert_edit", methods={"GET","POST"})
     * @param Request $request
     * @param int $id
     * @param AdvertRepository $advertRepository
     * @return Response
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function edit(Request $request, int $id, AdvertRepository $advertRepository): Response
    {
        $advert = $advertRepository->findOneAdvert($id);

        $form = $this->createForm(AdvertType::class, $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//         $doctrine = $this->container->get('doctrine');
//        $em =  $doctrine->getManager()
//          ou directement par $this->get('doctrine.orm.entity_manager');
//          $em->flush();
//           $doctrine = $this->get('doctrine');
//          $doctrine->getManager()->flush();
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Votre annonce a été modifiée avec succès !');
            return $this->redirectToRoute('advert_show', [
                'id'=>$id,
                'slug'=> $advert->getSlug()
            ]);
        }

        return $this->render('advert/edit.html.twig', [
            'advert' => $advert,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="advert_delete", methods={"DELETE"})
     * @param Request $request
     * @param Advert $advert
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @throws Exception
     */
    public function delete(Request $request, Advert $advert, EntityManagerInterface $entityManager): Response
    {
        if (count($advert->getApplications()) > 0){
            $this->addFlash('warning',
                "Vous ne pouvez pas supprimer l'annonce <strong>{$advert->getTitle()}</strong> 
                            Car elle possède déjà des candidatures !");
        }elseif ($this->isCsrfTokenValid('delete'.$advert->getId(), $request->request->get('_token'))) {

            $entityManager->remove($advert);
            $entityManager->flush();

            $this->addFlash('success', 'Votre annonce a été supprimée avec succès !');
            return $this->redirectToRoute('advert_index');
    }

        return $this->redirectToRoute('advert_show', ['id'=>$advert->getId(), 'slug'=>$advert->getSlug()]);
    }
}
