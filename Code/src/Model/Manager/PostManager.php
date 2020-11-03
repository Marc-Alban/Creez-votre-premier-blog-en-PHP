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
    private $errors = null;
    private $succes = null;
    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }
    public function findByIdPost(int $idPost): ?Post
    {
        return $this->postRepository->findById($idPost);
    }
    public function findIdUserByEmail(string $email): int
    {
        return $this->postRepository->findIdUser($email);
    }
    public function paginationPost(int $perpage = 1): array
    {
        $minPost = 6;
        $total = $this->postRepository->count();
        $nbPage = (int) ceil($total/$minPost);
        if (ctype_digit($perpage) === true || $perpage <= 0) {
            $perpage = 1;
        } elseif ($perpage > $nbPage) {
            $perpage = $nbPage;
        }
        $page =  ($perpage-1) * $minPost;
        $post = $this->postRepository->findAll($page, $minPost);
        return [
            'current' => $perpage,
            'nbPage' => $nbPage,
            'post' => $post
        ];
    }
    public function checkFormAddPost(Session $session, Token $token, Request $request): ?array
    {
        $post = $request->getPost() ?? null;
        $file = $request->getFile()['imagePost'] ?? null;
        if ($post->get('submit')) {
            $title = $post->get('title') ?? null;
            $chapo = $post->get('chapo') ?? null;
            $description = $post->get('description') ?? null;
            $tmpName = $file['tmp_name'] ?? null;
            $size = $file['size'] ?? null;
            $file = (empty($file['name'])) ? 'default.png' : $file['name'];
            $extention = mb_strtolower(mb_substr(mb_strrchr($file, '.'), 1)) ?? null;
            $extentions = ['jpg', 'png', 'gif', 'jpeg'];
            $tailleMax = 2097152;
            if (empty($title) && empty($chapo) && empty($description) && empty($tmpName)) {
                $this->errors['error']["formEmpty"] = 'Veuillez mettre un contenu';
            } elseif (empty($title)) {
                $this->errors['error']["titleEmpty"] = 'Veuillez renseigner un titre';
            } elseif (empty($tmpName) || in_array($extention, $extentions, true) || $size > $tailleMax) {
                $this->errors['error']["imgWrong"] = 'Image n\'est pas valide, doit être en dessous de 2MO';
            } elseif (empty($chapo)|| mb_strlen($chapo) <= 15) {
                $this->errors['error']["chapoEmpty"] = "Chapô obligatoire, doit être supérieur ou égal à 15 caractères minimum";
            } elseif (mb_strlen($description) <= 15) {
                $this->errors['error']["descShort"] = "Description trop petite, doit être supérieur ou égal à 15 caractères";
            }
            if ($token->compareTokens($session->getSessionName('token'), $post->get('token')) !== false) {
                $this->errors['error']['formRegister'] = "Formulaire incorrect";
            }
            $dataForm = [
                'title' => $title,
                'tmpName' => $tmpName,
                'extention' => $extention,
                'chapo' => $chapo,
                'description' => $description,
                'idUser' => $session->getSessionName('idUser'),
            ];
            if (empty($this->errors)) {
                $this->postRepository->create($dataForm);
                $this->succes['sendPost'] = "Article bien enregistré";
                return $this->succes;
            }
            return $this->errors;
        }
        return null;
    }
}
