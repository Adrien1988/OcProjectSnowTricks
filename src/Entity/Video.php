<?php

namespace App\Entity;

use App\Repository\VideoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VideoRepository::class)]
class Video
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le code d'intégration de la vidéo est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le code d'intégration ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $embedCode = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(targetEntity: Figure::class, inversedBy: 'videos', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Figure $figure = null;


    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();

    }//end __construct()


    public function getId(): ?int
    {
        return $this->id;

    }//end getId()


    public function getEmbedCode(): ?string
    {
        return $this->embedCode;

    }//end getEmbedCode()


    public function setEmbedCode(string $embedCode): static
    {
        $this->embedCode = $embedCode;

        return $this;

    }//end setEmbedCode()


    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;

    }//end getCreatedAt()


    public function getFigure(): ?Figure
    {
        return $this->figure;

    }//end getFigure()


    public function setFigure(?Figure $figure): static
    {
        $this->figure = $figure;

        return $this;

    }//end setFigure()


    public function __toString(): string
    {
        return ($this->embedCode ?? 'Vidéo');

    }//end __toString()


}//end class
