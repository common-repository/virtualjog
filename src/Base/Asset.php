<?php

namespace Netjog\Base;

use Netjog\Virtualjog;

/**
 * Class Asset
 * @package Netjog\Base
 *
 * Contains the logic for handling assets
 * Please refer to AssetController.php for the implementation
 *
 * TODO: Implement a way to handle any asset (css, js, pdf, etc...)
 */
class Asset
{
    /**
     * Get an image asset
     * @param $asset
     * @return string
     * @throws \Exception
     */
    public static function getImage($asset): string
    {
        // get the config file
        $config = require Virtualjog::$pluginSourcePath . '/Config/app.php';

        // get the image assets
        // TODO: refactor hardcoded images
        $imageAssets = $config['assets']['images'];

        // check if the asset exists
        if(!isset($imageAssets[$asset])){
            throw new \Exception('Image asset not found: ' . esc_html($asset));
        }

        // we return an AssetController url
        return esc_url($imageAssets[$asset]);
    }
}