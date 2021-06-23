<?php

namespace Motto\InstagramMediaLibrary;

use InstagramScraper\Instagram;
use InstagramScraper\Model\Media;

class RemoteUserMedia {
    
    private $username;
    private $scraper;
    private $allSaved = false;

    public function __construct( $username )
    {
        $this->username = $username;
        $this->scraper = $this->initScraper();
    }

    private function initScraper()
    {
        $scraper = new Instagram(new \GuzzleHttp\Client());
        // $scraper->setRapidApiKey('03620e4c12msh7be23e68d3400a1p1a7c7fjsnb60253c9306d');
        return $scraper;
    }

    private function saveMedia( Media $media ) {
        $filename = $media->getId() . "_ig_media.jpg";
        $parent_post_id = null;
        $upload_file = wp_upload_bits($filename, null, file_get_contents($media->getImageHighResolutionUrl()));

        if (!$upload_file['error']) {
            $wp_filetype = wp_check_filetype($filename, null);
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_parent' => $parent_post_id,
                'post_title' => $media->getCaption(),
                'post_date' => $media->getCreatedTime(),
                'post_name' => $media->getId(),
                'post_type' => INSTAGRAM_PULL_POST_TYPE,
                'post_content' => serialize($media),
                'post_status' => 'inherit',
                'guid' => $upload_file['url'],
            );
    
            $attachment_id = wp_insert_attachment( $attachment, $upload_file['file'], $parent_post_id );
            if (!is_wp_error($attachment_id)) {
                require_once(ABSPATH . "wp-admin" . '/includes/image.php');
                $attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file['file'] );
                wp_update_attachment_metadata( $attachment_id,  $attachment_data );
            }
        } else {
            throw new \Exception( $upload_file['error'] );
        }
    }

    private function mediaIsSaved( Media $media ) {
        global $wpdb;
        $id = $media->getId();
        $query = "SELECT COUNT(*) FROM {$wpdb->posts} WHERE `post_name` = $id";
        return !empty(intval($wpdb->get_var($query)));
    }

    private function rateLimitRequests()
    {
        sleep(30);
    }
        
    public function uploadUnsavedMedia()
    {
        $result = $this->scraper->getPaginateMedias($this->username);
        $this->saveMedias( $result['medias'] );

        while( $result['hasNextPage'] === true && $this->allSaved == false ) {
            $this->rateLimitRequests();
            $result = $this->scraper->getPaginateMedias(
                $this->username, $result['maxId']
            );
            $this->saveMedias( $result['medias'] );
        }    
    }

    public function saveMedias( $medias )
    {        
        foreach( $medias as $media ) {
            if( $media->getType() !== 'image' )
                continue;

            if( $this->mediaIsSaved( $media ) )
                return $this->allSaved = true;

            $this->saveMedia( $media );
        }    
    }
}