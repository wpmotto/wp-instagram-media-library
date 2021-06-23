<?php

namespace Motto\InstagramMediaLibrary;

class MediaUploads {

    private $args;
    
    public function __construct( Array $args )
    {
        $this->args = array_merge([
            'orderby' => 'date',
            'order' => 'DESC',
            'posts_per_page' => 10,
            'guid_ends_with' => '_igml_media.jpg'
        ], $args);
    }

    public function get()
    {
        return get_posts($this->args);
    }
}