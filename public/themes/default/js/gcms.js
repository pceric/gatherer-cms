/*
	Gatherer Content Management System
	Copyright © 2007-2012 by Eric Hokanson
	Powered by MooTools v1.4

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU Lesser General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU Lesser General Public License for more details.
	
	You should have received a copy of the GNU Lesser General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/* MooTools Class for GCMS */
var MooGCMS = new Class({
    Implements: Options,
    options: { baseUrl: '', locale: 'en_US' },
    initialize: function(options){
        this.setOptions(options)
    },
    doSearch: function(el) {
        var baseUrl = this.options.baseUrl + '/';
        if ($('content').retrieve('old') == null)
            this.storeContent();
        new Request.JSON({url: this.options.baseUrl + '/search/ajax/search', onSuccess: function(results){
            var count = Object.getLength(results);
            new Fx.Tween('content', { property: 'opacity' }).start(1,0).chain(function(){
                $('content').empty();
                if (count > 0) {
                    $('content').grab(new Element('h3', { text: 'Found ' + count + ' result(s) matching your search:' }));
                    var ul = new Element('ul', { 'class': 'search-results' });
                    $('content').grab(ul);
                    Object.each(results, function(v){
                        new Element('li', { html: '<h3><a href="' + baseUrl + v["url"] + '">' + v["title"] + '</a></h3><div><cite>' + v["url"] + ' - ' + new Date(v["timestamp"] * 1000).format('%x') + '</cite></div>'}).inject(ul);
                    });
                } else {
                    $('content').grab(new Element('h3', { text: 'Sorry, no results.' }));
                }
                $('content').grab(new Element('p', { html: '<a href="javascript:gcms.retrieveContent()">Cancel</a>' }));
                this.start(0,1);
            })
        }}).send('query=' + encodeURIComponent(el.value));
        return false;
    },
    storeContent: function() {
        $('content').store('old', $('content').get('html'));
    },
    retrieveContent: function() {
        $('content').set('html', $('content').retrieve('old'));
    },
    loadTips: function(id) {
        var myTips = new Tips(id, {
            text:null,
            className:'tooltip',
            onShow: function(tip, el){
                tip.fade('in');
            },
            onHide: function(tip, el){
                tip.fade('out');
            }
        });
    }
});

window.addEvent('domready', function(){
    gcms.loadTips('.myTips');  // gcms is declared in <head>
    $$('#sidebar-navigation ul').each(function(el){ el.addClass('nav nav-list'); });
});
