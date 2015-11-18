<?php namespace Ordercloud\Laravel\Auth;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Ordercloud\Support\OrdercloudBuilder;

class AccessTokenMiddleware
{
    /**
     * @var AccessTokenStorage
     */
    private $tokenStorage;
    /**
     * @var Application
     */
    private $app;

    /**
     * @param AccessTokenStorage $tokenStorage
     * @param Application        $app
     */
    public function __construct(AccessTokenStorage $tokenStorage, Application $app)
    {
        $this->tokenStorage = $tokenStorage;
        $this->app = $app;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($accessToken = $this->tokenStorage->get()) {
            $builder = $this->app->make(OrdercloudBuilder::class);
            $builder->setAccessToken($accessToken);

            $refresher = new TokenRefresher(
                config('ordercloud.organisation_code'),
                config('ordercloud.client_secret'),
                $accessToken->getRefreshToken(),
                $this->tokenStorage
            );

            $builder->setTokenRefresher($refresher);
            $builder->registerComponents($this->app);
        }

        return $next($request);
    }
}
