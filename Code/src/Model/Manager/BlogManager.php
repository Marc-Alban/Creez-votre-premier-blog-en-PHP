<?php
declare(strict_types=1);
namespace App\Model\Manager;
use App\Model\Repository\BlogRepository;

class BlogManager
{
    private BlogRepository $blogRepository;

    public function __construct(BlogRepository $blogRepository)
    {
        $this->blogRepository = $blogRepository;
    }


    public function paginationPost(array $data): array
    {
        $perPage = 6;
        $current =  $data['get']['pp'] ?? null;
        $postFront = null;

        
        if(isset($current)){
            $total = $this->blogRepository->count('front');
            $nbPage = ceil($total/$perPage);
            if(empty($current) || ctype_digit($current) === false || $current <= 0){
                $current = 1;
            }else if ($current > $nbPage){
                $current = $nbPage;
            }
            
            $firstOfPage = ($current - 1) * $perPage;
            $page = (int) $firstOfPage;
            $postFront = $this->blogRepository->readAllPost($page, $perPage, 'readAllNoOne');
        }
        
        var_dump($postFront); // Beau avoir fait ça je n'ai qu'un post qui apparait ? 
        die();

        return $tabPost = [
            'current' => (int) $current,
            'nbPage' => (int) $nbPage,
            'postFront' => $postFront,
        ];
    }
}