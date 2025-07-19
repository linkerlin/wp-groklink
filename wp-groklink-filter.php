<?php
/**
 * Plugin Name:       WP GrokLink Filter
 * Description:       Filters out <grok> tags from page content and same-origin iframes.
 * Version:           1.2.0
 * Author:            Steper Lin
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wp-groklink-filter
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Filters the content to remove <grok> tags and their content.
 * Also handles HTML entity encoded versions like &lt;grok and &gt;
 *
 * @param string $content The content to filter.
 * @return string The filtered content.
 */
function groklink_filter_the_content( $content ) {
    // Filter regular grok tags first
    $pattern = '/<grok(?::render|-[a-zA-Z0-9]+)?[^>]*>.*?<\/grok(?::render|-[a-zA-Z0-9]+)?>/is';
    $filtered_content = preg_replace( $pattern, '', $content );
    
    // Then filter HTML entity encoded versions
    $entity_pattern = '/&lt;grok(?::render|-[a-zA-Z0-9]+)?[^&]*&gt;.*?&lt;\/grok(?::render|-[a-zA-Z0-9]+)?&gt;/is';
    $filtered_content = preg_replace( $entity_pattern, '', $filtered_content );
    
    return $filtered_content;
}
add_filter( 'the_content', 'groklink_filter_the_content', 999 );
add_filter( 'render_block', 'groklink_filter_the_content', 999 );

/**
 * Enqueues the JavaScript file for iframe filtering.
 */
function groklink_enqueue_scripts() {
    wp_enqueue_script(
        'grok-filter-script',
        plugin_dir_url( __FILE__ ) . 'js/grok-filter.js',
        array(),
        '1.2.0',
        true // Load in the footer
    );
}
add_action( 'wp_enqueue_scripts', 'groklink_enqueue_scripts' );
