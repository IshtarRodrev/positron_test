<?php

namespace App\Entity;

use App\Repository\SettingsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SettingsRepository::class)]
class Settings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $count_book_element = null;

    #[ORM\Column(length: 255)]
    private ?string $support_email = null;

    #[ORM\Column(length: 255)]
    private ?string $parse_url = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountBookElement(): ?int
    {
        return $this->count_book_element;
    }

    public function setCountBookElement(int $count_book_element): static
    {
        $this->count_book_element = $count_book_element;

        return $this;
    }

    public function getSupportEmail(): ?string
    {
        return $this->support_email;
    }

    public function setSupportEmail(string $support_email): static
    {
        $this->support_email = $support_email;

        return $this;
    }

    public function getParseUrl(): ?string
    {
        return $this->parse_url;
    }

    public function setParseUrl(string $parse_url): static
    {
        $this->parse_url = $parse_url;

        return $this;
    }
}
