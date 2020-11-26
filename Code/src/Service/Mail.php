<?php
declare(strict_types=1);
namespace App\Service;

use App\Service\Http\Request;
use App\Service\Http\Session;
use App\Service\Security\Token;
use App\View\View;

final class Mail
{
    private $errors = null;
    private $success = null;
    private Request $request;
    private View $view;
    public function __construct(Request $request, View $view)
    {
        $this->request = $request;
        $this->view = $view;
    }
    /**
     * Send a mail with the function php mail()
     *
     * @return void
     */
    public function sendMail(): void
    {
        $entete  = 'MIME-Version: 1.0' . "\r\n";
        $entete .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $entete .= 'From: ' . $this->request->getPost()->getName('mail') . "\r\n";
        $message = $this->view->renderMail(['name'=>$this->request->getPost()->getName('name'),'lastName'=>$this->request->getPost()->getName('lastName'),'mail'=>$this->request->getPost()->getName('mail'),'message'=>$this->request->getPost()->getName('message')]);
        mail('millet.marcalban@gmail.com', 'E-mail envoyé du site DevDark', $message, $entete);
    }
    /**
     * Check the email to avoid errors
     *
     * @param Session $session
     * @param Token $token
     * @param Request $request
     * @return array|null
     */
    public function checkMail(Session $session, Token $token, Request $request): ?array
    {
        $post = $request->getPost() ?? null;
        $mail = $post->getName('mail') ?? null;
        $name = $post->getName('name') ?? null;
        $lastName = $post->getName('lastName') ?? null;
        $message = $post->getName('message') ?? null;
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
        if ($token->compareTokens($session->getSessionName('token'), $post->getName('token')) !== false) {
            $this->errors['error']['tokenEmpty'] = 'Formulaire incorrect';
        }
        if (empty($this->errors)) {
            $this->success['send'] = 'Votre message a bien été envoyé.';
            return $this->success;
        }
        return $this->errors;
    }
}
