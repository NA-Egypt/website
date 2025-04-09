$(function() {
	"use strict";

    $('.single-select').select2({
        theme: 'bootstrap4',
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        placeholder: $(this).data('placeholder'),
        allowClear: Boolean($(this).data('allow-clear')),
    });
    $('.multiple-select').select2({
        theme: 'bootstrap4',
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        placeholder: $(this).data('placeholder'),
        allowClear: Boolean($(this).data('allow-clear')),
    });


});

        // Initialize Select2 on existing select elements
        $(function() {
            $('.select2').select2();
        });

        document.getElementById('add-item').addEventListener('click', function() {
            // Clone the first item row
            const itemRow = document.querySelector('.item-row').cloneNode(true);
            
            // Clear the values of the cloned inputs
            const inputs = itemRow.querySelectorAll('select, input');
            inputs.forEach(input => {
                if (input.tagName.toLowerCase() === 'select') {
                    input.value = ''; // Clear the select value
                    $(input).val(null).trigger('change'); // Reset Select2
                } else {
                    input.value = ''; // Clear the input value
                }
            });

            // Append the cloned row to the items container
            document.getElementById('items-container').appendChild(itemRow);

            // Initialize Select2 on the new select element
            $(itemRow).find('.select2').select2();
        });