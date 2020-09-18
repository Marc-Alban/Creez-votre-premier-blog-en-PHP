<?php
declare(strict_types=1);
namespace App\Model\Manager;

use App\Service\Security\Token;

class HomeManager
{

    private Token $token;

    public function __construct( Token $token)
    {
        $this->token = $token;
    }

    private function sendMail(array $data): void
    {
            $message = $data['post']['message'];
            $entete  = 'MIME-Version: 1.0' . "\r\n";
            $entete .= 'Content-type: text/html; charset=utf-8' . "\r\n";
            $entete .= 'From: ' . $data['post']['mail'] . "\r\n";
    
            $message = '<h1>Message envoyé depuis la page Home de localhost:8000</h1>
            <p><b>Nom : </b>' . htmlentities(strip_tags(trim($data['post']['name']))) . '<br>
            <b>Prenom : </b>' . htmlentities(strip_tags(trim($data['post']['lastName']))) . '<br>
            <b>Email : </b>' . trim($data['post']['mail']) . '<br>
            <b>Message : </b>' . htmlentities(strip_tags(trim($data['post']['message']))) . '</p>';
    
            mail('millet.marcalban@gmail.com', 'Envoi depuis page home', $message, $entete);
    }

    public function verifMail(array $data): ?array
    {
        if (isset($data['post']['submit'])) {

            $mail = trim($data['post']['mail']);
            $nom = htmlentities(strip_tags(trim($data['post']['name'])));
            $prenom = htmlentities(strip_tags(trim($data['post']['lastName'])));
            $message = htmlentities(strip_tags(trim($data['post']['message'])));

            

            $errors = $data["session"]["error"] ?? null;
            unset($data["session"]["error"]);

            $succes = $data["session"]["succes"] ?? null;
            unset($data["session"]["succes"]);

            if (empty($sujet) && empty($tel) && empty($mail) && empty($message) && empty($nomPrenom) && empty($cp)) {
                $errors['error']['allEmpty'] = "Veuillez remplir le formulaire";
            } else if (empty($nom)) {
                $errors['error']['nameEmpty'] = "Veuillez mettre un nom";
            } else if (empty($prenom)) {
                $errors['error']['lastNameEmpty'] = "Veuillez mettre un prénom";
            } else if (empty($mail)) {
                $errors['error']['mailEmpty'] = "Veuillez mettre un mail";
            } else if (empty($message)) {
                $errors['error']['messageEmpty'] = "Veuillez mettre un message";
            } 

            if ($this->token->compareTokens($data) !== null) {
                $errors['error']['tokenEmpty'] = $this->token->compareTokens($data);
            }

            if (!empty($mail)) {
                if (!preg_match(" /^.+@.+\.[a-zA-Z]{2,}$/ ", $mail)) {
                    $errors['error']['mailWrong'] = "L'adresse e-mail est invalide";
                }
            }

            if (empty($errors)) {
                $this->sendMail($data);
                $succes['succes']['send'] = 'Votre message a bien été envoyé.';
                return $succes;
            }
            
            return $errors;
        }
        return null;
    }
}