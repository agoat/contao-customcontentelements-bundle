

Backend.makeGroupsSortable = function(pattern) {
	var group = new Sortables('sub_' + pattern, {
		constrain: true,
		opacity: 0.6,
		handle: '.drag-handle'
	});

	group.active = false;

	group.addEvent('start', function() {
		group.active = true;
	});

	group.addEvent('complete', function(el) {
		if (!group.active) return;
		var id, pid;

		if (el.getPrevious('div')) {
			id = el.get('class').replace(/\D/g, '');
			pid = el.getPrevious('div').get('class').replace(/\D/g, '');
			new Request.Contao().post({'action':'moveGroup', 'id':id, 'pid':pid, 'pattern':pattern, 'REQUEST_TOKEN':Contao.request_token});
		} else {
			id = el.get('class').replace(/\D/g, '');
			new Request.Contao().post({'action':'moveGroup', 'id':id, 'pattern':pattern, 'REQUEST_TOKEN':Contao.request_token});
		}
	});
	
	//if (typeof groups !== 'object') groups = {};
	//groups[pattern] = group;
};

AjaxRequest.deleteGroup = function(el, pattern, id) {
	el.blur()
	
	new Request.Contao({
		field: el,
		evalScripts: false,
		onSuccess: function(txt, json) {
			$(el).getParent('div.widget').destroy();

			if ($('sub_'+pattern).getChildren('div').length == 1)
			{
				$('sub_'+pattern).getElements('button.delete-handle').set('disabled','disabled');
			}
			$('sub_'+pattern).getElements('button.insert-handle').set('disabled','');
			$('sub_'+pattern).getPrevious().getElements('button.insert-handle').set('disabled','');
		}
	}).post({'action':'deleteGroup', 'id':id, 'pattern':pattern, 'REQUEST_TOKEN':Contao.request_token});
}

AjaxRequest.insertGroup = function(el, pattern, pid, max) {
	el.blur()
	
	new Request.Contao({
		field: el,
		evalScripts: false,
		onRequest: AjaxRequest.displayBox(Contao.lang.loading + ' …'),
		onSuccess: function(txt, json) {
			var div = new Element('div', {'html': txt,}).getChildren()
	
			if (pid) {
				div.inject(($(el).getParent('div.widget')), 'after');
			}
			else {
				div.inject(($('sub_' + pattern)), 'top');
			}

			// Execute scripts after the DOM has been updated
			if (json.javascript) {

				// Use Asset.javascript() instead of document.write() to load a
				// JavaScript file and re-execute the code after it has been loaded
				document.write = function(str) {
					var src = '';
					str.replace(/<script src="([^"]+)"/i, function(all, match){
						src = match;
					});
					src && Asset.javascript(src, {
						onLoad: function() {
							Browser.exec(json.javascript);
						}
					});
				};

				Browser.exec(json.javascript);
			}

			// Update the referer ID
			div[0].getElements('a').each(function(el) {
				el.href = el.href.replace(/&ref=[a-f0-9]+/, '&ref=' + Contao.referer_id);
			});
			
			// Add to Sortables
			//groups[pattern].addItems(div[0]);
			Backend.makeGroupsSortable(pattern);
			
			if ($('sub_'+pattern).getChildren('div').length >= max)
			{
				$('sub_'+pattern).getElements('button.insert-handle').set('disabled','disabled');
				$('sub_'+pattern).getPrevious().getElements('button.insert-handle').set('disabled','disabled');
			}
			$('sub_'+pattern).getElements('button.delete-handle').set('disabled','');

			AjaxRequest.hideBox();

			// HOOK
			window.fireEvent('ajax_change');
		}
	}).post({'action':'insertGroup', 'pid':pid, 'pattern':pattern, 'REQUEST_TOKEN':Contao.request_token});
}

AjaxRequest.toggleSubpattern = function(el, id, pattern) {
	el.blur();
	var item = $('sub_' + pattern);

	if (item) {
		if (!el.value) {
			el.value = 1;
			el.checked = 'checked';
			item.setStyle('display', null);
			item.getElements('[data-required]').each(function(el) {
				el.set('required', '').set('data-required', null);
			});
			new Request.Contao({field:el}).post({'action':'toggleSubpattern', 'id':id, 'pattern':pattern, 'state':1, 'REQUEST_TOKEN':Contao.request_token});
		} else {
			el.value = '';
			el.checked = '';
			item.setStyle('display', 'none');
			item.getElements('[required]').each(function(el) {
				el.set('required', null).set('data-required', '');
			});
			new Request.Contao({field:el}).post({'action':'toggleSubpattern', 'id':id, 'pattern':pattern, 'state':0, 'REQUEST_TOKEN':Contao.request_token});
		}
		return;
	}

	new Request.Contao({
		field: el,
		evalScripts: false,
		onRequest: AjaxRequest.displayBox(Contao.lang.loading + ' …'),
		onSuccess: function(txt, json) {
			var div = new Element('div', {
				'id': 'sub_' + pattern,
				'class': 'subpal cf',
				'html': txt
			}).inject($(el).getParent('div').getParent('div'), 'after');

			// Execute scripts after the DOM has been updated
			if (json.javascript) {

				// Use Asset.javascript() instead of document.write() to load a
				// JavaScript file and re-execute the code after it has been loaded
				document.write = function(str) {
					var src = '';
					str.replace(/<script src="([^"]+)"/i, function(all, match){
						src = match;
					});
					src && Asset.javascript(src, {
						onLoad: function() {
							Browser.exec(json.javascript);
						}
					});
				};

				Browser.exec(json.javascript);
			}

			el.value = 1;
			el.checked = 'checked';

			// Update the referer ID
			div.getElements('a').each(function(el) {
				el.href = el.href.replace(/&ref=[a-f0-9]+/, '&ref=' + Contao.referer_id);
			});

			AjaxRequest.hideBox();

			// HOOK
			window.fireEvent('subpalette'); // Backwards compatibility
			window.fireEvent('ajax_change');
		}
	}).post({'action':'toggleSubpattern', 'id':id, 'pattern':pattern, 'load':1, 'state':1, 'REQUEST_TOKEN':Contao.request_token});
}

AjaxRequest.switchSubpattern = function(el, id, pattern) {
	el.blur();

	new Request.Contao({
		field: el,
		evalScripts: false,
		onRequest: AjaxRequest.displayBox(Contao.lang.loading + ' …'),
		onSuccess: function(txt, json) {
			var div = new Element('div', {
				'id': 'sub_' + pattern,
				'class': 'subpal cf',
				'html': txt
			}).replaces($('sub_' + pattern));
			
			// Execute scripts after the DOM has been updated
			if (json.javascript) {

				// Use Asset.javascript() instead of document.write() to load a
				// JavaScript file and re-execute the code after it has been loaded
				document.write = function(str) {
					var src = '';
					str.replace(/<script src="([^"]+)"/i, function(all, match){
						src = match;
					});
					src && Asset.javascript(src, {
						onLoad: function() {
							Browser.exec(json.javascript);
						}
					});
				};

				Browser.exec(json.javascript);
			}

			// Update the referer ID
			div.getElements('a').each(function(el) {
				el.href = el.href.replace(/&ref=[a-f0-9]+/, '&ref=' + Contao.referer_id);
			});

			AjaxRequest.hideBox();

			// HOOK
			window.fireEvent('subpalette'); // Backwards compatibility
			window.fireEvent('ajax_change');
		}
	}).post({'action':'switchSubpattern', 'id':id, 'pattern':pattern, 'option':el.value, 'REQUEST_TOKEN':Contao.request_token});
}
