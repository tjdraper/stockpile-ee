/*============================================================================*\
	DO NOT EDIT THIS FILE. THIS IS A CORE FILE.
/*============================================================================*/

(function(F) {
	'use strict';

	// Set up model functions
	var setupModels = function() {
		// Set data-set variables in model storage
		$('[data-set]').each(function() {
			var $el = $(this),
				key = $el.data('set'),
				val = $el.data('value'),
				arrayObjTest = key.slice(-2),
				arrayObjKey = key.slice(0, -2);

			if (arrayObjTest === '[]') {
				if (! F.vars[arrayObjKey]) {
					F.vars[arrayObjKey] = [];
				}

				F.vars[arrayObjKey].push(val);
			} else if (arrayObjTest === '{}') {
				val = val.split(':');

				if (! F.vars[arrayObjKey]) {
					F.vars[arrayObjKey] = {};
				}

				F.vars[arrayObjKey][val[0]] = val[1];
			} else {
				F.vars[key] = val;
			}
		});

		/**
		 * Method for setting model vars
		 *
		 * @param {string} varName - Name of the variable to set
		 * @param {*} value - Value to set for this variable
		 * @param {object} [obj] - Object to set vars to
		 */
		F.set = function(varName, value, obj) {
			var initialVal;
			var namespace;

			// Set what object storage to get
			obj = obj || F;

			// Save the initial value
			initialVal = F.get(varName, null, obj);

			// Set the model value
			obj.vars[varName] = value;

			// If the initial value and the new value are the same, we're done
			// here
			if (initialVal === value) {
				return;
			}

			// Run global events
			if (obj.varEvents.global) {
				// Go through each namespace
				for (namespace in obj.varEvents.global) {
					// Run each function in the array
					obj.varEvents.global[namespace].forEach(function(callback) {
						callback(value, initialVal);
					});
				}
			}

			// Run events for only this variable
			if (obj.varEvents[varName]) {
				// Go through each namespace
				for (namespace in obj.varEvents[varName]) {
					// Run each function in the array
					obj.varEvents[varName][namespace].forEach(function(callback) {
						callback(value, initialVal);
					});
				}
			}
		};

		/**
		 * Method for getting model vars
		 *
		 * @param {string} getVar - Name of variable to get
		 * @param {*} [defaultVal] - Default value to return if no var
		 * @param {object} [obj] - Object to get vars from
		 * @return {*} - Variable value, default value, or null
		 */
		F.get = function(varName, defaultVal, obj) {
			obj = obj || F;

			if (
				obj.vars[varName] !== null &&
				obj.vars[varName] !== undefined
			) {
				return obj.vars[varName];
			}

			return defaultVal || null;
		};

		/**
		 * Model events
		 *
		 * @param  {string} e - On event
		 * @param  {Function} callback - Callback to run on event
		 * @param {object} obj - Object to set varEvents on
		 */
		F.on = function(e, callback, obj) {
			var event;
			var key;
			var namespace;
			var temp;

			// Determin obj storage to set event on
			obj = obj || F;

			// Get event namespace if it exists
			temp = e.split('.');
			namespace = temp[1] || 'noNamespace';

			// Split event into event and key
			temp = temp[0].split(':');
			event = temp[0];
			key = temp[1] || 'global';

			// May have other events in the future, for now, check that this
			// is a change event
			if (event !== 'change') {
				return;
			}

			// Make sure an object exists for the key
			if (! obj.varEvents[key]) {
				obj.varEvents[key] = {};
			}

			if (! obj.varEvents[key][namespace]) {
				obj.varEvents[key][namespace] = [];
			}

			// Add events
			obj.varEvents[key][namespace].push(callback);
		};

		/**
		 * Method to remove model events
		 *
		 * @param  {string} e - On event
		 * @param {object} obj - Object to set varEvents on
		 */
		F.off = function(e, obj) {
			var event;
			var key;
			var namespace;
			var temp;

			// Determine obj storage to set event on
			obj = obj || F;

			// Get event namespace if it exists
			temp = e.split('.');
			namespace = temp[1] || 'noNamespace';

			// Split event into event and key
			temp = temp[0].split(':');
			event = temp[0];
			key = temp[1] || 'global';

			if (obj.varEvents[key] && obj.varEvents[key][namespace]) {
				delete obj.varEvents[key][namespace];
			}
		};

		// Set run variables from DOM
		$('[data-exec]').each(function() {
			var name = $(this).data('exec');

			if (name && F.exec.indexOf(name) === -1) {
				F.exec.push(name);
			}
		});
	};

	// Run models setup when DOM ready
	$(function() {
		setupModels();
	});
})(window.STOCKPILE);
