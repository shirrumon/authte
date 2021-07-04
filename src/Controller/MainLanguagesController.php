<?php

namespace App\Controller;

use App\Entity\Languages;
use App\Form\LanguagesType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainLanguagesController extends AbstractController
{
    /**
     * @Route("/languages", name="main_languages")
     */
    public function addLang(Request $request): Response
    {
        $lang = new Languages();
        $form = $this->createForm(LanguagesType::class, $lang);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($lang);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('main');
        }

        return $this->render('main_languages/index.html.twig', [
            'langform' => $form->createView(),
        ]);
    }
}
