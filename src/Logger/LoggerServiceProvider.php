<?php // phpcs:ignore WordPress.Files.FileName

namespace Backyard\Logger;

use Backyard\Contracts\BootablePluginProviderInterface;
use Backyard\Exceptions\MissingConfigurationException;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;

/**
 * Registers the loging functionality into the plugin.
 */
class LoggerServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface, BootablePluginProviderInterface
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
        'logger',
    ];

    /**
     * Add the logger instance into the plugin.
     *
     * @return void
     * @throws MissingConfigurationException When the plugin configuration is missing the env, logs_path and logs_days specification.
     */
    public function boot()
    {
        $container = $this->getContainer();
        $logs_path = $container->config('logs_path');
        $logs_days = $container->config('logs_days');

        if (! $logs_path) {
            throw new MissingConfigurationException('Logger service provider requires "logs_path" to be configured.');
        }
        
        if (! $logs_days) {
            throw new MissingConfigurationException('Logger service provider requires "logs_days" to be configured.');
        }

        switch (wp_get_environment_type()) {

            case 'local':
            case 'development':
                $stream_handler = new StreamHandler($container->basePath($logs_path).'/debug.log', Logger::DEBUG);
                $container
                    ->share('logger', Logger::class)
                    ->addArgument('development_channel')
                    ->addMethodCall('pushHandler', [ $stream_handler ]);
                break;

            case 'staging':
            case 'production':
                $rotating_handler = new RotatingFileHandler($container->basePath($logs_path).'/production.log', (int)$logs_days, Logger::ERROR);
                $container
                    ->share('logger', Logger::class)
                    ->addArgument('production_channel')
                    ->addMethodCall('pushHandler', [ $rotating_handler ]);
                break;

        }
    }

    /**
     * @return void
     */
    public function register()
    {
    }

    /**
     * When the plugin is booted, register a new macro.
     *
     * Adds the `logger()` method that returns an instance of the Monolog\Logger class.
     *
     * @return void
     */
    public function bootPlugin()
    {
        $instance = $this;

        $this->getContainer()::macro(
            'logger',
            function () use ($instance) {
                return $instance->getContainer()->get('logger');
            }
        );
    }
}
