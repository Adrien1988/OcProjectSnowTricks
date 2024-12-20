<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'URL de l'image est obligatoire.")]
    #[Assert\Url(message: "L'URL de l'image doit être valide.")]
    private ?string $url = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le texte alternatif ne doit pas dépasser {{ limit }} caractères.'
    )]
    private ?string $altText = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(targetEntity: Figure::class, inversedBy: 'images', cascade: ['persist'])]
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


    public function getUrl(): ?string
    {
        return $this->url;

    }//end getUrl()


    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;

    }//end setUrl()


    public function getAltText(): ?string
    {
        return $this->altText;

    }//end getAltText()


    public function setAltText(?string $altText): static
    {
        $this->altText = $altText;

        return $this;

    }//end setAltText()


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
        return ($this->url ?? 'Image');

    }//end __toString()


}//end class
