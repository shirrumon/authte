<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class RegistrationController extends AbstractController
{
    private $verifyEmailHelper;
    private $mailer;

    public function __construct(VerifyEmailHelperInterface $helper, MailerInterface $mailer)
    {
        $this->verifyEmailHelper = $helper;
        $this->mailer = $mailer;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );



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

            $user->setMethod('UI');
            $user->setCreateDate(new DateTime("now"));

            $data = strtolower($user->getLang());
            $dataAr = explode(', ', $data);
            $unik = array_unique($dataAr);
            $st = implode(" ", $unik);
            $user->setLang($st);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
