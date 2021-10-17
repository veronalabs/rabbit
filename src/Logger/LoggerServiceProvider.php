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
        $logsPath  = $container->config('logs_path');
        $logsDays  = $container->config('logs_days');

        if (! $logsPath) {
            throw new MissingConfigurationException('Logger service provider requires "logs_path" to be configured.');
        }
        
        if (! $logsDays) {
            throw new MissingConfigurationException('Logger service provider requires "logs_days" to be configured.');
        }

        switch (wp_get_environment_type()) {

            case 'local':
            case 'development':
                $streamHandler = new StreamHandler($container->basePath($logsPath).'/debug.log', Logger::DEBUG);
                $container
                    ->share('logger', Logger::class)
                    ->addArgument('development_channel')
                    ->addMethodCall('pushHandler', [ $streamHandler ]);
                break;

            case 'staging':
            case 'production':
                $rotatingHandler = new RotatingFileHandler($container->basePath($logsPath).'/production.log', (int)$logsDays, Logger::ERROR);
                $container
                    ->share('logger', Logger::class)
                    ->addArgument('production_channel')
                    ->addMethodCall('pushHandler', [ $rotatingHandler ]);
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
