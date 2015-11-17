<?php namespace Ordercloud\Laravel\Auth;

use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Ordercloud\Entities\Users\User as OrdercloudUser;

class AuthenticatableUser extends OrdercloudUser implements Authenticatable
{
    /**
     * @var string
     */
    private $rememberToken;

    /**
     * @param OrdercloudUser $user
     *
     * @return static
     */
    public static function wrapUser(OrdercloudUser $user)
    {
        return new static(
            $user->getId(),
            $user->isEnabled(),
            $user->getUsername(),
            $user->getFacebookId(),
            $user->getProfile(),
            $user->getGroups(),
            $user->getOrganisations()
        );
    }

    public function getAuthIdentifier()
    {
        return $this->getId();
    }

    public function getAuthPassword()
    {
        throw new Exception('Not supported');
    }

    public function getRememberToken()
    {
        // TODO
        return $this->rememberToken;
    }

    public function setRememberToken($value)
    {
        // TODO
        $this->rememberToken = $value;
    }

    public function getRememberTokenName()
    {
        throw new Exception('Not supported');
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        // TODO
    }
}
