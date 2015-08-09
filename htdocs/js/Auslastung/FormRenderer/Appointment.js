Auslastung.FormRenderer.Appointment = new Class({
	Extends: Auslastung.FormRenderer,
	initialize: function(table, id, fields, title, parentEl) {
		this.parent( table, id, fields, title, parentEl );
		this.form.addClass('appointment');
        Auslastung.View.DatePicker.add(this.getField('day'));
	},
	entryCreated: function(result) {
		this.parent(result);
	}
});