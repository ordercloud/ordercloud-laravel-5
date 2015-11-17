<?php namespace Ordercloud\Laravel\Auth;

use Ordercloud\Entities\Auth\AccessToken;
use Ordercloud\Support\AbstractTokenRefresher;

class TokenRefresher extends AbstractTokenRefresher
{
    /**
     * @var AccessTokenStorage
     */
    private $accessTokenStorage;

    /**
     * @param string             $organisationCode
     * @param string             $clientSecret
     * @param string             $refreshToken
     * @param AccessTokenStorage $accessTokenStorage
     */
    public function __construct($organisationCode, $clientSecret, $refreshToken, AccessTokenStorage $accessTokenStorage)
    {
        parent::__construct($organisationCode, $clientSecret, $refreshToken);
        $this->accessTokenStorage = $accessTokenStorage;
    }

    public function onAccessTokenRefreshed(AccessToken $accessToken)
    {
        $this->accessTokenStorage->save($accessToken);
    }
}
