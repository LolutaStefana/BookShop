<?php

namespace App\Entity;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\GetCredentialsController;
use App\Controller\RegisterUserController;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ApiResource(

    operations: [
        new Get(),
        new Get(uriTemplate: '/credentials',
            controller: GetCredentialsController::class,
            normalizationContext: ['groups' => ['user']],
            read:false,
        ),
        new Post(),
        new Put(),
        new Patch(),
        new Delete(),
        new Post(
            uriTemplate: '/register',
            controller: RegisterUserController::class,
            denormalizationContext: ['groups' => ['registration']],
        ),
    ]
)]
#[ORM\Table(name:"users")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;
    #[ORM\Column]
    #[Assert\NotBlank] // required
    #[Assert\Length(min: 2, max: 20)] // between 2 and 20 characters
    #[Groups('user')]
    public string $firstName;
    #[ORM\Column(nullable: true)]
    #[Assert\Length(min: 2, max: 20)]
    public ?string $lastName = null;

    #[ORM\Column]
    #[Assert\NotBlank] //required
    #[Assert\Email] //making sure it is a valid email (something@something.com)
    public string $email;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[Groups('user')]
    public ?Organisation $organisation = null;

    #[ORM\ManyToMany(targetEntity:Book::class, inversedBy:"likingUsers", cascade: ['persist', 'remove'])]
    #[ORM\JoinTable(name:"user_liked_books")]
    public iterable $likedBooks;

    #[ORM\Column(nullable: true)]
    public $password=null;

    #[Assert\NotBlank(groups: ['user:create'])]
    #[Groups(['user:create', 'user:update'])]
    private ?string $plainPassword = null;

    #[ORM\Column(type: 'json',nullable: true)]
    private array $roles = [];

    public function getId(): ?int
    {
        return $this->id;
    }
    public function __construct()
    {
        $this->likedBooks = new ArrayCollection();
    }


    public function likeBook(Book $book): void
    {
        if (!$this->likedBooks->contains($book)) {
            $this->likedBooks[] = $book;
        }
    }

    public function unlikeBook(Book $book): void
    {
        $this->likedBooks->removeElement($book);
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }
    public function getUser(): User
    {
        return  $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }


}
