document.addEventListener('DOMContentLoaded', function() {
    const iframes = document.querySelectorAll('iframe');

    iframes.forEach(function(iframe) {
        iframe.addEventListener('load', function() {
            try {
                // Accessing contentDocument may throw a cross-origin error
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;

                if (iframeDoc) {
                    let bodyContent = iframeDoc.body.innerHTML;
                    // Regex to match a family of <grok...></grok...> tags and their content.
                    const pattern = /<(grok[\w:-]*)(?:[^>]*)>.*?<\/\1>/gis;
                    if (pattern.test(bodyContent)) {
                        iframeDoc.body.innerHTML = bodyContent.replace(pattern, '');
                    }
                }
            } catch (e) {
                console.warn('Could not access iframe content for filtering due to cross-origin policy:', iframe.src);
            }
        });
    });
});
