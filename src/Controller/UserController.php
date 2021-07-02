<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Service\UserSetUp;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{

    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository, UserSetUp $filter): Response
    {
        return $this->render('user/index.html.twig', [
            'dtNow' => new DateTime("now"),
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/index3", name="user_index3", methods={"GET"})
     */
    public function index3(UserRepository $userRepository, UserSetUp $filter)
    {
        return $this->render('user/index3.html.twig', [
            'usersTh' => $filter->Filter3($userRepository),
            'dtNow' => new DateTime("now"),
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/index7", name="user_index7", methods={"GET"})
     */
    public function index7(UserRepository $userRepository, UserSetUp $filter)
    {
        return $this->render('user/index7.html.twig', [
            'usersSv' => $filter->Filter3($userRepository),
            'dtNow' => new DateTime("now"),
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/index30", name="user_index30", methods={"GET"})
     */
    public function index30(UserRepository $userRepository, UserSetUp $filter)
    {
        return $this->render('user/index30.html.twig', [
            'usersThree' => $filter->Filter30($userRepository),
            'dtNow' => new DateTime("now"),
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder, UserSetUp $userReady): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userReady->UsrSet($user);
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }


    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */

    public function edit(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder, UserSetUp $confirm): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $confirm->UserConfirm($user);
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }
}
