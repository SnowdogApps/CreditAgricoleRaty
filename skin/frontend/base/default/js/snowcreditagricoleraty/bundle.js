if (typeof CreditagricoleRaty == 'undefined') {
    var CreditagricoleRaty = {};
}
CreditagricoleRaty.Bundle = Class.create();
CreditagricoleRaty.Bundle.prototype = {
    initialize: function (config) {
        this.url = config.url;
        this.btn = 'ca-raty__btn';
        this.btnEl = $$('.' + this.btn)[0];
        this.bundleBtnWrapperEl = $$('.ca-raty-bundle')[0];
        this.bindEvents();
        document.observe('bundle:reload-price', this.priceChange.bind(this));
    },
    bindEvents: function() {
        if (this.bundleBtnWrapperEl) {
            this.bundleBtnWrapperEl.on('click', this.checkPayment.bind(this));
        }
    },
    checkPayment: function (event) {
        if (event.target === $$('.snowcreditagricoleraty-product__image')[0]
            && !$$('.snowcreditagricoleraty-product__btn--disabled')[0]) {
            return true;
        }
        else {
            event.preventDefault();
            this.showTooltip();
        }
    },
    showTooltip: function() {
        var tooltip = $$('.snowcreditagricoleraty-product__tooltip')[0];
        tooltip.removeClassName('snowcreditagricoleraty-product__tooltip--hidden');
        setTimeout(function() {
            tooltip.addClassName('snowcreditagricoleraty-product__tooltip--hidden');
        }, 2500);
    },
    calculatePrice: function (event) {
        return event.memo.price + event.memo.bundle.config.basePrice
    },
    buildUrl: function (event) {
        var url = this.url;
        url += "id/" + event.memo.bundle.config.bundleId;
        url += "/price/" + this.calculatePrice(event);
        url += "/optionIds/";
        var optionIds = this.getSelected(event);
        for (var i = 0; i < optionIds.length; i++) {
            if (i != 0)
                url += ",";
            url += optionIds[i];
        }
        return url;
    },
    priceChange: function (event) {
        var url = this.buildUrl(event);
        new Ajax.Updater('snowcreditagricoleraty-bundleplaceholder', url, {
            method: 'get'
        });
    },
    getSelected: function (event) {
        var bundle = event.memo.bundle;
        var optionIds = new Array();

        if (bundle.config.selected < 1) {
            this.btnEl.addClassName(this.btn + '--disabled');
        }
        else {
            this.btnEl.removeClassName(this.btn + '--disabled');
        }
        for (var option in bundle.config.selected) {
            if (bundle.config.options[option]) {
                for (var i = 0; i < bundle.config.selected[option].length; i++) {
                    var options = bundle.config.selected[option];
                    optionIds.push(options[i]);
                }
            }
        }
        return optionIds;
    }
};
