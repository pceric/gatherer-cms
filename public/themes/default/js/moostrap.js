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

var Moostrap = new Class({
    Implements: [Chain, Events],
    
    initialize: function(){
        var that = this;
        this.chain.apply(this, arguments);
        // Alerts
        $$('[data-dismiss="alert"]').each(function(el){
            el.addEvent('click', function(){ that.alert(el); });
        });
        // Buttons
        $$('[data-loading-text]').each(function(el){
            el.addEvent('click', function(){ el.set('text', el.getProperty('data-loading-text')); });
        });
        $$('[data-toggle="button"]').each(function(el){
            el.addEvent('click', function(){ el.toggleClass('active'); });
        });
        $$('[data-toggle^=buttons]').each(function(el){
            el.getChildren('button').each(function(btn){
                btn.addEvent('click', function(){ that.buttons(el, btn); });
            });
        });
        // Collapse
        $$('[data-toggle="collapse"]').each(function(el){
            el.addEvent('click', function(){ that.collapse(el); });
        });
        // Modal
        $$('[data-toggle="modal"]').each(function(el){
            el.addEvent('click', function(){ that.modal(el); });
        });
    },

    alert: function(el){
        var p = el.getParent();
        this.fireEvent('close');
        p.get('tween').start('opacity',0).chain(function(){ p.dispose(); });
        this.fireEvent('closed');
        return this;
    },

    buttons: function(el, btn){
        if (el.getProperty('data-toggle') == 'buttons-radio') {
            el.getChildren('button').each(function(btn){ btn.removeClass('active'); });
            btn.addClass('active');
        } else {
            btn.toggleClass('active');
        }
        return this;
    },

    collapse: function(el){
        var props = el.getProperties('href', 'data-target');
        var target = ($$(props.data-target).length > 0) || ($$(props.href).length > 0) || el.getNext();
        if (el.get('slide').open)
            this.fireEvent('hide');
        else
            this.fireEvent('show');
        if (el.retrieve('toggle') == null) {
            el.store('toggle', target);
            el.store('slide', new Fx.Slide(target));
        }
        el.get('slide').toggle();
        if (el.get('slide').open)
            this.fireEvent('shown');
        else
            this.fireEvent('hidden');
        return this;
    },

    modal: function(el){
        var that = this;
        var props = el.getProperties('href', 'data-target');
        var target;
        this.fireEvent('show');
        var myMask = new Mask();
        myMask.show();
        if ($$(props.data-target).length > 0) {
            target = $$(props.data-target)[0];
        } else {
            target = $$(props.href)[0];
        }
        target.show();
        target.getElements('[data-dismiss="modal"]').each(function(el){
            el.addEvent('click', function(){ that.modalClose(target, myMask); });
        });
        window.addEvent('keydown', function(event){ if (event.key == 'esc') { that.modalClose(target, myMask); } });
        this.fireEvent('shown');
        return this;
    },

    modalClose: function(el, mask){
        this.fireEvent('hide');
        el.hide();
        mask.hide();
        this.fireEvent('hidden');
        return this;
    }
});

window.addEvent('domready', function() {
    var myMoostrap = new Moostrap();
});
