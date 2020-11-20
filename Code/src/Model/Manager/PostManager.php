<?php
declare(strict_types=1);
namespace App\Model\Manager;

use App\Model\Entity\Post;
use App\Model\Repository\PostRepository;
use App\Model\Repository\UserRepository;
use App\Service\Http\Request;
use App\Service\Http\Session;
use App\Service\Security\Token;

final class PostManager
{
    private PostRepository $postRepository;
    private ?array $errors = [];
    private ?array $success = [];
    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }
    /**
     * Return the post find with the idPost
     *
     * @param integer $idPost
     * @return Post|null
     */
    public function findPostByIdPost(int $idPost): ?Post
    {
        return $this->postRepository->findByIdPost($idPost);
    }
    /**
     * Get all idPosts in database
     *
     * @return array|null
     */
    public function findAllIdPost(): ?array
    {
        return $this->postRepository->findIdPost();
    }
    /**
     * Pagination of the posts page where all the articles are located
     *
     * @param integer $perpage
     * @return array
     */
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
    /**
     * Count All post with not parameters
     *
     * @return string
     */
    public function countAllPost(): string
    {
        $post = $this->postRepository->count();
        return $post;
    }
    /**
     * Method for deleted a post with idPost
     *
     * @param integer $idPost
     * @return array|null
     */
    public function deletePost(int $idPost): ?array
    {
        $post = $this->postRepository->findByIdPost($idPost);
        if ($post->getIdPost() !== $idPost) {
            return null;
        }
        $this->postRepository->delete($post->getIdPost());
        unlink('images/post/'.$post->getImagePost());
        $this->success['success'] = "Article bien supprimé";
        return $this->success;
    }
    /**
     * Create a default post if don't have in database
     *
     * @return array
     */
    public function defaultPost(): array
    {
        return [
            'title' => 'Article par défault',
            'description' => 'Description par défault',
            'chapo'=> 'Chapô par défault',
            'imagePost' => 'default.png',
            'date' => '**/**/**'
        ];
    }
    /**
     * Verification form for push or update post in bdd
     *
     * @param UserRepository $userRepository
     * @param Session $session
     * @param Token $token
     * @param Request $request
     * @param string $action
     * @return array
     */
    public function checkFormPost(UserRepository $userRepository, Session $session, Token $token, Request $request, string $action): array
    {
        $post = $request->getPost() ?? null;
        $file = $request->getFile('imagePost') ?? null;
        if ($post->getName('submit') !== null) {
            $title = $post->getName('title') ?? null;
            $chapo = $post->getName('chapo') ?? null;
            $description = $post->getName('description') ?? null;
            $tmpName = $file['tmp_name'] ?? null;
            $size = $file['size'] ?? null;
            $fileName = (empty($file['name'])) ? 'default.png' : $file['name'];
            $extention = mb_strtolower(mb_substr(mb_strrchr($fileName, '.'), 1)) ?? null;
            $extentionValide = ['jpg', 'png', 'gif', 'jpeg'];
            $tailleMax = 2097152;
            $userSession = ($session->getSessionName('user'))? $session->getSessionName('user') : $session->getSessionName('admin');
            $user = $userRepository->findByEmail($userSession);
            if (empty($title) && empty($chapo) && empty($description) && empty($tmpName)) {
                $this->errors['error']["formEmpty"] = 'Veuillez mettre un contenu';
            } elseif (empty($title)) {
                $this->errors['error']["titleEmpty"] = 'Veuillez renseigner un titre';
            } elseif (empty($tmpName) || in_array($extention, $extentionValide, true) === false || $size > $tailleMax) {
                $this->errors['error']["imgWrong"] = 'Image absente,ou extention invalide ou encore image trop grande, doit être en dessous de 2MO';
            } elseif (empty($chapo)|| mb_strlen($chapo) <= 10 || mb_strlen($chapo) >= 100) {
                $this->errors['error']["chapoEmpty"] = "Chapô absent,trop petit ou trop grand, doit être supérieur ou égal à 15 caractère ou encore inférieur ou égal à 100 carctères";
            } elseif (mb_strlen($description) <= 15) {
                $this->errors['error']["descShort"] = "Description trop petite, doit être supérieur ou égal à 15 caractère";
            }
            if ($token->compareTokens($session->getSessionName('token'), $post->getName('token')) !== false) {
                $this->errors['error']['formRegister'] = "Formulaire incorrect";
            }
            $dataForm = [
                'idPost' => null,
                'title' => $title,
                'tmpName' => $tmpName,
                'extention' => $extention,
                'chapo' => $chapo,
                'description' => $description,
                'statuPost' => 1,
                'userId' => $user->getIdUser(),
            ];
            if (empty($this->errors) && $action === 'create') {
                $this->postRepository->create($dataForm);
                $this->success['success'] = "Article bien enregistré";
                return $this->success;
            } elseif (empty($this->errors) && $action === 'update') {
                $this->postRepository->update($dataForm, (int) $request->getGet()->getName('id'));
                $this->success['success'] = "Article bien mis à jour";
                return $this->success;
            }
        }
        return $this->errors;
    }
}
