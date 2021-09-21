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
	 * Add the file system loader into the plugin.
	 *
	 * The createTwigCacheFolder() method creates a subfolder within the wp-uploads folder
	 * where cache files generated for twig templates are stored.
	 *
	 * The deleteTwigCacheFolder() method deletes the folder previously created.
	 *
	 * @return void
	 * @throws MissingConfigurationException When the plugin configuration is missing the views_path specification.
	 */
	public function boot() {

		$container = $this->getContainer();

		if ( ! $container->config( 'views_path' ) ) {
			throw new MissingConfigurationException( 'Blade service provider requires "views_path" to be configured.' );
		}

	}

	/**
	 * Register the twig functionality within the plugin.
	 *
	 * @return void
	 */
	public function register() {

		$container = $this->getContainer();

		$views_dir = $container->basePath( $container->config( 'views_path' ) );

		$container->share( 'views_path' , $views_dir );
		

		$container
			->add( 'BladeEngine', CompilerEngine::class )
			->addArgument( 'Compiler' );

		$container
			->add( 'Compiler', BladeCompiler::class )
			->addArgument( 'FileSystem')
			->addArgument( $views_dir );

		$container
			->share( 'FileSystem' , Filesystem::class );

		$container
			->add( 'Factory' , Factory::class )
			->addArgument( 'EngineResolver')
			->addArgument('FileViewFinder')
			->addArgument('Dispatcher');
		
		$container
			->add( 'EngineResolver' , EngineResolver::class);

		$container
			->add( 'Dispatcher' , Dispatcher::class )
			->addArgument( 'Container' );

		$container
			->add( 'Container' , Container::class );

		$container
			->add( 'FileViewFinder' , FileViewFinder::class )
			->addArgument( 'FileSystem')
			->addArgument( [$views_dir] );

	}

	/**
	 * When the plugin is booted, register a new macro.
	 *
	 * Adds the `twig()` method that returns an instance of the Twig\Environment class.
	 *
	 * @return void
	 */
	public function bootPlugin() {

		$instance = $this;

		$this->getContainer()::macro(
			'Blade',
			function( String $view , Array $data) use ( $instance ) {

				$container = $instance->getContainer();

				$factory     = $container->get('Factory');
				$BladeEngine = $container->get('BladeEngine');
				$views_path   = $container->get('views_path');

				$view_obj = new View(
					$factory,
					$BladeEngine,
					$view,
					$views_path.'/test.blade.php',
					$data
				);

				return $view_obj;
			}
		);

	}

}
