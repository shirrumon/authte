<?php

namespace App\Entity;

use App\Repository\LanguagesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LanguagesRepository::class)
 */
class Languages
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $languages;

    public function getLanguages(): ?string
    {
        return $this->languages;
    }

    public function setLanguages(string $languages): self
    {
        $this->languages = $languages;

        return $this;
    }
}
