<?php
/**
 * Plugin Name:       Social Media Library
 * Plugin URI:        https://github.com/wpmotto/wp-instagram-media-library
 * Description:       Save images from a public Instagram account to your WordPress library.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Motto
 * Author URI:        https://motto.ca
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       motto-igml
 * Domain Path:       /languages
 */
require __DIR__ . '/vendor/autoload.php';

define('MOTTO_IGML_VERSION', '1.0.0' );

use Motto\InstagramMediaLibrary\MediaUploads;
use Motto\InstagramMediaLibrary\RemoteUserMedia;
use Motto\InstagramMediaLibrary\Settings;

$settings = new Settings;
add_action( 'admin_init', [$settings, 'media']);

if( $settings->canSyncInstagram() ) {
    $remote = new RemoteUserMedia( $settings );

    add_action( 
        'igml_cron_hook', [$remote, 'uploadUnsavedMedia'] 
    );

    if ( !wp_next_scheduled( 'igml_cron_hook' ) )
        wp_schedule_event( time(), $settings->frequency ?? 'daily', 'igml_cron_hook' );
}

// Add Shortcode
add_shortcode( 'social_feed', function( $atts ) {
    $media = new MediaUploads( $atts );
    $images = implode('', array_map( function( $item ) {
        return "<li>{$item->html()}</li>";
    }, $media->get() ));

    return sprintf('<ul class="%s">%s</ul>', esc_attr('igml-list'), $images);
});

add_filter( 'posts_where', function( $where, $query ) {
    global $wpdb;

    $ends_with = esc_sql( $query->get( 'guid_ends_with' ) );
    if ( $ends_with )
        $where .= " AND $wpdb->posts.guid LIKE '%$ends_with'";

    return $where;
}, 10, 2 );