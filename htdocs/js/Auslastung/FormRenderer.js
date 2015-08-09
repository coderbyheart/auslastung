Auslastung.FormRenderer = new Class({
	Implements: Events,
	fields: null,
	form: null,
	fieldsDiv: null,
	errorDiv: null,
	table: null,
	entryid: null,
	title: null,
	elements: [],
	index2name: {},
	hasParent: false,
	hasMandatory: false,
	deleteAble: true,
    formHasErrors: false,
	initialize: function( table, id, form, title, parentEl ) {
		if ( form.fields.length <= 0 ) return;
		this.fields = form.fields;
		this.table = table;
		this.deleteAble = form.deleteable;
		if ( typeof id !== 'undefined' ) this.entryid = id;
		this.title = ( typeof title === 'undefined' ) ? 'Eintrag bearbeiten' : title;
		if (typeof parentEl === 'undefined') {
			parentEl = document.body;
			this.hasParent = false;
		} else {
			this.hasParent = true;
		}
		this.form = new Element( 'form', { 'method': 'post', 'action': '', 'class': 'dataform' } );
		if (!this.hasParent) {
			this.form.set('id', 'dataform');
			this.form.addClass('standalone');
		}
		this.form.addEvent( 'submit', function( ev ) { this.submit(); return false; }.bind( this ) );
		this.form.addEvent( 'click', function( ev ) {
			if ( ev.target.get('tag') === 'span' ) {
				switch( ev.target.retrieve( 'type' ) ) {
				case 'cancel':
					this.cancel();
					break;
				case 'submit':
					this.submit();
					break;
				case 'delete':
					if ( this.deleteAble ) this.del();
					break;
				}
			}
		}.bind( this ) );
		this.form.addEvent( 'keydown', function( ev ) {
			if ( ev.code === 27 ) {
				this.cancel();
			}
			if ( ev.code === 13 ) {
				ev.preventDefault();
			}
		}.bind( this ) );

		if (!this.hasParent) new Element( 'h2', { 'html': this.title } ).inject( this.form );
		this.errorDiv = new Element('div').inject(this.form);
		this.fieldsDiv = new Element('div').inject(this.form);
		for ( var i = 0; i < this.fields.length; i++ ) {
			var myField = this.fields[i];
			switch( this.fields[i].type ) {
			case 'radio':
				var myFieldset = new Element( 'fieldset', { 'class': 'radio' } );
				var n = 0;
				for ( var j = 0; j < myField.values.length; j++ ) {
					n++;
					var radioId = 'radio-' + this.table + '-' + myField.name + '-' + n;
					var myRadio = new Element( 'input', { 'type': 'radio', 'id': radioId, 'name': myField.name, 'value': j } ).inject( myFieldset );
					if ( this.fields[i].value == j ) myRadio.set( 'checked', 'checked' );
					new Element( 'label', { 'for': radioId, 'html': myField.values[ j ] } ).inject( myFieldset );
				}
				this.addField( myFieldset, myField.label, null, myField.mandatory );
				break;
			case 'select':
				var mySelect = new Element( 'select', { 'id': this.table + '-' + myField.name, 'name': myField.name } );
				new Element( 'option' ).inject( mySelect );
				for ( var j in myField.values ) {
					var myOption = new Element( 'option', { 'html': myField.values[ j ], 'value': j } ).inject( mySelect );
					if ( this.fields[i].value === j ) myOption.set( 'selected', 'selected' );
				}
                if (myField.mandatory) mySelect.set('required', 'required');
				this.addField( mySelect, myField.label, null, myField.mandatory );
				break;
			case 'static':
				this.addField( new Element( 'span', { 'html': myField.value } ), myField.label, null, myField.mandatory );
				break;
			case 'date':
				var myInput = new Element( 'input', { 'type': 'date', 'maxlength': 10, 'class': 'calendar', 'name': myField.name, 'id': this.table + '-' + myField.name, 'value': this.fields[i].value, 'placeholder': '2011-10-15', 'pattern': '2[0-9]{3}-[01][1-9]-[0123][0-9]' } );
				var fieldP = this.addField( myInput, myField.label, null, myField.mandatory );
                if (myField.mandatory) myInput.set('required', 'required');
				break;
			case 'boolean':
				var myRadio = new Element( 'input', { 'id': this.table + '-' + myField.name, 'name': myField.name, 'type': 'checkbox', 'value': '1' } );
				if (this.fields[i].value === '1') myRadio.set('checked', 'checked');
                if (myField.mandatory) myRadio.set('required', 'required');
				var fieldP = this.addField( myRadio, myField.label, null, myField.mandatory );
				fieldP.addClass('input-boolean');
				break;
            case 'time':
                var myInput = new Element( 'input', { 'type': 'time', 'class': 'time', 'name': myField.name, 'id': this.table + '-' + myField.name, 'value': this.fields[i].value, 'placeholder': '18:00', 'pattern': '[01]*[0-9](:[0-5][0-9])*' } );
                if (myField.mandatory) myInput.set('required', 'required');
                var fieldP = this.addField( myInput, myField.label, null, myField.mandatory );
                break;
			default:
				var fClass = this.fields[i].type;
				if (fClass === 'password') fClass = 'text';
				var myInput = new Element( 'input', { 'type': this.fields[i].type, 'class': fClass, 'name': myField.name, 'id': this.table + '-' + myField.name, 'value': this.fields[i].value } );
				if ( myField.autocomplete ) {
					var table = myField.name.indexOf( '__' ) > -1 ? myField.name.substr( 0, myField.name.indexOf( '__' ) ) : myField.name;
					var ac = new Auslastung.AutoCompleter( myInput, table );
					ac.addEvent( 'selected', function( data ) { this.value = data.name; }.bind( myInput ) );
				}
                if (myField.mandatory) myInput.set('required', 'required');
				this.addField( myInput, myField.label, null, myField.mandatory );
			}
		}
		if (this.hasMandatory) {
			var mandatoryInfo = new Element('small').inject(new Element('p').inject(this.form));
			new Element('span', {'class': 'required', 'html': '*'}).inject(mandatoryInfo);
			mandatoryInfo.appendText(' Pflichtfeld');
		}
		if ( this.entryid !== null && this.deleteAble ) {
			var pButtons = new Element( 'p', { 'class': 'buttons' } ).inject( this.form );
			new Element( 'span', { 'html': 'löschen', 'class': 'button button-delete' } ).inject( pButtons ).store( 'type', 'delete' );
		}
		var pButtons2 = new Element( 'p', { 'class': 'buttons' } ).inject( this.form );
		new Element( 'span', { 'html': 'abbrechen', 'class': 'button' } ).inject( pButtons2 ).store( 'type', 'cancel' );
		new Element( 'span', { 'html': 'absenden', 'class': 'button' } ).inject( pButtons2 ).store( 'type', 'submit' );
		if (!this.hasParent) {
			var oldForm = $('dataform');
			if (oldForm) oldForm.destroy();
		}
		this.form.inject( parentEl );
		if (!this.hasParent) Auslastung.Util.screenCenter( this.form );
	},
	submit: function() {
		for ( var i = 0; i < this.elements.length; i++ ) {
			this.elements[ i ].getParent().removeClass( 'error' );
		}
		var data = this.getData();
        if (this.formHasErrors) {
            this.showError('Die eingegebenen Daten enthielten Fehler.');
            return;
        }
		var url = '/api/' + this.table;
		if ( this.entryid !== null ) url += '/' + this.entryid;
		var FormSaverRequest = new Request.JSON({
			'url': url,
			onSuccess: function( response ) {
				if ( Auslastung.View.ResponseChecker.check( response ) ) {
					this.entryCreated( response.result );
					this.fireEvent( 'saved', response.result );
				} else if (response !== null && response.userFail) {
					this.showError(response.message);
					for( var i = 0; i < response.result.length; i++ ) {
						this.elements[ this.index2name[ response.result[ i ] ] ].getParent().addClass( 'error' );
					}
				}
			}.bind( this )
		}).post( data );
	},
	del: function() {
		if ( confirm( 'Wirklich löschen?' ) ) {
			var DeleteRequest = new Request.JSON({
				'url': '/api/' + this.table + '/' + this.entryid,
				onSuccess: function( response ) {
					if ( Auslastung.View.ResponseChecker.check( response ) ) {
						this.cancel();
						this.fireEvent( 'deleted', response.result );
					}
				}.bind( this )
			}).post( { 'method': 'delete' } );
		}
	},
	getData: function() {
        this.formHasErrors = false;
		var data = {};
		for ( var i = 0; i < this.elements.length; i++ ) {
			if ( this.elements[ i ].get( 'tag' ) === 'fieldset' ) {
				this.elements[ i ].getChildren( 'input' ).each( function( input ) {
                    if ( input.get( 'checked' ) ) {
                        data[ input.name ] = input.value;
                    }
				} );
			} else if (this.elements[i].get('tag') === 'input' && this.elements[i].get('type') === 'checkbox') {
				if (this.elements[i].get('checked')) data[this.elements[i].name] = this.elements[i].get('value');
			} else {
                var input = this.elements[ i ];
                if (input.checkValidity()) {
				    data[ this.elements[ i ].name ] = this.elements[ i ].value;
                } else {
                    input.getParent().addClass('error');
                    this.formHasErrors = true;
                }
			}
		}
		return data;
	},
	cancel: function() {
		Auslastung.View.DatePicker.hide();
		this.form.destroy();
		this.fireEvent( 'cancelled' );
	},
	entryCreated: function( result ) {
		this.cancel();
	},
	addField: function( field, labelText, name, mandatory, valueField ) {
		if (typeof field.name === 'undefined' && typeof name === 'undefined') return;
		if (typeof field.name !== 'undefined') name = field.name;
		if (typeof mandatory === 'undefined') mandatory = false;
        if (typeof valueField === 'undefined') valueField = field;
		if (mandatory) this.hasMandatory = true;
        if (valueField.get('tag') !== 'span') {
		    this.index2name[name] = this.elements.length;
		    this.elements.push( valueField );
        }
		var fieldP = new Element( 'p' ).inject(this.fieldsDiv);
		var label = new Element( 'label', { 'for': valueField.id, 'html': labelText } ).inject(fieldP);
		if (mandatory) new Element('span', {'class': 'required', 'html': ' *'}).inject(label);
		field.inject( fieldP );
		return fieldP;
	},
	getField: function(name)
	{
		return this.elements[this.index2name[name]];
	},
	showError: function(msg)
	{
		this.errorDiv.empty();
		new Element('p', {'html': msg, 'class': 'error'}).inject(this.errorDiv);
	},
    updateDateTimeField: function(ev)
    {
        var field = $(ev.target);
        var master = field.retrieve('master');
        master.set('value', master.retrieve('date').get('value') + ' ' + master.retrieve('time').get('value') + ':00');
    }
});