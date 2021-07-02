<?php

namespace App\Service;


use DateTime;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;


class UserSetUp
{

    private $mailer;

    public $usersTh;
    public $usersThree;
    public $usersSv;

    public function __construct(VerifyEmailHelperInterface $helper, MailerInterface $mailer)
    {
        $this->verifyEmailHelper = $helper;
        $this->mailer = $mailer;
    }

    public function UsrSet($user) // SET UP FUNCTION FOR USERS
    {
        $date = $user->getDate();
        $dateNow = new DateTime("now");
        $difer = $date->diff($dateNow);
        $difer = $difer->format("%a");
        if ($difer >= 6570) {
            $email = new TemplatedEmail();
            $email->from('nwchanel69@gmail.com');
            $email->to($user->getEmail());
            $email->htmlTemplate('registration/confirmation_email.html.twig');
            $email->context(['Your account has been activated']);

            $user->setIsVerified(true);
            $this->mailer->send($email);
        }

        $user->setCreateDate(new DateTime("now"));
        $user->setMethod('UI');

        $data = strtolower($user->getLang());
        $dataAr = explode(', ', $data);
        $unik = array_unique($dataAr);
        $st = implode(" ", $unik);
        $user->setLang($st);

        $user->setRoles((array)'ROLE_USER');
    }

    public function Filter($userRepository) // DATE FILTER FOR USERS REGISTER
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
    }

    public function UserConfirm($user) //CONFIRM YOUR USER FOR ADMIN PANEL
    {
        if($user->isVerified() == true){
            $email = new TemplatedEmail();
            $email->from('nwchanel69@gmail.com');
            $email->to($user->getEmail());
            $email->htmlTemplate('registration/confirmation_email.html.twig');
            $email->context(['Your account has been activated']);

            $this->mailer->send($email);
        }
    }
}