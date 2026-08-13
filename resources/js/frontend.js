import $ from 'jquery';

$(function() {   
    function setupSearch(inputSelector, containerSelector, itemSelector, fields) {
        const $input = $(inputSelector);
        if (!$input.length) return;

        let debounceTimer = null;
        let cachedItems = null;

        // Pre-cache item text data to eliminate layout thrashing during input
        function buildCache() {
            const items = [];
            $(containerSelector).find(itemSelector).each(function() {
                const $item = $(this);
                const fieldTexts = fields.map(field => $item.find(field).text().toLowerCase());
                items.push({
                    el: this,
                    text: fieldTexts.join(' ')
                });
            });
            return items;
        }

        async function performSearch(searchTerm) {
            if (!cachedItems) {
                cachedItems = buildCache();
            }

            const term = searchTerm.trim().toLowerCase();
            const chunkSize = 40;

            // Process updates in chunks to prevent blocking main thread (>50ms)
            for (let i = 0; i < cachedItems.length; i += chunkSize) {
                const chunk = cachedItems.slice(i, i + chunkSize);

                requestAnimationFrame(() => {
                    chunk.forEach(item => {
                        const matches = !term || item.text.includes(term);
                        if (matches) {
                            item.el.classList.remove('d-none');
                            item.el.style.display = '';
                        } else {
                            item.el.classList.add('d-none');
                            item.el.style.display = 'none';
                        }
                    });
                });

                if (i + chunkSize < cachedItems.length) {
                    if (typeof scheduler !== 'undefined' && scheduler.yield) {
                        await scheduler.yield();
                    } else {
                        await new Promise(resolve => setTimeout(resolve, 0));
                    }
                }
            }
        }

        $input.on('input', function() {
            const val = $(this).val();
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                performSearch(val);
            }, 150);
        });
    }

    setupSearch('#search-input', '.col-12.col-md-10', '.meeting-item', [
        '.meeting-day',
        '.meeting-topic',
        '.meeting-start-time',
        '.meeting-end-time',
        '.meeting-type'
    ]);
});