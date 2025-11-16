// assets/ec-lightbox.js
(function () {
    function initECLightbox() {
        if (typeof GLightbox === 'undefined') {
            return;
        }

        // All containers where the user explicitly added the "ec-lightbox" class.
        var containers = document.querySelectorAll('.ec-lightbox');
        if (!containers.length) {
            return;
        }

        containers.forEach(function (container, galleryIndex) {
            // Find all images inside this specific container.
            var imgs = container.querySelectorAll('img');
            if (!imgs.length) {
                return;
            }

            // Unique class for this gallery instance.
            var galleryClass = 'ec-lightbox-gallery-' + galleryIndex;

            imgs.forEach(function (img) {
                // Best possible URL for the full image.
                var src =
                    img.getAttribute('data-full-url') ||
                    img.currentSrc ||
                    img.src;

                if (!src) {
                    return;
                }

                // Reuse existing link or create a new one.
                var link = img.closest('a');

                if (!link) {
                    link = document.createElement('a');
                    link.href = src;
                    link.className = 'ec-lightbox-link';
                    img.parentNode.insertBefore(link, img);
                    link.appendChild(img);
                } else {
                    link.href = src;
                    link.classList.add('ec-lightbox-link');
                }

                // Add gallery-specific class so this instance only
                // handles links inside this container.
                link.classList.add(galleryClass);

                // Caption: figcaption > alt.
                var caption = '';
                var figure = img.closest('figure');
                if (figure) {
                    var figcaption = figure.querySelector('figcaption');
                    if (figcaption) {
                        caption = figcaption.innerText.trim();
                    }
                }
                if (!caption && img.alt) {
                    caption = img.alt.trim();
                }
                if (caption) {
                    link.setAttribute('data-title', caption);
                }
            });

            // Initialize a separate GLightbox instance for this gallery only.
            GLightbox({
                selector: 'a.ec-lightbox-link.' + galleryClass
            });
        });
    }

    // Run on DOM ready, regardless of being loaded in head or footer.
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initECLightbox);
    } else {
        initECLightbox();
    }
})();
