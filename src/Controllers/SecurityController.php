<?php

namespace Netjog\Controllers;

use JetBrains\PhpStorm\NoReturn;
use Netjog\Base\Controller;
use Netjog\Utils\Alert;
use Netjog\Utils\Notice;
use Netjog\Virtualjog;

/**
 * Class SecurityController
 * @package Netjog\Controllers
 *
 * Controller for handling plugin authorization
 * This controller is responsible for handling the authorization process of the plugin
 */
class SecurityController extends Controller
{

    /**
     * Authorize the plugin
     * This function is called when the user tries to authorize the plugin
     *
     * Note: Nonce is also sent in post request, check out \Netjog\Base\Controller::getSanitizedPostData()
     */
    public function authorize($nonce = null): void
    {
        $accessToken = $this->getSanitizedPostData('access_token', 'security_authorize');

        // check if the access token is valid
        // https://api.virtualjog.hu/api/v1/wordpress-authorize
        $response = wp_remote_post(Virtualjog::getApiServiceUrl('wordpress-authorize'), [
            'body' => [
                'access_token' => $accessToken,
            ],
        ]);

        $code = wp_remote_retrieve_response_code($response);
        if ($code !== 200) {
            // wrong access token
            Alert::store('Hibás hozzáférési kód', 'danger');
            wp_redirect('/wp-admin/admin.php?page=virtualjog/authorization-page');
            die();
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        //save access token
        update_option('virtualjog_access_token', $accessToken);
        update_option('virtualjog_client_data', $data['client']);

        //redirect to the dashboard
        wp_redirect('/wp-admin/admin.php?page=virtualjog/account');
        die();
    }

    /**
     * Logout the plugin
     * This function is called when the user tries to log out from the plugin
     */
    public function logout(): void
    {
        delete_option('virtualjog_access_token');
        delete_option('virtualjog_client_data');
        delete_option('virtualjog_cookie_module_script');
        delete_option('virtualjog_cookie_module_domain');
        update_option('virtualjog_cookie_module_enabled', false);
        wp_redirect('/wp-admin/admin.php?page=virtualjog/authorization-page');
        die();
    }
}