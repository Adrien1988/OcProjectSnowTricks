<?php

namespace App\Entity;

use App\Repository\FigureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Classe représentant une figure.
 */
#[ORM\Entity(repositoryClass: FigureRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(
    fields: ['name'],
    message: 'Une figure avec ce nom existe déjà. Veuillez en choisir un autre.'
)]
class Figure
{
    /**
     * Identifiant unique de la figure.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Nom de la figure.
     */
    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'Le nom de la figure est obligatoire.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le nom ne doit pas dépasser {{ limit }} caractères.'
    )]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $author;

    /**
     * Description de la figure.
     */
    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'La description est obligatoire.')]
    private ?string $description = null;

    /**
     * Slug de la figure.
     */
    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    /**
     * Groupe de la figure.
     */
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le groupe de figure est obligatoire.')]
    private ?string $figureGroup = null;

    /**
     * Date de création de la figure.
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * Date de dernière mise à jour de la figure.
     */
    #[ORM\Column]
    private ?\DateTime $updatedAt = null;

    /**
     * Images associées à la figure.
     *
     * @var Collection<int, Image>
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'figure', orphanRemoval: true, cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(['id' => 'ASC'])]
    private Collection $images;

    #[ORM\OneToOne(targetEntity: Image::class, cascade: ['persist'], orphanRemoval: false)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Image $mainImage = null;

    /**
     * Vidéos associées à la figure.
     *
     * @var Collection<int, Video>
     */
    #[ORM\OneToMany(targetEntity: Video::class, mappedBy: 'figure', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $videos;

    /**
     * Collection des commentaires associés à la figure.
     *
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'figure', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $comments;


    /**
     * Constructeur de la classe Figure.
     * Initialise les dates et les collections.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTime();
        $this->images = new ArrayCollection();
        $this->videos = new ArrayCollection();
        $this->comments = new ArrayCollection();
    } // end __construct()


    /**
     * Met à jour la date de mise à jour avant chaque modification.
     *
     * @ORM\PreUpdate
     *
     * @return void
     */
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime();
    } // end setUpdatedAtValue()


    /**
     * Récupère l'identifiant de la figure.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    } // end getId()


    /**
     * Récupère le nom de la figure.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    } // end getName()


    /**
     * Définit le nom de la figure.
     *
     * @param string $name nom de la figure
     *
     * @return $this
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    } // end setName()


    /**
     * Récupère l'auteur de la figure.
     *
     * @return user L'utilisateur ayant créé la figure
     */
    public function getAuthor(): User
    {
        return $this->author;
    }


    /**
     * Définit l'auteur de la figure.
     *
     * @param user $author L'utilisateur qui a créé la figure
     *
     * @return self retourne l'instance mise à jour
     */
    public function setAuthor(User $author): self
    {
        $this->author = $author;

        return $this;
    }


    /**
     * Récupère la description de la figure.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    } // end getDescription()


    /**
     * Définit la description de la figure.
     *
     * @param string $description description de la figure
     *
     * @return $this
     */
    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    } // end setDescription()


    /**
     * Récupère le slug de la figure.
     *
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    } // end getSlug()


    /**
     * Définit le slug de la figure.
     *
     * @param string $slug slug de la figure
     *
     * @return $this
     */
    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    } // end setSlug()


    /**
     * Génère un slug à partir du nom de la figure.
     *
     * @param SluggerInterface $slugger service pour créer un slug SEO-friendly
     *
     * @return void
     */
    public function generateSlug(SluggerInterface $slugger): void
    {
        if ($this->name) {
            $this->slug = $slugger->slug($this->name)->lower();
        }
    }


    /**
     * Récupère le groupe de la figure.
     *
     * @return string|null
     */
    public function getFigureGroup(): ?string
    {
        return $this->figureGroup;
    } // end getFigureGroup()


    /**
     * Définit le groupe de la figure.
     *
     * @param string $figureGroup groupe de la figure
     *
     * @return $this
     */
    public function setFigureGroup(string $figureGroup): static
    {
        $this->figureGroup = $figureGroup;

        return $this;
    } // end setFigureGroup()


    /**
     * Récupère la date de création de la figure.
     *
     * @return \DateTimeImmutable|null
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    } // end getCreatedAt()


    /**
     * Récupère la date de mise à jour de la figure.
     *
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    } // end getUpdatedAt()


    /**
     * Récupère les images associées à la figure.
     *
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    } // end getImages()


    /**
     * Ajoute une image à la figure.
     *
     * @param Image $image image à ajouter
     *
     * @return $this
     */
    public function addImage(Image $image): static
    {
        if (false === $this->images->contains($image)) {
            $this->images[] = $image;
            $image->setFigure($this);
        }

        return $this;
    } // end addImage()


    /**
     * Retourne l'image principale de la figure.
     *
     * @return Image|null L'entité Image de la figure, ou null si aucune image n'est définie
     */
    public function getMainImage(): ?Image
    {
        return $this->mainImage;
    }


    /**
     * Définit l'image principale de la figure.
     *
     * @param Image|null $mainImage L'entité Image à associer comme image principale
     *
     * @return self
     */
    public function setMainImage(?Image $mainImage): self
    {
        $this->mainImage = $mainImage;

        return $this;
    }


    /**
     * Récupère les vidéos associées à la figure.
     *
     * @return Collection<int, Video>
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    } // end getVideos()


    /**
     * Ajoute une vidéo à la figure.
     *
     * @param Video $video vidéo à ajouter
     *
     * @return $this
     */
    public function addVideo(Video $video): static
    {
        if (false === $this->videos->contains($video)) {
            $this->videos[] = $video;
            $video->setFigure($this);
        }

        return $this;
    } // end addVideo()


    /**
     * Convertit la figure en chaîne de caractères (nom de la figure).
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->name;
    }// end __toString()


    /**
     * Récupère les commentaires associés à la figure.
     *
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }


    /**
     * Ajoute un commentaire à la figure.
     *
     * @param Comment $comment commentaire à ajouter
     *
     * @return $this
     */
    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setFigure($this);
        }

        return $this;
    }


}// end class
