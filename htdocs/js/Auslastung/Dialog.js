Auslastung.Dialog = new Class({
	el: null,
	title: null,
	body: null,
	initialize: function() {
		this.el = new Element( 'div', { 'class': 'dialogue' } );
		this.title = new Element( 'h2' );
		this.body = new Element( 'p' );
		new Element( 'span', { 'html': '[x]' } ).inject( this.el );
		this.title.inject( this.el );
		this.body.inject( this.el );
		this.el.inject( document.body );
		this.el.addEvent( 'click', function() { this.setStyle( 'display', 'none' ); } );
		this.el.setStyle( 'display', 'none' );
	},
	setTitle: function( text ) {
		this.title.empty();
		this.title.appendText( text );
        return this;
	},
	setBody: function( text ) {
		this.body.empty();
		this.body.appendText( text );
        return this;
	},
	show: function() {
		this.el.setStyle( 'display', 'block' );
		this.el.setStyle( 'left', ( window.getSize().x - this.el.getSize().x ) / 2 );
		this.el.setStyle( 'top', '2em' );
        return this;
	}
});