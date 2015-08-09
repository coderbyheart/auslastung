Auslastung.DatePicker = new Class({
	Implements: Events,
	calDiv: null,
	prevButton: null,
	nextButton: null,
	calTable: null,
	calRequest: null,
	calCacheRequest: null,
	caption: null,
	thr: null,
	tbody: null,
	nextDate: null,
	prevDate: null,
	currentInput: null,
	months: new Hash({}),
	defaultMonth: null,
	initialize: function() {
		new Request.JSON({
			'url': '/api/calendar',
			'onComplete': function(response){
				if ( Auslastung.View.ResponseChecker.check( response ) ) {
					this.months.set(response.result.month, response.result);
					this.defaultMonth = response.result.month;
					this.cacheMonthData(response.result.next);
					this.cacheMonthData(response.result.previous);
				}
			}.bind(this)
		}).get();
		this.cacheMonthData();
		this.initPicker();
	},
	add: function(input, options)
	{
		input.addClass('datepicker-input');
		input.addEvent('click', this.eventListener.bind(this));
		if (typeof options !== 'undefined') {
			for(var i in options) {
				input.store(i, options[i]);
			}
		}
	},
	initPicker: function()
	{
		this.calDiv = new Element('div', {'class': 'datepicker-div'}).inject(document.body);
		this.calDiv.addEvent('click', this.eventListener.bind(this));
		this.caption = new Element('div', {'class': 'datepicker-caption'}).inject(this.calDiv);
		this.calTable = new Element('table', {'class': 'datepicker-table' }).inject(this.calDiv);
		var thead = new Element('thead').inject(this.calTable);
		this.thr = new Element('tr').inject(thead);
		this.tbody = new Element('tbody').inject(this.calTable);
		this.prevButton = new Element('span', {'class': 'datepicker-prev', 'html': '&lt;'}).inject(this.calDiv);
		this.nextButton = new Element('span', {'class': 'datepicker-next', 'html': '&gt;'}).inject(this.calDiv);
	},
	cacheMonthData: function(startDate)
	{
		var data = {};
		if (typeof startDate !== 'undefined') data.start = startDate;
		if (typeof startDate === 'undefined' || !this.months.has(this.getMonthFromDate(startDate))) {
			new Request.JSON({
				'url': '/api/calendar',
				'onComplete': function(response){
					if ( Auslastung.View.ResponseChecker.check( response ) ) {
						this.months.set(response.result.month, response.result);
					}
				}.bind(this)
			}).get(data);
		}
	},
	showMonth: function(monthData)
	{
		this.prevDate = monthData.previous;
		this.nextDate = monthData.next;
		this.caption.set('html', monthData.label);
		this.thr.empty();
		for(var i = 0; i < monthData.weekdays.length; i++) {
			new Element('th', {'html': monthData.weekdays[i]}).inject(this.thr);
		}
		this.tbody.empty();
		for(var i = 0; i < monthData.weeks.length; i++) {
			var tr = new Element('tr').inject(this.tbody);
			for(var j = 0; j < monthData.weeks[i].length; j++) {
				var td = new Element('td', {'html': monthData.weeks[i][j].label}).inject(tr);
				if (monthData.weeks[i][j].holiday) td.addClass('datepicker-holiday');
				if (monthData.weeks[i][j].weekend) td.addClass('datepicker-weekend');
				if (monthData.weeks[i][j].date.substr(5,2) !== monthData.month.substr(5,2)) td.addClass('datepicker-offmonth');
				if (this.getCurrentDate() === monthData.weeks[i][j].date) td.addClass('datepicker-selected');
				td.store('date', monthData.weeks[i][j].date);
			}
		}
		switch (this.currentInput.retrieve('selectType')) {
		case 'week':
			// mark whole week
			this.tbody.getChildren().each( function(tr) {
				tr.getChildren().each( function(td) {
					if (td.hasClass('datepicker-selected')) {
						td.getParent().getChildren().each(function(td) {
							td.addClass('datepicker-selected');
						});
						return;
					}
				});
			});
			this.calTable.addClass('datepicker-table-weekselect');
			this.calTable.removeClass('datepicker-table-dayselect');
			break;
		default:
			this.calTable.removeClass('datepicker-table-weekselect');
			this.calTable.addClass('datepicker-table-dayselect');
		}
		this.calDiv.setStyles({'display': 'block'});
	},
	hide: function()
	{
		this.calDiv.setStyles({'display': 'none'});
		if(this.currentInput !== null) this.currentInput.store('showStatus', false);
	},
	eventListener: function( ev )
	{
		ev.stopPropagation();
		switch(ev.type) {
		case 'click':
			if(ev.target.hasClass('datepicker-prev')) {
				if (this.months.has(this.getMonthFromDate(this.prevDate))) {
					var monthData = this.months.get(this.getMonthFromDate(this.prevDate));
					this.showMonth(monthData);
					this.cacheMonthData(monthData.previous);
				}
			} else if(ev.target.hasClass('datepicker-next')) {
				if (this.months.has(this.getMonthFromDate(this.nextDate))) {
					var monthData = this.months.get(this.getMonthFromDate(this.nextDate));
					this.showMonth(monthData);
					this.cacheMonthData(monthData.next);
				}
			} else if(ev.target.hasClass('datepicker-input')) {
				if (ev.target.retrieve('showStatus') !== true) {
					if (this.currentInput !== null) this.currentInput.store('showStatus', false);
					this.currentInput = ev.target;
					var co = this.currentInput.getCoordinates();
					this.calDiv.setStyles({'left': co.left, 'top': co.top + co.height});
					this.show(this.currentInput);
					this.currentInput.store('showStatus', true);
				} else {
					this.hide();
				}
			}
			// Click on day
			switch(ev.target.get('tag')) {
			case 'td':
				if(!ev.target.hasClass('datepicker-weekend')) {
					switch (this.currentInput.retrieve('selectType')) {
					case 'week':
						var selectedDate = ev.target.getParent().getFirst().retrieve('date');
						break;
					default:
						var selectedDate = ev.target.retrieve('date');
					}
					this.setCurrentDate(selectedDate);
					if (this.currentInput.get('tag') === 'input') {
						this.currentInput.set('value', this.getCurrentDate());
                        this.currentInput.fireEvent('change', {'target': this.currentInput});
						this.currentInput.focus();
					} else {
						this.currentInput.store('value', this.getCurrentDate());
					}
					this.hide();
					this.fireEvent('change', new Array(this.getCurrentDate(), this.currentInput));
				}
				break;
			}
			break;
		}
	},
	show: function(myInput)
	{
		// Get start date for calendar from inputs value
		var selectedDate = this.getInputValue(myInput);
		var startDate = selectedDate;

		// Should we care about a date range?
		if(selectedDate === '') {
			var startInput = myInput.retrieve('startInput');
			if (typeof startInput === 'object' && startInput !== null) {
				startDate = this.getInputValue(myInput.retrieve('startInput'));
			}
		}
		if(startDate !== '' && startDate !== null) {
			this.setCurrentDate(selectedDate);
			if (this.months.has(this.getMonthFromDate(startDate))) {
				this.showMonth(this.months.get(this.getMonthFromDate(startDate)));
			} else {
				new Request.JSON({
					'url': '/api/calendar',
					'onComplete': function(response){
						this.months.set(response.result.month, response.result);
						this.cacheMonthData(response.result.next);
						this.cacheMonthData(response.result.previous);
						this.showMonth(response.result);
					}.bind(this)
				}).get({'start': startDate});
			}
		} else {
			this.showMonth(this.months.get(this.defaultMonth));
		}
	},
	getInputValue: function(myInput)
	{
		if (myInput.get('tag') === 'input') {
			return myInput.get('value');
		} else {
			return myInput.retrieve('value');
		}
	},
	getCurrentDate: function()
	{
		if (this.currentInput === null) return '';
		return this.currentInput.retrieve('currentDate');
	},
	setCurrentDate: function(cdate)
	{
		this.currentInput.store('currentDate', cdate);
		this.currentInput.store('currentMonth', this.getMonthFromDate(cdate));
	},
	getCurrentMonth: function()
	{
		if (this.currentInput === null) return '';
		return this.currentInput.retrieve('currentMonth');
	},
	getMonthFromDate: function(date)
	{
		return date.substr(0, 7);
	}
});