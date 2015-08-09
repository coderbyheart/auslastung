Auslastung.Dialog.Success = new Class({
    Extends: Auslastung.Dialog,
	initialize: function() {
        this.parent();
        this.el.addClass('success');
		this.setTitle('Success!');
	}
});