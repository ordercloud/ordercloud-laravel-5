<?php namespace Ordercloud\Laravel\Providers;

use Illuminate\Support\ServiceProvider;
use Ordercloud\Laravel\Auth\AccessTokenStorage;
use Ordercloud\Laravel\Auth\SessionAccessTokenStorage;
use Ordercloud\Laravel\Auth\TokenRefresher;
use Ordercloud\Laravel\Auth\UserProvider;
use Ordercloud\Support\OrdercloudBuilder;
use Psr\Log\LoggerInterface;

class OrdercloudServiceProvider extends ServiceProvider
{
    /**
     * @var OrdercloudBuilder
     */
    private $builder;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $app = $this->app;
        $config = $app['config'];

        // Client logging
        if ($config->get('ordercloud::logging', false)) {
            $this->builder->registerClientLogger($app->make('ordercloud.logging.client'));
        }

        // Extended logging (exceptions 'n such)
        $app['log']->getMonolog()->pushHandler($app['logging.file-handler']);

        // Extend laravel's auth with OC user provider
        $app['auth']->extend('ordercloud', function () use ($app)
        {
            return $app->make(UserProvider::class);
        });

        $accessTokenStorage = $app->make(AccessTokenStorage::class);
        if ($accessToken = $accessTokenStorage->get()) {
            $this->builder->setAccessToken($accessToken);

            $refresher = new TokenRefresher(
                $config->get('ordercloud::organisation_code'),
                $config->get('ordercloud::client_secret'),
                $accessToken->getRefreshToken(),
                $accessTokenStorage
            );

            $this->builder->setTokenRefresher($refresher);
        }

        // Register all oc components in the app container
        $this->builder->registerComponents($app);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $config = $this->app['config'];

        $this->builder = OrdercloudBuilder::create()
            ->setBaseUrl($config->get('ordercloud::api_url'))
            ->setUsername($config->get('ordercloud::username'))
            ->setPassword($config->get('ordercloud::password'))
            ->setOrganisationToken($config->get('ordercloud::organisation_token'));

        // Use registered logger interface for client logging
        $this->app->alias(LoggerInterface::class, 'ordercloud.logging.client');

        // Use session access token storage
        $this->app->singleton(AccessTokenStorage::class, SessionAccessTokenStorage::class);

        $this->registerFileLogHandler();
    }

    protected function registerFileLogHandler()
    {
        $this->app->singleton('ordercloud.logging.file-handler', function ()
        {
            $handler = new StreamHandler(storage_path('logs/application.log'), Logger::DEBUG);

            $handler->pushProcessor(new IlluminateSessionProcessor($this->app['session.store'], Logger::WARNING));
            $handler->pushProcessor(new ReflectionExceptionsProcessor());
            $handler->pushProcessor(new OrdercloudRequestExceptionProcessor());

            $handler->setFormatter(new VerboseMultilineFormatter());

            return $handler;
        });
    }
}
