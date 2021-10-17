<?php

namespace Backyard\RestApi;

use Backyard\Exceptions\MissingConfigurationException;
use Backyard\Application;

class Route
{
    /**
     * Route's address
     *
     * @var string
     */
    protected $route;

    /**
     * Routes method
     *
     * @var string
     */
    protected $method;

    /**
     * Route's namespace
     *
     * @var string
     */
    protected $restNameSpace;

    /**
     * Nonce filed name in inbound ajax request.
     *
     * @var string
     */
    protected $nonceFieldName;

    /**
     * Route's nonce handle name
     *
     * @var string
     */
    protected $nonceHandle;

    /**
     * Route's endpoint callback
     *
     * @var string
     */
    protected $endpoint;

    /**
     * Route's permission
     *
     * @see https://wordpress.org/support/article/roles-and-capabilities/
     * @var string
     */
    protected $permission;

    /**
     * Constructor
     */
    public function __construct()
    {
        $plugin = Application::get()->plugin;

        $restNameSpace  = $plugin->config('rest_namespace');
        $nonceFieldName = $plugin->config('rest_nonce_field_name');

        if (! $restNameSpace) {
            throw new MissingConfigurationException('Rest api service requires "rest_namespace" to be configured.');
        }
        if (! $nonceFieldName) {
            throw new MissingConfigurationException('Rest api service requires "rest_nonce_field_name" to be configured.');
        }

        $this->restNameSpace  = $restNameSpace;
        $this->nonceFieldName = $nonceFieldName;
    }

    /**
     * Register a get route
     *
     * @param string $route
     * @param callable $endpoint
     * @param string|null $permission
     * @param string|null $nonceHandle
     * @return void
     */
    public static function get(string $route, callable $endpoint, string $permission = null, string $nonceHandle = null)
    {
        $instance = new self;
        
        $instance->method      = 'GET';
        $instance->route       = $route;
        $instance->endpoint    = $endpoint;
        $instance->permission  = $permission;
        $instance->nonceHandle = $nonceHandle;

        $instance->register();
    }

    /**
     * Register a post route
     *
     * @param string $route
     * @param callable $endpoint
     * @param string|null $permission
     * @param string|null $nonceHandle
     * @return void
     */
    public static function post(string $route, callable $endpoint, string $permission = null, string $nonceHandle = null)
    {
        $instance = new self;
        
        $instance->method      = 'POST';
        $instance->route       = $route;
        $instance->endpoint    = $endpoint;
        $instance->permission  = $permission;
        $instance->nonceHandle = $nonceHandle;

        $instance->register();
    }
    /**
     * Register the obj
     *
     * @return void
     */
    protected function register()
    {
        $instance = $this;

        add_action(
            'rest_api_init',
            function () use ($instance) {
                register_rest_route(
                    $instance->restNameSpace,
                    $instance->route,
                    [
                        'methods'             => $instance->method,
                        'callback'            => $instance->endpoint,
                        'permission_callback' => [$instance , 'permissionCallback'],
                    ]
                );
            },
            10,
            0
        );
    }

    /**
     * Check user permission
     *
     * @return WP_Error|true
     */
    public function permissionCallback()
    {
        if (isset($this->permission) && !user_can($this->permission)) {
            return new WP_Error('401', 'Not Authorized', array( 'status' => 401 ));
        }
        if (isset($this->nonceHandle) && !check_ajax_referer($this->nonceHandle, $this->nonceFieldName)) {
            return new WP_Error('403', 'Not Authorized', array( 'status' => 403 ));
        }
        return true;
    }
}
