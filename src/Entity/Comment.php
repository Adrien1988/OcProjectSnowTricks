<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le contenu ne peut pas être vide.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le contenu ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $content = '';

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'comments', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\ManyToOne(inversedBy: 'comments', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Figure $figure = null;


    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();

    }//end __construct()


    public function __toString(): string
    {
        return (string) $this->content;

    }//end __toString()


    public function getId(): ?int
    {
        return $this->id;

    }//end getId()


    public function getContent(): ?string
    {
        return $this->content;

    }//end getContent()


    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;

    }//end setContent()


    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;

    }//end getCreatedAt()


    public function getAuthor(): ?User
    {
        return $this->author;

    }//end getAuthor()


    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;

    }//end setAuthor()


    public function getFigure(): ?Figure
    {
        return $this->figure;

    }//end getFigure()


    public function setFigure(?Figure $figure): static
    {
        $this->figure = $figure;

        return $this;

    }//end setFigure()


}//end class
