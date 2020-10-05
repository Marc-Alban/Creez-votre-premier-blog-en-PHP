<?php
declare(strict_types=1);
namespace App\Model\Repository;
use App\Model\Entity\Comment;
use App\Model\Repository\Interfaces\CommentRepositoryInterface;
final class CommentRepository implements CommentRepositoryInterface
{

    public function getComment(int $postId): ?array
    {
        return null;
    }
    public function createComment(string $comment, string $UserComment, int $idUser, int $idPost): void
    {
        
    }
    public function deleteComment(Comment $comment): bool
    {
        return false;
    }

}
