<?php

namespace Netjog\Utils;

class Security
{
    public static function enforceWPKernel()
    {
        // check if WP is loaded
        if (!defined('ABSPATH')) {
            die('You can not directly access this file');
        }
    }
}