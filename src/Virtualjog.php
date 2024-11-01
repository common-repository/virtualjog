<?php

namespace Netjog;

use Netjog\Utils\Alert;

/**
 * Class Virtualjog
 * @package Netjog
 *
 * Main class for the plugin, contains the autoloader and the main logic
 */
class Virtualjog
{
    /**
     * The root path of the plugin (for file access)
     * @deprecated
     * @var string
     */
    public static string $pluginRootPath;

    /**
     * The source path of the plugin (for file access)
     * @var string
     */
    public static string $pluginSourcePath;

    /**
     * The web path of the plugin
     * @var string
     */
    public static string $pluginWebPath;

    /**
     * The API URL to the virtualjog service
     * Please refer to the API.md file for more information
     * @var string
     */
    public static string $apiUrl = 'https://api.virtualjog.hu/api/v1/';

    /**
     * Get the API service URL
     * @param $service
     * @return string
     */
    public static function getApiServiceUrl($service): string
    {
        return self::$apiUrl . $service;
    }

    /**
     * Virtualjog constructor.
     * When the plugin is loaded, we initialize the autoloader
     */
    public function __construct()
    {
        // initialize plugin
        static::$pluginSourcePath = plugin_dir_path(__FILE__);

        static::$pluginWebPath = plugin_dir_url(__FILE__);

        static::$pluginRootPath = dirname(plugin_dir_path(__FILE__));

        // register autoloader
        spl_autoload_register([$this, 'autoload']);
    }

    /**
     * Autoloader for the plugin
     * When php is searching for a Netjog namespace, it will look in our src folder
     * @param $class
     */
    public function autoload($class): void
    {
        // everything under Netjog is in the src folder
        $prefix = 'Netjog\\';
        $base_dir = static::$pluginSourcePath;

        $len = strlen($prefix);

        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }

        $relative_class = substr($class, $len);

        $file = $base_dir . '/' . str_replace('\\', '/', $relative_class) . '.php';

        if (file_exists($file)) {
            require $file;
        }
    }

    /**
     * Initialize the plugin
     * This function is called when the plugin is loaded
     * Refer to the virtualjog.php file for the action hook
     */
    public function init(): void
    {
        // hook stuff here
        Alert::release();

        // initialize the admin menu
        $this->initAdminMenu();

        // wp plugin check made me do this :(
        if (isset($_SERVER['REQUEST_URI'])) {
            // get the request uri
            $requestURI = sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI']));

            //remove query string
            $requestURI = strtok($requestURI, '?');

            //remove trailing slash
            $requestURI = rtrim($requestURI, '/');

            // check if we need to handle the request
            if (array_key_exists($requestURI, $this->getUrls()['urls'])) {
                $config = $this->getUrls()['urls'][$requestURI];
                $this->handleRequest($config);
            }
        }
    }

    /**
     * Initialize the admin menu
     * This function is called when the plugin is loaded
     * It loads all the controllers, and reads all the menu items from the definitions.
     * All the menu items are actually defined in VirtualjogController::definitions()
     */
    public function initAdminMenu(): void
    {
        // load all the controllers
        $controllers = glob(static::$pluginSourcePath . '/Controllers/*.php');

        foreach ($controllers as $controllerName) {
            $controllerName = str_replace('.php', '', $controllerName);
            $controllerName = str_replace(static::$pluginSourcePath . '/Controllers/', '', $controllerName);
            $controller = 'Netjog\\Controllers\\' . $controllerName;

            // we just need the constructor to run
            (new $controller());
        }
    }

    /**
     * Handle the request
     * This function is called when the plugin is loaded
     * It reads the config, and calls the controller and the action
     * @param $config
     */
    public function handleRequest($config)
    {
        if ($config['role'] === 'admin' && !current_user_can('administrator')) {
            wp_send_json_error('Unauthorized');
        }

        $controller = $config['controller'];
        $action = $config['action'];

        // instantiate the controller
        $controller = new $controller();

        // check for get params, and feed them to the action
        // we're also ignoring the nonce check here, because we're only handling our own defined routes here
        call_user_func_array([$controller, $action], $_GET); //phpcs:ignore WordPress.Security.NonceVerification.Recommended
    }

    /**
     * Get the urls from the config
     * @return mixed
     */
    public function getUrls()
    {
        // get the urls from the config
        return require static::$pluginSourcePath . '/Config/app.php';
    }
}