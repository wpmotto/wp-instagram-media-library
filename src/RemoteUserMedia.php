<?php

namespace Motto\InstagramMediaLibrary;

use InstagramScraper\Instagram;
use InstagramScraper\Model\Media;
use Motto\InstagramMediaLibrary\Settings;
use InstagramScraper\Exception\InstagramException;

class RemoteUserMedia {
    
    private $settings;
    private $scraper;
    private $allSaved = false;

    public function __construct( Settings $settings )
    {
        $this->settings = $settings;
        $this->scraper = $this->initScraper();
    }

    private function initScraper()
    {
        $scraper = new Instagram();
        $scraper->setRapidApiKey($this->settings->rapidapi_key);
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
                'post_date' => date('Y-m-d H:i:s', $media->getCreatedTime()),
                'post_name' => $media->getId(),
                'post_status' => 'inherit',
                'guid' => $upload_file['url'],
            );
    
            
            $attachment_id = wp_insert_attachment( $attachment, $upload_file['file'], $parent_post_id );
            if (!is_wp_error($attachment_id)) {
                require_once(ABSPATH . "wp-admin" . '/includes/image.php');
                $attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file['file'] );
                $attachment_data['social'] = $this->socialMeta($media);
                wp_update_attachment_metadata( $attachment_id,  $attachment_data );

                // Set Terms
                wp_set_object_terms( 
                    $attachment_id, 'instagram', 'social_media_attachments'
                );
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

    private function socialMeta( Media $media )
    {
        return [
            'id' => $media->getId(),
            'created' => $media->getCreatedTime(),
            'shortcode' => $media->getShortcode(),
            'link' => $media->getLink(),
            'thumb_tiny_url' => $media->getImageLowResolutionUrl(),
            'thumb_url' => $media->getImageStandardResolutionUrl(),
            'thumb_hires_url' => $media->getImageHighResolutionUrl(),
            'square_images' => $media->getSquareImages(),
            'likes_count' => $media->getLikesCount(),
        ];
    }

    private function rateLimitRequests()
    {
        sleep(30);
    }
        
    private function request( $username, $maxId = null )
    {
        try {
            return $this->scraper->getPaginateMedias($username, $maxId);
        } catch( InstagramException $e ) {
            add_action('admin_notices', function() use ( $e ) {
                ?>
                <div class="notice notice-error is-dismissible"> 
                    <p><strong>Social Media Library.</strong><br/>
                        <?php echo esc_html($e->getMessage()); ?></p>
                    <button type="button" class="notice-dismiss">
                        <span class="screen-reader-text">Dismiss this notice.</span>
                    </button>
                </div>            
                <?php
            });

            return false;
        }
    }

    public function uploadUnsavedMedia()
    {
        $result = $this->request( $this->settings->username );
        if( !$result ) return;

        $this->saveMedias( $result['medias'] );

        while( $result['hasNextPage'] === true && $this->allSaved == false ) {
            $this->rateLimitRequests();
            $result = $this->request(
                $this->settings->username, $result['maxId']
            );
            if( !$result ) break;

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