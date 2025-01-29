<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Authenticator for handling user login and authentication success.
 */
class UserAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private UserRepository $userRepository;


    /**
     * Constructor.
     *
     * @param UrlGeneratorInterface $urlGenerator   Service permettant de générer des URLs dans l'application
     * @param UserRepository        $userRepository Repository pour gérer les utilisateurs dans la base de données
     */
    public function __construct(private UrlGeneratorInterface $urlGenerator, UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;

    }// end_construct()


    /**
     * Authenticates the user by creating a Passport with credentials.
     *
     * @param Request $request the HTTP request containing user credentials
     *
     * @return Passport the constructed Passport for the authentication process
     */
    public function authenticate(Request $request): Passport
    {

        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $csrfToken = $request->request->get('_csrf_token');

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge(
                $email,
                function ($userIdentifier) {
                    $user = $this->userRepository->findOneBy(['email' => $userIdentifier]);

                    if (!$user) {
                        throw new CustomUserMessageAuthenticationException('Compte non trouvé.');
                    }

                    if (!$user->isActive()) {
                        throw new CustomUserMessageAuthenticationException('Votre compte n\'est pas encore activé.');
                    }

                    return $user;
                }
            ),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $csrfToken),
                new RememberMeBadge(),
            ]
        );
    }// end authenticate()


    /**
     * Handles actions after successful authentication.
     *
     * @param Request        $request      the current HTTP request
     * @param TokenInterface $token        the authentication token
     * @param string         $firewallName the name of the firewall
     *
     * @return Response|null a redirect response to the target path or default route
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Redirect to the main route if no target path is set.
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }// end onAuthenticationSuccess()


    /**
     * Generates the login URL.
     *
     * @param Request $request the current HTTP request
     *
     * @return string the URL for the login page
     */
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }// end getLoginUrl()


}
