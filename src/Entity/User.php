<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
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
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
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
    private ?string $password = null;

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
     * Token utilisé pour la réinitialisation du mot de passe.
     */
    #[ORM\Column(length: 64, nullable: true)]
    private ?string $resetToken = null;

    /**
     * Date d'expiration du token de réinitialisation du mot de passe.
     */
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $resetTokenExpiresAt = null;


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
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }// end getId()


    /**
     * Récupère le nom d'utilisateur.
     *
     * @return string|null
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
     *
     * @return string|null
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
     *
     * @return string|null
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
     * Vérifie si le compte de l'utilisateur est actif.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }// end isActive()


    /**
     * Définit si le compte de l'utilisateur est actif.
     *
     * @param bool $isActive indique si le compte est actif ou non
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
     * @param Comment $comment le commentaire à ajouter
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
     * @param Comment $comment le commentaire à supprimer
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
     *
     * @return void
     */
    public function eraseCredentials(): void
    {
    }// end eraseCredentials()


    /**
     * Récupère l'identifiant pour l'authentification (email).
     *
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }// end getUserIdentifier()


    /**
     * Récupère le token d'activation.
     *
     * @return string|null
     */
    public function getActivationToken(): ?string
    {
        return $this->activationToken;
    }// end getActivationToken()


    /**
     * Définit le token d'activation.
     *
     * @param string|null $activationToken le token d'activation à définir
     *
     * @return $this
     */
    public function setActivationToken(?string $activationToken): self
    {
        $this->activationToken = $activationToken;

        return $this;
    }// end setActivationToken()


    /**
     * Récupère le token de réinitialisation du mot de passe.
     *
     * @return string|null
     */
    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }// end getResetToken()


    /**
     * Définit le token de réinitialisation du mot de passe.
     *
     * @param string|null $resetToken Le token de réinitialisation
     *
     * @return $this
     */
    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }// end setResetToken()


    /**
     * Récupère la date d'expiration du token de réinitialisation.
     *
     * @return \DateTimeImmutable|null La date d'expiration ou null si elle n'est pas définie
     */
    public function getResetTokenExpiresAt(): ?\DateTimeImmutable
    {
        return $this->resetTokenExpiresAt;
    }// end getResetTokenExpiresAt()


    /**
     * Définit la date d'expiration du token de réinitialisation.
     *
     * @param \DateTimeImmutable|null $expiresAt La date d'expiration
     *
     * @return $this
     */
    public function setResetTokenExpiresAt(?\DateTimeImmutable $expiresAt): self
    {
        $this->resetTokenExpiresAt = $expiresAt;

        return $this;
    }// end setResetTokenExpiresAt()


    /**
     * Vérifie si le token de réinitialisation est valide.
     *
     * @return bool true si le token est valide, false sinon
     */
    public function isResetTokenValid(): bool
    {
        return $this->resetToken && $this->resetTokenExpiresAt > new \DateTimeImmutable();
    }// end isResetTokenValid()


    /**
     * Convertit l'utilisateur en une chaîne de caractères (retourne le nom d'utilisateur).
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->username ?? 'Utilisateur';
    }// end __toString()


}// end class
