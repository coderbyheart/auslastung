Auslastung.PlanUpdater = new Class({
	AuslastungTable: null,
	date: new Date(),
	calendarWeekEl: null,
	initialize: function( AuslastungTable ) {
        this.AuslastungTable = AuslastungTable;
		this.PlanRequest = new Request.JSON({
			'autoCancel': true,
			'url': '/api/plan',
			'onComplete': function ( response ) {
				if ( Auslastung.View.ResponseChecker.check( response ) ) {
					this.update( response.result );
				}
			}.bind( this )
		});
		if (View.getHashParam() !== null) {
			this.date = Auslastung.DateHelper.dateFromString(View.getHashParam());
		}
		this.startUpdate();
    },
	startUpdate: function() {
        var myMonth = ( this.date.getMonth() + 1 ).toString();
        if ( myMonth.length === 1 ) myMonth = '0' + myMonth;
        var myDay = this.date.getDate().toString();
        if ( myDay.length === 1 ) myDay = '0' + myDay;
        View.setHashParam(this.date.getFullYear() + '-' + myMonth + '-' + myDay);
        this.PlanRequest.get( { 'date': this.date.getFullYear() + '-' + myMonth + '-' + myDay } );
	},
	update: function( data ) {
		var peopleTrs = this.AuslastungTable.tbody.getChildren();
		this.date = new Date(
			parseInt( data.startdate.substr( 0, 4 ), 10 ),
			parseInt( data.startdate.substr( 5, 2 ), 10 ) - 1,
			parseInt( data.startdate.substr( 8, 2 ), 10 )
		);
		for ( var i = 0; i < data.days.length; i++ ) {
			var Day = new Hash(data.days[i]);

            // Date
			var dateThId = 'day-' + ( i + 1 );
			var dateTh = $( dateThId );
			if ( dateTh ) {
				dateTh.empty();
			} else {
				var dateTh = new Element( 'th', { 'class': 'day', 'id': 'day-' + ( i + 1 ) } ).inject( this.AuslastungTable.trdays );
			}
			dateTh.appendText(Day.date);
			if(Day.get('is_holiday') === true) {
				dateTh.addClass('holiday');
				new Element('br').inject(dateTh);
				new Element('small', {'html': Day.holiday}).inject(dateTh);
			} else {
				dateTh.removeClass('holiday');
			}
            
            // Appointments
            var appointmentThId = 'appointment-' + ( i + 1 );
            var appointmentTh = $( appointmentThId );
            if ( appointmentTh ) {
                appointmentTh.empty();
            } else {
                var appointmentTh = new Element( 'th', { 'class': 'appointmentday', 'id': 'appointment-' + ( i + 1 ) } ).inject( this.AuslastungTable.trappointments );
            }
            appointmentTh.store('date', data.days[ i ].ts);
            var appointments = Day.get('appointments');
            var alen = Day.get('appointments').length;
            while(alen--) {
                var Appointment = new Hash(appointments[alen]);
                var aspan = new Element('span', {'class': 'appointment', 'text': Appointment.get('description'), 'style': 'width: ' + Math.floor( Math.max(( Appointment.get('duration') / 8 ) * 100, 5) ) + '%; color: #' + Appointment.get('textcolor') + '; background-color: #' + Appointment.get('color') + ';'});
                if (Appointment.get('is_holiday')) aspan.addClass('holiday');
                if (Appointment.get('duration') < 8) {
                    aspan.appendText(' (' + Appointment.get('start_time').replace(/:00/, '') + '—' + Appointment.get('end_time').replace(/:00/, ')'));
                }
                aspan.inject(appointmentTh);
                aspan.store('data', appointments[alen]);
            }

			// PersonDays
			for ( var k = 0; k < peopleTrs.length; k++ ) {
				if ( !peopleTrs[ k ].hasClass( 'person' ) ) continue;
				var persondayId = 'personday-' + parseInt( peopleTrs[ k ].retrieve( 'person' ), 10 ) + '-' + i;
				var persondayTd = $( persondayId );
				if ( persondayTd ) {
					persondayTd.empty();
				} else {
					var persondayTd = new Element( 'td', { 'class': 'personday', 'id': persondayId } ).inject( peopleTrs[ k ] );
				}
				persondayTd.store('date', data.days[ i ].ts);
				persondayTd.store('person', peopleTrs[ k ].retrieve('person'));
				persondayTd.store('person_name', peopleTrs[ k ].retrieve('person_name'));
				if(Day.get('is_holiday') === true) {
					persondayTd.addClass('holiday');
					persondayTd.removeClass('personday');
				} else {
					persondayTd.removeClass('holiday');
					persondayTd.addClass('personday');
				}
			}
            

		}
		if ( this.AuslastungTable.weekinfo === null ) {
			this.AuslastungTable.weekinfo = new Element( 'ul', { 'id': 'weekinfo' } ).inject( $( 'main' ) );
			this.AuslastungTable.weekinfo.addEvent( 'click', function( ev ) {
					ev.stopPropagation();
					if ( ev.target.get('tag') === 'li' && ev.target.hasClass( 'iprev' ) ) {
						do {
							this.date.setTime( this.date.getTime() - 86400000 );
						} while( this.date.getDay() !== 1 );
						this.startUpdate();
					} else if ( ev.target.get('tag') === 'li' && ev.target.hasClass( 'inext' ) ) {
						do {
							this.date.setTime( this.date.getTime() + 86400000 );
						} while( this.date.getDay() !== 1 );
						this.startUpdate();
					} else if ( ev.target.get('tag') === 'li' && ev.target.hasClass( 'datepicker-input' ) ) {

					}
			}.bind( this ) );

			new Element( 'li', { 'class': 'link icon iprev' } ).inject( this.AuslastungTable.weekinfo );
			this.calendarWeekEl = new Element( 'li', { 'class': 'link' } ).inject( this.AuslastungTable.weekinfo );
			new Element( 'li', { 'class': 'link icon inext' } ).inject( this.AuslastungTable.weekinfo );

			Auslastung.View.DatePicker.add(this.calendarWeekEl, {'selectType': 'week'});
			Auslastung.View.DatePicker.addEvent('change', function(date, input) {
				if(input === this.calendarWeekEl) {
					this.date = Auslastung.DateHelper.dateFromString(date);
					this.startUpdate();
				}
			}.bind(this));
		}
		if (this.calendarWeekEl === null) {
			this.calendarWeekEl = this.AuslastungTable.weekinfo.getChildren('li')[1];
		}
		this.calendarWeekEl.store('value', data.startdate);
		this.calendarWeekEl.set('html', 'Kalenderwoche ' + data.weeknumber + ' / ' + data.year);
		// Assignments
		if ( typeof data.assignments !== 'undefined' ) {
			for ( var i = 0; i < data.assignments.length; i++ ) {
				var assignmentColor = data.assignments[i].color;
				var assignmentTextColor = data.assignments[i].textcolor === null ? 'inherit' : '#' + data.assignments[i].textcolor;
				for ( var j = 0; j < data.assignments[ i ].days.length; j++ ) {
					var myTd = $( 'personday-' +  data.assignments[ i ].person + '-' + data.assignments[ i ].days[ j ].day );
					if ( myTd ) {
						var mySpan = new Element( 'span', { 'class': 'assignment', 'html': data.assignments[ i ].title } ).inject( myTd ).setStyles( { 'width': Math.floor( ( data.assignments[ i ].days[ j ].hours / 8 ) * 100 ) + '%', 'background-color': '#' + assignmentColor, 'color': assignmentTextColor } );
                        if (data.assignments[i].is_homeoffice == '1') {
                            mySpan.addClass('homeoffice');
                        }
						mySpan.store( 'data', data.assignments[ i ] );
					}
				}
			}
		}
		// Vacations
		if (typeof data.vacations !== 'undefined') {
			for (var i = 0; i < data.vacations.length; i++) {
				for (var j = 0; j < data.vacations[i].days.length; j++) {
					var vacationColor = (data.vacations[i].color === null ? 'BBBBBB' : data.vacations[i].color);
					var vacationTextColor = data.vacations[i].textcolor === null ? 'inherit' : '#' + data.vacations[i].textcolor;
					var myTd = $('personday-' +  data.vacations[i].person + '-' + data.vacations[i].days[j].day);
					if (myTd) {
						var title = data.vacations[i].type;
						if (data.vacations[i].description !== '') title += ': ' + data.vacations[i].description;
						var mySpan = new Element('span', {'class': 'vacation', 'html': title}).inject(myTd).setStyles({'width': Math.floor((data.vacations[i].days[j].hours / 8) * 100) + '%', 'background-color': '#' + vacationColor, 'color': vacationTextColor});
						mySpan.store('data', data.vacations[i]);
					}
				}
			}
		}
	}
});
