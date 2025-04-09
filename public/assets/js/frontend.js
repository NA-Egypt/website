// $(function() {   
//     function setupSearch(inputSelector, containerSelector, itemSelector, fields) {
//         $(inputSelector).on('input', function() {
//             var searchTerm = $(this).val().toLowerCase();

//             $(containerSelector).find(itemSelector).each(function() {
//                 var $item = $(this); // Cache the item

//                 var matches = fields.some(function(field) {
//                     return $item.find(field).text().toLowerCase().includes(searchTerm);
//                 });

//                 if (matches) {
//                     $item.removeClass('d-none').css('display', 'flex'); // Show properly
//                 } else {
//                     $item.addClass('d-none').css('display', 'none'); // Hide properly
//                 }
//             });
//         });
//     }

//     setupSearch('#search-input', '.filter-meetings-container', '.meeting-item', [
//         '.meeting-day',
//         '.meeting-topic',
//         '.meeting-start-time',
//         '.meeting-end-time',
//         '.meeting-type'
//     ]);
// });

$(function() {   
    function setupSearch(inputSelector, containerSelector, itemSelector, fields) {
        $(inputSelector).on('input', function() {
            var searchTerm = $(this).val().toLowerCase();

            $(containerSelector).find(itemSelector).each(function() {
                var $item = $(this);

                var matches = fields.some(function(field) {
                    return $item.find(field).text().toLowerCase().includes(searchTerm);
                });

                if (matches) {
                    $item.removeClass('d-none').show(); // Show item
                } else {
                    $item.addClass('d-none').hide(); // Hide item
                }
            });
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
