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


/* MooTools Admin Class for GCMS */
var MooGCMSAdmin = new Class({
    Implements: Options,
    options: { baseUrl: '', locale: 'en_US' },
    initialize: function(options){
        this.setOptions(options)
    },
    enableEdit: function(){
        // InlineEdit enable
	    $$('p.annotation').each(function(el){
		    if (el.get('text') == '')
			    el.set('text', '[Add Annotation]');
		    el.setStyle('cursor','pointer');
		    el.addEvent('click',function(){
			    this.inlineEdit({stripHtml:false,
				    onComplete:function(el){
					    if (this.originalText != this.getContent())
						    var myAjax = new Request({ url: gcms.options.baseUrl + '/news/ajax/annotate/' }).send('table=reader&id=' + el.getProperty('id') + '&data=' + encodeURIComponent(this.getContent()));
				    }
			    });
		    });
	    });

        /* TODO: Inline Edit News Body
        $$('.news .story p.content').each(function(el){
            el.setStyle('cursor','pointer');
            el.addEvent('click',function(){
                this.inlineEdit({stripHtml:false,
                    onComplete:function(el){
                        if (this.originalText != this.getContent())
                            var myAjax = new Request({ url: gcms.options.baseUrl + '/news/ajax/edit/table/news/' }).send('id=' + el.getProperty('id') + '&data=' + encodeURIComponent(this.getContent()));
                    }
                });
            });
        });
        */
    },
    initEvents: function(){
        // Path checker
        $$('#imageuploaddir, #fileuploaddir').addEvent('change', function(event){
            //TODO: Check file path
        });
        // Aggregator engine selection
        $$('#import-engine').addEvent('change', function(event) {
            if (this[this.selectedIndex].value == 'googleplus') {
                $('engine-ajax-feed').hide();
                $('engine-ajax-gplus').show();
            } else if (this[this.selectedIndex].value == 'feed') {
                $('engine-ajax-gplus').hide();
                $('engine-ajax-feed').show();
            } else {
                $('engine-ajax-gplus').hide();
                $('engine-ajax-feed').hide();
            }
        });
        $$('#import-engine').fireEvent('change');
        // Toggle checkboxes based on Published box
        $$('input[name=published]').addEvent('change', function(event) {
            if (this.checked) {
                if (document.forms[0].comments)
                    document.forms[0].comments.disabled = false;
                if (document.forms[0].menu)
                    document.forms[0].menu.disabled = false;
                if (document.forms[0].sticky)
                    document.forms[0].sticky.disabled = false;
            } else {
                if (document.forms[0].comments) {
                    document.forms[0].comments.disabled = true;
                    document.forms[0].comments.checked = false;
                }
                if (document.forms[0].menu) {
                    document.forms[0].menu.disabled = true;
                    document.forms[0].menu.checked = false;
                }
                if (document.forms[0].sticky) {
                    document.forms[0].sticky.disabled = true;
                    document.forms[0].sticky.checked = false;
                }
            }
        });
        $$('input[name=published]').fireEvent('change');
        // Banner type toggle
        $$('#banner-type').addEvent('change', function(event) {
            if (this[this.selectedIndex].value == 'img') {
                $('banner-fs-custom').hide();
                $('banner-fs-img').show();
            } else {
                $('banner-fs-custom').show();
                $('banner-fs-img').hide();
            }
        });
        $$('#banner-type').fireEvent('change');
    }
});

/*
// Populate dropdown with phpBB3 forum names
function popTopic(path, id) {
	$('phpbbtopic').empty();
	new Request.JSON({url: 'index.php/admin/ajax/phpbb3/', onComplete: function(topic){
		topic.each(function(item){
			if (id && id == item['forum_id'])
				$('phpbbtopic').grab(new Element('option', { value: item['forum_id'], text: item['forum_name'], selected: true }));
			else
				$('phpbbtopic').grab(new Element('option', { value: item['forum_id'], text: item['forum_name'] }));
		});
	}}).send("path=" + path);
}
*/

window.addEvent('domready', function() {
    gcmsAdmin.initEvents();
    gcmsAdmin.enableEdit();
    if (typeof Picker == "function" && $$('input[id=calendar]') != null) {
        new Picker.Date('calendar', {
            timePicker: true,
            format: 'db',
            positionOffset: {x: 150, y: -100},
            pickerClass: 'datepicker_vista',
            useFadeInOut: !Browser.ie
        });
    }
});
