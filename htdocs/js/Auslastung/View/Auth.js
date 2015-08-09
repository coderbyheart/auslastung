Auslastung.View.Auth = new Class({
	Implements: Events,
	userinfo: null,
	initialize: function() {
		window.addEvent('domready', this.setup.bind(this));
	},
	setup: function()
	{
		this.userinfo = new Element('ul', {'id': 'userinfo'}).inject(document.body);
		this.userinfo.addEvent('click', function (event) {
			event.stopPropagation();
			switch (event.target.get('id')) {
			case 'link-login':
				this.handleLogin();
				break;
			case 'link-logout':
				this.handleLogout();
				break;
			case 'link-profile':
				View.showProfile();
				break;
			case 'link-register':
				this.showRegisterForm();
				break;
			}
		}.bind(this));
		// Check if user is already logged in
		new Request.JSON({
			'url': '/api/login',
			'onComplete': function(response) {
				if (Auslastung.View.ResponseChecker.check(response, false)) {
					this.viewLogoutInterface(response.result);
				} else {
					this.viewLoginInterface();
				}
			}.bind(this),
			'onFailure': function(xhr) {
				this.viewLoginInterface();
			}.bind(this)
		}).get();
	},
	hide: function()
	{
		$('about').setStyle('display', 'none');
	},
	show: function()
	{
		$('about').setStyle('display', 'block');
	},
	viewLoginInterface: function()
	{
		var userTemp = this.userinfo.dispose();
		userTemp.empty();
		new Element('li', {'id': 'link-login', 'class': 'link', 'html': 'Anmelden'}).inject(userTemp);
		var registerLink = new Element('li', {'class': 'last'}).inject(userTemp);
		registerLink.appendText('Noch keinen Account? Jetzt ');
		new Element('span', {'id': 'link-register', 'class': 'link', 'html': 'registrieren'}).inject(registerLink);
		registerLink.appendText('!');
		userTemp.inject(document.body)
		Auslastung.User = null;
		this.fireEvent('loggedout');
	},
	viewLogoutInterface: function(updatedEntry)
	{
		new Element('li', {'html': 'Angemeldet als ' + updatedEntry.name}).inject(this.userinfo);
		new Element('li', {'id': 'link-profile', 'class': 'link', 'html': 'Profil'}).inject(this.userinfo);
		new Element('li', {'id': 'link-logout', 'class': 'link last', 'html': 'Abmelden'}).inject(this.userinfo);
		Auslastung.User = new Auslastung.Model.User(updatedEntry);
		this.fireEvent('loggedin', updatedEntry);
	},
	handleLogin: function()
	{
		new Request.JSON({
			'url': '/api/user/form',
			'onComplete': function(response) {
				if ( Auslastung.View.ResponseChecker.check(response)) {
					var form = new Auslastung.FormRenderer('login', null, response.result, 'Anmelden');
					var passwordP = form.getField('password').getParent();
					new Element('br').inject(passwordP);
					var lostPwLink = new Element('span', {'html': 'Passwort vergessen?', 'class': 'link'}).inject(passwordP);
					lostPwLink.addEvent('click', function(event, oldForm) {
						oldForm.cancel();
						new Request.JSON({
							'url': '/api/user/form',
							'onComplete': function(response) {
								if ( Auslastung.View.ResponseChecker.check(response)) {
									var form = new Auslastung.FormRenderer('lostpassword', null, response.result, 'Passwort vergessen');
									form.addEvent('saved', function() {
										Auslastung.View.ResponseChecker.alertWindow.setTitle('Passwort vergessen');
										Auslastung.View.ResponseChecker.alertWindow.setBody('Ein neues Passwort wurde an deine E-Mail-Adresse versandt.');
										Auslastung.View.ResponseChecker.alertWindow.show();
									});
								}
							}.bind(this)
						}).get({'type': 'lostpassword'});
					}.bindWithEvent(this, form));
					form.getField('email').set('value', $('credentials_email').get('value'));
					form.getField('password').set('value', $('credentials_password').get('value'));
					form.addEvent('saved', function(updatedEntry) {
						$('credentials_email').set('value', form.getField('email').get('value'));
						$('credentials_password').set('value', form.getField('password').get('value'));
						$('credentials').submit();
					}.bindWithEvent(this, form));
				}
			}.bind(this)
		}).get({'type': 'login'});
	},
	handleLogout: function()
	{
		new Request.JSON({
			'url': '/api/logout',
			'onComplete': function(response) {
				if ( Auslastung.View.ResponseChecker.check(response)) {
					this.userinfo.empty();
					this.viewLoginInterface();
				}
			}.bind(this)
		}).get();
	},
	showRegisterForm: function()
	{
		new Request.JSON({
			'url': '/api/user/form',
			'onComplete': function(response) {
				if ( Auslastung.View.ResponseChecker.check( response ) ) {
					var form = new Auslastung.FormRenderer( 'user', null, response.result, 'Registrieren' );
                    form.addEvent('saved', function(updatedEntry) {
                        new Auslastung.Dialog.Success().setBody('Eine E-Mail mit den Zugangsdaten wurde an ' + updatedEntry.email + ' versandt.').show();
                    }.bindWithEvent(this, form));
				}
			}.bind(this)
		}).get({'type': 'register'});
	}
});