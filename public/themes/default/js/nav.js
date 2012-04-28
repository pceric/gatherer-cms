/*
	Gatherer Content Management System
	Copyright © 2007-2012 by Eric Hokanson
	Powered by MooTools v1.4
*/

var oldColor, oldOrder;

window.addEvent('domready', function() {
	$('navList').setStyle('cursor', 'default');
	var navSortables = new Sortables('#navList, #conList', {
		constrain: true,
		clone: true,
		onStart: function (e) {
			$('navList').setStyle('cursor', 'move');
			oldOrder = navSortables.serialize().flatten();
			oldColor = e.getStyle('background-color');
			e.setStyle('background-color', 'yellow');
		},
		onComplete: function (e) {
			$('navList').setStyle('cursor', 'default');
			e.setStyle('background-color', oldColor);
			if (!navSortables.serialize().flatten().every(function(item, index) {return item == oldOrder[index];}))
				new Request.JSON({url: gcms.options.baseUrl + '/admin/ajax/sort' }).send("nav=" + navSortables.serialize());
		}
	});
});
