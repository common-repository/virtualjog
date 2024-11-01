<?php
/**
 * Plugin main file.
 *
 * PLEASE NOTE
 * This plugin is intended for hungarian clients; there are no translations available.
 * The plugin is a legaltech tool that helps to generate legal documents for websites.
 *
 * Future plans:
 * - refactor the plugin to use assets (legal documents, pdf, image) directly from the Virtualjog API (see AssetController.php)
 *
 * @package   Netjog
 * @copyright 2024 Net-jog.hu Kft.
 * @license   GPL-3.0-or-later
 * @link      https://virtualjog.hu
 *
 * @wordpress-plugin
 * Plugin Name:       Virtualjog
 * Plugin URI:        https://virtualjog.hu/virtualjog-worpress-bovitmeny/
 * Description:       Sokkal több, mint egy ÁSZF generátor. ÁSZF és GDPR modulok, adatvédelmi tájékoztató, ÁSZF készítése, adatvédelmi szabályzat készítése, webshop szabályzatok, ÁSZF kezelő, GDPR kezelő, legaltech, ügyvédi szoftver.
 * Version:           1.0.5
 * Requires at least: 6.0
 * Tested up to:      6.6.1
 * Requires PHP:      7.4
 * Author:            Net-jog.hu Kft.
 * Author URI:        https://virtualjog.hu
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       virtualjog
 */

// prohibit direct access
if (!defined('ABSPATH')) {
    die('You can not directly access this file');
}

require_once plugin_dir_path(__FILE__) . '/src/Virtualjog.php';

// initialize plugin
add_action('init', function (){
    (new Netjog\Virtualjog())->init();
});

// add extra css for admin ( mimicking the same bootstrap sb-admin style here that is used in the official software
add_action('admin_enqueue_scripts', function (){
    wp_enqueue_style('virtualjog', plugin_dir_url(__FILE__) . 'src/Resources/css/virtualjog.css', [], '1.0.2');
});

// registering the cookie module script, if enabled
add_action('wp_head', function (){
    $script = get_option('virtualjog_cookie_module_script', false);

    if ($script) {
        echo wp_kses($script, [
            'script' => [
                'src' => true,
                'type' => true,
                'async' => true,
                'defer' => true,
                'id' => true,
                'access-token' => true,
                'domain' => true,
                'serverside' => true
            ]
        ]);
    }
});

// preprocessing scripts, removing tracking scripts if cookie module is enabled
// we are doing this, to allow our cookie module to disable tracking scripts if the user does not allow them
// NOTE: when our cookie module (js runtime based) is inserted without the use of this plugin, this feature won't disable tracking scripts
function netjog_virtualjog_preProcessScripts()
{
    // check option virtualjog_cookie_module_enabled
    if (!get_option('virtualjog_cookie_module_enabled', false)) {
        return;
    }

    // load global stuff
    global $wp_scripts;
    $queue = $wp_scripts->queue;

    // define categories for providers
    $allowBaseProviders = true; // basic site cookies
    $allowStatProviders = false; // Google Analytics, etc.
    $allowMarketingProviders = false; // facebook, etc.
    $allowOtherProviders = false; // banner based tracking software

    // define cookie handles
    $cookieProviderHandles = [
        'allowStatProviders' => 'vjog_allow_stat_providers',
        'allowMarketingProviders' => 'vjog_allow_marketing_providers',
        'allowOtherProviders' => 'vjog_allow_other_providers'
    ];

    // define provider handles
    $connectProviders = [
        'allowStatProviders' => 'statProviders',
        'allowMarketingProviders' => 'marketingProviders',
        'allowOtherProviders' => 'otherProviders'
    ];

    // check for current session cookies
    foreach ($cookieProviderHandles as $key => $value) {
        if (isset($_COOKIE[$value])) {
            $$key = (bool) $_COOKIE[$value];
        }
    }

    // TODO: refactor this feature, lets specify the providers on our side.

    // define providers
    $statProviders = [
        'googlesitekit',
        'google_gtagjs'
    ];

    $marketingProviders = [
        'facebook'
    ];

    $otherProviders = [
        'doubleclick',
        'adsbygoogle'
    ];

    // we're going to disable all providers that are not allowed
    $disabledProviders = [];

    foreach ($connectProviders as $key => $value) {
        if (!$$key) {
            $disabledProviders = array_merge($disabledProviders, $$value);
        }
    }

    // and we process the queue, removing the providers
    foreach ($queue as $key => $value) {
        foreach ($disabledProviders as $provider) {
            if (strpos($value, $provider) !== false) {
                unset($wp_scripts->queue[$key]);
            }
        }
    }

}

// add the preProcessScripts function to the wp_enqueue_scripts action
add_action('wp_enqueue_scripts','netjog_virtualjog_preProcessScripts',999);


