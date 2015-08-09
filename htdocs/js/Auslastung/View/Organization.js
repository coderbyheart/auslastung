Auslastung.View.Organization = new Class({
	myH2: null,
	myDiv: null,
	backLink: null,
	organization: null,
	addInput: null,
	teamUl: null,
	initialize: function(organization) {
		this.organization = organization;
		window.addEvent('domready', function(event, organization) {
			this.myH2 = new Element('h2').inject($('header'));
			this.backLink = new Element('p', {'class': 'planback iconlink icon iprev', 'html': 'Zurück zum Plan'}).inject($('main'));
			this.backLink.addEvent('click', function() { View.switchView('main'); });
			this.myDiv = new Element('div', {'id': 'organization', 'class': 'props'}).inject($('main'));
			this.update(organization);
		}.bindWithEvent(this, organization));
	},
	update: function(organization)
	{
		this.myDiv.empty();
		this.myH2.set('html', organization.name + ' bearbeiten');
		// Eigenschaften
		var orgaDiv = new Element('div', {'class': 'propstab'}).inject(this.myDiv);
		new Element('h3', {'html': 'Die Organisation'}).inject(orgaDiv);
		var propsDl = new Element('dl').inject(orgaDiv);
		new Element('dt', {'html': 'Name:'}).inject(propsDl);
		new Element('p', {'class': 'value', 'html': organization.name}).inject(new Element('dd').inject(propsDl));
		var orgaEdit = new Element('span', {'class': 'button', 'html': 'bearbeiten'}).inject(new Element('p', {'class': 'buttons'}).inject(orgaDiv));
		orgaEdit.addEvent('click', function() {
			new Request.JSON({
				url: '/api/organization/form',
				onSuccess: function(response) {
					if (Auslastung.View.ResponseChecker.check(response)) {
						var form = new Auslastung.FormRenderer('organization', this.organization.id, response.result, 'Organisation bearbeiten');
						form.addEvent('saved', function(updatedEntry) {
							this.update(updatedEntry);
						}.bind(this));
					}
				}.bind(this)
			}).get({'id': this.organization.id});
		}.bind(this));

		// Team
		var orgaDiv = new Element('div', {'class': 'propstab'}).inject(this.myDiv);
		new Element('h3', {'html': 'Das Team'}).inject(orgaDiv);

		new Element('h4', {'html': 'Mitglieder hinzufügen'}).inject(orgaDiv);
		new Element('p', {'html': 'Hier kannst Du E-Mail-Adressen von Personen hinzufügen, die an dieser Organisation mitarbeiten dürfen.'}).inject(orgaDiv);
		var addForm = new Element('form', {'action': '', 'method': 'post'}).inject(orgaDiv);
		addForm.addEvent('submit', function(event) {
			event.stopPropagation();
			event.preventDefault();
		}.bind(this));
		var formP = new Element('p').inject(addForm);
		this.addInput = new Element('input', {'value': 'E-Mail Adresse', 'class': 'inactive text'}).inject(formP);
		this.addInput.store('default', 'E-Mail Adresse');
		this.addInput.addEvent('focus', function() {
			this.removeClass('inactive')
			if (this.get('value') === this.retrieve('default')) this.set('value', '');
		});
		this.addInput.addEvent('blur', function() {
			if (!this.get('value')) {
				this.addClass('inactive');
				this.set('value', this.retrieve('default'));
			}
		});
		var addAutoCompleter = new Auslastung.AutoCompleter(this.addInput, 'organization/team');
		addAutoCompleter.addEvent('selected', function(data) {
			this.addInput.set('value', data.email);
		}.bind(this));
		var addMemberLink = new Element('span', {'html': 'Mitglied hinzufügen', 'class': 'icon iconlink iadd'}).inject(formP);
		addMemberLink.addEvent('click', function() {
			this.addMember();
		}.bind(this));

        this.teamUl = new Element('ul', {'class': 'team'}).inject(orgaDiv);
        this.teamUl.addEvent('click', this.userClickHandler.bind(this));
        this.updateMembers();
	},
	addMember: function()
	{
		var email = this.addInput.get('value');
		if (email === '' || email === this.addInput.retrieve('default')) return;
		new Request.JSON({
			url: '/api/organization/' + this.organization.id + '/team',
			onSuccess: function(response) {
				if (Auslastung.View.ResponseChecker.check(response)) {
					var inListLi;
					this.teamUl.getChildren('li').each(function(li) {
						if(li.retrieve('id') === response.result.id) {
							inListLi = li;
						}
					});
					if (typeof inListLi === 'undefined') {
						this.highlightUser(this.addMemberEntry(response.result));
					} else {
						this.highlightUser(inListLi);
					}
				}
			}.bind(this)
		}).post({'email': email});
		this.addInput.addClass('inactive');
		this.addInput.set('value', this.addInput.retrieve('default'));
	},
	updateMembers: function()
	{
		this.teamUl.empty();
		new Request.JSON({
			url: '/api/organization/' + this.organization.id + '/team',
			onSuccess: function(response) {
				if (Auslastung.View.ResponseChecker.check(response)) {
					response.result.each(function (user) {
						this.addMemberEntry(user);
					}.bind(this));
				}
			}.bind(this)
		}).get();
	},
	addMemberEntry: function(user)
	{
		var userLi = new Element('li').inject(this.teamUl);
		userLi.store('id', user.id);
		if (!user.owner && !user.you) {
			var deleteSpan = new Element('span', {'class': 'icon iconlink idelete', 'title': 'löschen'}).inject(userLi);
		}
		if (user.you) new Element('span', {'class': 'icon icontext iuser', 'title': 'Das bist Du!'}).inject(userLi);
		new Element('span', {'class': 'name', 'html': user.name}).inject(userLi);
		if (user.owner) new Element('span', {'class': 'icon icontext iowner', 'title': 'Besitzer'}).inject(userLi);
		new Element('a', {'class': 'icon iemail icontext', 'href': 'mailto:' + user.email, 'html': user.email}).inject(userLi);
		return userLi;
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
	},
	highlightUser: function(li)
	{
		li.highlight('#F8E3A0');
	},
	userClickHandler: function(event)
	{
		event.stopPropagation();
		if (event.target.hasClass('idelete')) {
			var userLi = event.target.getParent();
			new Request.JSON({
				url: '/api/organization/' + this.organization.id + '/team?method=delete',
				onSuccess: function(response) {
					if (Auslastung.View.ResponseChecker.check(response)) {
						this.updateMembers();
					}
				}.bind(this)
			}).post({'user': userLi.retrieve('id')});
		}
	}
});