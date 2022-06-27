# Rabbit Framework - A modern way of building WordPress plugins.

[![Total Downloads](https://img.shields.io/packagist/dt/veronalabs/framework.svg)](https://packagist.org/packages/veronalabs/framework)
[![Latest Stable Version](https://img.shields.io/packagist/v/veronalabs/framework.svg)](https://packagist.org/packages/veronalabs/framework)


## About
Rabbit Framework is a modern framework designed to be a solid foundation for your WordPress plugins. the project is forked from [Backyard](https://backyard.sematico.com/)

## Benefits

1. Dependency injection container & service providers.
2. template that helps you load the view easily.
3. OOP Nonces wrapper & Factory methods.
4. Caching helpers.
5. HTTP Redirects with admin notices flashing.
6. Macroable classes.
7. [LoggerWP](https://github.com/veronalabs/logger-wp)
8. Illuminate Database (Eloquent)

## Requirements

1. PHP 7.4 or higher.
2. Composer

## Usage

```bash
composer require veronalabs/rabbit
```



Plugin header fields
--------------------

When creating a WordPress plugin, you are required to add [header fields](https://developer.wordpress.org/plugins/plugin-basics/header-requirements/) to your plugin's entry file. The Rabbit framework requires 1 additional field: `Plugin Prefix`.

The `Plugin Prefix` field is used by the framework to automatically [define constants](/docs/constants) about the plugin.

    /**
     * Plugin Name:     Example plugin
     * Plugin URI:      https://example.com
     * Plugin Prefix:   TD
     * Description:     Description
     * Author:          Alessandro Tesoro
     * Author URI:      https://example.com
     * Text Domain:     example-plugin
     * Domain Path:     /languages
     * Version:         0.1.0
     */
    


Load Composer autoloader
------------------------

Copy and paste the code below right after the header fields to load all the dependencies of your plugin, including the Rabbit framework.

    if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
        require dirname( __FILE__ ) . '/vendor/autoload.php';
    }
    


Create a new Application instance
---------------------------------

Every Rabbit powered plugin must create an instance of a Rabbit Application. The application is responsible for the loading of the plugin.

Create a new application instance by using the `get()` method.

    $myPlugin = Application::get();
    


The plugin container
--------------------

The framework handles a plugin through the `Plugin` class. The `Plugin` class is an extension of the [PHP League dependency injection container](https://container.thephpleague.com/). The container is responsible for the loading of configuration files, the registration & booting process of service providers and more.


Load your plugin into the application
-------------------------------------

After the instantiation of a Rabbit Application, you need to load your plugin into the application via the `loadPlugin()` method.

The `loadPlugin()` method takes 3 arguments, **the third is optional**.

1.  The path to the plugin.
2.  The path to plugin's entry file.
3.  The name of the folder that holds the configuration files.

    $myPlugin = $myPlugin->loadPlugin( __DIR__, __FILE__, 'config' );
    

The `loadPlugin()` method returns the `Plugin` container. You will then use the container to add functionalities to your plugin.


Configuration files
-------------------

Configuration files provide an easy way to set options required by parts of application to work. Values can then be easily accessed by the plugin. A configuration file returns an associative array.

    $myPlugin->config( 'key' );
    

Use the `config()` method to access values. Keys use dot notation starting with the name of the configuration file. Take a look at the example below:

File: `config/plugin.php`

    <?php
    
    $config = [
        'my_key' => 'my_value',
    ];
    

In order to access the `my_key` value, you need to call the `config()` method this way:

    $value = $myPlugin->config( 'my_key' );
    

> Configuration files are used by some features provided by Rabbit.


Add service providers
---------------------

Service providers in Rabbit are used to add functionalities to your plugin. Providers can be loaded by using the `addServiceProvider()` method of the container class.

    $myPlugin->addServiceProvider( RedirectServiceProvider::class );
    


The activation & deactivation hook
----------------------------------

Activation and deactivation hooks provide ways to execute actions when the plugin is activated or deactivated. If you're familiar [with these hooks](https://developer.wordpress.org/plugins/plugin-basics/activation-deactivation-hooks/), you may remember they are called with the `register_activation_hook` and `register_deactivation_hook` functions.

In Rabbit you can register the activation and deactivation process by using the `onActivation` and `onDeactivation` methods of the `Plugin` class. Both methods use a [Closure](https://www.php.net/manual/en/class.closure.php) as argument.

    $myPlugin->onActivation(
        function() use ( $myPlugin ) {
            // Do something on activation here like update_option()
        }
    );
    

    $myPlugin->onDeactivation(
        function() use ( $myPlugin ) {
            // Do something on deactivation here
        }
    );
    


Boot the plugin
---------------

Use the `boot()` method of the `Plugin` class to boot your plugin. The `boot()` method uses a Closure that allows you to add more functionalities to your plugin.

When booting, the plugin boots the registered service providers on the `plugins_loaded` hook.

To boot your plugin use the following snippet:

    $myPlugin->boot(
        function( $plugin ) {
            // Do something when the plugins_loaded hook is fired.
        }
    );
    


Load plugin textdomain
----------------------

If your plugin requires localization, you can use the `loadPluginTextDomain` method inside the `boot` method. The framework will use the information in your header fields to set the correct textdomain and languages folder path.

    $myPlugin->boot(
        function( $plugin ) {
            $plugin->loadPluginTextDomain();
        }
    );
    

Include files
-------------

The Rabbit framework comes with an easy way to `include()` files in your WordPress plugin. Use the `includes()` method of the `Plugin` class to include files from a specific folder. Files are loaded alphabetically.

    $myPlugin->boot(
        function( $plugin ) {
            $plugin->includes( 'includes' );
        }
    );
    

The example above will automatically include *.php files from the `includes` subfolder of your plugin.


Example entry file
------------------

    <?php
    /**
     * Plugin Name:     Rabbit example plugin
     * Plugin URI:      https://example.com
     * Plugin Prefix:   TD
     * Description:     Example plugin
     * Author:          John Doe
     * Author URI:      https://example.me
     * Text Domain:     example-plugin
     * Domain Path:     /languages
     * Version:         0.1.0
     */
    
    use Rabbit\Application;
    use Rabbit\Redirects\RedirectServiceProvider;
    
    if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
        require dirname( __FILE__ ) . '/vendor/autoload.php';
    }
    
    $myPlugin = ( Application::get() )->loadPlugin( __DIR__, __FILE__, 'config' );
    
    $myPlugin->addServiceProvider( RedirectServiceProvider::class );
    
    $myPlugin->onActivation(
        function() use ( $myPlugin ) {
            // do something on activation
        }
    );
    
    $myPlugin->onDeactivation(
        function() use ( $myPlugin ) {
            // do something on deactivation
        }
    );
    
    $myPlugin->boot(
        function( $plugin ) {
            $plugin->loadPluginTextDomain();
        }
    );





## License

Distributed under the MIT License. See `LICENSE` for more information.
