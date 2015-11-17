<?php namespace Ordercloud\Laravel\Auth;

use Illuminate\Contracts\Auth\Guard;
use Ordercloud\Entities\Auth\AccessToken;
use Ordercloud\Ordercloud;
use Ordercloud\Requests\Auth\Entities\Authorisation;
use Ordercloud\Services\UserService;

class OrdercloudAuth
{
    /**
     * @var UserService
     */
    private $users;
    /**
     * @var Guard
     */
    private $guard;
    /**
     * @var Ordercloud
     */
    private $ordercloud;
    /**
     * @var AccessTokenStorage
     */
    private $accessToken;

    public function __construct(UserService $users, Guard $guard, Ordercloud $ordercloud, AccessTokenStorage $accessToken)
    {
        $this->users = $users;
        $this->guard = $guard;
        $this->ordercloud = $ordercloud;
        $this->accessToken = $accessToken;
    }

    /**
     * Log the user in with access token
     *
     * @param AccessToken $accessToken
     */
    public function login(AccessToken $accessToken)
    {
        $user = $this->users->getLoggedInUser(Authorisation::createWithAccessToken($accessToken));

        $this->guard->login(AuthenticatableUser::wrapUser($user));

        $this->ordercloud->setAccessToken($accessToken);

        $this->accessToken->save($accessToken);
    }

    /**
     * Log the user out of the application.
     */
    public function logout()
    {
        $this->guard->logout();

        $this->accessToken->destroy();
    }
}
