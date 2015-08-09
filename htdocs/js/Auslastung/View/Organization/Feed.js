Auslastung.View.Organization.Feed = new Class({
	organization: null,
	myDiv: null,
	myUl: null,
	myBg: null,
	lastTime: null,
	hasItems: false,
	initialize: function(organization) {
		this.organization = organization;
		window.addEvent('domready', function(event, organization) {
			this.myDiv = new Element('div', {'id': 'organizationfeed'}).inject($(document.body), 'bottom');
			this.myDiv.setStyle('display', 'none');
			this.myUl = new Element('ul').inject(this.myDiv);
			this.myBg = new Element('div', {'class': 'bg'}).inject(this.myDiv, 'top');
			new Element('span', {'class': 'dismiss', 'title': 'ausblenden'}).inject(this.myDiv).addEvent('click', this.dismiss.bind(this));
		}.bindWithEvent(this, organization));
		this.update();
		this.update.periodical(60000, this);
	},
	update: function()
	{
		var userLastTime = Auslastung.User.getConfig('organization_feed_dismisstime');
		if (userLastTime === null) userLastTime = this.lastTime;
		new Request.JSON({
			'url': '/api/organization/' + this.organization.id + '/feed',
			'onComplete': function(response) {
				if (Auslastung.View.ResponseChecker.check(response) && response.result.length > 0) {
					this.hasItems = true;

					this.myDiv.setStyle('display', 'block');
					for(var i = 0; i < response.result.length; i++) {
						if (i === 0) this.lastTime = response.result[i].time;
						var myLi = new Element('li').inject(this.myUl);
						myLi.appendText('vor ' + this.niceAge(response.result[i].time));
						myLi.appendText(' | ');
						this.buildItemText(response.result[i]).inject(myLi);
					}
					this.myBg.setStyle('width', this.myUl.getCoordinates().width);
				 	this.myBg.setStyle('height', this.myUl.getCoordinates().height);
					this.myDiv.setStyle('height', this.myUl.getCoordinates().height);
					this.myDiv.setStyle('top', window.getScrollSize().y - this.myDiv.getSize().y - (window.getScrollSize().y * .06));
				}
			}.bind(this)
		}).get({'ts': userLastTime});
	},
	buildItemText: function(item)
	{
		var ret = new Element('span');
		new Element('span', {'class': 'user link', 'html': item.operator}).inject(ret);
		ret.appendText(' hat ');
		switch(item.action) {
		case 'organization.team.add':
			new Element('span', {'class': 'user link', 'html': item.subject}).inject(ret);
		 	ret.appendText(' zum Team ');
			new Element('strong', {'html': 'hinzugefügt'}).inject(ret);
			break;
		case 'organization.team.remove':
			new Element('span', {'class': 'user link', 'html': item.subject}).inject(ret);
		 	ret.appendText(' aus dem Team ');
			new Element('strong', {'html': 'entfernt'}).inject(ret);
			break;
		}
		ret.appendText('.');
		return ret;
	},
	dismiss: function()
	{
		this.myDiv.setStyle('display', 'none');
		this.myUl.empty();
		this.hasItems = false;
		Auslastung.User.setConfig('organization_feed_dismisstime', this.toIso(new Date()));
		Auslastung.User.saveConfig();
	},
	show: function()
	{
		if (this.hasItems) this.myDiv.setStyle('display', 'block');
	},
	hide: function()
	{
		if (this.hasItems) this.myDiv.setStyle('display', 'none');
	},
	destroy: function()
	{
		this.myDiv.destroy();
	},
	niceAge: function(timestamp)
	{
		var then = new Date();
		then.setUTCFullYear(parseInt(timestamp.substr(0, 4), 10));
		then.setUTCMonth(parseInt(timestamp.substr(5, 2), 10) - 1);
		then.setUTCDate(parseInt(timestamp.substr(8, 2), 10));
		then.setUTCHours(parseInt(timestamp.substr(11, 2), 10));
		then.setUTCMinutes(parseInt(timestamp.substr(14, 2), 10));
		then.setUTCSeconds(parseInt(timestamp.substr(17, 2), 10));
		var now = new Date();
		var diff = Math.round((now.getTime() - then.getTime()) / 1000);
		if (diff > 60 * 60 * 24) return this.nText(Math.round(diff / (60 * 60 * 24)), ' Tag', ' Tagen');
		if (diff > 60 * 60 ) return this.nText(Math.round(diff / (60 * 60)), ' Stunde', ' Stunden');
		if (diff > 60 ) return this.nText(Math.round(diff / (60)), ' Minute', ' Minuten');
		return this.nText(diff, ' Sekunde', ' Sekunden');
	},
	nText: function(number, singular, plural) {
		if (parseInt(number) === 1) return number + singular;
		return number + plural;
	},
	toIso: function(date)
	{
		dy = date.getUTCFullYear();
		dm = date.getUTCMonth() + 1;
		dd = date.getUTCDate();
		dH = date.getUTCHours();
		dM = date.getUTCMinutes();
		dS = date.getUTCSeconds();
		ys = new String(dy);
		ms = new String(dm);
		ds = new String(dd);
		Hs = new String(dH);
		Ms = new String(dM);
		Ss = new String(dS);
		if (ms.length === 1 ) ms = '0' + ms;
		if (ds.length === 1 ) ds = '0' + ds;
		if (Hs.length === 1 ) Hs = '0' + Hs;
		if (Ms.length === 1 ) Ms = '0' + Ms;
		if (Ss.length === 1 ) Ss = '0' + Ss;
		return ys + '-' + ms + '-' + ds + ' ' + Hs + ':' + Ms + ':' + Ss;
	}
});