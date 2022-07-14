<?php

namespace App\Security\OauthClient;

use App\Entity\UserOauth;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\Facebook;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class FacebookOauthClient implements OauthClientInterface
{
    public function __construct(
        private RouterInterface $router,
        private string $facebookAppId,
        private string $facebookAppSecret
    ) {
    }

    public function getUserOauth(Request $request): UserOauth
    {
        $error = new AuthenticationException('An error happened while connecting to your Facebook account');

        $code = $request->query->get('code');
        if (!$code) {
            throw $error;
        }

        $facebook = $this->getProvider();

        try {
            $accessToken = $facebook->getAccessToken('authorization_code', [
                'code' => $code,
            ]);

            $fbUser = $facebook->getResourceOwner($accessToken);
        } catch (IdentityProviderException) {
            throw $error;
        }

        $userOauth = new UserOauth();
        $userOauth->setOriginId($fbUser->getId());
        $userOauth->setProvider(UserOauth::PROVIDER_FACEBOOK);
        $userOauth->setEmail($fbUser->getEmail());
        $userOauth->setAvatarUrl($fbUser->getPictureUrl());
        $userOauth->setName($fbUser->getName());

        return $userOauth;
    }

    public function getAuthorizationUrl(): string
    {
        return $this->getProvider()->getAuthorizationUrl([
            'scope' => ['email', 'public_profile']
        ]);
    }

    private function getProvider(): Facebook
    {
        return new Facebook([
            'clientId' => $this->facebookAppId,
            'clientSecret' => $this->facebookAppSecret,
            'graphApiVersion' => 'v2.10',
            'redirectUri' => $this->router->generate(
                'app.security.oauth_callback',
                ['providerName' => UserOauth::PROVIDER_FACEBOOK],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
        ]);
    }
}
