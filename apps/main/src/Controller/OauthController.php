<?php

namespace App\Controller;

use App\Enum\CookieKeys;
use App\Security\OauthClient\FacebookOauthClient;
use App\Security\OauthClient\GoogleOauthClient;
use App\Security\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OauthController extends AbstractController
{
    public function __construct(
        private Security $security,
        private FacebookOauthClient $facebookOauthClient,
        private GoogleOauthClient $googleOauthClient,
    ) {
    }

    #[Route(path: '/oauth/fb', name: 'app.security.facebook')]
    public function loginFacebookAction(Request $request): RedirectResponse
    {
        return $this->createSocialLoginResponse($this->facebookOauthClient->getAuthorizationUrl());
    }

    #[Route(path: '/oauth/fbcb', name: 'app.security.facebook_callback')]
    public function facebookCallbackAction(): RedirectResponse
    {
        // this method is called only when user denied access

        return $this->redirectToRoute('app.security.login');
    }

    #[Route(path: '/oauth/google', name: 'app.security.google')]
    public function loginGoogleAction(): RedirectResponse
    {
        return $this->createSocialLoginResponse($this->googleOauthClient->getAuthorizationUrl());
    }

    #[Route(path: '/oauth/googlecb', name: 'app.security.google_callback')]
    #[Route(path: '/oauth-callback/{providerName}', name: 'app.security.oauth_callback')]
    public function googleCallbackAction(): RedirectResponse
    {
        // this method is called only when user denied access

        return $this->redirectToRoute('app.security.login');
    }

    private function createSocialLoginResponse(string $url): RedirectResponse
    {
        $response = new RedirectResponse($url);

        $user = $this->security->getUser();

        if ($user) {
            $uid = (string) $user->getId();
            $cookieExpirationTimestamp = time() + 60 * 60 * 24;
            $response->headers->setCookie(new Cookie(CookieKeys::USER_ID, $uid, $cookieExpirationTimestamp));
        }

        return $response;
    }
}
