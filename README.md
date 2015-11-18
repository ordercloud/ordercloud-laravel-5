# Ordercloud Laravel 5 extensions
Laravel 5 extensions for ordercloud client

## Installation
Add the following to your require block in composer.json config
``` "ordercloud/laravel": "dev-master" ```

## Configuration
To install into a Laravel project, first do the composer install then add the ServiceProvider to your config/app.php service providers list.
```php 
Ordercloud\Laravel\Providers\OrdercloudServiceProvider::class 
```

Publish the config fie
```
php artisan vendor:publish --provider="Ordercloud\Laravel\Providers\OrdercloudServiceProvider"
```
