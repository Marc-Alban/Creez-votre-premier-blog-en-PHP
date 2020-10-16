<?php
declare(strict_types=1);
namespace App\Model\Manager;

use App\Service\Http\Request;
use App\Service\Http\Session;
use App\Service\Security\Token;

final class MailManager
{
    private $errors = null;
    private $succes = null;
    public function sendMail(Request $request): void
    {
        $message = $request->getPost()->get('message');
        $entete  = 'MIME-Version: 1.0' . "\r\n";
        $entete .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $entete .= 'From: ' . $request->getPost()->get('mail') . "\r\n";
        $message = require_once ROOT . 'templates\frontoffice\mail.html.twig';
        mail('millet.marcalban@gmail.com', 'Envoi depuis page home', $message, $entete);
    }
    public function checkMail(Session $session, Token $token, Request $request, string $action = null): ?array
    {
        $post = $request->getPost() ?? null;
        if ($action === "send") {
            $mail = $post->get('mail') ?? null;
            $name = $post->get('name') ?? null;
            $lastName = $post->get('lastName') ?? null;
            $message = $post->get('message') ?? null;
            if (empty($mail) && empty($message) && empty($name) && empty($lastName)) {
                $this->errors['error']['allEmpty'] = "Veuillez remplir le formulaire";
            } elseif (empty($name)) {
                $this->errors['error']['nameEmpty'] = "Veuillez mettre un nom";
            } elseif (empty($lastName)) {
                $this->errors['error']['lastNameEmpty'] = "Veuillez mettre un prénom";
            } elseif (empty($mail) || !preg_match(" /^.+@.+\.[a-zA-Z]{2,}$/ ", $mail)) {
                $this->errors['error']['mailEmpty'] = "Veuillez mettre un mail valide";
            } elseif (empty($message)) {
                $this->errors['error']['messageEmpty'] = "Veuillez mettre un message";
            }
            if ($token->compareTokens($session->getSessionName('token'), $post->get('token')) !== false) {
                $this->errors['error']['tokenEmpty'] = 'Formulaire incorrect';
            }
            if (empty($this->errors)) {
                $this->succes['succes']['send'] = 'Votre message a bien été envoyé.';
                return $this->succes;
            }
            return $this->errors;
        }
        return null;
    }
}
