<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProgrammerRepository")
 */
class Programmer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nickName;

    /**
     * @ORM\Column(type="integer", length=255)
     */
    private $avatarNumber;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tagLine;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNickName(): ?string
    {
        return $this->nickName;
    }

    public function setNickName(string $nickName): self
    {
        $this->nickName = $nickName;

        return $this;
    }

    public function getAvatarNumber(): ?int
    {
        return $this->avatarNumber;
    }

    public function setAvatarNumber(int $avatarNumber): self
    {
        $this->avatarNumber = $avatarNumber;

        return $this;
    }

    public function getTagLine(): ?string
    {
        return $this->tagLine;
    }

    public function setTagLine(string $tagLine): self
    {
        $this->tagLine = $tagLine;

        return $this;
    }
}
