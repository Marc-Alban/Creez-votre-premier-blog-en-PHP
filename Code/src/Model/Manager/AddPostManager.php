<?php

declare(strict_types=1);

namespace App\Model\Manager;

use App\Service\Security\Token;
use App\Model\Repository\AddPostRepository;


final class AddPostManager
{
    private Token $token;
    private AddPostRepository $AddPostRepository;

    public function __construct(array $datas)
    {
        $this->token = $datas['token'];
        $this->AddPostRepository = $datas['repository']['repoPage'];
    }

    public function verifFormAddPost(array $datas): ?array
    {
        $post = $datas["post"] ?? null;

        if (isset($post)) {
            $title = htmlentities(trim($datas["post"]['title'])) ?? null;
            $chapo = htmlentities(trim($datas["post"]['chapo'])) ?? null;
            $description = htmlentities(trim($datas["post"]['description'])) ?? null;
            $tmpName = $datas['files']['imagePost']['tmp_name'] ?? null;
            $size = $datas['files']['imagePost']['size'] ?? null;
            $file = (empty($datas['files']['imagePost']['name'])) ? 'default.png' : $datas['files']['imagePost']['name'];
            $extention = strtolower(substr(strrchr($file, '.'), 1)) ?? null;
            $extentions = ['jpg', 'png', 'gif', 'jpeg'];
            $tailleMax = 2097152;

            $succes = $datas['succes'] ?? null;
            unset($succes['succes']);

            $errors = $datas['wrong'] ?? null;
            unset($datas['wrong']);

            if (empty($title) && empty($chapo) && empty($description) && empty($tmpName)) {
                $errors['error']["formEmpty"] = 'Veuillez mettre un contenu';
            } else if (empty($title)){
                $errors['error']["titleEmpty"] = 'Veuillez renseigner un titre';
            } else if (empty($tmpName)) {
                $errors['error']["imgEmpty"] = 'Image obligatoire';
            } else if (!in_array($extention, $extentions)) {
                $errors['error']["imgWrong"] = 'Image n\'est pas valide';
            } else if ($size > $tailleMax) {
                $errors['error']["imgLarge"] = "Image trop grande, mettre une image en dessous de 2 MO ";
            } else if (empty($chapo)) {
                $errors['error']["chapoEmpty"] = "Veuillez mettre un chapô";
            } else if (strlen($chapo) <= 15) {
                $errors['error']["chapoShort"] = "Chapô trop petit, doit être inférieur ou égal à 15 caractères minimum";
            } else if (strlen($description) <= 6) {
                $errors['error']["descShort"] = "Description trop petite, doit être inférieur ou égal à 15 caractères";
            } 
            if ($this->token->compareTokens($datas) !== null) {
                $errors['error']['token'] = "Formulaire incorrect";
            }

            $dataForm = [
                'title' => $title,
                'tmpName' => $tmpName,
                'extention' => $extention,
                'chapo' => $chapo,
                'description' => $description,
                'idUser' => $datas['session']['idUser'],
            ];

            if (empty($errors)) {
                $this->AddPostRepository->createPost($dataForm);
                $succes['sendPost'] = "Article bien enregistré";
                return $succes;
            }

            return $errors;
        }

        return null;
    }

}
