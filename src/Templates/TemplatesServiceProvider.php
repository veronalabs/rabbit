<?php // phpcs:ignore WordPress.Files.FileName

/**
 * Registers the templates engine functionality within the plugin.
 *
 * @package   rabbit-framework
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://sematico.com
 */

namespace Rabbit\Templates;

use Rabbit\Contracts\BootablePluginProviderInterface;
use Rabbit\Exceptions\MissingConfigurationException;
use League\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Registers the templates' functionality within the plugin.
 */
class TemplatesServiceProvider extends AbstractServiceProvider implements BootablePluginProviderInterface
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
		'template',
	];

	/**
	 * Register the templates' functionality within the plugin's container.
	 *
	 * @return void
	 * @throws MissingConfigurationException When the requirement config is missing.
	 */
	public function register()
	{
		/** @var \Rabbit\Plugin $container */
		$container = $this->getContainer();
		$viewPath  = $container->config('views_path');

		if (!$viewPath) {
			throw new MissingConfigurationException('Templates service provider requires "views_path" to be configured.');
		}
	}

	/**
	 * When the plugin is booted, register a new macro.
	 *
	 * Adds the `template()` method that returns an instance of the Rabbit\Templates class.
	 *
	 * @return void
	 */
	public function bootPlugin()
	{
		$this->getContainer()::macro('template', function (string $view, array $data = []) {
			$engine = new Engine($view, $data);
			return $engine->render();
		});
	}
}
