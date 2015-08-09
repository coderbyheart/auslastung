Auslastung.View.Profile = new Class({
	myH2: null,
	myDiv: null,
	backLink: null,
	user: null,
	initialize: function(user) {
		this.user = user;
		window.addEvent('domready', function(event, user) {
			this.myH2 = new Element('h2').inject($('header'));
			this.backLink = new Element('p', {'class': 'planback iconlink icon iprev', 'html': 'Zurück zum Plan'}).inject($('main'));
			this.backLink.addEvent('click', function() { View.switchView('main'); });
			this.myDiv = new Element('div', {'id': 'profile'}).inject($('main'));
		}.bindWithEvent(this, user));
		this.update(user);
	},
	update: function(user)
	{
		this.myDiv.empty();
		this.myH2.set('html', 'Profil bearbeiten');
		// Eigenschaften
		var orgaDiv = new Element('div', {'class': 'props'}).inject(this.myDiv);
		new Element('h3', {'html': 'Dein Profil'}).inject(orgaDiv);
		var propsDl = new Element('dl').inject(orgaDiv);
		new Element('dt', {'html': 'Name:'}).inject(propsDl);
		new Element('p', {'class': 'value', 'html': user.name}).inject(new Element('dd').inject(propsDl));
		new Element('dt', {'html': 'E-Mail:'}).inject(propsDl);
		new Element('p', {'class': 'value', 'html': user.email}).inject(new Element('dd').inject(propsDl));
		var profileEdit = new Element('span', {'class': 'button', 'html': 'bearbeiten'}).inject(new Element('p', {'class': 'buttons'}).inject(orgaDiv));
		profileEdit.addEvent('click', function() {
			new Request.JSON({
				url: '/api/user/form',
				onSuccess: function(response) {
					if (Auslastung.View.ResponseChecker.check(response)) {
						var form = new Auslastung.FormRenderer('user', this.user.id, response.result, 'Organisation bearbeiten');
						form.addEvent('saved', function(updatedEntry) {
							this.update(updatedEntry);
						}.bind(this));
					}
				}.bind(this)
			}).get({'type': 'profile', 'id': this.user.id});
		}.bind(this));
	},
	show: function()
	{
		this.myDiv.setStyle('display', 'block');
		this.myH2.setStyle('display', 'block');
		this.backLink.setStyle('display', 'block');
	},
	hide: function()
	{
		this.myDiv.setStyle('display', 'none');
		this.myH2.setStyle('display', 'none');
		this.backLink.setStyle('display', 'none');
	},
	destroy: function()
	{
		this.myDiv.destroy();
		this.myH2.destroy();
		this.backLink.setStyle('display', 'none');
	}
});