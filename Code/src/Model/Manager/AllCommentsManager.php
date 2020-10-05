<?php

declare(strict_types=1);

namespace App\Model\Manager;

use App\Model\Repository\CommentRepository;

final class AllCommentsManager
{
    // private Session $session;
    private CommentRepository $CommentRepository;

    public function __construct(array $datas)
    {
        $this->CommentRepository = $datas['repository']['repoAdd'];
    }

    public function getAllComments(): ?array
    {
        return $this->CommentRepository->getAllCommentBdd();
    }

    public function validedComment(int $idComment, int $signal = null): ?string
    {
        return $this->CommentRepository->validedCommentBdd($idComment, $signal);
    }

    public function deletedComment(int $idComment): ?string
    {
        return $this->CommentRepository->deletedCommentBdd($idComment);
    }

}