<?php // phpcs:ignore WordPress.Files.FileName

namespace Rabbit\Logger;

use Rabbit\Contracts\BootablePluginProviderInterface;
use Rabbit\Exceptions\MissingConfigurationException;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use LoggerWp\Logger;

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

		if (!$logsPath or !$logsDays) {
			return;
		}

		$container
			->add('logger', Logger::class)
			->addArgument([
				'dir_name'  => sprintf('%s-logs', $container->getDirectoryName()),
				'channel'   => defined('WP_DEBUG') && WP_DEBUG ? 'development' : 'production',
				'logs_days' => $logsDays,
			]);
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
	 * Adds the `logger()` method that returns an instance of the LoggerWP\Logger class.
	 *
	 * @return void
	 */
	public function bootPlugin()
	{
		$instance = $this;

		$this->getContainer()::macro('logger',
			function () use ($instance) {
				return $instance->getContainer()->get('logger');
			}
		);

		add_action('init', function () use ($instance) {
			$instance->getContainer()->get('logger');
		});
	}
}
