<?php
/**
 * Plugin Name:       Social Media Library
 * Plugin URI:        https://github.com/wpmotto/wp-instagram-media-library
 * Description:       Save images from a public Instagram account to your WordPress library.
 * Version:           1.1.0
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

    /**
     * Setup Cron Job
     */
    add_action( 
        'igml_cron_hook', [$remote, 'uploadUnsavedMedia'] 
    );

    /**
     * Initial Run when settings are updated.
     */
    add_action(
        'update_option_igml_settings', [$remote, 'uploadUnsavedMedia']
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

 
add_action( 'wp_enqueue_media', function() {
	wp_enqueue_script( 
        'media-library-taxonomy-filter', 
        plugin_dir_url(__FILE__) . 'assets/js/media-filter.js',
        ['media-editor', 'media-views']
    );
	// Load 'terms' into a JavaScript variable that collection-filter.js has access to
	wp_localize_script( 'media-library-taxonomy-filter', 'MediaLibraryTaxonomyFilterData', array(
		'terms'     => get_terms( 'social_media_attachments', array( 'hide_empty' => false ) ),
	) );
	// Overrides code styling to accommodate for a third dropdown filter
	add_action( 'admin_footer', function(){
		?>
		<style>
		.media-modal-content .media-frame select.attachment-filters {
			max-width: -webkit-calc(33% - 12px);
			max-width: calc(33% - 12px);
		}
		</style>
		<?php
	});
});

add_action( 'init', function() {     
    register_taxonomy('social_media_attachments', ['attachment'], [
        'hierarchical'      => false,
        'show_ui'           => false,
        'show_in_nav_menus' => false,
        'query_var'         => is_admin(),
        'rewrite'           => false,
        'public'            => false,
        'label'             => _x( 'Social Media Attachment', 'Taxonomy name', 'motto-igml' ),
    ]);
}, 0 );
 
