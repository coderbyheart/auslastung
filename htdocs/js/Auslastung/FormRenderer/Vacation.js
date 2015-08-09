Auslastung.FormRenderer.Vacation = new Class({
	Extends: Auslastung.FormRenderer,
	daysInput: null,
	initialize: function( table, id, fields, title, parentEl ) {
		this.parent( table, id, fields, title, parentEl );
		this.form.addClass('vacation');
		Auslastung.View.DatePicker.addEvent('change', this.updateDays.bind(this));
		Auslastung.View.DatePicker.add(this.getField('start'));
		Auslastung.View.DatePicker.add(this.getField('end'), {'startInput': this.getField('start')});
		this.getField('days').addEvent('change', function() {
			var end = this.getField('end');
			if(end.get('value') !== '') end.set('value', '');
		}.bind(this));

		this.getField('end').addEvent('change', function() {
			var days = this.getField('days');
			if(days.get('value') !== '') days.set('value', '');
		}.bind(this));
	},
	updateDays: function(date, input)
	{
		if(input !== this.getField('start') && input !== this.getField('end')) return;
		var start = this.getField('start').get('value');
		var end = this.getField('end').get('value');
		if(start === '' || end === '') return;
		new Request.JSON({
			url: '/api/datehelper/getWorkingDaysBetweenDates',
			onComplete: function( response ) {
				if ( Auslastung.View.ResponseChecker.check( response ) ) {
					this.getField('days').set('value', response.result);
				}
			}.bind( this )
		}).get({'start': start, 'end': end});
	}
});