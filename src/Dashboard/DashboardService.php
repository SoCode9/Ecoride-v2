<?php

namespace App\Dashboard;

use App\Utils\MailService;

final class DashboardService
{
    public function __construct(
        private MailService $mailer
    ) {}

    public function sendContact()
    {
        try {
            $nameVisitor = $_POST['firstname'] . " " . $_POST['lastname'];
            $emailVisitor = $_POST['email'];
            $phoneVisitor = $_POST['phone'];
            $messageVisitor = $_POST['message'] .
                "\n\nNom: $nameVisitor \nEmail: $emailVisitor \nTéléphone: $phoneVisitor";

            $ok = $this->mailer->sendContact($nameVisitor, $emailVisitor, nl2br($messageVisitor));

            $_SESSION[$ok ? 'success_message' : 'error_message'] =
                $ok ? "Votre message a été envoyé avec succès" : "Une erreur est survenue lors de l'envoi";
            header('Location:' . BASE_URL . '/');
            exit;
        } catch (\Throwable $mailErr) {
            error_log("Mail contact non envoyé : " . $mailErr->getMessage());
        }
    }
}
