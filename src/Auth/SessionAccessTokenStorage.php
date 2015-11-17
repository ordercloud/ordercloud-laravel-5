<?php namespace Ordercloud\Laravel\Auth;

use Illuminate\Session\Store;
use Ordercloud\Entities\Auth\AccessToken;

class SessionAccessTokenStorage implements AccessTokenStorage
{
    /**
     * @var Store
     */
    private $session;

    /**
     * @param Store $session
     */
    public function __construct(Store $session)
    {
        $this->session = $session;
    }

    public function get()
    {
        return $this->session->get('ordercloud.accessToken');
    }

    public function save(AccessToken $accessToken)
    {
        $this->session->put('ordercloud.accessToken', $accessToken);
    }

    public function destroy()
    {
        $this->session->forget('ordercloud.accessToken');
    }
}
