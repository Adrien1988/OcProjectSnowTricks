<?php

namespace App\Security\Voter;

use App\Entity\Video;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class VideoVoter extends Voter
{
    public const CREATE = 'VIDEO_CREATE';
    public const EDIT = 'VIDEO_EDIT';
    public const DELETE = 'VIDEO_DELETE';


    /**
     * Vérifie si l'attribut et le sujet donnés sont pris en charge par ce Voter.
     *
     * @param string $attribute L'action demandée (ex: "FIGURE_EDIT", "FIGURE_DELETE")
     * @param mixed  $subject   L'entité sur laquelle porte l'action
     *
     * @return bool retourne true si le Voter supporte cette vérification
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::CREATE, self::EDIT, self::DELETE]) && $subject instanceof Video;
    }


    /**
     * Vérifie si l'utilisateur a les permissions nécessaires pour l'action demandée.
     *
     * @param string         $attribute L'action demandée (ex: "FIGURE_EDIT", "FIGURE_DELETE")
     * @param mixed          $subject   L'entité Figure sur laquelle porte l'action
     * @param TokenInterface $token     le token de sécurité contenant l'utilisateur
     *
     * @return bool retourne true si l'utilisateur a l'autorisation, false sinon
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Video $video */
        $video = $subject;

        switch ($attribute) {
            case self::CREATE:
                // Exemple : seul l'auteur de la figure peut créer une vidéo
                return $user === $video->getFigure()->getAuthor();

            case self::EDIT:
            case self::DELETE:
                // Même logique : seul l'auteur de la figure
                return $user === $video->getFigure()->getAuthor();
        }

        return false;
    }


}
