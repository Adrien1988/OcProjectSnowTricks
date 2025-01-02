<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Classe représentant une image associée à une figure.
 */
#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    /**
     * Identifiant unique de l'image.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * URL de l'image.
     */
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'URL de l'image est obligatoire.")]
    #[Assert\Url(message: "L'URL de l'image doit être valide.")]
    private ?string $url = null;

    /**
     * Texte alternatif de l'image.
     */
    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le texte alternatif ne doit pas dépasser {{ limit }} caractères.'
    )]
    private ?string $altText = null;

    /**
     * Date de création de l'image.
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * Figure associée à l'image.
     */
    #[ORM\ManyToOne(targetEntity: Figure::class, inversedBy: 'images', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Figure $figure = null;

    /**
     * Constructeur de la classe Image.
     * Initialise la date de création.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }// end __construct()

    /**
     * Récupère l'identifiant unique de l'image.
     */
    public function getId(): ?int
    {
        return $this->id;
    }// end getId()

    /**
     * Récupère l'URL de l'image.
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }// end getUrl()

    /**
     * Définit l'URL de l'image.
     *
     * @param string $url URL de l'image
     *
     * @return $this
     */
    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }// end setUrl()

    /**
     * Récupère le texte alternatif de l'image.
     */
    public function getAltText(): ?string
    {
        return $this->altText;
    }// end getAltText()

    /**
     * Définit le texte alternatif de l'image.
     *
     * @param string|null $altText texte alternatif
     *
     * @return $this
     */
    public function setAltText(?string $altText): static
    {
        $this->altText = $altText;

        return $this;
    }// end setAltText()

    /**
     * Récupère la date de création de l'image.
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }// end getCreatedAt()

    /**
     * Récupère la figure associée à l'image.
     */
    public function getFigure(): ?Figure
    {
        return $this->figure;
    }// end getFigure()

    /**
     * Définit la figure associée à l'image.
     *
     * @param Figure|null $figure figure associée
     *
     * @return $this
     */
    public function setFigure(?Figure $figure): static
    {
        $this->figure = $figure;

        return $this;
    }// end setFigure()

    /**
     * Convertit l'objet Image en chaîne de caractères.
     * Retourne l'URL de l'image ou 'Image' si l'URL est null.
     */
    public function __toString(): string
    {
        return $this->url ?? 'Image';
    }// end __toString()


}// end class
