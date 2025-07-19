document.addEventListener('DOMContentLoaded', function() {
    const iframes = document.querySelectorAll('iframe');

    iframes.forEach(function(iframe) {
        iframe.addEventListener('load', function() {
            try {
                // Accessing contentDocument may throw a cross-origin error
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;

                if (iframeDoc) {
                    let bodyContent = iframeDoc.body.innerHTML;
                    // Regex to specifically target <grok:render> tags
                    const pattern = /<grok:render[^>]*>.*?<\/grok:render>/gis;
                    if (pattern.test(bodyContent)) {
                        iframeDoc.body.innerHTML = bodyContent.replace(pattern, '');
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

    // Also apply filtering to the main document body, in case tags are directly in the main page
    let mainBodyContent = document.body.innerHTML;
    const mainPattern = /<grok:render[^>]*>.*?<\/grok:render>/gis;
    if (mainPattern.test(mainBodyContent)) {
        document.body.innerHTML = mainBodyContent.replace(mainPattern, '');
        console.log('Grok:render tags filtered in main document body.');
    } else {
        console.log('No grok:render tags found in main document body.');
    }
});