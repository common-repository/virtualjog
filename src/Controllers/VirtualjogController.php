<?php

namespace Netjog\Controllers;

use Netjog\Base\Asset;
use Netjog\Base\Controller;
use Netjog\Utils\Post;
use Netjog\Virtualjog;

/**
 * Class VirtualjogController
 * @package Netjog\Controllers
 *
 * Controller for handling the Virtualjog plugin
 */
class VirtualjogController extends Controller
{
    /**
     * Get the menu definitions
     * This function is called when the plugin is loaded
     * @return array
     * @throws \Exception
     */
    public static function definitions(): array
    {
        // check if the user is authorized and return the correct menu items
        $virtualjogClientData = get_option('virtualjog_client_data', false);

        if (!$virtualjogClientData) {
            return [
                'Virtualjog' => [
                    'icon' => Asset::getImage('admin-menu-icon'),
                    'items' =>[
                        [
                            'title' => 'Bejelentkezés',
                            'slug' => 'virtualjog/authorization-page',
                            'action' => 'authorizationPage',
                        ],
                    ]
                ]
            ];
        }else{
            return self::buildAuthorizedMenuItems($virtualjogClientData);
        }
    }

    /**
     * Build the menu items for the authorized user
     * @param array $virtualjogClientData
     * @return array
     * @throws \Exception
     */
    public static function buildAuthorizedMenuItems(array $virtualjogClientData): array
    {
        $menuItems = [
            'Virtualjog' => [
                'icon' => Asset::getImage('admin-menu-icon'),
            ]
        ];

        $menuItems['Virtualjog']['items'][] = [
            'title' => 'Fiók',
            'slug' => 'virtualjog/account',
            'action' => 'accountPage',
        ];

        $menuItems['Virtualjog']['items'][] = [
            'title' => 'Dokumentumok',
            'slug' => 'virtualjog/documents',
            'action' => 'documentsPage',
        ];

        // subscriptionEndDate = 2025-04-03T15:35:53+02:00 ISO 8601
        foreach ($virtualjogClientData['packages'] as $package){
            $subscriptionEndDate = new \DateTime($package['subscriptionEndDate']);
            $now = new \DateTime();
            if ($package['slug'] === 'cookie-panel' && $subscriptionEndDate > $now){
                $menuItems['Virtualjog']['items'][] = [
                    'title' => 'Süti modul',
                    'slug' => 'virtualjog/cookie',
                    'action' => 'cookiePage',
                ];
            }
        }

        return $menuItems;
    }

    /**
     * Authorization page
     * Contains the access token form
     */
    public function authorizationPage(){
        $this->render('authorize');
    }

    /**
     * Account page
     * Contains the client data
     */
    public function accountPage(){
        $virtualjogClientData = get_option('virtualjog_client_data', false);
        $this->render('account', [
            'virtualjogClientData' => $virtualjogClientData
        ]);
    }

    /**
     * Documents page
     * Contains the list of documents that can be embedded on the site
     */
    public function documentsPage(){

        // fetch the legal documents connected to the client from the API
        // https://api.virtualjog.hu/api/v1/wordpress-document-list
        $response = wp_remote_get(Virtualjog::getApiServiceUrl('wordpress-document-list'), [
            'body' => [
                'access_token' => get_option('virtualjog_access_token')
            ]
        ]);

        $data = json_decode(wp_remote_retrieve_body($response), true);
        $documents = $data['documents'];

        foreach ($documents as &$document){
            // check for inserted documents
            $post_id = Post::getIdByGuid(home_url('/'.$document['slug']));
            $document['isInserted'] = $post_id ? true : false;
        }

        $this->render('documents', [
            'documents' => $documents,
        ]);
    }

    /**
     * Cookie page
     * Contains the cookie module settings
     */
    public function cookiePage(){
        $domains = [];

        $currentDomain = '';

        if (isset($_SERVER['SERVER_NAME'])){
            $currentDomain = sanitize_text_field(wp_unslash($_SERVER['SERVER_NAME']));
        }

        $currentDomain = 'www.kockafalu.hu';

        $cookieModuleDomain = get_option('virtualjog_cookie_module_domain', false);
        $cookieModuleEnabled = get_option('virtualjog_cookie_module_enabled', false);
        $cookieModuleAllowed = (bool) $cookieModuleDomain;

        if (!$cookieModuleDomain){
            // fetch the clients websites from the API
            // https://api.virtualjog.hu/api/v1/wordpress-valid-domains
            $response = wp_remote_get(Virtualjog::getApiServiceUrl('wordpress-valid-domains'), [
                'body' => [
                    'access_token' => get_option('virtualjog_access_token')
                ]
            ]);

            $data = json_decode(wp_remote_retrieve_body($response), true);
            $domains = $data['domains'];

            // check if currentdomain is somehow available in the list
            foreach ($domains as $domain){
                if (strpos($domain, $currentDomain) !== false){
                    $cookieModuleAllowed = true;
                    update_option('virtualjog_cookie_module_domain', $currentDomain);
                    break;
                }
            }
        }

        $this->render('cookie',[
            'domains' => $domains,
            'currentDomain' => $currentDomain,
            'cookieModuleAllowed' => $cookieModuleAllowed,
            'cookieModuleEnabled' => $cookieModuleEnabled
        ]);
    }
}