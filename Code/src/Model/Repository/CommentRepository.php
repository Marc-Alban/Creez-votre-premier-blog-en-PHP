<?php
declare(strict_types=1);
namespace App\Model\Repository;
use App\Model\Entity\Comment;
use App\Model\Repository\Interfaces\CommentRepositoryInterface;
final class CommentRepository implements CommentRepositoryInterface
{

    public function getComment(Comment $comment): ?Comment
    {
        return null;
    }
    public function createComment(string $name, string $comment, int $idPost): void
    {
        
    }
    public function deleteComment(Comment $comment): bool
    {
        return false;
    }

}
