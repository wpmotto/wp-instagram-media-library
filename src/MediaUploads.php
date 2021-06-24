<?php

namespace Motto\InstagramMediaLibrary;

use WP_Query;

class MediaUploads {

    private $args;
    
    public function __construct( Array $args )
    {
        $this->args = array_merge([
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'orderby' => 'date',
            'order' => 'DESC',
            'posts_per_page' => 10,
            'post_status' => 'inherit',
            'guid_ends_with' => '_ig_media.jpg'
        ], $args);
    }

    public function get()
    {
        $query = new WP_Query($this->args);
        return array_map(function( $post ) {
            return new Image( $post );
        }, $query->get_posts());
    }
}