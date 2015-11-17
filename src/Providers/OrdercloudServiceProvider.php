<?php namespace Ordercloud\Laravel\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Ordercloud\Laravel\Auth\AccessTokenStorage;
use Ordercloud\Laravel\Auth\SessionAccessTokenStorage;
use Ordercloud\Laravel\Auth\TokenRefresher;
use Ordercloud\Laravel\Auth\UserProvider;
use Ordercloud\Monolog\Formatters\VerboseMultilineFormatter;
use Ordercloud\Monolog\Processors\IlluminateSessionProcessor;
use Ordercloud\Monolog\Processors\OrdercloudRequestExceptionProcessor;
use Ordercloud\Monolog\Processors\ReflectionExceptionsProcessor;
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

        // Laravel 4 does not have
        if ($this->isVersion5()) {
            $this->publishes([ __DIR__.'/../config/config.php' => config_path('ordercloud.php') ]);
        }

        $app = $this->app;

        // Client logging
        if ($this->config('logging', false)) {
            $this->builder->registerClientLogger($app->make('ordercloud.logging.client'));
        }

        // Extended logging (exceptions 'n such)
        $app['log']->getMonolog()->pushHandler($app->make('ordercloud.logging.file-handler'));

        // Extend laravel's auth with OC user provider
        $app['auth']->extend('ordercloud', function () use ($app)
        {
            return $app->make(UserProvider::class);
        });

        $accessTokenStorage = $app->make(AccessTokenStorage::class);
        if ($accessToken = $accessTokenStorage->get()) {
            $this->builder->setAccessToken($accessToken);

            $refresher = new TokenRefresher(
                $this->config('organisation_code'),
                $this->config('client_secret'),
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
        $this->builder = OrdercloudBuilder::create()
            ->setBaseUrl($this->config('api_url'))
            ->setUsername($this->config('username'))
            ->setPassword($this->config('password'))
            ->setOrganisationToken($this->config('organisation_token'));

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

    public function config($key, $default = null)
    {
        if ($this->isVersion5()) {
            return config("ordercloud.{$key}", $default);
        }

        return $this->app['config']->get("ordercloud::{$key}", $default);
    }

    /**
     * @return mixed
     */
    protected function isVersion5()
    {
        return version_compare(str_replace(' (LTS)', '', Application::VERSION), '5.0.0', '>=');
    }
}
