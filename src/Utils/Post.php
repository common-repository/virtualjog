<?php

namespace Netjog\Utils;

/**
 * Class Post
 * @package Netjog\Utils
 *
 * Helper class for handling posts
 */
class Post
{
    /**
     * Returns the GUID by the WP_Post ID
     * @param $guid
     * @return string|null
     */
    public static function getIdByGuid($guid){
        global $wpdb;
        //we need direct database access here, to get the ID by the guid
        //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        return $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid=%s", $guid ));
    }
}