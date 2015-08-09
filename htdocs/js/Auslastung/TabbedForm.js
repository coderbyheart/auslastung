Auslastung.TabbedForm = new Class({
	Implements: Events,
	myDiv: null,
	myTabs: new Hash(),
	bodies: new Hash(),
	tabsUl: null,
	activeTab: null,
	initialize: function( tabs ) {
		this.tabs = tabs;
		this.myDiv = new Element('div', {'class': 'tabbedform-bodies'}).inject(document.body);
		this.tabsUl = new Element('ul', {'class': 'tabbedform-tabs'}).inject(document.body);
		for(var i in tabs) {
			this.myTabs.set(i, new Element('li', {'html': tabs[i].label}).inject(this.tabsUl));
			var bodyDiv = new Element('div').inject(this.myDiv);
			bodyDiv.addClass('tabbedform-body');
			this.bodies.set(i, bodyDiv);
			if(this.myTabs.getLength() === 1) {
				this.activeTab = i;
				this.myTabs.get(i).addClass('active');
				bodyDiv.addClass('tabbedform-body-active');
			} else {
				bodyDiv.addClass('tabbedform-body-inactive');
			}
			this.myTabs.get(i).store('id', i);
		}
		this.tabsUl.addEvent('click', this.eventListener.bind(this));
	},
	changeTabs: function()
	{
		for(var i in this.tabs) {
			if(this.activeTab === i) {
				this.myTabs.get(i).addClass('active');
				this.bodies.get(i).addClass('tabbedform-body-active');
				this.bodies.get(i).removeClass('tabbedform-body-inactive');
			} else {
				this.myTabs.get(i).removeClass('active');
				this.bodies.get(i).addClass('tabbedform-body-inactive');
				this.bodies.get(i).removeClass('tabbedform-body-active');
			}
		}
	},
	eventListener: function(ev)
	{
		if(ev.target.get('tag') !== 'li') return;
		if(ev.target.retrieve('id') === this.activeTab) return;
		this.activeTab = ev.target.retrieve('id');
		this.changeTabs();
	},
	show: function()
	{
		this.myDiv.setStyle('display', 'block');
		if (this.activeTab === null) this.activeTab = this.myTabs.getKeys()[0];
		this.changeTabs();
		this.tabHeight = this.myTabs.get(this.activeTab).getSize().y - 1;
		Auslastung.Util.screenCenter(this.myDiv);
		var coords = this.myDiv.getCoordinates();
		this.tabsUl.setStyles({'left': coords.left, 'top': coords.top - this.tabsUl.getCoordinates().height + 1 });
	},
	hide: function()
	{
		this.myDiv.setStyle('display', 'none');
	},
	destroy: function()
	{
		this.myDiv.destroy();
		this.tabsUl.destroy();
	}
});