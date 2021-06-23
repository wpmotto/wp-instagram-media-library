<?php
/**
 * Plugin Name:       IG Media Library
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

if( $settings->username && !$settings->sync_off ) {
    $remote = new RemoteUserMedia( $settings->username );

    add_action( 
        'igml_cron_hook', [$remote, 'uploadUnsavedMedia'] 
    );

    if ( !wp_next_scheduled( 'igml_cron_hook' ) )
        wp_schedule_event( time(), $settings->frequency ?? 'daily', 'igml_cron_hook' );
}


add_shortcode( 'igml', function( $args ) {
    $media = new MediaUploads( $args );
    foreach( $media->get() as $post ):
    ?>
    <img src="<?php echo $post->guid ?>" alt="<?php echo $post->post_title ?>" />
    <?php
    endforeach;
});

add_filter( 'posts_where', function( $where, $query ) {
    global $wpdb;

    $ends_with = esc_sql( $query->get( 'guid_ends_with' ) );

    if ( $ends_with )
        $where .= " AND $wpdb->posts.guid LIKE '%$ends_with'";

    return $where;
}, 10, 2 );