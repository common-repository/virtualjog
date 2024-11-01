<?php

namespace Netjog\Base;

use Netjog\Virtualjog;

/**
 * Class ViewRenderer
 * @package Netjog\Base
 *
 * Contains the logic for rendering views
 */
class ViewRenderer implements RendererInterface
{
    /**
     * @var $_controller Controller
     */
    private Controller $_controller;

    /**
     * ViewRenderer constructor.
     * @param Controller $controller
     */
    public function __construct(Controller $controller)
    {
        $this->_controller = $controller;
    }

    /**
     * Render a view
     *
     * Tries to render a view file from the plugin's Resources/views folder
     * It basically opens a buffer, requires the view file and returns the content.
     * Refer to the php documentation for extracting variables here: https://www.php.net/manual/en/function.extract.php
     * @param $view
     * @param array $params
     * @throws \Exception
     */
    public function render($view, array $params = []): void
    {
        // get the base path, controller id and view path
        $basePath = Virtualjog::$pluginSourcePath . '/Resources/views/';
        $controllerID = $this->_controller->id;
        $viewPath = $basePath.$controllerID.'/'.$view.'.php';

        // check if the view file exists
        if(!file_exists($viewPath)){
            throw new \Exception('View not found: ' . esc_html($viewPath) );
        }

        // start the buffer, load variables into symbol table, require the view and get the content
        ob_start();
        extract($params);
        require $viewPath;
        $content = ob_get_clean();

        global $allowedposttags;
        $allowedposttags['form'] = [
            'action' => true,
            'method' => true,
            'class' => true,
            'id' => true,
            'name' => true,
            'value' => true,
            'type' => true,
            'placeholder' => true,
            'style' => true,
            'target' => true,
        ];

        $allowedposttags['input'] = [
            'type' => true,
            'name' => true,
            'value' => true,
            'placeholder' => true,
            'style' => true,
            'class' => true,
        ];

        // dump the content
        echo wp_kses_post($content);
    }
}