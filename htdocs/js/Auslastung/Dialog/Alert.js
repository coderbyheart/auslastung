Auslastung.Dialog.Alert = new Class({
    Extends: Auslastung.Dialog,
	initialize: function() {
        this.parent();
        this.el.addClass('alert');
		this.setTitle('Oops. An error occurred.');
	}
});