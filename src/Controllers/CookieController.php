<?php

namespace Netjog\Controllers;

use Netjog\Base\Controller;
use Netjog\Virtualjog;

/**
 * Class CookieController
 * @package Netjog\Controllers
 *
 * Controller for handling cookies
 */
class CookieController extends Controller
{

    /**
     * This method will enable the cookie module
     */
    public function enableCookieModule(): void
    {
        $cookieModuleDomain = get_option('virtualjog_cookie_module_domain', false);

        // fetch the WordPress serverside modified cookie script from the API
        // we need the access token and the domain to get the exact script
        // https://api.virtualjog.hu/api/v1/wordpress-cookie-script
        $response = wp_remote_post(Virtualjog::getApiServiceUrl('wordpress-cookie-script'), [
            'body' => [
                'access_token' => get_option('virtualjog_access_token'),
                'domain' => $cookieModuleDomain
            ]
        ]);

        $data = json_decode(wp_remote_retrieve_body($response), true);
        $script = $data['script'];

        update_option('virtualjog_cookie_module_script', $script);
        update_option('virtualjog_cookie_module_enabled', true);
        // redirect to virtualjog/cookie page
        wp_redirect(admin_url('admin.php?page=virtualjog/cookie'));
        die();
    }

    /**
     * This method will disable the cookie module
     */
    public function disableCookieModule(): void
    {
        delete_option('virtualjog_cookie_module_script');
        delete_option('virtualjog_cookie_module_domain');
        update_option('virtualjog_cookie_module_enabled', false);
        // redirect to virtualjog/cookie page
        wp_redirect(admin_url('admin.php?page=virtualjog/cookie'));
        die();
    }

    /**
     * This method is called from the frontend by our embedded cookie module.
     * When called it will set the cookie for the allowed providers,
     * and return the headers so at the next reload the plugin could enable the tracking scripts
     */
    public function allowProviders(): void
    {
        $cookieProviderHandles = [
            'allowStatProviders' => 'vjog_allow_stat_providers',
            'allowMarketingProviders' => 'vjog_allow_marketing_providers',
            'allowOtherProviders' => 'vjog_allow_other_providers'
        ];

        // get the request body and read the json
        $body = file_get_contents('php://input');
        $data = json_decode($body, true);

        foreach ($cookieProviderHandles as $key => $value) {

            if (isset($data[$key]) && $data[$key] === true) {
                setcookie($value, 1, time() + 3600, '/');
            }

            if (isset($data[$key]) && $data[$key] === false) {
                setcookie($value, 0, time() + 3600, '/');
            }
        }

        // use header to redirect back to the previous page
        // NOTE: we don't actually redirect, we just need a way to return the headers with the cookies
        if (isset($_SERVER['HTTP_REFERER'])){
            header('Location: ' . sanitize_text_field(wp_unslash($_SERVER['HTTP_REFERER'])));
        }

        // stop the script
        die();
    }
}