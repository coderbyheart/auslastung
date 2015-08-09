Auslastung.AutoCompleter = new Class({
	Implements: Events,
	field: null,
	table: null,
	searchRequest: null,
	resultUl: null,
	selectedIndex: -1,
	results: [],
	lastSearchString: '',
	lastFailedSearch: '',
	initialize: function( field, table ) {
		this.field = field;
		this.table = table;
		this.field.addEvent( 'keyup', function( ev ) {
			if ( ev.code === 40 ) { // down
				ev.stopPropagation();
				this.selectResult( 1 );
			} else if ( ev.code === 38 ) { // up
				ev.stopPropagation();
				this.selectResult( -1 );
			} else if ( ev.code === 13 ) { // enter
				ev.stopPropagation();
				if ( this.selectedIndex > -1 ) {
					this.resultUl.setStyle( 'display', 'none' );
					if ( typeof this.results[ this.selectedIndex ] !== 'undefined' ) this.fireEvent( 'selected', this.results[ this.selectedIndex ].retrieve( 'data' ) );
					this.reset();
				}
			} else if ( ev.code === 39 || ev.code === 37 ) { // left or right
				ev.stopPropagation();
			} else if ( this.field.value.length > 2 ) {
				this.search();
			}
		}.bind( this ) );
		this.searchRequest = new Request.JSON({
			'url': '/api/' + this.table + '/autocompleter',
			'link': 'ignore',
			onSuccess: function( response ) {
				if ( Auslastung.View.ResponseChecker.check( response ) ) {
					if ( response.length > 0 ) {
						this.resultUl.setStyles( { 'display': 'block', 'position': 'absolute' } );
						for ( var i = 0; i < response.result.length; i++ ) {
							var resultLi = new Element( 'li' )
								.inject( this.resultUl );
							resultLi.store( 'data', response.result[i] );
							resultLi.addEvent( 'mouseenter', function( ev ) {
								ev.target.addClass( 'active' );
							} );
							resultLi.addEvent( 'mouseleave', function( ev ) {
								ev.target.removeClass( 'active' );
							} );
							resultLi.addEvent( 'click', function( ev ) {
								var target = ev.target;
								while( target.get('tag') !== 'li' ) target = target.getParent();
								target.getParent().setStyle( 'display', 'none' );
								var data = target.retrieve( 'data' );
								this.fireEvent( 'selected', data );
							}.bind( this ) );
							var lastIndex = 0;
							var index = 1;
							var acResultName = this.escape(response.result[i].name);
							while( index > -1 && index < acResultName.length && acResultName.length > 0 ) {
								var index = acResultName.toLowerCase().indexOf( this.field.value.toLowerCase(), lastIndex );
								if ( index > lastIndex ) {
									new Element( 'span', { 'html': acResultName.substr( lastIndex, index - lastIndex ) } ).inject( resultLi );
								}
								if ( index > -1 ) {
									new Element( 'strong', { 'html': acResultName.substr( index, this.field.value.length ) } ).inject( resultLi );
								} else {
									new Element( 'span', { 'html': acResultName.substr( lastIndex ) } ).inject( resultLi );
								}
								lastIndex = index + this.field.value.length;
							}
							this.results.push( resultLi );
						}
					} else {
						this.lastFailedSearch = this.field.value;
					}
				}
			}.bind( this )
		});
		this.reset();
	},
	reset: function()
	{
		this.results = [];
		this.lastSearchString = '';
		this.lastFailedSearch = '';
	},
	search: function()
	{
		if (this.lastFailedSearch.length === 0 || this.field.value.indexOf(this.lastFailedSearch) === -1) {
			if (this.results.length > 1) {
				this.doSearch();
			} else if (this.results.length === 0) {
				if (this.lastSearchString.length === 0 || this.field.value.indexOf(this.lastSearchString) === -1) this.doSearch();
			} else if (this.results.length === 1) {
				if (this.field.value.indexOf(this.lastSearchString) === -1) this.doSearch();
			} else {
				this.doSearch();
			}
		}
	},
	doSearch: function()
	{
		this.selectedIndex = -1;
		this.results = [];
		this.resultUl = $(this.table + '-autocompleter');
		if (this.resultUl) { this.resultUl.empty(); } else { this.resultUl = new Element('ul', {'class': 'autocompleter', 'id': this.table + '-autocompleter'}).inject(this.field , 'after'); }
		this.lastSearchString = this.field.value;
		this.searchRequest.get({'search': this.field.value});
	},
	selectResult: function( dir )
	{
		if ( this.selectedIndex > -1 ) this.results[ this.selectedIndex ].removeClass( 'active' );
		if ( dir > 0 ) {
			this.selectedIndex++;
		} else {
			this.selectedIndex--;
		}
		if (this.selectedIndex >= this.results.length) {
			this.selectedIndex = 0;
		} else if (this.selectedIndex <= -1) {
			this.selectedIndex = this.results.length - 1;
		}
		this.results[ this.selectedIndex ].addClass( 'active' );
	},
	escape: function(string)
	{
		string = string.replace(/&/, '&amp;');
		string = string.replace(/</, '&lt;');
		string = string.replace(/>/, '&gt;');
		return string;
	}
});