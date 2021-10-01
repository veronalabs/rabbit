<?php // phpcs:ignore WordPress.Files.FileName

namespace Backyard\DataBase;

use Backyard\Contracts\BootablePluginProviderInterface;
use Backyard\Exceptions\MissingConfigurationException;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Registers the illuminate\Database into the plugin
 */
class DataBaseServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface, BootablePluginProviderInterface {

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
	public function boot() {

		$container = $this->getContainer();

		$container
			->share('database' , Capsule::class )
			->addMethodCall('addConnection' , [
				['driver'    => 'mysql',
				'host'      => DB_HOST,
				'database'  => DB_NAME,
				'username'  => DB_USER,
				'password'  => DB_PASSWORD,
				'charset'   => DB_CHARSET]
			])
			->addMethodCall('setAsGlobal')
			->addMethodCall('bootEloquent');


	}

	/**
	 * @return void
	 */
	public function register() {


	}

	/**
	 * When the plugin is booted, register a new macro.
	 *
	 * Adds the `Database()` method that returns shard instance of the Illuminate\Database\Capsuel\Manager class.
	 *
	 * @return void
	 */
	public function bootPlugin() {

		$instance = $this;

		$this->getContainer()::macro(
			'database',
			function() use ( $instance ) {
				return $instance->getContainer()->get( 'database' );
			}
		);

	}

}
