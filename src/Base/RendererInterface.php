<?php

namespace Netjog\Base;

/**
 * Interface RendererInterface
 * @package Netjog\Base
 *
 * Interface for rendering stuff (views, commands, templates)
 */
interface RendererInterface
{
    /**
     * @param $view
     * @param array $params
     */
    public function render($view, array $params = []): void;
}