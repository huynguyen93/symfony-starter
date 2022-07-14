<?php

namespace App\Security\OauthClient;

use App\Entity\UserOauth;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\Google;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class GoogleOauthClient implements OauthClientInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private string $googleOauthClientId,
        private string $googleOauthClientSecret,
    ) {
    }

    public function getAuthorizationUrl(): string
    {
        return $this->getProvider()->getAuthorizationUrl();
    }

    public function getUserOauth(Request $request): UserOauth
    {
        $error = new AuthenticationException('An error happened while connecting to your Google account');

        $authCode = $request->query->get('code');
        if (!$authCode) {
            throw $error;
        }

        try {
            $accessToken = $this->getProvider()->getAccessToken('authorization_code', ['code' => $authCode]);
        } catch (IdentityProviderException) {
            throw $error;
        }

        $googleUser = $this->getGoogleUser($accessToken->getToken());
        if (!$googleUser) {
            throw $error;
        }

        $UserOauth = new UserOauth();
        $UserOauth->setProvider(UserOauth::PROVIDER_GOOGLE);
        $UserOauth->setOriginId($googleUser['id']);
        $UserOauth->setEmail($googleUser['email'] ?? null);
        $UserOauth->setAvatarUrl($googleUser['picture'] ?? null);
        $UserOauth->setName($googleUser['name'] ?? null);

        return $UserOauth;
    }

    private function getGoogleUser(string $accessToken)
    {
        $url = 'https://www.googleapis.com/oauth2/v1/userinfo?access_token=' . $accessToken;
        $googleUserDataJson = file_get_contents($url);

        return json_decode($googleUserDataJson, true);
    }

    private function getProvider(): Google
    {
        $params = ['providerName' => UserOauth::PROVIDER_GOOGLE];
        $redirectUri = $this->urlGenerator->generate(
            'app.security.oauth_callback',
            $params,
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new Google([
            'clientId'     => $this->googleOauthClientId,
            'clientSecret' => $this->googleOauthClientSecret,
            'redirectUri'  => $redirectUri,
        ]);
    }
}
