<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class LoginFormAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    public function __construct(
        private RouterInterface $router,
        private UserRepository $userRepository,

    ) {
    }

    public function supports(Request $request): bool
    {
        return '/login' === $request->getPathInfo() && $request->isMethod('POST');
    }

    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        return new RedirectResponse($this->router->generate('app.security.login'));
    }

    public function authenticate(Request $request): Passport
    {
        $credentials = $request->toArray();
        $email = $credentials['email'] ?? null;
        $password = $credentials['password'] ?? null;

        if (!$email || !$password) {
            throw new AuthenticationException();
        }

        $user = $this->userRepository->findOneByEmail($email);
        if (!$user || $password !== $user->getPassword()) {
            throw new AuthenticationException();
        }

        return new SelfValidatingPassport(new UserBadge($user->getUserIdentifier()), [new RememberMeBadge()]);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        return new JsonResponse(['message' => 'Email or password is not correct'], 400);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): JsonResponse
    {
        return new JsonResponse();
    }
}
