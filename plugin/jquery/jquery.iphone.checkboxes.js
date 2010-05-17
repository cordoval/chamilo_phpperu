/**
 * iphone-style-checkboxes Script
 * Turn your checkboxes into iPhone-style binary switches
 *  
 * @author Thomas Reynolds (tdreyno AT gmail DOT com) 
 * @source http://blog.jedijawa.com
 * @source http://tdreyno.github.com/iphone-style-checkboxes/
 * @copyright Copyright (c) 2009, Thomas Reynolds
 * @license MIT-license
 */

(function($){
  $.iphoneStyle = {
    defaults: { checkedLabel: 'ON', uncheckedLabel: 'OFF', background: '#fff' }
  }
  
  $.fn.iphoneStyle = function(options) {
    options = $.extend($.iphoneStyle.defaults, options);
    
    return this.each(function() {
      var elem = $(this);
      
      if (!elem.is(':checkbox'))
        return;
      
      elem.css({ opacity: 0 });
      elem.wrap('<div class="binary_checkbox" />');
      elem.after('<div class="handle"><div class="bg" style="background: ' + options.background + '"/><div class="slider" /></div>')
          .after('<label class="off">'+ options.uncheckedLabel + '</label>')
          .after('<label class="on">' + options.checkedLabel   + '</label>');
      
      var handle    = elem.siblings('.handle'),
          handlebg  = handle.children('.bg'),
          offlabel  = elem.siblings('.off'),
          onlabel   = elem.siblings('.on'),
          container = elem.parent('.binary_checkbox'),
          rightside = container.outerWidth() - 39;
      
      container.click(function() {
        var is_onstate = (handle.position().left <= 0);
            new_left   = (is_onstate) ? rightside : 0,
            bgleft     = (is_onstate) ? 34 : 0;

        handlebg.hide();
        handle.animate({ left: new_left }, 100, function() {
          handlebg.css({ left: bgleft }).show();
        });
        
        if (is_onstate) {
          offlabel.animate({ opacity: 0 }, 200);
          onlabel.animate({ opacity: 1 }, 200);
        } else {
          offlabel.animate({ opacity: 1 }, 200);
          onlabel.animate({ opacity: 0 }, 200);
        }
        
        if (is_onstate)
        {
        	elem.attr('checked', true);
        }
        else
        {
        	elem.attr('checked', false);
        }
        
        return false;
      });
      
      // initial load
      if (elem.is(':checked')) {
        offlabel.css({ opacity: 0 });
        onlabel.css({ opacity: 1 });
        handle.css({ left: rightside });
        handlebg.css({ left: 34 });
      }
    });
  };
})(jQuery);