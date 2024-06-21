<?php
/**
 * Rental entity.
 */

namespace App\Entity;

use App\Repository\RentalRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Rental.
 *
 * @psalm-suppress MissingConstructor
 */
#[ORM\Entity(repositoryClass: RentalRepository::class)]
#[ORM\Table(name: 'rentals')]
class Rental
{
    /**
     * Primary key.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Gedmo\Timestampable(on: 'create')]
    #[Assert\Type(\DateTimeImmutable::class)]
    private ?\DateTimeImmutable $rentalDate = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?User $owner = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?Book $book = null;

    #[ORM\Column]
    #[Assert\NotNull]
    private ?bool $status = null;

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
     * Getter for rental date.
     *
     * @return \DateTimeImmutable|null Rental date
     */
    public function getRentalDate(): ?\DateTimeImmutable
    {
        return $this->rentalDate;
    }

    /**
     * Setter for rental date.
     *
     * @param \DateTimeImmutable|null $rentalDate Rental date
     */
    public function setRentalDate(\DateTimeImmutable $rentalDate): void
    {
        $this->rentalDate = $rentalDate;
    }

    /**
     * Getter for owner.
     *
     * @return User|null Owner
     */
    public function getOwner(): ?User
    {
        return $this->owner;
    }

    /**
     * Setter for owner.
     *
     * @param User|null $owner Owner
     */
    public function setOwner(?User $owner): void
    {
        $this->owner = $owner;
    }

    /**
     * Getter for book.
     *
     * @return Book|null Book
     */
    public function getBook(): ?Book
    {
        return $this->book;
    }

    /**
     * Setter for book.
     *
     * @param Book|null $book Book
     */
    public function setBook(Book $book): void
    {
        $this->book = $book;
    }

    /**
     * Check status.
     *
     * @return bool|null Bool
     */
    public function isStatus(): ?bool
    {
        return $this->status;
    }

    /**
     * Setter for status.
     *
     * @param bool $status Status
     */
    public function setStatus(bool $status): void
    {
        $this->status = $status;
    }
}
