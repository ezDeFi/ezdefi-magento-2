require(
    [
        'jquery',
        'Magento_Ui/js/modal/alert',
        'domReady!',
        'Ezdefi_Payment/js/select2.min',
        'mage/url'
    ],
    function ($, alert, url) {

        function addSelectOrderListener(exceptionId) {
            if($('#ezdefi__select-pending-order-' + exceptionId).data('check-created') == '1') return;
            $('#ezdefi__select-pending-order-' + exceptionId).data('check-created', '1');
            $('#ezdefi__select-pending-order-' + exceptionId).select2({
                ajax: {
                    url: $('#ezdefi__select-pending-order-' + exceptionId).data('url-get-order'),
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            keyword: params.term, // search term
                            page: params.page
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.data
                        };
                    },
                    cache: true
                },
                escapeMarkup: function (markup) {
                    return markup;
                },
                minimumInputLength: 1,
                templateResult: formatRepo,
                templateSelection: formatRepoSelection,
                placeholder: "Enter order"
            });
            $("#ezdefi__select-pending-order-"+exceptionId).on('select2:select', selectOrderPendingListener);
        }

        $(document).on('click', '.ezdefi__btn-show-list-order-pending', function () {
            let exceptionId = $(this).data('exception-id');

            $(this).parent().find('.select2-container').css('display', 'inline-block');
            $('#ezdefi__select-pending-order-' + exceptionId).css('display', 'block');
            $('#ezdefi__btn-cancel-assign-' + exceptionId).css('display', 'inline-block');
            $('#ezdefi__btn-assign-order-' + exceptionId).css('display', 'inline-block');
            $(this).css('display', 'none');
            addSelectOrderListener(exceptionId);
        });


        $(document).on('click', '.ezdefi__btn-cancel-assign', function () {
            let exceptionId = $(this).data('exception-id');

            $('#ezdefi__btn-show-list-order-pending-' + exceptionId).css('display', 'block');
            $('#ezdefi__btn-cancel-assign-' + exceptionId).css('display', 'none');
            $('#ezdefi__select-pending-order-' + exceptionId).css('display', 'none');
            $(this).parent().find('.select2-container').css('display', 'none');
            console.log($(this).parent().find('.select2-container'));
            $('#ezdefi__btn-assign-order-' + exceptionId).css('display', 'none');
            $(this).css('display', 'none');
        });

        var selectOrderPendingListener = function (e) {
            var data = e.params.data;
            var buttonAssign = $(this).parent().find('.ezdefi__btn-assign-order');
            buttonAssign.css('display', 'inline-block');

            $(buttonAssign).click(function () {
                alert({
                    title: $.mage.__('Assign order?'),
                    content: $.mage.__('Are you sure you want to confirm this order?'),
                    actions: {
                        always: function () {
                        }
                    },
                    buttons: [{
                        text: $.mage.__('Close'),
                        class: 'action primary accept',
                        click: function () {
                            this.closeModal(true);
                        }
                    }, {
                        text: $.mage.__('Assign'),
                        class: 'action',
                        click: function () {
                            window.location.href = buttonAssign.data('url-assign') + 'order_id/' + data.id
                        }
                    }]
                });
            });


        };

        var formatRepo = function (repo) {
            if (repo.loading) {
                return repo.text;
            }
            return `<div class='select2-result-repository clearfix' id="order-pending-${repo.id}" style="padding-bottom: 12px;">
                    <div class='select2-result-repository__meta'>
                        <div class='select2-result-repository__title text-justify'>
                            <table class="exception__list-order-pending--table">
                                <tbody>
                                    <tr>
                                        <td>Order id</td>
                                        <td class="padding-left-md">:</td>
                                        <td class="exception__order-pending--data">${parseInt(repo.increment_id)}</td>
                                    </tr>
                                    <tr>
                                        <td class="exception-order-label-2">Email</td>
                                        <td class="padding-left-md">:</td>
                                        <td class="exception__order-pending--data">${repo.customer_email}</td>
                                    </tr>
                                    <tr>
                                        <td class="exception-order-label-2">Customer</td>
                                        <td class="padding-left-md">:</td>
                                        <td class="exception__order-pending--data">${repo.customer_firstname + ' ' + repo.customer_lastname}</td>
                                    </tr>
                                    <tr>
                                        <td class="exception-order-label-2">Price</td>
                                        <td class="padding-left-md">:</td>
                                        <td class="exception__order-pending--data">${repo.total_due + ' ' + repo.order_currency_code}</td>
                                    </tr>
                                    <tr>
                                        <td class="exception-order-label-2">Create at</td>
                                        <td class="padding-left-md">:</td>
                                        <td class="exception__order-pending--data">${repo.created_at}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>`;
        };

        var formatRepoSelection = function (repo) {
            return repo.id ? 'Order: ' + parseInt(repo.increment_id) : 'Choose order to assign';
        };

    })