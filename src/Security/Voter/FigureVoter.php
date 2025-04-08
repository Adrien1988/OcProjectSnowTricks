<?php

namespace App\Security\Voter;

use App\Entity\Figure;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class FigureVoter extends Voter
{
    public const EDIT = 'FIGURE_EDIT';
    public const DELETE = 'FIGURE_DELETE';


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
        return in_array($attribute, [self::EDIT, self::DELETE]) && $subject instanceof Figure;
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

        /** @var Figure $figure */
        $figure = $subject;

        // Vérification que l'utilisateur est bien le créateur de la figure
        return $user === $figure->getAuthor();
    }


}
