<?php

declare(strict_types=1);

namespace App\Model\Entity;

final class Post
{
    private int $idComment;
    private string $content;
    private string $dateCreation;
    private int $disabled;
    private int $UserId;
    private int $PostId;


    /**
     * Get the value of idComment
     */
    public function getIdComment(): int
    {
        return $this->idComment;
    }

    /**
     * Set the value of idComment
     *
     * @return  self
     */
    public function setIdComment($idComment): self
    {
        $this->idComment = $idComment;

        return $this;
    }

    /**
     * Get the value of content
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Set the value of content
     *
     * @return  self
     */
    public function setContent($content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the value of dateCreation
     */
    public function getDateCreation(): string
    {
        return $this->dateCreation;
    }

    /**
     * Set the value of dateCreation
     *
     * @return  self
     */
    public function setDateCreation($dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get the value of disabled
     */
    public function getDisabled(): int
    {
        return $this->disabled;
    }

    /**
     * Set the value of disabled
     *
     * @return  self
     */
    public function setDisabled($disabled): self
    {
        $this->disabled = $disabled;

        return $this;
    }

    /**
     * Get the value of UserId
     */
    public function getUserId(): int
    {
        return $this->UserId;
    }

    /**
     * Set the value of UserId
     *
     * @return  self
     */
    public function setUserId($UserId): self
    {
        $this->UserId = $UserId;

        return $this;
    }

    /**
     * Get the value of PostId
     */
    public function getPostId(): int
    {
        return $this->PostId;
    }

    /**
     * Set the value of PostId
     *
     * @return  self
     */
    public function setPostId($PostId): self
    {
        $this->PostId = $PostId;

        return $this;
    }
}
