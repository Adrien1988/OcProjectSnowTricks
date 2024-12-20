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
 * User entity representing application users.
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
     * User ID (primary key).
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Username of the user.
     */
    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: "Le nom d'utilisateur est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le nom d'utilisateur ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $username = null;

    /**
     * Email address of the user.
     */
    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: "L'email est obligatoire.")]
    #[Assert\Email(message: "L'email '{{ value }}' n'est pas valide.")]
    private ?string $email = null;

    /**
     * Hashed password of the user.
     */
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le mot de passe est obligatoire.')]
    private ?string $password = null;

    /**
     * URL of the user's avatar.
     */
    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: "L'URL de l'avatar ne doit pas dépasser {{ limit }} caractères."
    )]
    #[Assert\Url(message: 'L’URL de l’avatar n’est pas valide.')]
    private ?string $avatarUrl = null;

    /**
     * Indicates whether the user's account is active.
     */
    #[ORM\Column(nullable: false, options: ['default' => false])]
    private bool $isActive = false;

    /**
     * Comments authored by the user.
     *
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    /**
     * Token used for account activation.
     */
    #[ORM\Column(length: 64, nullable: true)]
    private ?string $activationToken = null;


    /**
     * Initializes a new User instance.
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->isActive = false;

    }//end __construct()


    /**
     * Gets the user ID.
     */
    public function getId(): ?int
    {
        return $this->id;

    }//end getId()


    /**
     * Gets the username.
     */
    public function getUsername(): ?string
    {
        return $this->username;

    }//end getUsername()


    /**
     * Sets the username.
     */
    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;

    }//end setUsername()


    /**
     * Gets the email address.
     */
    public function getEmail(): ?string
    {
        return $this->email;

    }//end getEmail()


    /**
     * Sets the email address.
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;

    }//end setEmail()


    /**
     * Gets the hashed password.
     */
    public function getPassword(): ?string
    {
        return $this->password;

    }//end getPassword()


    /**
     * Sets the hashed password.
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;

    }//end setPassword()


    /**
     * Gets the avatar URL.
     */
    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;

    }//end getAvatarUrl()


    /**
     * Sets the avatar URL.
     */
    public function setAvatarUrl(?string $avatarUrl): static
    {
        $this->avatarUrl = $avatarUrl;

        return $this;

    }//end setAvatarUrl()


    /**
     * Checks if the user account is active.
     */
    public function isActive(): ?bool
    {
        return $this->isActive;

    }//end isActive()


    /**
     * Sets the user account's active status.
     */
    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;

    }//end setIsActive()


    /**
     * Gets the comments authored by the user.
     */
    public function getComments(): Collection
    {
        return $this->comments;

    }//end getComments()


    /**
     * Adds a comment authored by the user.
     */
    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAuthor($this);
        }

        return $this;

    }//end addComment()


    /**
     * Removes a comment authored by the user.
     */
    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;

    }//end removeComment()


    /**
     * Gets the roles assigned to the user.
     */
    public function getRoles(): array
    {
        return ['ROLE_USER'];

    }//end getRoles()


    /**
     * Removes sensitive data from the user.
     */
    public function eraseCredentials(): void
    {

    }//end eraseCredentials()


    /**
     * Gets the identifier for authentication (email in this case).
     */
    public function getUserIdentifier(): string
    {
        return $this->email;

    }//end getUserIdentifier()


    /**
     * Gets the activation token.
     */
    public function getActivationToken(): ?string
    {
        return $this->activationToken;

    }//end getActivationToken()


    /**
     * Sets the activation token.
     */
    public function setActivationToken(?string $activationToken): self
    {
        $this->activationToken = $activationToken;

        return $this;

    }//end setActivationToken()


    /**
     * Converts the user object to a string (returns the username).
     */
    public function __toString(): string
    {
        return ($this->username ?? 'Utilisateur');

    }//end __toString()


}//end class
