Auslastung.Clock = new Class({
	el: null,
	ClockRequest: null,
	timer: null,
	initialize: function( element ) {
        this.el = element;
		this.ClockRequest = new Request.JSON({
			'autoCancel': true,
			'url': '/api/clock',
			'onComplete': function ( response ) {
				if ( Auslastung.View.ResponseChecker.check( response ) ) {
					this.update( response.result );
				} else {
					$clear(this.timer);
				}
			}.bind( this )
		});
		this.ClockRequest.get();
		this.timer = this.ClockRequest.get.periodical( 15000, this.ClockRequest );
    },
	update: function( value ) {
		this.el.empty();
		this.el.appendText( value );
	}
});