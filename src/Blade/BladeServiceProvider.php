<?php // phpcs:ignore WordPress.Files.FileName

namespace Backyard\Blade;

use Backyard\Contracts\BootablePluginProviderInterface;
use Backyard\Exceptions\MissingConfigurationException;
use Illuminate\Container\Container as Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem as Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;
use Illuminate\View\View as View;
use League\Container\Argument\Literal;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

/**
 * Registers the Blade templating engine functionality into the plugin.
 */
class BladeServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface, BootablePluginProviderInterface
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
        CompilerEngine::class,
        BladeCompiler::class,
        Factory::class,
        EngineResolver::class,
        FileViewFinder::class,
        Filesystem::class,
        Dispatcher::class,
        Container::class,
    ];

    /**
     * Boot Blade ServiceProvider
     *
     * @return void
     * @throws MissingConfigurationException When the plugin configuration is missing the views_path or the views_cache_path specifications.
     */
    public function boot()
    {
        $container = $this->getContainer();

        if (!$container->config('views_path')) {
            throw new MissingConfigurationException('Blade service provider requires "views_path" to be configured.');
        }

        if (!$container->config('views_cache_path')) {
            throw new MissingConfigurationException('Blade service provider requires "views_cache_path" to be configured.');
        }

        $container = $this->getContainer();

        $viewsDir      = $container->basePath($container->config('views_path'));
        $ViewsCacheDir = $container->basePath($container->config('views_cache_path'));


        $container
            ->share(CompilerEngine::class)
            ->addArgument(BladeCompiler::class);

        $container
            ->share(BladeCompiler::class)
            ->addArgument(Filesystem::class)
            ->addArgument($ViewsCacheDir);

        $container
            ->share(Filesystem::class);

        $container
            ->share(Factory::class)
            ->addArgument(EngineResolver::class)
            ->addArgument(FileViewFinder::class)
            ->addArgument(Dispatcher::class);

        $container
            ->share(EngineResolver::class);

        $container
            ->share(Dispatcher::class)
            ->addArgument(Container::class);

        $container
            ->share(Container::class);

        $container
            ->share(FileViewFinder::class)
            ->addArgument(Filesystem::class)
            ->addArgument([$viewsDir]);
    }

    /**
     * Register the blade functionality within the plugin.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * When the plugin is booted, register a new macro.
     *
     * Adds the `blade()` method that returns an instance of the Illuminate\view class.
     *
     * @return void
     */
    public function bootPlugin()
    {
        $instance = $this;

        $this->getContainer()::macro(
            'blade',
            function (string $view, array $data) use ($instance) {
                $container = $instance->getContainer();

                $factory        = $container->get(Factory::class);
                $bladeEngine    = $container->get(CompilerEngine::class);
                $fileViewFinder = $container->get(FileViewFinder::class);
                $viewFilePath   = $fileViewFinder->find($view);


                $view_obj = new View(
                    $factory,
                    $bladeEngine,
                    $view,
                    $viewFilePath,
                    $data
                );

                return $view_obj->render();
            }
        );
    }
}
