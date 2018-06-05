if (typeof CreditagricoleRaty == 'undefined') {
    var CreditagricoleRaty = Class.create();
}

CreditagricoleRaty.Service = Class.create();
CreditagricoleRaty.Service.prototype = {
    initialize: function (url) {
        this.url = url;
    },

    calculateInstallment: function (creditAmount) {
        var request = new Ajax.Updater('service-calculate-installment', this.url, {
            parameters: {creditAmount: creditAmount},
            onLoading: function () {
                this.container = $('service-calculate-installment');
                this.container.addClassName('disabled');
                this.container.setStyle({opacity: .5});
                Element.show('calculateinstallment-please-wait');
            },
            onComplete: function () {
                this.container.removeClassName('disabled');
                this.container.setStyle({opacity: 1});
            }
        });
    }
}
