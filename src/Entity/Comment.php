<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Classe représentant un commentaire dans l'application.
 */
#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{

    /**
     * Identifiant unique du commentaire.
     *
     * @var integer|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Contenu du commentaire.
     *
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le contenu ne peut pas être vide.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le contenu ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $content = '';

    /**
     * Date de création du commentaire.
     *
     * @var \DateTimeImmutable|null
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * Auteur du commentaire.
     *
     * @var User|null
     */
    #[ORM\ManyToOne(inversedBy: 'comments', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    /**
     * Figure associée au commentaire.
     *
     * @var Figure|null
     */
    #[ORM\ManyToOne(inversedBy: 'comments', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Figure $figure = null;


    /**
     * Constructeur de la classe Comment.
     * Initialise la date de création.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();

    }//end __construct()


    /**
     * Convertit le commentaire en chaîne de caractères.
     *
     * @return string Représentation textuelle du commentaire.
     */
    public function __toString(): string
    {
        return (string) $this->content;

    }//end __toString()


    /**
     * Récupère l'identifiant du commentaire.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;

    }//end getId()


    /**
     * Récupère le contenu du commentaire.
     *
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;

    }//end getContent()


    /**
     * Définit le contenu du commentaire.
     *
     * @param string $content Contenu du commentaire.
     *
     * @return $this
     */
    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;

    }//end setContent()


    /**
     * Récupère la date de création du commentaire.
     *
     * @return \DateTimeImmutable|null
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;

    }//end getCreatedAt()


    /**
     * Récupère l'auteur du commentaire.
     *
     * @return User|null
     */
    public function getAuthor(): ?User
    {
        return $this->author;

    }//end getAuthor()


    /**
     * Définit l'auteur du commentaire.
     *
     * @param User|null $author Auteur du commentaire.
     *
     * @return $this
     */
    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;

    }//end setAuthor()


    /**
     * Récupère la figure associée au commentaire.
     *
     * @return Figure|null
     */
    public function getFigure(): ?Figure
    {
        return $this->figure;

    }//end getFigure()


    /**
     * Définit la figure associée au commentaire.
     *
     * @param Figure|null $figure Figure associée.
     *
     * @return $this
     */
    public function setFigure(?Figure $figure): static
    {
        $this->figure = $figure;

        return $this;

    }//end setFigure()


}//end class
