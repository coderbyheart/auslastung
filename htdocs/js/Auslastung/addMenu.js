Auslastung.addMenu = new Class({
	Extends: Auslastung.actionMenu,
	Implements: Events,
	initialize: function( parent, table, text ) {
		this.parent( parent, table );
		if ( typeof text === 'undefined' ) text = 'Eintrag hinzufügen';
		this.el = new Element( 'ul', { 'class': 'addmenu' } );
		var add = new Element( 'li', { 'class': 'add', 'html': text } ).inject( this.el );
		this.el.inject( parent );
		add.addEvent( 'click', function( ev ) {
			ev.stopPropagation();
			new Request.JSON({
				url: '/api/' + this.table + '/form',
				onSuccess: function( response ) {
					if ( Auslastung.View.ResponseChecker.check( response ) ) {
						var form = new Auslastung.FormRenderer( this.table, null, response.result, text );
						form.addEvent( 'saved', function( newEntry ) {
							this.fireEvent( 'added', newEntry );
						}.bind( this ) );
					}
				}.bind( this )
			}).get();
		}.bind( this ) );
	}
});