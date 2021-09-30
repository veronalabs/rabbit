<?php // phpcs:ignore WordPress.Files.FileName

namespace Backyard\Blade;

use Backyard\Contracts\BootablePluginProviderInterface;
use Backyard\Exceptions\MissingConfigurationException;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use League\Container\Argument\Literal;

use Illuminate\View\FileViewFinder;
use Illuminate\Filesystem\Filesystem as Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container as Container;
use Illuminate\View\Factory;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\View as View;

/**
 * Registers the Blade templating engine functionality into the plugin.
 */
class BladeServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface, BootablePluginProviderInterface {

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
		'Blade',
		'BladeEngine',
		'Compiler',
		'Factory',
		'EngineResolver',
		'FileViewFinder',
		'FileSystem',
		'Dispatcher',
		'Container',
		'views_path'
	];

	/**
	 * Boot Blade ServiceProvider
	 *
	 * @return void
	 * @throws MissingConfigurationException When the plugin configuration is missing the views_path or the views_cache_path specifications.
	 */
	public function boot() {

		$container = $this->getContainer();

		if ( ! $container->config( 'views_path' ) ) {
			throw new MissingConfigurationException( 'Blade service provider requires "views_path" to be configured.' );
		}

		if ( ! $container->config( 'views_cache_path' ) ) {
			throw new MissingConfigurationException( 'Blade service provider requires "views_cache_path" to be configured.' );
		}

		$container = $this->getContainer();

		$views_dir      = $container->basePath( $container->config( 'views_path' ) );
		$view_cache_dir = $container->basePath( $container->config( 'views_cache_path'));

		$container->share( 'views_path' , $views_dir );
		

		$container
			->share( 'BladeEngine', CompilerEngine::class )
			->addArgument( 'Compiler' );

		$container
			->share( 'Compiler', BladeCompiler::class )
			->addArgument( 'FileSystem')
			->addArgument( $view_cache_dir );

		$container
			->share( 'FileSystem' , Filesystem::class );

		$container
			->share( 'Factory' , Factory::class )
			->addArgument( 'EngineResolver')
			->addArgument('FileViewFinder')
			->addArgument('Dispatcher');
		
		$container
			->share( 'EngineResolver' , EngineResolver::class);

		$container
			->share( 'Dispatcher' , Dispatcher::class )
			->addArgument( 'Container' );

		$container
			->share( 'Container' , Container::class );

		$container
			->share( 'FileViewFinder' , FileViewFinder::class )
			->addArgument( 'FileSystem')
			->addArgument( [$views_dir] );

	}

	/**
	 * Register the blade functionality within the plugin.
	 *
	 * @return void
	 */
	public function register() {


	}

	/**
	 * When the plugin is booted, register a new macro.
	 *
	 * Adds the `blade()` method that returns an instance of the Illuminate\view class.
	 *
	 * @return void
	 */
	public function bootPlugin() {

		$instance = $this;

		$this->getContainer()::macro(
			'Blade',
			function( String $view , Array $data) use ( $instance ) {

				$container = $instance->getContainer();

				$factory           = $container->get('Factory');
				$BladeEngine       = $container->get('BladeEngine');
				$file_view_finder  = $container->get('FileViewFinder');
				$view_file_path    = $file_view_finder->find($view);


				$view_obj = new View(
					$factory,
					$BladeEngine,
					$view,
					$view_file_path,
					$data
				);

				return $view_obj->render();
			}
		);

	}

}
