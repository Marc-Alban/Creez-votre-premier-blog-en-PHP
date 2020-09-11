<?php
declare(strict_types=1);
namespace App\Model\Manager;
use App\Model\Repository\BlogRepository;
use App\Service\Database;

class BlogManager
{
    private BlogRepository $blogRepository;
    private Database $db;

    public function __construct(BlogRepository $blogRepository)
    {
        $this->blogRepository = $blogRepository;
    }


    /*************************Laste Post********************************** */
    /**
     * Retourne le dernier post dans un tableau
     * ou false si il n'y a rien
     *
     * @return Object
     *
     */
    public function lastPost(): ?Object
    {
        return $this->blogRepository->lastPost();
    }
    /*************************End Laste Post********************************** */

    public function paginationPost(array $data): array
    {
        $perPage = 6;
        $current = $data['get']['pp'] ?? null;
        $perCurrent = $data['get']['pp'] ?? null;
        $postFront = null;
        $postAll = null;

        if(isset($current)){
            $total = $this->blogRepository->count('front');
            $nbPage = ceil($total/$perPage);
            if(!isset($current) || empty($current) || ctype_digit($current) === false || $current <= 0){
                $current = 1;
            }else if ($current > $nbPage){
                $current = $nbPage;
            }
            $firstOfPage = ($current - 1) * $perPage;
            $page = (int) $firstOfPage;
            $postFront = $this->blogRepository->readAllPost($page, $perPage, 'readAllPost');
        }

        if (isset($perCurrent)) {
            $total = $this->blogRepository->count('back');
            $nbPage = ceil($total / $perPage);
            if (!isset($perCurrent) || empty($perCurrent) || ctype_digit($perCurrent) === false || $perCurrent <= 0) {
                $perCurrent = 1;
            } else if ($perCurrent > $nbPage) {
                $perCurrent = $nbPage;
            }
            $twoOfPage = ($perCurrent - 1) * $perPage;
            $page = (int) $twoOfPage;
            $postAll = $this->blogRepository->readAllPost($page, $perPage, 'readAll');
        }

        // var_dump($nbPage);
        // die();

        return $tabPost = [
            'current' => (int) $current,
            'perCurrent' => (int) $perCurrent,
            'nbPage' => (int) $nbPage,
            'post' => $postFront,
            'postAll' => $postAll
        ];
    }
}