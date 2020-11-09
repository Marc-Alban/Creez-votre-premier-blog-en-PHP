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
    private $success = null;
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
     * Give the UserId with the mail
     * @param string $email
     * @return integer|null
     */
    public function findUserIdByEmail(string $email): ?int
    {
        return $this->postRepository->findUserByEmail($email);
    }
    /**
     * Get all idPosts in database
     *
     * @return array
     */
    public function findAllIdPost(): array
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
     * Verification of the bdd post insertion form
     *
     * @param Session $session
     * @param Token $token
     * @param Request $request
     * @return array|null
     */
    public function checkFormAddPost(Session $session, Token $token, Request $request): ?array
    {
        $post = $request->getPost() ?? null;
        $file = $request->getFile('imagePost') ?? null;
        if ($post) {
            $title = $post->get('title') ?? null;
            $chapo = $post->get('chapo') ?? null;
            $description = $post->get('description') ?? null;
            $tmpName = $file['tmp_name'] ?? null;
            $size = $file['size'] ?? null;
            $fileName = (empty($file['name'])) ? 'default.png' : $file['name'];
            $extention = mb_strtolower(mb_substr(mb_strrchr($fileName, '.'), 1)) ?? null;
            $extentionValide = ['jpg', 'png', 'gif', 'jpeg'];
            $tailleMax = 2097152;
            $user = $this->postRepository->findUserByEmail($session->getSessionName('user'));
            if (empty($title) && empty($chapo) && empty($description) && empty($tmpName)) {
                $this->errors['error']["formEmpty"] = 'Veuillez mettre un contenu';
            } elseif (empty($title)) {
                $this->errors['error']["titleEmpty"] = 'Veuillez renseigner un titre';
            } elseif (empty($tmpName) || in_array($extention, $extentionValide, true) === false || $size > $tailleMax) {
                $this->errors['error']["imgWrong"] = 'Image absente,ou extention invalide ou encore image trop grande, doit être en dessous de 2MO';
            } elseif (empty($chapo)|| mb_strlen($chapo) <= 10 || mb_strlen($chapo) >= 30) {
                $this->errors['error']["chapoEmpty"] = "Chapô absent,trop petit ou trop grand, doit être supérieur ou égal à 15 caractère ou encore inférieur ou égal à 30 carctères";
            } elseif (mb_strlen($description) <= 15 ) {
                $this->errors['error']["descShort"] = "Description trop petite, doit être supérieur ou égal à 15 caractère";
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
                'userId' => $user->getIdUser(),
            ];
            if (empty($this->errors)) {
                $this->postRepository->create($dataForm);
                $this->success['sendPost'] = "Article bien enregistré";
                return $this->success;
            }
            return $this->errors;
        }
        return null;
    }
}
