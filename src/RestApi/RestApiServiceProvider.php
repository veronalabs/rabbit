<?php // phpcs:ignore WordPress.Files.FileName

namespace Backyard\RestApi;

use Backyard\Contracts\BootablePluginProviderInterface;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

/**
 * Registers the rest wrapper into the plugin
 */
class RestApiServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface, BootablePluginProviderInterface
{

    /**
     * The provided array is a way to let the container
     * know that a service is provided by this service
     * provider. Every service that is registered via
     * this service provider must have an alias added
     * to this array or it will be ignored.
     *
     * @var array
     */
    protected $provides = [
        'route'
    ];

    /**
     * Add Route functionality to the container
     *
     * @return void
     * @throws MissingConfigurationException when "rest_namespace" and "rest_nonce_field_name" congig keys are missing
     */
    public function boot()
    {
        $container = $this->getContainer();

        $restNameSpace  = $container->config('rest_namespace');
        $nonceFieldName = $container->config('rest_nonce_field_name');

        if (! $restNameSpace) {
            throw new MissingConfigurationException('Rest api service requires "rest_namespace" to be configured.');
        }
        if (! $nonceFieldName) {
            throw new MissingConfigurationException('Rest api service requires "rest_nonce_field_name" to be configured.');
        }
        $container
            ->add('route', Route::class)
            ->addArgument($restNameSpace)
            ->addArgument($nonceFieldName);
    }

    /**
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Register a the route macro to the plugin
     *
     * @return void
     */
    public function bootPlugin()
    {
        $instance = $this;

        $this->getContainer()::macro(
            'route',
            function () use ($instance) {
                return $instance->getContainer()->get('route');
            }
        );
    }
}
