<?php
namespace App\Entity;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\BookController;
use App\Controller\CreateBookController;
use App\Controller\LikeBookController;
use App\Controller\UnlikeBookController;
use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/** A book. */
#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ApiResource(
    operations: [

        new Get(),
        new GetCollection(
            uriTemplate: '/books',
            controller:BookController::class,
            normalizationContext: ['groups' => ['book']],
            ),
        new Post(uriTemplate: '/books',
            controller: CreateBookController::class,
        ),
        new Put(security:"object.owner == user",securityMessage: 'Sorry, but you are not the book owner.'),
        new Patch(security:"object.owner == user",securityMessage: 'Sorry, but you are not the book owner.'),
        new Delete(security:"object.owner == user",securityMessage: 'Sorry, but you are not the book owner.'),
        new Post(
            uriTemplate: '/books/{id}/like',
            controller: LikeBookController::class,
            normalizationContext: ['groups' => ['read']],
            denormalizationContext: ['groups' => ['write']],

        ),
        new Delete(
            uriTemplate:  '/books/{id}/like',
            controller: UnlikeBookController::class,
            normalizationContext: ['groups' => ['read']],
            denormalizationContext: ['groups' => ['write']],
        ),
    ],
)]
class Book
{
    /** The ID of this book. */
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    #[Groups('book')]
    private ?int $id = null;

    /** The ISBN of this book (or null if it doesn't have one). */
    #[ORM\Column(nullable: true)]
    #[Assert\Isbn]
    public ?string $isbn = null;

    /** The title of this book. */
    #[ORM\Column]
    #[Assert\NotBlank]
    #[Groups('book')]

    public string $title = '';

    /** The description of this book. */
    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Groups('book')]
    public string $description = '';

    /** The author of this book. */
    #[ORM\Column]
    #[Assert\NotBlank]
    #[Groups('book')]
    public string $author = '';

    /** The publication date of this book. */
    #[ORM\Column]
    #[Assert\NotNull]
    #[Groups('book')]
    public ?\DateTimeImmutable $publicationDate = null;

    /** @var Review[] Available reviews for this book. */
    #[Groups('book')]
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'book', cascade: ['persist', 'remove'])]
    public iterable $reviews;


    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: "likedBooks",cascade: ['persist', 'remove'])]
    #[Groups('book')]
    public  iterable $likingUsers;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
        $this->likingUsers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    #[ORM\ManyToOne]
    #[Groups('book')]
    public User $owner;


}
