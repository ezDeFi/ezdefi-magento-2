require(
    [
        'jquery',
        'mage/url',
        'Magento_Ui/js/modal/alert',
    ],
    function ($, url, alert) {

        $(document).on("change", '.ezdefi__gateway-url', async () => {
            var validator = $("#config-edit-form").validate();
            validator.resetForm();
            $('.ezdefi__gateway-url').valid()
        });

        $(document).on("change", '.ezdefi__api-key', function () {
            $('.ezdefi__api-key').valid();
        });

        $(document).on("change", '.ezdefi__public-key', function () {
            $('.ezdefi__public-key').valid();
        });
    }
);