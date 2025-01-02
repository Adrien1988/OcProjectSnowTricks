<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entité représentant les utilisateurs de l'application.
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(
    name: 'user',
    uniqueConstraints: [
        new ORM\UniqueConstraint(name: 'unique_username', columns: ['username']),
        new ORM\UniqueConstraint(name: 'unique_email', columns: ['email']),
    ]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * Identifiant unique de l'utilisateur.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Nom d'utilisateur.
     */
    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: "Le nom d'utilisateur est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le nom d'utilisateur ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $username = null;

    /**
     * Adresse email de l'utilisateur.
     */
    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: "L'email est obligatoire.")]
    #[Assert\Email(message: "L'email '{{ value }}' n'est pas valide.")]
    private ?string $email = null;

    /**
     * Mot de passe haché de l'utilisateur.
     */
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le mot de passe est obligatoire.')]
    private ?string $password = null;

    /**
     * URL de l'avatar de l'utilisateur.
     */
    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: "L'URL de l'avatar ne doit pas dépasser {{ limit }} caractères."
    )]
    #[Assert\Url(message: 'L’URL de l’avatar n’est pas valide.')]
    private ?string $avatarUrl = null;

    /**
     * Indique si le compte de l'utilisateur est actif.
     */
    #[ORM\Column(nullable: false, options: ['default' => false])]
    private bool $isActive = false;

    /**
     * Commentaires rédigés par l'utilisateur.
     *
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    /**
     * Token utilisé pour l'activation du compte.
     */
    #[ORM\Column(length: 64, nullable: true)]
    private ?string $activationToken = null;

    /**
     * Initialise une nouvelle instance de l'utilisateur.
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->isActive = false;
    }// end __construct()

    /**
     * Récupère l'identifiant de l'utilisateur.
     */
    public function getId(): ?int
    {
        return $this->id;
    }// end getId()

    /**
     * Récupère le nom d'utilisateur.
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }// end getUsername()

    /**
     * Définit le nom d'utilisateur.
     *
     * @param string $username le nom d'utilisateur à définir
     *
     * @return $this
     */
    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }// end setUsername()

    /**
     * Récupère l'adresse email.
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }// end getEmail()

    /**
     * Définit l'adresse email.
     *
     * @param string $email L'adresse email à définir
     *
     * @return $this
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }// end setEmail()

    /**
     * Récupère le mot de passe haché.
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }// end getPassword()

    /**
     * Définit le mot de passe haché.
     *
     * @param string $password le mot de passe haché à définir
     *
     * @return $this
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }// end setPassword()

    /**
     * Récupère l'URL de l'avatar.
     */
    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }// end getAvatarUrl()

    /**
     * Définit l'URL de l'avatar.
     *
     * @param string|null $avatarUrl L'URL de l'avatar de l'utilisateur. Peut être null si aucun avatar n'est défini.
     *
     * @return $this
     */
    public function setAvatarUrl(?string $avatarUrl): static
    {
        $this->avatarUrl = $avatarUrl;

        return $this;
    }// end setAvatarUrl()

    /**
     * Vérifie si le compte de l'utilisateur est actif.
     */
    public function isActive(): ?bool
    {
        return $this->isActive;
    }// end isActive()

    /**
     * Définit si le compte de l'utilisateur est actif.
     *
     * @param bool $isActive indique si le compte est actif (true) ou inactif (false)
     *
     * @return $this
     */
    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }// end setIsActive()

    /**
     * Récupère les commentaires rédigés par l'utilisateur.
     *
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }// end getComments()

    /**
     * Ajoute un commentaire rédigé par l'utilisateur.
     *
     * @param Comment $comment le commentaire à ajouter à l'utilisateur
     *
     * @return $this
     */
    public function addComment(Comment $comment): static
    {
        if (false === $this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAuthor($this);
        }

        return $this;
    }// end addComment()

    /**
     * Supprime un commentaire rédigé par l'utilisateur.
     *
     * @param Comment $comment le commentaire à supprimer de l'utilisateur
     *
     * @return $this
     */
    public function removeComment(Comment $comment): static
    {
        if (true === $this->comments->removeElement($comment)) {
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }// end removeComment()

    /**
     * Récupère les rôles attribués à l'utilisateur.
     *
     * @return string[]
     */
    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }// end getRoles()

    /**
     * Efface les données sensibles de l'utilisateur.
     */
    public function eraseCredentials(): void
    {
    }// end eraseCredentials()

    /**
     * Récupère l'identifiant pour l'authentification (email dans ce cas).
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }// end getUserIdentifier()

    /**
     * Récupère le token d'activation.
     */
    public function getActivationToken(): ?string
    {
        return $this->activationToken;
    }// end getActivationToken()

    /**
     * Définit le token d'activation.
     *
     * @param string|null $activationToken le token d'activation à associer à l'utilisateur
     *
     * @return $this
     */
    public function setActivationToken(?string $activationToken): self
    {
        $this->activationToken = $activationToken;

        return $this;
    }// end setActivationToken()

    /**
     * Convertit l'utilisateur en une chaîne de caractères (retourne le nom d'utilisateur).
     */
    public function __toString(): string
    {
        return $this->username ?? 'Utilisateur';
    }// end __toString()


}// end class
