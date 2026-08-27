

$(document).ready(function() {
    // === PAGINATION LOGIC ===
    function loadPage(page) {
        $.get('/admin/productsPage/' + page, function(data) {
            $('#products-table-body').html(data);
            updatePagination(page);
        });
    }

    function updatePagination(currentPage) {
        $('.pagination a, .pagination strong').each(function() {
            if ($(this).text() == currentPage) {
                $(this).replaceWith('<strong>' + currentPage + '</strong>');
            } else if ($(this).is('strong')) {
                const pageNum = $(this).text();
                $(this).replaceWith('<a href="/admin/manageProducts/' + pageNum + '">' + pageNum + '</a>');
            }
        });
    }

    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        
        const href = $(this).attr('href'); // get /admin/manageProducts/2
        const pageMatch = href.match(/\/(\d+)$/); // get number at end

        if (pageMatch) {
            const page = parseInt(pageMatch[1]);
            loadPage(page);
        }
    });
    

        // RECEIPT PAGINATION LOGIC 
    function loadReceiptPage(page) {
        const query = window.location.search; // Preserve ?month=..&year=..
        $.get('/admin/receiptsPage/' + page + query, function(data) {
            $('#receipts-table-body').html(data);
            updateReceiptPagination(page);
        });
    }

    function updateReceiptPagination(currentPage) {
        $('#receipt-pagination a, #receipt-pagination strong').each(function () {
            if ($(this).text() == currentPage) {
                $(this).replaceWith('<strong>' + currentPage + '</strong>');
            } else if ($(this).is('strong')) {
                const pageNum = $(this).text();
                $(this).replaceWith('<a href="/admin/receiptHistory/' + pageNum + '">' + pageNum + '</a>');
            }
        });
    }

    $(document).on('click', '#receipt-pagination a', function (e) {
        e.preventDefault();
        const href = $(this).attr('href');
        const pageMatch = href.match(/\/(\d+)(\?.*)?$/);
        if (pageMatch) {
            const page = parseInt(pageMatch[1]);
            loadReceiptPage(page);
        }
    });


    //  AUTO-CALCULATE PRICE_PER_BOX 
    const $quantity = $('#quantity');
    const $unitPrice = $('#unit_price');
    const $pricePerBox = $('#price_per_box');

    function updatePricePerBox() {
        const quantity = parseFloat($quantity.val());
        const unitPrice = parseFloat($unitPrice.val());

        if (!isNaN(quantity) && !isNaN(unitPrice) && quantity > 0 && unitPrice >= 0) {
            $pricePerBox.val((quantity * unitPrice).toFixed(2));
        } else {
            $pricePerBox.val('');
        }
    }

    $quantity.on('input', updatePricePerBox);
    $unitPrice.on('input', updatePricePerBox);

        // === CREATE RECEIPT PAGE ===
    if ($('#receipt-form').length > 0) {
        // 1. Live search filter for product list
        $('#product-search').on('input', function () {
            const search = $(this).val().toLowerCase();
            $('.product-row').each(function () {
                const name = $(this).find('label').text().toLowerCase();
                $(this).toggle(name.includes(search));
            });
        });

        // 2. Enable/disable quantity input based on checkbox
        $(document).on('change', '.product-checkbox', function () {
            const quantityInput = $(this).closest('.product-row').find('.quantity-input');
            quantityInput.prop('disabled', !this.checked);
            if (!this.checked) quantityInput.val('');
            calculateTotal();
        });

        // 3. Update total price on quantity input change
        $(document).on('input', '.quantity-input', function () {
            let val = $(this).val();
            // If val is less than 1 or not a number, reset to 1
            if (val !== '' && (isNaN(val) || parseInt(val) < 1)) {
                $(this).val(1);
            }

            calculateTotal();
        });

        // 4. Calculate total price
        function calculateTotal() {
            let total = 0;
            $('.product-row').each(function () {
                const checkbox = $(this).find('.product-checkbox')[0];
                const quantityInput = $(this).find('.quantity-input');
                const price = parseFloat($(this).data('price'));

                if (checkbox.checked && quantityInput.val()) {
                    const quantity = parseFloat(quantityInput.val());
                    if (!isNaN(price) && !isNaN(quantity)) {
                        total += price * quantity;
                    }
                }
            });
            $('#total-price').text(total.toFixed(2));
        }

    }
});


