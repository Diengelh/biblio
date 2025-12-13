<?php

namespace App\Entity;

use App\EventSubscriber\EasyAdminSubscriber;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(["email"])]
#[ORM\Table(name: '`user`')] // spécifier le nom de la table de cette entité
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    //le mot de passe en clair
    private ?string $plainPassword = null;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Email(message: "Veillez entrer une adresse email valide")]
    #[ORM\Column(length: 180)] //ajouter "unique: true" est équivalent à l'utlisation de UniqueEntity pour cette colonne
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     * La contrainte Regex valide que le mot de passe :
     * * contient au moins un chiffre
     * * contient au moins une lettre en minuscule
     * * contient au moins une lettre en majuscule
     * * contient au moins un caractère spécial qui n'est pas un espace
     * * fait entre 8 et 32 caractères de long
     */
    
    // Ne jamais mettre ce champ à notNull ou NotBlank
    // #[Assert\Regex('/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*\W)(?!.*\s).{8,32}$/')]
    #[Assert\NotCompromisedPassword] // S'assurer que l'utilisateur ne choisit pas un mot de passe qui a déjà été exposé dans des failles de sécurité publiques.
    #[Assert\PasswordStrength(minScore: Assert\PasswordStrength::STRENGTH_MEDIUM)] //le mot de passe doit être au minimum fort
    #[ORM\Column]
    private ?string $password = null;

    #[Assert\NotBlank()]
    #[ORM\Column(length: 255)] 
    private ?string $firstname = null;

    #[Assert\NotBlank()]
    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $username = null;

    /**
     * @var Collection<int, Book>
     */
    #[ORM\OneToMany(targetEntity: Book::class, mappedBy: 'createdBy')]
    private Collection $books;

    /**
     * @var Collection<int, Mark>
     */
    #[ORM\OneToMany(targetEntity: Mark::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $marks;

    /**
     * @var Collection<int, Borrowing>
     */
    #[ORM\OneToMany(targetEntity: Borrowing::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $borrowings;

    /**
     * @var Collection<int, Shop>
     */
    #[ORM\OneToMany(targetEntity: Shop::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $shops;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $ConnectedAt = null;

    public function __construct()
    {
        $this->books = new ArrayCollection();
        $this->marks = new ArrayCollection();
        $this->borrowings = new ArrayCollection();
        $this->shops = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getPlainPassWord(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassWord(?string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;

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
            $book->setCreatedBy($this);
        }

        return $this;
    }

    public function removeBook(Book $book): static
    {
        if ($this->books->removeElement($book)) {
            // set the owning side to null (unless already changed)
            if ($book->getCreatedBy() === $this) {
                $book->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Mark>
     */
    public function getMarks(): Collection
    {
        return $this->marks;
    }

    public function addMark(Mark $mark): static
    {
        if (!$this->marks->contains($mark)) {
            $this->marks->add($mark);
            $mark->setUser($this);
        }

        return $this;
    }

    public function removeMark(Mark $mark): static
    {
        if ($this->marks->removeElement($mark)) {
            // set the owning side to null (unless already changed)
            if ($mark->getUser() === $this) {
                $mark->setUser(null);
            }
        }

        return $this;
    }
    public function isAdmin(){
        return in_array("ROLE_ADMIN", $this->roles);
    }

    public function __toString()
    {
        return $this->firstname." ".$this->lastname;
    }

    /**
     * @return Collection<int, Borrowing>
     */
    public function getBorrowings(): Collection
    {
        return $this->borrowings;
    }

    public function addBorrowing(Borrowing $borrowing): static
    {
        if (!$this->borrowings->contains($borrowing)) {
            $this->borrowings->add($borrowing);
            $borrowing->setUser($this);
        }

        return $this;
    }

    public function removeBorrowing(Borrowing $borrowing): static
    {
        if ($this->borrowings->removeElement($borrowing)) {
            // set the owning side to null (unless already changed)
            if ($borrowing->getUser() === $this) {
                $borrowing->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Shop>
     */
    public function getShops(): Collection
    {
        return $this->shops;
    }

    public function addShop(Shop $shop): static
    {
        if (!$this->shops->contains($shop)) {
            $this->shops->add($shop);
            $shop->setUser($this);
        }

        return $this;
    }

    public function removeShop(Shop $shop): static
    {
        if ($this->shops->removeElement($shop)) {
            // set the owning side to null (unless already changed)
            if ($shop->getUser() === $this) {
                $shop->setUser(null);
            }
        }

        return $this;
    }

    public function getConnectedAt(): ?\DateTimeImmutable
    {
        return $this->ConnectedAt;
    }

    public function setConnectedAt(\DateTimeImmutable $ConnectedAt): static
    {
        $this->ConnectedAt = $ConnectedAt;

        return $this;
    }
}
