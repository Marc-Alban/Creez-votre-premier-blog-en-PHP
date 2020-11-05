<?php
declare(strict_types=1);
namespace App\Model\Entity;

final class Post
{
    private int $idPost;
    private string $title;
    private string $description;
    private string $chapo;
    private string $imagePost;
    private string $datePost;
    private int $statuPost;
    private int $UserId;
    /**
     * Get the value of idPost
     */
    public function getIdPost(): int
    {
        return $this->idPost;
    }
    /**
     * Set the value of idPost
     *
     * @return  self
     */
    public function setIdPost($idPost): self
    {
        $this->idPost = $idPost;
        return $this;
    }
    /**
     * Get the value of title
     */
    public function getTitle(): string
    {
        return $this->title;
    }
    /**
     * Set the value of title
     *
     * @return  self
     */
    public function setTitle($title): self
    {
        $this->title = $title;
        return $this;
    }
    /**
     * Get the value of description
     */
    public function getDescription(): string
    {
        return $this->description;
    }
    /**
     * Set the value of description
     *
     * @return  self
     */
    public function setDescription($description): self
    {
        $this->description = $description;
        return $this;
    }
    /**
    * Get the value of chapo
    */
    public function getChapo(): string
    {
        return $this->chapo;
    }
    /**
     * Set the value of chapo
     *
     * @return  self
     */
    public function setChapo($chapo): self
    {
        $this->chapo = $chapo;
        return $this;
    }
    /**
     * Get the value of imagePost
     */
    public function getImagePost(): string
    {
        return $this->imagePost;
    }
    /**
     * Set the value of imagePost
     *
     * @return  self
     */
    public function setImagePost($imagePost): self
    {
        $this->imagePost = $imagePost;
        return $this;
    }
    /**
     * Get the value of datePost
     */
    public function getDatePost(): string
    {
        return $this->datePost;
    }
    /**
     * Set the value of datePost
     *
     * @return  self
     */
    public function setDatePost($datePost): self
    {
        $this->datePost = $datePost;
        return $this;
    }
    /**
     * Get the value of statuPost
     */
    public function getStatuPost(): int
    {
        return $this->statuPost;
    }
    /**
     * Set the value of statuPost
     *
     * @return  self
     */
    public function setStatuPost($statuPost): self
    {
        $this->statuPost = $statuPost;
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
}
