<?php

namespace Motto\InstagramMediaLibrary;

use WP_Post;

class Image {

    protected $post;

    public function __construct( WP_Post $post )
    {
        $this->post = $post;
    }

    public function post()
    {
        return $this->post;
    }

    public function alt()
    {
        return $this->post->post_title;
    }

    public function html( $size = 'full' )
    {
        return wp_get_attachment_image($this->post->ID, $size);
    }

    public function src( $size = 'full' )
    {
        return wp_get_attachment_image_src($this->post->ID, $size);
    }
}