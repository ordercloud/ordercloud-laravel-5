<?php namespace Ordercloud\Laravel\Auth;

use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider as IlluminateUserProvider;
use Ordercloud\Services\UserService;

class UserProvider implements IlluminateUserProvider
{
    /**
     * @var UserService
     */
    private $users;

    public function __construct(UserService $users)
    {
        $this->users = $users;
    }

    public function retrieveById($identifier)
    {
        $user = $this->users->getUser($identifier);

        return AuthenticatableUser::wrapUser($user);
    }

    public function retrieveByToken($identifier, $token)
    {
        throw new Exception('Not supported');
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        // TODO
    }

    public function retrieveByCredentials(array $credentials)
    {
        throw new Exception('Not supported');
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        throw new Exception('Not supported');
    }
}
