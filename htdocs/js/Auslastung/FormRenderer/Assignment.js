Auslastung.FormRenderer.Assignment = new Class({
	Extends: Auslastung.FormRenderer,
	person: null,
	initialize: function(table, id, fields, title, parentEl, personId) {
		this.person = personId;
		this.parent( table, id, fields, title, parentEl );
		this.form.addClass('assignment');
		Auslastung.View.DatePicker.addEvent('change', this.updateHours.bind(this));
		Auslastung.View.DatePicker.add(this.getField('start'));
		Auslastung.View.DatePicker.add(this.getField('manual_end'));
		var durationP = this.getField('duration').getParent();
		durationP.addClass('datepicker');
		var pickerSpan = new Element('span').inject(durationP);
		Auslastung.View.DatePicker.add(pickerSpan, {'startInput': this.getField('start')});
	},
	updateHours: function(date, input)
	{
		if(input.get('tag') !== 'span') return;
		var start = this.getField('start').get('value');
		if(start === '') return;
		new Request.JSON({
			url: '/api/datehelper/getHoursBetweenDates',
			onComplete: function( response ) {
				if ( Auslastung.View.ResponseChecker.check( response ) ) {
					this.getField('duration').set('value', response.result);
				}
			}.bind( this )
		}).get({'start': start, 'end': date, 'person': this.person});
	},
	entryCreated: function(result) {
		this.parent(result);
	}
});