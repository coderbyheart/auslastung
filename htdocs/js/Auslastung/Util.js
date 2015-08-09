Auslastung.Util = {
	screenCenter: function( element )
	{
		var eSize = element.getSize();
		var wSize = window.getSize();
		element.setStyles( {
			'position': 'absolute',
			'top': ( wSize.y - eSize.y ) / 2,
			'left': ( wSize.x - eSize.x ) / 2
		} );
	}
};