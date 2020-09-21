<?php
declare(strict_types=1);
namespace App\Model\Repository\Interfaces;
use App\Model\Entity\Comment;


interface CommentRepositoryInterface 
{
    public function getComment(Comment $comment): ?Comment;
    public function createComment(string $idUser, string $comment, int $idPost): void;
    public function deleteComment(Comment $comment): bool;
}
