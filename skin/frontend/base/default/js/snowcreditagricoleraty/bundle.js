if (typeof CreditagricoleRaty == 'undefined') {
	var CreditagricoleRaty = {};
}
CreditagricoleRaty.Bundle = Class.create();
CreditagricoleRaty.Bundle.prototype = {
	initialize: function(config){
		this.url = config.url;
		document.observe('bundle:reload-price', this.priceChange.bind(this));
	},
	calculatePrice: function(event) {
		return event.memo.price + event.memo.bundle.config.basePrice
	},
	buildUrl: function(event) {
		var url = this.url;
		url += "id/" + event.memo.bundle.config.bundleId;
		url += "/price/" + this.calculatePrice(event);
		url += "/optionIds/";
		var optionIds = this.getSelected(event);
		for (var i=0; i < optionIds.length; i++) {
			if(i!=0)
				url += ",";
			url += optionIds[i];
		}
		return url;
	},
	priceChange: function(event) {
		var url = this.buildUrl(event);
		new Ajax.Updater('snowcreditagricoleraty-bundleplaceholder', url, {
			method: 'get'
		});
	},
	getSelected: function(event) {
		var bundle = event.memo.bundle;
		var optionIds = new Array();
		for (var option in bundle.config.selected) {
            if (bundle.config.options[option]) {
                for (var i=0; i < bundle.config.selected[option].length; i++) {
					var options = bundle.config.selected[option];
					optionIds.push(options[i]);
				}
			}
		}
		return optionIds;
	}
}