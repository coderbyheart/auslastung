Auslastung.DateHelper = {
	dateFromString: function(string) {
		return new Date(
			parseInt(string.substr(0, 4), 10),
			parseInt(string.substr(5, 2), 10) - 1,
			parseInt(string.substr(8, 2), 10)
		);
	}
};