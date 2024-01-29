<?php
namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\CreateReviewController;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/** A review of a book. */
#[ORM\Entity]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(normalizationContext: ['groups' => ['book']]),
        new Post(uriTemplate: '/reviews',
            controller: CreateReviewController::class,),
        new Put(security:"object.owner == user",securityMessage: 'Sorry, but you are not the review owner.'),
        new Patch(security:"object.owner == user",securityMessage: 'Sorry, but you are not the organisation owner.'),
        new Delete(security:"object.owner == user",securityMessage: 'Sorry, but you are not the organisation owner.'),

    ]
)]
class Review
{
    /** The ID of this review. */
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    #[Groups('book')]
    private ?int $id = null;

    /** The rating of this review (between 0 and 5). */
    #[ORM\Column(type: 'smallint')]
    #[Assert\Range(min: 0, max: 5)]
    #[Groups('book')]
    public int $rating = 0;

    /** The body of the review. */
    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Groups('book')]
    public string $body = '';

    /** The author of the review. */
    #[ORM\Column]
    #[Assert\NotBlank]
    #[Groups('book')]
    public string $author = '';

    /** The date of publication of this review.*/
    #[ORM\Column]
    #[Assert\NotNull]
    #[Groups('book')]
    public ?\DateTimeImmutable $publicationDate = null;

    /** The book this review is about. */
    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[Assert\NotNull]
    #[Groups('book')]
    public ?Book $book = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    #[ORM\ManyToOne]
    #[Groups('book')]
    public User $owner;
}
