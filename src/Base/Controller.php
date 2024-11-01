<?php

namespace Netjog\Base;

/**
 * Class Controller
 * @package Netjog\Base
 *
 * Base class for all controllers
 */
class Controller
{
    public string $id;
    private RendererInterface $_renderer;

    private $runtimeNonceChecked = false;

    /**
     * Houses the definitions for the controller
     * Definitions are used to create the admin menu items on WordPress.
     * @return array
     */
    public static function definitions(): array
    {
        return [];
    }

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        // get the short name of the class
        $shortName = (new \ReflectionClass($this))->getShortName();

        // set the id and create a new renderer
        $this->id = strtolower(str_replace('Controller', '', $shortName));
        $this->_renderer = new ViewRenderer($this);

        // start loading definitions into the admin menu
        add_action('admin_menu', [$this, 'wp_admin_menu']);
    }

    /**
     * Creates the admin menu items
     */
    public function wp_admin_menu(): void
    {
        $definitions = static::definitions();

        foreach ($definitions as $key => $definition) {
            add_menu_page(
                $key,
                $key,
                null,
                $key,
                null,
                $definition['icon'],
                1000
            );

            foreach ($definition['items'] as $item) {
                add_submenu_page(
                    $key,
                    $item['title'],
                    $item['title'],
                    'manage_options',
                    $item['slug'],
                    [$this, $item['action']],
                );
            }
        }
    }

    /**
     * Renders the view
     * @param $view
     * @param array $params
     * @throws \Exception
     */
    protected function render($view, array $params = []): void
    {
        $this->_renderer->render($view, $params);
    }

    /**
     * Get sanitized post data
     * @param string $data
     * @param string $action
     * @param string|null $missingFieldError
     * @return string
     */
    protected function getSanitizedPostData(string $data, string $action, string $missingFieldError = null): string
    {
        // if we haven't checked the nonce yet, do it
        if (!$this->runtimeNonceChecked){
            // check nonce
            if (!isset($_POST['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), $action)) {
                wp_send_json_error('Hiányzó form azonosító (_wpnonce)');
            }
            $this->runtimeNonceChecked = true;
        }

        // if the data is not set, return an empty string or send an error
        if (!isset($_POST[$data])) {
            if ($missingFieldError){
                wp_send_json_error($missingFieldError);
            }
            return '';
        }

        return sanitize_text_field(wp_unslash($_POST[$data]));
    }
}