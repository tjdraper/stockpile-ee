(function($) {
	$.fn.hasScrollBar = function() {
		if (! this.get(0)) {
			return false;
		}

		return this.get(0).scrollHeight > this.height();
	}
})(jQuery);
