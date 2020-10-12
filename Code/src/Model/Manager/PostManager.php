<?php
declare(strict_types=1);
namespace App\Model\Manager;

use App\Model\Entity\Post;
use App\Model\Repository\PostRepository;
use App\Service\Http\Request;
use App\Service\Http\Session;
use App\Service\Security\Token;

final class PostManager
{
    private PostRepository $postRepository;
    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }
    public function showOne(int $id): ?Post
    {
        return $this->postRepository->findById($id);
    }
    public function paginationPost(int $pp = null): array
    {
        $perPage = 6;
        $post = null;
        if (isset($pp)) {
            $total = $this->postRepository->count();
            $nbPage = ceil($total/$perPage);
            if (empty($pp) || ctype_digit($pp) === false || $pp <= 0) {
                $pp = 1;
            } elseif ($pp > $nbPage) {
                $pp = $nbPage;
            }
            
            $firstOfPage = ($pp - 1) * $perPage;
            $page = (int) $firstOfPage;
            $post= $this->postRepository->readAllPost($page, $perPage);
        }
        return $tabPost = [
            'current' => (int) $pp,
            'nbPage' => (int) $nbPage,
            'post' => $post,
        ];
    }
    public function verifFormAddPost(Session $session, Token $token, Request $request): ?array
    {
        $post = $request->getPost() ?? null;
        $file = $request->getFile()['imagePost'] ?? null;
        if (isset($post)) {
            $title = $post->get('title') ?? null;
            $chapo = $post->get('chapo') ?? null;
            $description = $post->get('description') ?? null;
            $tmpName = $file['tmp_name'] ?? null;
            $size = $file['size'] ?? null;
            $file = (empty($file['name'])) ? 'default.png' : $file['name'];
            $extention = mb_strtolower(mb_substr(mb_strrchr($file, '.'), 1)) ?? null;
            $extentions = ['jpg', 'png', 'gif', 'jpeg'];
            $tailleMax = 2097152;
            $succes = $session['succes'] ?? null;
            unset($succes);
            $errors = $session['wrong'] ?? null;
            unset($errors);
            if (empty($title) && empty($chapo) && empty($description) && empty($tmpName)) {
                $errors['error']["formEmpty"] = 'Veuillez mettre un contenu';
            } elseif (empty($title)) {
                $errors['error']["titleEmpty"] = 'Veuillez renseigner un titre';
            } elseif (empty($tmpName) || in_array($extention, $extentions, true) || $size > $tailleMax) {
                $errors['error']["imgWrong"] = 'Image n\'est pas valide, doit être en dessous de 2MO';
            } elseif (empty($chapo)|| mb_strlen($chapo) <= 15) {
                $errors['error']["chapoEmpty"] = "Chapô obligatoire, doit être inférieur ou égal à 15 caractères minimum";
            } elseif (mb_strlen($description) <= 15) {
                $errors['error']["descShort"] = "Description trop petite, doit être inférieur ou égal à 15 caractères";
            }
            if ($token->compareTokens($session, $post->get('token')) !== null) {
                $errors['error']['token'] = "Formulaire incorrect";
            }
            $dataForm = [
                'title' => $title,
                'tmpName' => $tmpName,
                'extention' => $extention,
                'chapo' => $chapo,
                'description' => $description,
                'idUser' => $session['idUser'],
            ];
            if (empty($errors)) {
                $this->postRepository->createPost($dataForm);
                $succes['sendPost'] = "Article bien enregistré";
                return $succes;
            }
            return $errors;
        }
        return null;
    }
}
