Auslastung.actionMenu = new Class({
	Implements: Events,
	el: null,
	parentEl: null,
	table: null,
	initialize: function( parent, table ) {
		this.parentEl = parent;
		this.table = table;
	},
	show: function() {
		this.el.setStyle( 'display', 'block' );
	},
	hide: function() {
		this.el.setStyle( 'display', 'none' );
	}
});