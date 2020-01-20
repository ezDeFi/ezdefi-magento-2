require(
    [
        'jquery',
        'mage/translate',
        'Ezdefi_Payment/js/select2.min'
    ],
    function ($) {
        var selectors = {
            simplePaymentCheckbox   : '.ezdefi__simple-payment-checkbox',
            ezdefiPaymentCheckbox   : '.ezdefi__ezdefi-payment-checkbox',
            checkPaymentMethodInput : '.check-payment-method-input',
            currencySymbolInput     : '.ezdefi__currency-symbol-input',
            currencyNameInput       : '.ezdefi__currency-name-input',
            currencyIdInput         : '.ezdefi__currency-id-input',
            currencyDescriptionInput: '.ezdefi__currency-description-input',
            currencyLogoInput       : '.ezdefi__currency-logo-input',
            currencydiscountInput   : '.ezdefi__currency-discount-input',
            currencyLifetimeInput   : '.ezdefi__payment-lifetime-input',
            currencyDecimalInput    : '.ezdefi__currency-decimal-input',
            blockConfirmationInput  : '.ezdefi_block-confirmation-input',
            walletAddressInput      : '.ezdefi__wallet-address-input',
            btnCancelAddCurrency    : '.canel-add-currency-input',
            btnDeleteCurrency       : '.btn-delete-curency-config'
        }
        var tmp = 1;

        $(document).on("change", selectors.simplePaymentCheckbox, checkPaymentMethodRequire);
        $(document).on("change", selectors.ezdefiPaymentCheckbox, checkPaymentMethodRequire);

        function checkPaymentMethodRequire() {
            if( !$(selectors.simplePaymentCheckbox).is(':checked') && !$(selectors.ezdefiPaymentCheckbox).is(':checked')) {
                $(selectors.checkPaymentMethodInput).val('');
            } else {
                $(selectors.checkPaymentMethodInput).val('1');
            }
        }

        $(document).on("click", "#ezdefi-configuration-add-coin", function () {
            tmp += 1;
            var container = `<tr>
                <td>
                    <select class="ezdefi-select-coin" style="width: 200px" id="select-currency-${tmp}">
                        <option value=""></option>
                    </select><br>
                    <input type="hidden" class="${selectorToClass(selectors.currencySymbolInput)}">
                    <input type="hidden" class="${selectorToClass(selectors.currencyNameInput)}">
                    <input type="hidden" class="${selectorToClass(selectors.currencyIdInput)}">
                    <input type="hidden" class="${selectorToClass(selectors.currencyDescriptionInput)}">
                    <input type="hidden" class="${selectorToClass(selectors.currencyLogoInput)}">
                </td>
                <td>
                    <input type="text" class="${selectorToClass(selectors.currencydiscountInput)}">
                </td>
                <td>
                    <input type="text" class="${selectorToClass(selectors.currencyLifetimeInput)}">
                </td>
                <td><input type="text" class="${selectorToClass(selectors.walletAddressInput)}"></td>
                <td><input type="text" class="${selectorToClass(selectors.blockConfirmationInput)}"></td>
                <td><input type="text" class="${selectorToClass(selectors.currencyDecimalInput)}"></td>
                <td>
                    <button class="action-delete delete-curency-config ${selectorToClass(selectors.btnCancelAddCurrency)}" type="button"><span>Delete</span></button>
                </td>
            </tr>`;
            $("#ezdefi-configuration-coin-table").append(container);
            initCancelAddCurrency();
            initSelectCoinConfig("#select-currency-"+tmp);
            $(".ezdefi-select-coin").on('select2:select', selectCoinListener);
        });

        $(document).on("click", selectors.btnDeleteCurrency, function () {
            var currencyId = $(this).data('currency-id');
            $(".ezdefi__list-currency-delete").append('<input type="hidden" name="groups[ezdefi_payment][fields][currency][value][ids_delete][]" value="'+currencyId+'">');
            $(this).parent().parent().remove();
        });

        function initCancelAddCurrency() {
            $(selectors.btnCancelAddCurrency).click(function () {
                $(this).parent().parent().remove();
            });
        }

        function initSelectCoinConfig(select) {
            var that = this;
            $(select).select2({
                ajax: {
                    url: $("#ezdefi-configuration-add-coin").data('url-get-coin'),
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
                    <div class="ezdefi__select-currency--item">
                        <span>
                            <img src="${repo.logo}" alt="" class="ezdefi__select-currency--logo">
                        </span>
                        <span class='select2-result-repository__title text-justify ezdefi__select-currency--name'>${repo.name}</span>
                    </div>
                </div>
            </div>`;
        };

        function formatRepoSelection(repo) {
            return `<div class='select2-result-repository clearfix select-coin-box' id="${repo.id}">
                <div class='select2-result-repository__meta'>
                    <div class="ezdefi__select-currency--item">
                        <span>
                            <img src="${repo.logo}" alt="" class="ezdefi__select-currency--logo">
                        </span>
                        <span class='select2-result-repository__title text-justify ezdefi__select-currency--name'>${repo.name}</span>
                   </div>
                </div>
            </div>`;
        }

        function selectCoinListener(e) {
            let data = e.params.data;
            let rowElement = e.currentTarget.parentNode.parentNode;

            let idInput          = $(rowElement).find(selectors.currencyIdInput);
            let nameInput        = $(rowElement).find(selectors.currencyNameInput);
            let symbolInput      = $(rowElement).find(selectors.currencySymbolInput);
            let descriptionInput = $(rowElement).find(selectors.currencyDescriptionInput);
            let logoInput        = $(rowElement).find(selectors.currencyLogoInput);
            let decimal          = $(rowElement).find(selectors.currencyDecimalInput);

            idInput         .attr('name', 'groups[ezdefi_payment][fields][currency][value][add]['+data._id+'][id]');
            nameInput       .attr('name', 'groups[ezdefi_payment][fields][currency][value][add]['+data._id+'][name]');
            symbolInput     .attr('name', 'groups[ezdefi_payment][fields][currency][value][add]['+data._id+'][symbol]');
            descriptionInput.attr('name', 'groups[ezdefi_payment][fields][currency][value][add]['+data._id+'][description]');
            logoInput       .attr('name', 'groups[ezdefi_payment][fields][currency][value][add]['+data._id+'][logo]');
            decimal         .attr('name', 'groups[ezdefi_payment][fields][currency][value][add]['+data._id+'][decimal]');

            idInput         .val(data._id)
            nameInput       .val(data.name)
            symbolInput     .val(data.symbol);
            descriptionInput.val(data.description);
            logoInput       .val(data.logo);
            decimal         .val(data.suggestedDecimal);

            $(rowElement).find(selectors.currencyLifetimeInput) .attr('name', 'groups[ezdefi_payment][fields][currency][value][add]['+data._id+'][lifetime]');
            $(rowElement).find(selectors.walletAddressInput)    .attr('name', 'groups[ezdefi_payment][fields][currency][value][add]['+data._id+'][wallet_address]');
            $(rowElement).find(selectors.blockConfirmationInput).attr('name', 'groups[ezdefi_payment][fields][currency][value][add]['+data._id+'][block_confirmation]');
            $(rowElement).find(selectors.currencydiscountInput) .attr('name', 'groups[ezdefi_payment][fields][currency][value][add]['+data._id+'][discount]');
        }
        
        function selectorToClass(selector) {
            return selector.slice(1);
        }
    }
);