<?php


namespace App\Service\Notification\Channels\Mail;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailManager
{

    /** @var MailerInterface mailer */
    private $mailer;

    /**
     * MailManager constructor.
     * @param MailerInterface $mailer
     */
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @return MailerInterface
     */
    public function getMailer(): MailerInterface
    {
        return $this->mailer;
    }

    /**
     * @param MailerInterface $mailer
     */
    public function setMailer(MailerInterface $mailer): void
    {
        $this->mailer = $mailer;
    }

    /**
     * @param $data
     * @return Email
     */
    public function createNotification($data)
    {
        $email = new Email();
        $email->from($data['from']);
        $email->to($data['from']);
        $email->subject($data['subject']);
        $email->text($data['text']);
        return $email;
    }

    /**
     * @param Email $email
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function send(Email $email)
    {
        $this->mailer->send($email);
    }
}