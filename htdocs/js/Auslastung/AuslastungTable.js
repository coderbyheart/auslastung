Auslastung.AuslastungTable = new Class({
	table: null,
	trdays: null,
    trappointments: null,
    tr: null,
	tdfoot: null,
	tbody: null,
	weekinfo: null,
	PlanUpdater: null,
	overlay: null,
	organization: null,
	filter: new Hash.Cookie( 'AuslastungTableFilter', { 'duration': 356 } ),
	initialize: function(organization) {
		this.organization = organization;
		this.table = new Element( 'table', { 'id': 'auslastung' } ).inject( $( 'main' ) );
		var thead = new Element( 'thead' ).inject( this.table );
		this.trdays = new Element( 'tr' ).inject( thead );
        this.trappointments = new Element( 'tr' ).inject( thead );
		var tfoot = new Element( 'tfoot' ).inject( this.table );
		var trfoot = new Element( 'tr' ).inject( tfoot );
		this.tdfoot = new Element( 'td', { 'colspan': 6 } ).inject( trfoot );
		this.tbody = new Element( 'tbody' ).inject( this.table );
		var myPersonAdd = new Auslastung.addMenu( this.tdfoot, 'person', 'Mitarbeiter hinzufügen' );
		myPersonAdd.addEvent( 'added', function( newEntry ) {
			this.AuslastungTableRequest.get();
		}.bind( this ) );
		this.AuslastungTableRequest = new Request.JSON({
			'autoCancel': true,
			'url': '/api/organization/' + this.organization.id + '/people',
			'onComplete': function ( response ) {
				if ( Auslastung.View.ResponseChecker.check( response ) ) {
					this.update( response.result );
				}
			}.bind( this )
		});
		this.AuslastungTableRequest.get();
		this.table.addEvent( 'click', this.listener.bind( this ) );
		this.overlay = new Element( 'div', { 'id': 'planoverlay' } ).inject( $( 'main' ), 'after' );
		this.table.addEvent( 'mouseover', this.overlayListener.bind( this ) );
		this.table.addEvent( 'mouseout', this.overlayListener.bind( this ) );
    },
	update: function( data ) {
		this.trdays.empty();
        this.trappointments.empty();
		this.tbody.empty();
		new Element( 'th', { 'html': 'Wochentag', 'class': 'right' } ).inject( this.trdays );
        new Element( 'th', { 'html': 'Termine', 'class': 'right' } ).inject( this.trappointments );
		for( var i = 0; i < data.length; i++ ) {
			// List units
			var unitFilter = new Hash(this.filter.get('unit'));
			var unitHidden = unitFilter.get(data[i].id) === true;
			var unitTr = new Element( 'tr', { 'class': 'unit' } ).inject( this.tbody );
			var unitTd = new Element( 'td', { 'class': 'unit' } ).inject( unitTr );
			if (unitHidden) unitTd.addClass('hidden');
			var unitSpan = new Element( 'span', { 'html': data[i].name } ).inject( unitTd );
			for ( var k = 0; k < 5; k++ ) new Element( 'td', { 'class': 'unitday' } ).inject( unitTr );
			var unitEditMenu = new Auslastung.editMenu( unitSpan, 'unit', data[i].id, data[i].name, { 'hideAble': true, 'hideState': unitHidden, 'deleteAble': false } );
			unitEditMenu.addEvent( 'updated', function( updatedEntry ) {
				this.AuslastungTableRequest.get();
			}.bind( this ) );
			unitEditMenu.addEvent( 'hide', function( data ) {
				var unitFilter = new Hash(this.filter.get('unit'));
				unitFilter.set(data.id, true);
				this.filter.set( 'unit', unitFilter );
				this.AuslastungTableRequest.get();
			}.bind( this ).pass( data[i] ) );
			unitEditMenu.addEvent( 'show', function( data ) {
				var unitFilter = new Hash(this.filter.get('unit'));
				unitFilter.set(data.id, false);
				this.filter.set( 'unit', unitFilter );
				this.AuslastungTableRequest.get();
			}.bind( this ).pass( data[i] ) );

			if( unitHidden ) continue;

			// List disciplines
			if( typeof data[i].disciplines !== 'undefined' && data[i].disciplines.length > 0 ) {
				for ( var l = 0; l < data[i].disciplines.length; l++ ) {
					var disciplineFilter = new Hash(this.filter.get('unit-' + data[i].id + '-disciplines'));
					var disciplineHidden = disciplineFilter.get(data[i].disciplines[l].id) === true;
					var disciplineTr = new Element( 'tr', { 'class': 'discipline' } ).inject( this.tbody );
					var disciplineTd = new Element( 'td', { 'class': 'discipline' } ).inject( disciplineTr );
					if (disciplineHidden) disciplineTd.addClass('hidden');
					var disciplineSpan = new Element( 'span', { 'html': data[i].disciplines[l].name } ).inject( disciplineTd );
					for ( var k = 0; k < 5; k++ ) new Element( 'td', { 'class': 'disciplineday' } ).inject( disciplineTr );
					var disciplineEditMenu = new Auslastung.editMenu( disciplineSpan, 'discipline', data[i].disciplines[l].id, data[i].disciplines[l].name, { 'hideAble': true, 'hideState': disciplineHidden, 'deleteAble': false } );
					disciplineEditMenu.addEvent( 'updated', function( updatedEntry ) {
						this.AuslastungTableRequest.get();
					}.bind( this ) );
					disciplineEditMenu.addEvent( 'hide', function( unit, discipline ) {
						var disciplineFilter = new Hash(this.filter.get('unit-' + unit + '-disciplines'));
						disciplineFilter.set(discipline, true);
						this.filter.set('unit-' + unit + '-disciplines', disciplineFilter);
						this.AuslastungTableRequest.get();
					}.bind( this ).pass( new Array( data[i].id, data[i].disciplines[l].id ) ) );
					disciplineEditMenu.addEvent( 'show', function( unit, discipline ) {
						var disciplineFilter = new Hash(this.filter.get('unit-' + unit + '-disciplines'));
						disciplineFilter.set(discipline, false);
						this.filter.set('unit-' + unit + '-disciplines', disciplineFilter);
						this.AuslastungTableRequest.get();
					}.bind( this ).pass( new Array( data[i].id, data[i].disciplines[l].id ) ) );

					if (disciplineHidden) continue;

					// List people
					if( typeof data[i].disciplines[l].people !== 'undefined' && data[i].disciplines[l].people.length > 0 ) {
						for( var j = 0; j < data[i].disciplines[l].people.length; j++ ) {
							var personTr = new Element( 'tr', { 'class': 'person' } ).inject( this.tbody );
							personTr.store( 'person', data[i].disciplines[l].people[j].id );
							personTr.store( 'person_name', data[i].disciplines[l].people[j].name );
							var personTd = new Element( 'td', { 'class': 'person' } ).inject( personTr );
							var personSpan = new Element( 'span', { 'html': data[i].disciplines[l].people[j].name } ).inject( personTd );
							var personEditMenu = new Auslastung.editMenu( personSpan, 'person', data[i].disciplines[l].people[j].id, data[i].disciplines[l].people[j].name );
							personEditMenu.addEvent( 'updated', function( updatedEntry ) {
								this.AuslastungTableRequest.get();
							}.bind( this ) );
						}
					}

				}
			}
		}
		this.PlanUpdater = new Auslastung.PlanUpdater( this );
	},
	listener: function( ev )
	{
		ev.stopPropagation();
		var myEl = new Element( ev.target );
		if ( myEl.hasClass( 'assignment' ) ) {
			new Request.JSON({
				url: '/api/assignment/form',
				onSuccess: function( response, myEl ) {
					if ( Auslastung.View.ResponseChecker.check( response ) ) {
						var form = new Auslastung.FormRenderer.Assignment( 'assignment', myEl.retrieve( 'data' ).id, response.result, 'Zugewiesene Aufgabe bearbeiten' );
						form.addEvent( 'saved', function( updatedEntry ) {
							this.PlanUpdater.startUpdate();
						}.bind( this ) );
						form.addEvent( 'deleted', function( updatedEntry ) {
							this.PlanUpdater.startUpdate();
						}.bind( this ) );
					}
				}.bindWithEvent( this, myEl )
			}).get( { 'id': myEl.retrieve( 'data' ).id } );
		} else if (myEl.hasClass('vacation')) {
			new Request.JSON({
				url: '/api/vacation/form',
				onSuccess: function(response, myEl) {
					if (Auslastung.View.ResponseChecker.check(response)) {
						var form = new Auslastung.FormRenderer.Vacation('vacation', myEl.retrieve('data').id, response.result, 'Urlaub bearbeiten');
						form.addEvent('saved', function(updatedEntry) {
							this.PlanUpdater.startUpdate();
						}.bind(this));
						form.addEvent('deleted', function(updatedEntry) {
							this.PlanUpdater.startUpdate();
						}.bind(this));
					}
				}.bindWithEvent(this, myEl)
			}).get({'id': myEl.retrieve('data').id});
        } else if (myEl.hasClass('appointment')) {
            new Request.JSON({
                url: '/api/appointment/form',
                onSuccess: function(response, myEl) {
                    if (Auslastung.View.ResponseChecker.check(response)) {
                        var form = new Auslastung.FormRenderer.Appointment('appointment', myEl.retrieve('data').id, response.result, 'Termin bearbeiten');
                        form.addEvent('saved', function(updatedEntry) {
                            this.PlanUpdater.startUpdate();
                        }.bind(this));
                        form.addEvent('deleted', function(updatedEntry) {
                            this.PlanUpdater.startUpdate();
                        }.bind(this));
                    }
                }.bindWithEvent(this, myEl)
            }).get({'id': myEl.retrieve('data').id});
        } else if (myEl.hasClass('appointmentday')) {
           new Request.JSON({
               url: '/api/appointment/form',
               onSuccess: function(response, myEl) {
                   if (Auslastung.View.ResponseChecker.check(response)) {
                       response.result.fields[ 1 ].value = myEl.retrieve('date');
                       var form = new Auslastung.FormRenderer.Appointment('appointment', null, response.result, 'Termin anlegen');
                       form.addEvent('saved', function(updatedEntry) {
                           this.PlanUpdater.startUpdate();
                       }.bind(this));
                   }
               }.bindWithEvent(this, myEl)
           }).get();
		} else if ( myEl.hasClass( 'personday' ) ) {
			var tabbedForm = new Auslastung.TabbedForm({
				'aufgabe': {
					'label': 'Aufgabe'
				},
				'urlaub': {
					'label': 'Abwesenheit'
				}
			});

			// Assignment Form
			new Request.JSON({
				url: '/api/assignment/form',
				onSuccess: function( response, myEl, tabbedForm ) {
					if ( Auslastung.View.ResponseChecker.check( response ) ) {
						response.result.fields[ 1 ].value = myEl.retrieve('person_name');
						response.result.fields[ 2 ].value = myEl.retrieve('date');
						var form = new Auslastung.FormRenderer.Assignment('assignment', null, response.result, 'Aufgabe zuweisen', tabbedForm.bodies.aufgabe, myEl.retrieve('person'));
						form.addEvent( 'saved', function( updatedEntry ) {
							this.PlanUpdater.startUpdate();
						}.bind( this ) );
						form.addEvent( 'deleted', function( updatedEntry ) {
							this.PlanUpdater.startUpdate();
						}.bind( this ) );
						form.addEvent( 'cancelled', function( el, tabbedForm ) {
							tabbedForm.destroy();
						}.bindWithEvent( this, tabbedForm ) );
						tabbedForm.show();
					}
				}.bindWithEvent( this, new Array( myEl, tabbedForm ) )
			}).get();

			// Vacation Form
			new Request.JSON({
				url: '/api/vacation/form',
				onSuccess: function( response, myEl, tabbedForm ) {
					if ( Auslastung.View.ResponseChecker.check( response ) ) {
						response.result.fields[ 0 ].value = myEl.retrieve('person_name');
						response.result.fields[ 2 ].value = myEl.retrieve('date');
						var form = new Auslastung.FormRenderer.Vacation( 'vacation', null, response.result, 'Urlaub zuweisen', tabbedForm.bodies.urlaub );
						form.addEvent( 'saved', function( updatedEntry ) {
							this.PlanUpdater.startUpdate();
						}.bind( this ) );
						form.addEvent( 'deleted', function( updatedEntry ) {
							this.PlanUpdater.startUpdate();
						}.bind( this ) );
						form.addEvent( 'cancelled', function( el, tabbedForm ) {
							tabbedForm.destroy();
						}.bindWithEvent( this, tabbedForm ) );
						tabbedForm.show();
					}
				}.bindWithEvent( this, new Array( myEl, tabbedForm ) )
			}).get();
		}
	},
	overlayListener: function( ev )
	{
		ev.stopPropagation();

		if (ev.target.get('tag') === 'span') {
			if ( ev.type === 'mouseout' ) {
				this.overlay.setStyle( 'display', 'none' );
				this.overlay.empty();
			} else {
				var data = ev.target.retrieve('data');
                if (ev.target.hasClass('assignment') || ev.target.hasClass('vacation')) {
                    if (ev.target.hasClass('assignment')) {
                        new Element('strong', {'html': data.title}).inject(this.overlay);
                        new Element('hr').inject(this.overlay);
                    }
                    this.overlay.appendText(
                        data.start.substr(8, 2) + '.'
                        + data.start.substr(5, 2) + '. - '
                        + data.end.substr(8, 2) + '.'
                        + data.end.substr(5, 2) + '.'
                    );
                    new Element('br').inject(this.overlay);
                    this.overlay.appendText(data.duration + ' Stunden');
                    if ( ev.target.retrieve( 'data' ).description !== '' ) {
                        new Element( 'br' ).inject( this.overlay );
                        new Element('strong', {'html': 'Bemerkung: '}).inject(this.overlay);
                        this.overlay.appendText( ev.target.retrieve( 'data' ).description );
                    }
                }
                if (ev.target.hasClass('appointment')) {
                    new Element('strong', {'html': data.description}).inject(this.overlay);
                    new Element('hr').inject(this.overlay);
                    this.overlay.appendText(
                        data.start.substr(8, 2) + '.'
                        + data.start.substr(5, 2) + '. '
                        + data.start.substr(11, 5) + '—'
                        + data.end.substr(11, 5)
                    );
                }

                // Author
                if (data.author != null) {
                    new Element('br').inject(this.overlay);
                    new Element('small', {'html': 'Angelegt von: <strong>' + (data.author.isme ? 'mir' : data.author.name) + '</strong>'}).inject(this.overlay);
                }
				this.overlay.setStyles( { 'display': 'block' } );
			 	this.overlay.setStyles( { 'left': ev.target.getPosition().x + 5, 'top': ev.target.getPosition().y - 5 - this.overlay.getSize().y } );
			}
		}
	}
});
