Auslastung.View = new Class({
    ResponseChecker:null,
    DatePicker:null,
    loggedIn:false,
    currentView:null,
    views:new Hash({}),
    userinfo:null,
    initialize:function () {
        window.addEvent('domready', this.onDomReady.bind(this));
    },
    switchView:function (newView) {
        this.views.each(function (view, id) {
            if (id !== newView) {
                view.hide();
            }
        });
        this.views.each(function (view, id) {
            if (id === newView) {
                view.show();
            }
        });
        this.currentView = newView;
        document.location.hash = newView;
    },
    onDomReady:function () {
        Auslastung.View.DatePicker = new Auslastung.DatePicker();
        var myAlertWindow = new Auslastung.Dialog.Alert();
        Auslastung.View.ResponseChecker = new Auslastung.checkResponse();
        Auslastung.View.ResponseChecker.alertWindow = myAlertWindow;
        var clock = $('clock');
        if (clock) {
            var myClock = new Auslastung.Clock(clock);
        }
        this.views.set('auth', new Auslastung.View.Auth());
        this.currentView = 'auth';
        this.views.get('auth').addEvent('loggedin', function (userinfo) {
            this.userinfo = userinfo;
            if (!this.views.has('main')) this.views.set('main', new Auslastung.View.Main(this.userinfo.organization));
            this.switchView('main');
        }.bind(this));
        this.views.get('auth').addEvent('loggedout', function () {
            this.switchView('auth');
            this.views.each(function (view, id) {
                if (id !== 'auth') {
                    view.destroy();
                    this.views.erase(id);
                }
            }.bind(this));
        }.bind(this));
    },
    showOrganization:function () {
        if (!this.views.has('organization')) this.views.set('organization', new Auslastung.View.Organization(this.userinfo.organization));
        this.switchView('organization');
    },
    showProfile:function () {
        if (!this.views.has('profile')) this.views.set('profile', new Auslastung.View.Profile(this.userinfo));
        this.switchView('profile');
    },
    showMain:function () {
        this.switchView('main');
    },
    getHashParam:function () {
        var pos = 1;
        var hashParams = document.location.hash.split('/');
        return (typeof hashParams[ pos ] !== 'undefined') ? hashParams[ pos ] : null;
    },
    setHashParam:function (value) {
        document.location.hash = this.currentView + '/' + value;
    }

});
var View = new Auslastung.View();