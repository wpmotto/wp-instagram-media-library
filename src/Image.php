<?php

namespace Motto\InstagramMediaLibrary;

use WP_Post;

class Image {

    protected $post;
    protected $social;

    public function __construct( WP_Post $post )
    {
        $this->post = $post;
        $this->social = wp_get_attachment_metadata( $this->post->ID )['social'];
    }

    public function post()
    {
        return $this->post;
    }

    public function attachment_url()
    {
        return get_permalink($this->post->ID);
    }

    public function alt()
    {
        return $this->post->post_title;
    }

    public function social( $prop = null )
    {
        if( isset($this->social[$prop]) )
            return $this->social[$prop];

        return $this->social;
    }

    public function html( $size = 'full' )
    {
        return wp_get_attachment_image($this->post->ID, $size);
    }

    public function src( $size = 'full', $array = false )
    {
        $img = wp_get_attachment_image_src($this->post->ID, $size);
        if( $array )
            return $img;
        
        return $img[0];
    }
}