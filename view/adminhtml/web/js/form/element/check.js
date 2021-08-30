define([
    'ko',
    'Magento_Ui/js/form/element/abstract',
    'mage/storage',
    'Magento_Ui/js/modal/alert',
    'jquery',
    'mage/translate'
], function (ko, Abstract, storage, alert, $) {
    'use strict';

    return Abstract.extend({
        defaults: {
            bodyTmpl: 'MageSuite_ErpConnector/form/element/check'
        },
        isChecking: ko.observable(false),
        checkConnection: function () {
            const self = this;
            this.isChecking(true);
            // noinspection JSUnresolvedVariable
            const parentData = self.source.get(self.parentScope);
            storage.post(
                window.checkConfigUrl,
                parentData,
                false,
                'application/x-www-form-urlencoded'
            ).done(function (response) {
                self.isChecking(false);
                if (response.status === 'error') {
                    alert({
                        title: $.mage.__('Error'),
                        content: response.message
                    });
                } else {
                    alert({
                        title: $.mage.__('Success'),
                        content: response.message
                    });
                }
            }).error(function () {
                self.isChecking(false);
            });
        }
    });
});
