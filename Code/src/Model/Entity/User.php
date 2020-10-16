<?php

declare(strict_types=1);

namespace App\Model\Entity;

final class User
{
    private int $idUser;
    private string $userName;
    private string $email;
    private string $passwordUser;
    private int $activated;
    private int $validationKey;
    private string $userType;
    private string $dateCreation;


    /**
     * Get the value of idUser
     */
    public function getIdUser(): int
    {
        return $this->idUser;
    }

    /**
     * Set the value of idUser
     *
     * @return  self
     */
    public function setIdUser($idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }

    /**
     * Get the value of userName
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * Set the value of userName
     *
     * @return  self
     */
    public function setUserName($userName): self
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * Get the value of email
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */
    public function setEmail($email): self
    {
        $this->email = $email;

        return $this;
    }


    /**
     * Get the value of passwordUser
     */
    public function getPasswordUser(): string
    {
        return $this->passwordUser;
    }

    /**
     * Set the value of passwordUser
     *
     * @return  self
     */
    public function setPasswordUser($passwordUser): self
    {
        $this->passwordUser = $passwordUser;

        return $this;
    }

    /**
     * Get the value of activated
     */
    public function getActivated():int
    {
        return $this->activated;
    }

    /**
     * Set the value of activated
     *
     * @return  self
     */
    public function setActivated($activated): self
    {
        $this->activated = $activated;

        return $this;
    }

    /**
     * Get the value of validationKey
     */
    public function getValidationKey(): int
    {
        return $this->validationKey;
    }

    /**
     * Set the value of validationKey
     *
     * @return  self
     */
    public function setValidationKey($validationKey): self
    {
        $this->validationKey = $validationKey;

        return $this;
    }

    /**
     * Get the value of userType
     */
    public function getUserType(): string
    {
        return $this->userType;
    }

    /**
     * Set the value of userType
     *
     * @return  self
     */
    public function setUserType($userType): self
    {
        $this->userType = $userType;

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
}
