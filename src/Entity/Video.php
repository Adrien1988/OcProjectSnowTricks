<?php

namespace App\Entity;

use App\Repository\VideoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Classe représentant une vidéo associée à une figure.
 */
#[ORM\Entity(repositoryClass: VideoRepository::class)]
class Video
{
    /**
     * Identifiant unique de la vidéo.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Code d'intégration de la vidéo (embed code).
     */
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le code d'intégration de la vidéo est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le code d'intégration ne doit pas dépasser {{ limit }} caractères."
    )]
    #[Assert\Regex(
        pattern: "/<iframe.*>.*<\/iframe>/",
        message: "Le code d'intégration doit être un iframe valide."
    )]
    private ?string $embedCode = null;

    /**
     * Date de création de la vidéo.
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * Figure associée à la vidéo.
     */
    #[ORM\ManyToOne(targetEntity: Figure::class, inversedBy: 'videos', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Figure $figure = null;


    /**
     * Initialise une nouvelle instance de la classe Video.
     * Initialise la date de création.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }// end __construct()


    /**
     * Récupère l'identifiant unique de la vidéo.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }// end getId()


    /**
     * Récupère le code d'intégration de la vidéo.
     *
     * @return string|null
     */
    public function getEmbedCode(): ?string
    {
        return $this->embedCode;
    }// end getEmbedCode()


    /**
     * Définit le code d'intégration de la vidéo.
     *
     * @param string $embedCode code d'intégration de la vidéo
     *
     * @return $this
     */
    public function setEmbedCode(string $embedCode): static
    {
        $this->embedCode = $embedCode;

        return $this;
    }// end setEmbedCode()


    /**
     * Récupère la date de création de la vidéo.
     *
     * @return \DateTimeImmutable|null
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }// end getCreatedAt()


    /**
     * Récupère la figure associée à la vidéo.
     *
     * @return Figure|null
     */
    public function getFigure(): ?Figure
    {
        return $this->figure;
    }// end getFigure()


    /**
     * Définit la figure associée à la vidéo.
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
     * Convertit l'objet Video en chaîne de caractères.
     * Retourne le code d'intégration ou 'Vidéo' si le code est null.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->embedCode ?? 'Vidéo';
    }// end __toString()


}// end class
