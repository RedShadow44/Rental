<?php
/**
 * Avatar entity.
 */

namespace App\Entity;

use App\Repository\AvatarRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Avatar.
 *
 * @psalm-suppress MissingConstructor
 */
#[ORM\Entity(repositoryClass: AvatarRepository::class)]
#[ORM\Table(name: 'avatars')]
#[ORM\UniqueConstraint(name: 'uq_avatars_filename', columns: ['filename'])]
#[UniqueEntity(fields: ['filename'])]
class Avatar
{
    /**
     * Primary key.
     *
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * User.
     *
     * @var User|null
     */
    #[ORM\OneToOne(inversedBy: 'avatar', targetEntity: User::class, cascade: ['persist', 'remove'], fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Type(User::class)]
    private ?User $user;

    /**
     * Filename.
     *
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 191)]
    #[Assert\Type('string')]
    private ?string $filename;

    /**
     * Getter for Id.
     *
     * @return int|null Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for user.
     *
     * @return User|null User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Setter for user.
     *
     * @param User|null $user User
     */
    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    /**
     * Getter for filename.
     *
     * @return string|null Filename
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * Setter for filename.
     *
     * @param string|null $filename Filename
     */
    public function setFilename(?string $filename): void
    {
        $this->filename = $filename;
    }
}
