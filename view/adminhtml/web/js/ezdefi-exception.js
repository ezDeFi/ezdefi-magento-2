require(
    [
        'jquery',
        'Magento_Ui/js/modal/alert',
        'domReady!',
        'Ezdefi_Payment/js/select2.min',
    ],
    function ($, alert) {

        var selectOrderInterval = setInterval(function () {
            console.log($('.ezdefi__select-pending-order').data('check-loaded'), $('.ezdefi__select-pending-order').data('check-loaded') == 1);
            if($('.ezdefi__select-pending-order').data('check-loaded') == 1) {
                $('.ezdefi__select-pending-order').select2({
                    ajax: {
                        url: "http://ezdefi-magento2.lan/admin/admin/exception/getorderpending",
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
                    escapeMarkup: function (markup) { return markup; },
                    minimumInputLength: 1,
                    templateResult: formatRepo,
                    templateSelection: formatRepoSelection,
                    placeholder: "Enter order"
                });
                $(".ezdefi__select-pending-order").on('select2:select', selectOrderPendingListener);
                clearInterval(selectOrderInterval);
            }
        }, 500);


        var selectOrderPendingListener = function (e) {
            var data = e.params.data;
            var buttonAssign = $(this).parent().find('.ezdefi__btn-assign-order');
            buttonAssign.css('display', 'block');

            $(buttonAssign).click( function () {
                alert({
                    title: $.mage.__('Some title'),
                    content: $.mage.__('Some content'),
                    actions: {
                        always: function(){}
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

        var formatRepo = function(repo) {
            if (repo.loading) {
                return repo.text;
            }
            return `<div class='select2-result-repository clearfix' id="order-pending-${repo.id}" style="border-bottom: 1px solid #999">
                    <div class='select2-result-repository__meta'>
                        <div class='select2-result-repository__title text-justify'>
                            <p><span class="exception-order-label-2">orderId:</span>${repo.increment_id}</p>
                            <p><span class="exception-order-label-2">email:</span>${repo.customer_email}</p>
                            <p><span class="exception-order-label-2">customer:</span>${repo.customer_firstname + ' ' + repo.customer_lastname}</p>
                            <p><span class="exception-order-label-2">price:</span>${repo.total_due +' ' + repo.order_currency_code}</p>
                            <p><span class="exception-order-label-2">createAt:</span>${repo.created_at}</p>
                        </div>
                    </div>
                </div>`;
        };

        var formatRepoSelection = function (repo) {
            console.log(repo)
            return repo.id ? 'Order: ' + repo.increment_id : 'Choose order to assign';
        };

    })