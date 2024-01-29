<?php
namespace App\Entity;

use ApiPlatform\Action\PlaceholderAction;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\CreateBookController;
use App\Controller\CreateOrganisationController;
use App\Controller\OrganisationReportController;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(uriTemplate: '/organisations',
            controller: CreateOrganisationController::class,),
        new Put(security:"object.owner == user",securityMessage: 'Sorry, but you are not the organisation owner.'),
        new Patch(security:"object.owner == user",securityMessage: 'Sorry, but you are not the organisation owner.'),
        new Delete(security:"object.owner == user",securityMessage: 'Sorry, but you are not the organisation owner.'),
        new Get(
            uriTemplate: '/organisation/{id}/report',
            controller: OrganisationReportController::class,
            normalizationContext: ['groups' => ['read']],
            read:false,
        )
    ]
)]
class Organisation
{
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 20)]
    public string $name;

    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'organisation', cascade: ['persist', 'remove'])]
    public iterable $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    #[ORM\ManyToOne]
    public User $owner;
}
?>
