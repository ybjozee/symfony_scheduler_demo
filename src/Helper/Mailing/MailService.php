<?php

namespace App\Helper\Mailing;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;

readonly class MailService
{
    public function __construct(
        private MailerInterface $mailer,
        #[Autowire('%env(resolve:SENDER_EMAIL)%')]
        private string $senderEmail,
        #[Autowire('%env(resolve:RECIPIENT_EMAIL)%')]
        private string $recipientEmail,
    ) {}

    /**
     * @throws TransportExceptionInterface
     */
    public function sendMail(
        string $subject,
        string $message,
        string $attachmentPath,
        string $filename
    ): void {
        $email = new Email()
            ->from($this->senderEmail)
            ->to($this->recipientEmail)
            ->subject($subject)
            ->text($message)
            ->html($message)
            ->addPart(new DataPart(new File($attachmentPath, $filename)));

        $this->mailer->send($email);
    }
}