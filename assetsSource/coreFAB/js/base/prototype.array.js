Array.prototype.filterUnique = function() {
	var arr = [];
	var orig = this;

	orig.forEach(function(a, i) {
		if (arr.indexOf(a) < 0) {
			arr.push(a);
		} else {
			orig.splice(i, 1);
		}
	});

	return orig;
};

Array.prototype.getUnique = function() {
	var arr = [];

	this.forEach(function(a) {
		if (arr.indexOf(a) < 0) {
			arr.push(a);
		}
	});

	return arr;
};

Array.prototype.pushUnique = function() {
	for (var i = 0; i < arguments.length; ++i) {
		if (this.indexOf(arguments[i]) < 0) {
			this.push(arguments[i]);
		}
	}

	return this;
};
