Auslastung.View.Main = new Class({
	organization: null,
	organizationFeed: null,
	initialize: function(organization) {
		this.organization = organization;
		window.addEvent( 'domready', function(event, organization) {
			var orgH2 = new Element('h2', {'id': 'h2-main'}).inject($('header'));
			var orgH2Text = new Element( 'span', {'html': organization.name}).inject(orgH2);
			var orgaEdit = new Auslastung.editMenu( orgH2, 'organization', organization.id, organization.name, { 'deleteAble': false, 'customEventListener': this.eventListener } );
			var myTable = new Auslastung.AuslastungTable(organization);
			this.organizationFeed = new Auslastung.View.Organization.Feed(organization);
		}.bindWithEvent(this, organization) );
	},
	show: function()
	{
		var auslastung = $('auslastung');
		if (auslastung) {
			auslastung.setStyle('display', 'table');
			$('h2-main').setStyle('display', 'block');
			var weekInfo = $('weekinfo');
			if (weekInfo) weekInfo.setStyle('display', 'block');
		}
		this.organizationFeed.show();
	},
	hide: function()
	{
		var auslastung = $('auslastung');
		if (auslastung) {
			auslastung.setStyle('display', 'none');
			$('h2-main').setStyle('display', 'none');
			$('weekinfo').setStyle('display', 'none');
		}
		this.organizationFeed.hide();
	},
	destroy: function()
	{
		var auslastung = $('auslastung');
		if (auslastung) {
			auslastung.destroy();
			$('weekinfo').destroy();
			$('h2-main').destroy();
		}
		this.organizationFeed.destroy();
	},
	eventListener: function(ev)
	{
		ev.stopPropagation();
		if (ev.target.hasClass('edit')) {
			View.showOrganization(this.organization);
		}
	}
});