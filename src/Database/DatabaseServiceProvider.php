<?php // phpcs:ignore WordPress.Files.FileName

namespace Rabbit\Database;

use Rabbit\Contracts\BootablePluginProviderInterface;
use Illuminate\Database\Capsule\Manager as Capsule;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

/**
 * Registers the illuminate\Database into the plugin
 */
class DatabaseServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface, BootablePluginProviderInterface
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
		'database'
	];

	/**
	 * Add the Capsule\Manager into the plugin.
	 *
	 * @return void
	 *
	 */
	public function boot()
	{
		global $wpdb;
		$container = $this->getContainer();

		$container
			->share('database', Capsule::class)
			->addMethodCall('addConnection', [
				'config' => [
					'driver'   => 'mysql',
					'host'     => DB_HOST,
					'database' => DB_NAME,
					'username' => DB_USER,
					'password' => DB_PASSWORD,
					'charset'  => DB_CHARSET,
					'prefix'   => $wpdb->prefix,
				]
			])
			->addMethodCall('setAsGlobal');

		// Boot eloquent should be when capsule is initialized.
		$container->get('database')->bootEloquent();


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
	 * Adds the `database()` method that returns shard instance of the Illuminate\Database\Capsule\Manager class.
	 *
	 * @return void
	 * @example Model::where('wp_users', 1)->get();
	 */
	public function bootPlugin()
	{

		$instance = $this;

		$this->getContainer()::macro(
			'database',
			function () use ($instance) {
				return $instance->getContainer()->get('database');
			}
		);

	}

}
