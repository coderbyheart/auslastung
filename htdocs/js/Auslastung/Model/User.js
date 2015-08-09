Auslastung.Model.User = new Class({
	id: null,
	email: null,
	name: null,
	config: null,
	initialize: function(data) {
		this.id = data.id;
		this.name = data.name;
		this.email = data.email;
		this.config = new Hash(data.config);
	},
	setConfig: function(name, value)
	{
		this.config.set(name, value);
	},
	getConfig: function(name)
	{
		if (this.config.has(name)) return this.config.get(name);
		return null;
	},
	saveConfig: function()
	{
		new Request.JSON({
			'url': '/api/user/' + this.id + '/config',
			'onSuccess': function(response){
				if (Auslastung.View.ResponseChecker.check(response)) {
					this.config = new Hash(response.result);
				}
			}
		}).post(this.config.getClean());
	}
});