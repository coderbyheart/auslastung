Auslastung.checkResponse = new Class({
	alertWindow: null,
	check: function(response, showMessage) {
		if (typeof showMessage === 'undefined') showMessage = true;
		if (typeof response === 'undefined' || response === null) {
			if (showMessage) {
				this.alertWindow.setBody('Empty response received.');
				this.alertWindow.show();
			}
			return false;
		}
		if (typeof response.status === 'undefined' || typeof response.status !== 'string') {
			if (showMessage) {
				this.alertWindow.setBody('Invalid response!');
				this.alertWindow.show();
			}
			return false;
		}
		if (response.status === 'FAILED') {
			if (showMessage) {
				this.alertWindow.setTitle('Request failed.');
				this.alertWindow.setBody(response.message);
				this.alertWindow.show();
			}
			return false;
		}
		return true;
	}
});