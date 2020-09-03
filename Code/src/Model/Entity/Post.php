<?php

declare(strict_types=1);

namespace App\Model\Entity;

final class Post
{
    private int $idPost;
    private string $title;
    private string $description;
    private string $label;
    private string $imagePost;
    private string $categorie;
    private string $dateCreation;
    private string $dateUpdate;
    private string $statuPost;
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
     * Get the value of label
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * Set the value of label
     *
     * @return  self
     */
    public function setLabel($label): self
    {
        $this->label = $label;

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
     * Get the value of categorie
     */
    public function getCategorie(): string
    {
        return $this->categorie;
    }

    /**
     * Set the value of categorie
     *
     * @return  self
     */
    public function setCategorie($categorie): self
    {
        $this->categorie = $categorie;

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
     * Get the value of dateUpdate
     */
    public function getDateUpdate(): ?string
    {
        return $this->dateUpdate;
    }

    /**
     * Set the value of dateUpdate
     *
     * @return  self
     */
    public function setDateUpdate($dateUpdate): self
    {
        $this->dateUpdate = $dateUpdate;

        return $this;
    }

    /**
     * Get the value of statuPost
     */
    public function getStatuPost(): string
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
