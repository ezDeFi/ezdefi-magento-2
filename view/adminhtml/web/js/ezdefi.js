require(
    [
        'jquery',
        'mage/translate',
        'Ezdefi_Payment/js/select2.min'
    ],
    function ($) {
        var selectors = {
            formConfig: '#ezdefi-form-config',
            selectCoinConfig: '.ezdefi-select-coin',
            coinConfigTable: '#ezdefi-configuration-coin-table',
            cloneRowCoinConfig: '.coin-config-clone',
            btnAdd: '#btn-add',
            btnDelete: '.btn-confirm-delete',
            btnEdit: '.btn-submit-edit',
            btnCancel: '.btn-cancel',
            gatewayApiUrlInput: "#gateway-api-url-input",
            apiKeyInput: "#api-key-input",
            orderStatusInput: "#order-status-input",
            enableSimplePayInput: "#enable-simple-pay",
            enableEscrowPayInput: "#enable-escrow-pay",
            variationInput: "#variation-input",
            decimalBox: ".decimal-input-box",
            variationBox: ".variation-input-box",
            coinIdInput: '.coin-config__id',
            coinOrderInput: '.coin-config__order',
            coinSymbolInput: '.coin-config__fullname',
            coinNameInput: '.coin-config__name',
            coinDiscountInput: '.coin-config__discount',
            coinPaymentLifetimeInput: '.coin-config__payment-lifetime',
            coinWalletAddressInput: '.coin-config__wallet-address',
            coinSafeBlockDistantInput: '.coin-config__safe-block-distant',
            coinDecimalInput: '.coin-config__decimal',
            editCoinDecimalInput: '.edit-coin-config__decimal',
            cancelSelectCoin: '.cancel-select-coin',
            modalEditCoin: '.modal-edit-coin'
        }
        var tmp = 1;

        $(document).on("click", "#ezdefi-configuration-add-coin", function () {
            tmp += 1;
            var container = `<tr>
                <td>
                    <select class="ezdefi-select-coin" style="width: 200px" data-test="1" id="select-currency-${tmp}">
                        <option value=""></option>
                    </select><br>
                    <input type="hidden" class="ezdefi__currency-symbol">
                    <input type="hidden" class="ezdefi__currency-name">
                    <input type="hidden" class="ezdefi__currency-id">
                    <input type="hidden" class="ezdefi__currency-description">
                    <input type="hidden" class="ezdefi__currency-logo">
                </td>
                <td>
                    <input type="text" class="form-control ezdefi__discount">
                </td>
                <td>
                    <input type="text" class="form-control ezdefi__payment-lifetime">
                </td>
                <td><input type="text" class="form-control ezdefi__wallet-address"></td>
                <td><input type="text" class="form-control ezdefi__block-confirmation"></td>
                <td><input type="text" class="form-control ezdefi__currency-decimal"></td>
                <td>
                    <button class="action-delete delete-curency-config cancel-add-currency" type="button"><span>Delete</span></button>
                </td>
            </tr>`;
            $("#ezdefi-configuration-coin-table").append(container);
            initCancelAddCurrency();
            initSelectCoinConfig("#select-currency-"+tmp);
            $(".ezdefi-select-coin").on('select2:select', selectCoinListener);
        });

        function initCancelAddCurrency() {
            $('.cancel-add-currency').click(function () {
                $(this).parent().parent().remove();
            });
        }

        function initSelectCoinConfig(select) {
            var that = this;
            $(select).select2({
                ajax: {
                    url: "http://ezdefi-magento2.lan/admin/admin/gateway/listcoin",
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
                placeholder: "Enter name"
            });
        }

        function formatRepo(repo) {
            if (repo.loading) {
                return repo.text;
            }
            return `<div class='select2-result-repository clearfix select-coin-box' id="${repo.id}">
                <div class='select2-result-repository__meta'>
                    <div>
                        <span>
                            <img src="${repo.logo}" alt="" class="select-coin__logo">
                        </span>
                        <span class='select2-result-repository__title text-justify select-coin__name'>${repo.name}</span>
                    </div>
                </div>
            </div>`;
        };

        function formatRepoSelection(repo) {
            return `<div class='select2-result-repository clearfix select-coin-box' id="${repo.id}">
                <div class='select2-result-repository__meta'>
                    <div>
                        <span>
                            <img src="${repo.logo}" alt="" class="select-coin__logo">
                        </span>
                        <span class='select2-result-repository__title text-justify select-coin__name'>${repo.name}</span>
                   </div>
                </div>
            </div>`;
        }

        function selectCoinListener(e) {
            let data = e.params.data;
            let rowElement = e.currentTarget.parentNode.parentNode;

            let idInput             = $(rowElement).find('.ezdefi__currency-id');
            let nameInput           = $(rowElement).find('.ezdefi__currency-name');
            let symbolInput         = $(rowElement).find('.ezdefi__currency-symbol');
            let descriptionInput    = $(rowElement).find('.ezdefi__currency-description');
            let logoInput           = $(rowElement).find('.ezdefi__currency-logo');
            let decimal             = $(rowElement).find('.ezdefi__currency-decimal');

            idInput         .attr('name', 'groups[ezdefi_payment][fields][currency][value]['+data._id+'][id]');
            nameInput       .attr('name', 'groups[ezdefi_payment][fields][currency][value]['+data._id+'][name]');
            symbolInput     .attr('name', 'groups[ezdefi_payment][fields][currency][value]['+data._id+'][symbol]');
            descriptionInput.attr('name', 'groups[ezdefi_payment][fields][currency][value]['+data._id+'][description]');
            logoInput       .attr('name', 'groups[ezdefi_payment][fields][currency][value]['+data._id+'][logo]');
            decimal         .attr('name', 'groups[ezdefi_payment][fields][currency][value]['+data._id+'][decimal]');

            idInput         .val(data._id)
            nameInput       .val(data.name)
            symbolInput     .val(data.symbol);
            descriptionInput.val(data.description);
            logoInput       .val(data.logo);
            decimal         .val(data.suggestedDecimal);

            $(rowElement).find('.ezdefi__payment-lifetime')  .attr('name', 'groups[ezdefi_payment][fields][currency][value]['+data._id+'][lifetime]');
            $(rowElement).find('.ezdefi__wallet-address')    .attr('name', 'groups[ezdefi_payment][fields][currency][value]['+data._id+'][wallet_address]');
            $(rowElement).find('.ezdefi__block-confirmation').attr('name', 'groups[ezdefi_payment][fields][currency][value]['+data._id+'][block_confirmation]');
            $(rowElement).find('.ezdefi__discount')          .attr('name', 'groups[ezdefi_payment][fields][currency][value]['+data._id+'][discount]');
        }
    }
);