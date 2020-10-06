<?php

declare(strict_types=1);

namespace App\Model\Manager;

use App\Model\Repository\CommentRepository;
use App\Service\Http\Session;

final class AllCommentsManager
{
    private CommentRepository $CommentRepository;

    public function __construct(array $datas)
    {
        $this->CommentRepository = $datas['repository']['repoAdd'];
    }

    public function getAllComments(): ?array
    {
        return $this->CommentRepository->getAllCommentBdd();
    }

    public function validedComment(int $idComment, int $signal = null, array $datas): ?array
    {
        $validation =  $datas['session']['valide'] ?? null;
        unset($validation);
        $validComment = $this->CommentRepository->validedCommentBdd($idComment, $signal);
        if($validComment === true){
            $validation['sendValide'] = "Commentaire validé";
            return $validation;
        }
        return null;
    }

    public function deletedComment(int $idComment, array $datas): ?array
    {
        $suppression =  $datas['session']['deleted'] ?? null;
        unset($suppression);
        $delComment = $this->CommentRepository->deletedCommentBdd($idComment);
        if ($delComment === true) {
            $suppression['sendDelete'] = "Commentaire supprimé";
            return $suppression;
        }
        return null;
    }

}