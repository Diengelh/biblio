<?php

namespace App\Entity;

use App\Repository\AuthorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

//à importer si on veut tester qu'un attribut est unique dans la base de donnée
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
//à importer pour pouvoir utiliser les contraintes
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AuthorRepository::class)]

//  le nom d'un auteur doit être unique dans la base de donnée
#[UniqueEntity(["name"], message: "Cet auteur est déja enregistré dans la base")]
class Author
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Length(min: 10)]
    #[Assert\NotBlank]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Assert\NotBlank]
    #[ORM\Column]
    private ?\DateTimeImmutable $dateOfBirth = null;

    // comment comparer la valeur d'une colonne avec une autre de la même entité.
    #[Assert\GreaterThan(
        propertyPath: "dateOfBirth",
        message: "la date de naissance doit être inférieure à la date de décès"
        )
    ]
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $DateOfDeath = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nationality = null;

    /**
     * @var Collection<int, Book>
     */
    #[ORM\ManyToMany(targetEntity: Book::class, mappedBy: 'authors')]
    private Collection $books;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeImmutable
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(\DateTimeImmutable $dateOfBirth): static
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    public function getDateOfDeath(): ?\DateTimeImmutable
    {
        return $this->DateOfDeath;
    }

    public function setDateOfDeath(?\DateTimeImmutable $DateOfDeath): static
    {
        $this->DateOfDeath = $DateOfDeath;

        return $this;
    }

    public function getNationality(): ?string
    {
        return $this->nationality;
    }

    public function setNationality(?string $nationality): static
    {
        $this->nationality = $nationality;

        return $this;
    }

    /**
     * @return Collection<int, Book>
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): static
    {
        if (!$this->books->contains($book)) {
            $this->books->add($book);
            $book->addAuthor($this);
        }

        return $this;
    }

    public function removeBook(Book $book): static
    {
        if ($this->books->removeElement($book)) {
            $book->removeAuthor($this);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name; // Assurez-vous que 'name' est une propriété de votre entité Author
    }
}
