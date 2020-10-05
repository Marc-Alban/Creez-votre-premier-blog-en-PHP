<?php
declare(strict_types=1);
namespace App\Model\Repository\Interfaces;
use App\Model\Entity\Comment;


interface CommentRepositoryInterface 
{
    public function getComment(int $postId): ?array;
    public function signalCommentBdd(int $idComment): void;
    public function validedCommentBdd(int $idComment, int $signal = null): ?string;
    public function deletedCommentBdd(int $idComment): ?string;
    public function createComment(string $comment, string $UserComment, int $idUser, int $idPost): void;
    public function getAllCommentBdd(): ?array;
}
