<?php namespace Ordercloud\Laravel\Auth;

use Ordercloud\Entities\Auth\AccessToken;

interface AccessTokenStorage
{
    /**
     * @return AccessToken
     */
    public function get();

    /**
     * @param AccessToken $accessToken
     */
    public function save(AccessToken $accessToken);

    /**
     * @return void
     */
    public function destroy();
}
