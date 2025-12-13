<?php

namespace App\Entity;

use App\Repository\BorrowingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BorrowingRepository::class)]
class Borrowing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $borrowingAt = null;

    #[ORM\ManyToOne(inversedBy: 'borrowings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'borrowings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Book $book = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $toRenderAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBorrowingAt(): ?\DateTimeImmutable
    {
        return $this->borrowingAt;
    }

    public function setBorrowingAt(\DateTimeImmutable $borrowingAt): static
    {
        $this->borrowingAt = $borrowingAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): static
    {
        $this->book = $book;

        return $this;
    }

    public function getToRenderAt(): ?\DateTimeImmutable
    {
        return $this->toRenderAt;
    }

    public function setToRenderAt(\DateTimeImmutable $toRenderAt): static
    {
        $this->toRenderAt = $toRenderAt;

        return $this;
    }
}
