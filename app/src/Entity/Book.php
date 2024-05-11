<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Book.
 *
 * @psalm-suppress MissingConstructor
 */
#[ORM\Table(name: 'books')]
#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    /**
     *Primary key.
     *
     *@var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     *Title.
     *
     *@var string|null
     */
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    /**
     *Author.
     *
     *@var string|null
     */
    #[ORM\Column(length: 255)]
    private ?string $author = null;

    /**
     *Description.
     *
     *@var string|null
     */
    #[ORM\Column(length: 255)]
    private ?string $description = null;

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
     * Getter for title.
     *
     * @return string|null Title
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Setter for title.
     *
     * @param string|null $title Title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * Getter for author.
     *
     * @return string|null Author
     */
    public function getAuthor(): ?string
    {
        return $this->author;
    }

    /**
     * Setter for author.
     *
     * @param string|null $author Author
     */
    public function setAuthor(?string $author): void
    {
        $this->author = $author;
    }

    /**
     * Getter for description.
     *
     * @return string|null Description
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Setter for description.
     *
     * @param string|null $description Description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }
}
