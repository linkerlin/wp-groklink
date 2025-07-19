<?php
/**
 * WPCode Snippet: GrokLink Filter
 * Description: Filters out <grok> tags and their variations from page content and same-origin iframes.
 * Version: 1.0
 * Author: AI Assistant
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Filters the content to remove <grok> tags and their content.
 * Also handles HTML entity encoded versions like &lt;grok and &gt;
 *
 * @param string $content The content to filter.
 * @return string The filtered content.
 */
function groklink_filter_the_content($content) {
    // Filter regular grok tags first
    $pattern = '/<grok(?::render|-[a-zA-Z0-9]+)?[^>]*>.*?<\/grok(?::render|-[a-zA-Z0-9]+)?>/is';
    $filtered_content = preg_replace($pattern, '', $content);
    
    // Then filter HTML entity encoded versions
    $entity_pattern = '/&lt;grok(?::render|-[a-zA-Z0-9]+)?[^&]*&gt;.*?&lt;\/grok(?::render|-[a-zA-Z0-9]+)?&gt;/is';
    $filtered_content = preg_replace($entity_pattern, '', $filtered_content);
    
    return $filtered_content;
}

// Add filters for content and blocks
add_filter('the_content', 'groklink_filter_the_content', 999);
add_filter('render_block', 'groklink_filter_the_content', 999);

/**
 * Enqueues the JavaScript file for dynamic filtering.
 */
function groklink_enqueue_scripts() {
    // Inline JavaScript for WPCode compatibility
    $js_code = "
    document.addEventListener('DOMContentLoaded', function() {
        const filterGrokTags = (targetNode) => {
            if (!targetNode || !targetNode.innerHTML) return false;
            
            let content = targetNode.innerHTML;
            let modified = false;
            
            // Filter regular grok tags
            const pattern = /<grok(?::render|-[a-zA-Z0-9]+)?[^>]*>.*?<\/grok(?::render|-[a-zA-Z0-9]+)?>/gis;
            if (pattern.test(content)) {
                content = content.replace(pattern, '');
                modified = true;
            }
            
            // Filter HTML entity encoded grok tags
            const entityPattern = /&lt;grok(?::render|-[a-zA-Z0-9]+)?[^&]*&gt;.*?&lt;\/grok(?::render|-[a-zA-Z0-9]+)?&gt;/gis;
            if (entityPattern.test(content)) {
                content = content.replace(entityPattern, '');
                modified = true;
            }
            
            if (modified) {
                targetNode.innerHTML = content;
                console.log('Grok tags filtered in node:', targetNode.nodeName);
                return true;
            }
            return false;
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
    ";
    
    wp_add_inline_script('jquery', $js_code);
}
add_action('wp_enqueue_scripts', 'groklink_enqueue_scripts');

