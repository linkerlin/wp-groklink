<?php
/**
 * WPCode Snippet: GrokLink Filter
 * Description: Filters out <grok> tags and their variations from page content and same-origin iframes.
 * Version: 1.0
 * Author: AI Assistant
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Filters the content to remove <grok> tags and their content.
 *
 * @param string $content The content to filter.
 * @return string The filtered content.
 */
function wpcode_groklink_filter_the_content( $content ) {
    // Regex to match a family of <grok...></grok...> tags and their content.
    $pattern = '/<grok(?::render|-[a-zA-Z0-9]+)?[^>]*>.*?<\/grok(?::render|-[a-zA-Z0-9]+)?>/is';
    return preg_replace( $pattern, '', $content );
}
add_filter( 'the_content', 'wpcode_groklink_filter_the_content', 999 );
add_filter( 'render_block', 'wpcode_groklink_filter_the_content', 999 );

/**
 * Embeds the JavaScript for iframe filtering directly into the footer.
 */
function wpcode_groklink_embed_scripts() {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterGrokTags = (targetNode) => {
                let content = targetNode.innerHTML;
                const pattern = /<grok(?::render|-[a-zA-Z0-9]+)?[^>]*>.*?<\/grok(?::render|-[a-zA-Z0-9]+)?>/gis;
                if (pattern.test(content)) {
                    targetNode.innerHTML = content.replace(pattern, '');
                    console.log('Grok tags filtered in node:', targetNode.nodeName);
                    return true; // Tags were filtered
                }
                return false; // No tags found
            };

            // Initial filtering on the main document body
            if (filterGrokTags(document.body)) {
                console.log('Initial filtering on main document body successful.');
            } else {
                console.log('No grok tags found in main document body on DOMContentLoaded.');
            }

            // Observe changes in the document body for dynamically added content
            const observer = new MutationObserver((mutationsList) => {
                for (const mutation of mutationsList) {
                    if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                        mutation.addedNodes.forEach(node => {
                            // Only process element nodes and if they contain our tags
                            if (node.nodeType === Node.ELEMENT_NODE) {
                                if (filterGrokTags(node)) {
                                    console.log('Grok tags filtered in dynamically added node:', node.nodeName);
                                }
                            }
                        });
                    }
                }
            });

            // Start observing the document body for configured mutations
            observer.observe(document.body, { childList: true, subtree: true });

            // Handle iframes (cross-origin policy will still block, but keep the logic)
            const iframes = document.querySelectorAll('iframe');
            iframes.forEach(function(iframe) {
                iframe.addEventListener('load', function() {
                    try {
                        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                        if (iframeDoc) {
                            if (filterGrokTags(iframeDoc.body)) {
                                console.log('Grok tags filtered in iframe:', iframe.src);
                            } else {
                                console.log('No grok tags found in iframe:', iframe.src);
                            }
                        }
                    } catch (e) {
                        console.warn('Could not access iframe content for filtering due to cross-origin policy:', iframe.src, e);
                    }
                });
            });
        });
    </script>
    <?php
}
add_action( 'wp_footer', 'wpcode_groklink_embed_scripts' );
