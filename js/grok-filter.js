document.addEventListener('DOMContentLoaded', function() {
    const filterGrokTags = (targetNode) => {
        let content = targetNode.innerHTML;
        const pattern = /<grok(?::render|-[a-zA-Z0-9]+)?[^>]*>.*?<\/grok(?::render|-[a-zA-Z0-9]+)?>/gis;
        if (pattern.test(content)) {
            targetNode.innerHTML = content.replace(pattern, '');
            console.log('Grok:render tags filtered in node:', targetNode.nodeName);
            return true; // Tags were filtered
        }
        return false; // No tags found
    };

    // Initial filtering on the main document body
    if (filterGrokTags(document.body)) {
        console.log('Initial filtering on main document body successful.');
    } else {
        console.log('No grok:render tags found in main document body on DOMContentLoaded.');
    }

    // Observe changes in the document body for dynamically added content
    const observer = new MutationObserver((mutationsList) => {
        for (const mutation of mutationsList) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                mutation.addedNodes.forEach(node => {
                    // Only process element nodes and if they contain our tags
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        if (filterGrokTags(node)) {
                            console.log('Grok:render tags filtered in dynamically added node:', node.nodeName);
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
                        console.log('Grok:render tags filtered in iframe:', iframe.src);
                    } else {
                        console.log('No grok:render tags found in iframe:', iframe.src);
                    }
                }
            } catch (e) {
                console.warn('Could not access iframe content for filtering due to cross-origin policy:', iframe.src, e);
            }
        });
    });
});