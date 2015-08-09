Auslastung.editMenu = new Class({
	Extends: Auslastung.actionMenu,
	Implements: Events,
	entryid: null,
	name: null,
	hideAble: false,
	showEl: null,
	hideEl: null,
	hideState: false,
	deleteAble: true,
	customEventListener: null,
	initialize: function( parent, table, id, name, options ) {
		this.parent( parent, table );
		this.entryid = id;
		this.name = name;
		if ( typeof options !== 'undefined' ) {
			for( var i in options ) this[i] = options[i];
		}
		this.el = new Element( 'ul', { 'class': 'editmenu' } );
		this.offEl = new Element( 'li', { 'class': 'off' } ).inject( this.el );
		if ( this.hideAble ) {
			this.hideEl = new Element( 'li', { 'class': 'hide', 'title': 'Verbergen' } ).inject( this.el );
			this.showEl = new Element( 'li', { 'class': 'show', 'title': 'Einblenden' } ).inject( this.el );
			if ( this.hideState ) {
				this.hideEl.setStyle( 'display', 'none' );
			} else {
				this.showEl.setStyle( 'display', 'none' );
			}
		}
		var edit = new Element( 'li', { 'class': 'edit', 'title': 'Bearbeiten' } ).inject( this.el );
		if (this.deleteAble) {
			var del = new Element( 'li', { 'class': 'delete', 'title': 'Löschen' } ).inject( this.el );
		}
		this.el.inject( parent );
		this.el.getChildren().each(function(li) {
			if (!li.hasClass('off')) li.setStyle('display', 'none');
		});
		if (this.customEventListener === null) {
			this.el.addEvent('click', this.eventListener.bind(this));	
		} else {
			this.el.addEvent('click', this.customEventListener.bind(this));
		}
		parent.store( 'editmenu', this );
		parent.setStyle('position', 'relative');
		parent.addEvent( 'mouseenter', function( ev ) {
			this.addClass('active');
			this.retrieve('editmenu').el.getChildren().each(function(li) {
				li.setStyle('display', li.hasClass('off') ? 'none' : 'block');
			});
		} );
		parent.addEvent( 'mouseleave', function( ev ) {
			this.removeClass('active');
			this.retrieve('editmenu').el.getChildren().each(function(li) {
				li.setStyle('display', li.hasClass('off') ? 'block' : 'none');
			});
		} );

	},
	eventListener: function( ev )
	{
		ev.stopPropagation();
		if ( ev.target.hasClass( 'delete' ) ) {
			if ( confirm( this.name + ' wirklich löschen?' ) ) {
				new Request.JSON({
					url: '/api/' + this.table + '/' + this.entryid + '?method=delete',
					onSuccess: function( response ) {
						if ( Auslastung.View.ResponseChecker.check( response ) ) {
							this.parentEl.destroy();
						}
					}.bind( this )
				}).post();
			}
		} else if ( ev.target.hasClass( 'edit' ) ) {
			new Request.JSON({
				url: '/api/' + this.table + '/form',
				onSuccess: function( response ) {
					if ( Auslastung.View.ResponseChecker.check( response ) ) {
						var form = new Auslastung.FormRenderer( this.table, this.entryid, response.result, this.name + ' bearbeiten' );
						form.addEvent( 'saved', function( updatedEntry ) {
							this.fireEvent( 'updated', updatedEntry );
						}.bind( this ) );
					}
				}.bind( this )
			}).get( { 'id': this.entryid } );
		} else if ( ev.target.hasClass( 'hide' ) ) {
			this.hideState = true;
			this.showEl.setStyle( 'display', 'block' );
			this.hideEl.setStyle( 'display', 'none' );
			this.fireEvent( 'hide' );
		} else if ( ev.target.hasClass( 'show' ) ) {
			this.hideState = false;
			this.showEl.setStyle( 'display', 'none' );
			this.hideEl.setStyle( 'display', 'block' );
			this.fireEvent( 'show' );
		}
	}
});