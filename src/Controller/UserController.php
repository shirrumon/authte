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
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Validator\Constraints\Date;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{

    private $verifyEmailHelper;
    private $mailer;

    public function __construct(VerifyEmailHelperInterface $helper, MailerInterface $mailer)
    {
        $this->verifyEmailHelper = $helper;
        $this->mailer = $mailer;
    }

    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        $date = date('Y-m-d h:i:s', strtotime("-30 days"));

        $usersThree = $userRepository
            ->createQueryBuilder('e')
            ->select('e')
            ->setParameter('today', date("Y-m-d"))
            ->setParameter('days', $date)
            ->where('e.createDate BETWEEN :days AND :today')
            ->getQuery()
            ->getArrayResult();

        $dateTw = date('Y-m-d h:i:s', strtotime("-3 days"));

        $usersTh = $userRepository
            ->createQueryBuilder('e')
            ->select('e')
            ->setParameter('today', date("Y-m-d"))
            ->setParameter('n3days', $dateTw)
            ->where('e.createDate BETWEEN :n3days AND :today')
            ->getQuery()
            ->getArrayResult();

        $dateSv = date('Y-m-d h:i:s', strtotime("-7 days"));

        $usersSv = $userRepository
            ->createQueryBuilder('e')
            ->select('e')
            ->where('e.createDate BETWEEN :n7days AND :today')
            ->setParameter('today', date("Y-m-d"))
            ->setParameter('n7days', $dateSv)
            ->getQuery()
            ->getArrayResult();

        return $this->render('user/index.html.twig', [
            'usersThree' => $usersThree,
            'usersTh' => $usersTh,
            'usersSv' => $usersSv,
            'dtNow' => new DateTime("now"),
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date = $user->getDate();
            $dateNow = new DateTime("now");
            $difer = $date->diff($dateNow);
            $difer = $difer->format("%a");
            if ($difer >= 6570){
                $email = new TemplatedEmail();
                $email->from('nwchanel69@gmail.com');
                $email->to($user->getEmail());
                $email->htmlTemplate('registration/confirmation_email.html.twig');
                $email->context(['Your account has been activated']);

                $user->setIsVerified(true);
                $this->mailer->send($email);
            }
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $user->setMethod('UI');
            $user->setCreateDate(new DateTime("now"));
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

    public function edit(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($user->isVerified() == true){
                $email = new TemplatedEmail();
                $email->from('nwchanel69@gmail.com');
                $email->to($user->getEmail());
                $email->htmlTemplate('registration/confirmation_email.html.twig');
                $email->context(['Your account has been activated']);

                $this->mailer->send($email);
            }

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
