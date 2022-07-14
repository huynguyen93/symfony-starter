<?php

namespace App\Security;

use App\Entity\User;
use App\Entity\UserOauth;
use App\Enum\CookieKeys;
use App\Exception\OauthUserAlreadyAssignedException;
use App\Exception\OauthUserMissingEmailException;
use App\Repository\UserRepository;
use App\Security\OauthClient\FacebookOauthClient;
use App\Security\OauthClient\GoogleOauthClient;
use App\Security\OauthClient\OauthClientInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class OauthAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private RouterInterface $router,
        private UserRepository $userRepository,
        private FacebookOauthClient $facebookOauthClient,
        private GoogleOauthClient $googleOauthClient,
        private UserOauthConnector $userOauthConnector,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        $route = $request->attributes->get('_route');
        $routeParams = $request->attributes->get('_route_params');
        $provider = $routeParams['providerName'] ?? null;

        $matchesRoute = $route === 'app.security.oauth_callback';
        $validProvider = in_array($provider, [UserOauth::PROVIDER_GOOGLE, UserOauth::PROVIDER_FACEBOOK]);

        return $matchesRoute && $validProvider;
    }

    public function authenticate(Request $request): Passport
    {
        $oauthClient = $this->getOauthClient($request);
        $userOauth = $oauthClient->getUserOauth($request);
        $loggedInUserId = $request->cookies->get(CookieKeys::USER_ID);
        $loggedInUser = $loggedInUserId ? $this->userRepository->find($loggedInUserId) : null;

        try {
            $user = $this->userOauthConnector->connect($userOauth, $loggedInUser);
        } catch (OauthUserAlreadyAssignedException) {
            throw new AuthenticationException('This social account is already connected to another user!');
        } catch (OauthUserMissingEmailException) {
            throw new AuthenticationException('You must share your email to continue!');
        }

        return new SelfValidatingPassport(
            new UserBadge($user->getUserIdentifier()),
            [new RememberMeBadge(),]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($request->cookies->get(CookieKeys::USER_ID)) {
            $response = new RedirectResponse($this->router->generate('app.user.profile'));
            $response->headers->clearCookie(CookieKeys::USER_ID);

            return $response;
        }

        /** @var User $user */
        $user = $token->getUser();
        if (!$user->hasBeenWelcomed()) {
            $flashBag = $request->getSession()->getFlashBag();
            $flashBag->add(
                'success',
                sprintf('Welcome, %s! We are very happy to have you here! :)', $user->getDisplayName())
            );
        }

        $route = $request->getSession()->get('last_visited_lesson_url');

        if (!$route) {
            $route = $this->router->generate('app.homepage');
        } else {
            $request->getSession()->remove('last_visited_lesson_url');
        }

        return new RedirectResponse($route);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $flashBag = $request->getSession()->getFlashBag();
        $flashBag->add('oauthError', $exception->getMessage());

        return new RedirectResponse($this->router->generate('app.security.login'));
    }

    private function getOauthClient(Request $request): OauthClientInterface
    {
        $routeParams = $request->attributes->get('_route_params');
        $provider = $routeParams['providerName'] ?? null;

        if ($provider === UserOauth::PROVIDER_GOOGLE) {
            return $this->googleOauthClient;
        } else {
            return $this->facebookOauthClient;
        }
    }
}
