<?php

namespace Netjog\Controllers;

use JetBrains\PhpStorm\NoReturn;
use Netjog\Base\Controller;

/**
 * Class DocumentController
 * @package Netjog\Controllers
 *
 * Controller for handling documents
 */
class DocumentController extends Controller
{
    /**
     * This function will embed the document into a page
     * @return void
     *
     * Note: Nonce is also sent in post request, check out \Netjog\Base\Controller::getSanitizedPostData()
     */
    public function insertDocument($nonce = null): void
    {
        $documentName = $this->getSanitizedPostData('documentName', 'document_insert');
        $documentSlug = $this->getSanitizedPostData('documentSlug', 'document_insert');
        $embedUrl = $this->getSanitizedPostData('embedUrl', 'document_insert');

        $iframe = '<iframe style="border: none;" src="'.$embedUrl.'" width="100%" height="1000px"></iframe>';

        $page = [
            'post_title' => $documentName,
            'post_content' => $iframe,
            'post_status' => 'publish',
            'post_author' => get_current_user_id(),
            'post_type' => 'page',
            'guid' => home_url('/'.$documentSlug),
        ];

        $pageId = wp_insert_post($page);

        if (is_wp_error($pageId)) {
            // error occurred during document creation
            wp_send_json_error('Hiba történt a dokumentum létrehozása közben');
        }

        wp_redirect('/wp-admin/admin.php?page=virtualjog/documents');
        die();
    }
}