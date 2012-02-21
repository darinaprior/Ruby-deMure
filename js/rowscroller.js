/**
* jQuery rowscroller allows incremental scrolling over rows of elements.
* Author: Jon Thomas
* Email: jon@bigluxdesign.com
* URL: http://www.bigluxdesign.com
* Version: 1
* License: Free for personal or commercial use
* Example usage: 
* $('.scroller').rowscroller();
**/

(function($){

 $.fn.rowscroller = function(options){
 
 	var settings = {
 		visibleRows:        3,                  //default is 2
 		itemsPerRow:        3,                  //default is 1
 		rowsToScroll:       1,                  //how many rows to scroll at a time
 		navUp:              '.rowScrollUp',     //id or class of element for navigating up
 		navDown:            '.rowScrollDown',   //id or class of element for navigating down
 		navDisabledClass:   'disabled'          //class of disabled nav button
 	};
 	
	if (options) {
		$.extend(settings, options);
	}
	
	this.each(function(){
	
		//disable the buttons to start
		$(settings.navUp + ',' + settings.navDown).addClass(settings.navDisabledClass);	
		
		//elements
		var viewport = $(this);
		viewport.addClass('viewport').css({'position':'relative', 'overflow':'hidden'}).contents().wrapAll('<div class="scroller-pane" style="position: absolute;" />');		
		
		var scroller = $('.scroller-pane', viewport);
		
		//create rows
		var $items = scroller.children();
		$items.addClass('scroller-item');
		var totalItems = $items.length;
		var itemsPerRow = settings.itemsPerRow;
		
		for (var i = 0; i < totalItems; i+=itemsPerRow) {
			$items.filter(':eq('+i+'),:lt('+(i+itemsPerRow)+'):gt('+i+')').wrapAll('<div class="scroller-row" />');
		}
		
		//dimensions
		var rows = $('.scroller-row', viewport);
		var totalRows = rows.length;
		var rowHeight = 0;
		var rowFullHeight = 0;
		
		var rowMargin = parseInt(rows.css('marginTop')) + parseInt(rows.css('marginBottom'));
		
		rows.css('overflow','hidden');
			
		$items.each(function(){
			if ($(this).height() > rowHeight) {
				rowHeight = $(this).height();
				rowFullHeight = rowHeight + rowMargin; //use outerHeight(true) to include margin set by user
			}
		});
	
		rows.height(rowHeight);
		
		var viewportWidth = rows.width();
		
		//container heights		
		var viewportHeight = rowFullHeight * settings.visibleRows;
		var scrollerHeight = rowFullHeight * totalRows;
		
		viewport.height(viewportHeight);
		scroller.height(scrollerHeight);
		
		
		
		//enable the down button if there are rows to scroll
		if (totalRows > settings.visibleRows) {
			$(settings.navDown).removeClass(settings.navDisabledClass);
		}
		
		//where to stop
		var maxOffset = (totalRows - settings.visibleRows) * rowFullHeight;
		maxOffset = parseInt('-' + maxOffset);
		
		//how many rows to scroll at a time
		var scrollSize = settings.rowsToScroll * rowFullHeight;
		
		
		
		//slide on click
		$(settings.navUp + ', ' + settings.navDown).click(function(e){		
		
			var disabled = $(this).hasClass(settings.navDisabledClass);
			var busy = scroller.is(':animated');
			
			//which direction was clicked?
			var slideUp = $(this).is(settings.navUp);
			
			//offsets
			var viewportOffset = viewport.offset();
			var scrollerOffset = scroller.position();
	
			//positions
			var scrollerPosition = scrollerOffset.top;
		
			//calculate slide positioning
			if (slideUp) {
				scrollerPosition += scrollSize;
			} else {
				scrollerPosition -= scrollSize;
			}
			
			//animate slide and set button states
			if (!disabled && !busy) {
			
				//reset buttons state
				$(settings.navUp + ', ' + settings.navDown).removeClass(settings.navDisabledClass);			
			
				//animate and set button states
				scroller.animate({
					top: scrollerPosition + 'px'
				}, 500, function(){
					if (scrollerPosition === 0) {
						$(settings.navUp).addClass(settings.navDisabledClass);
						return false;
					}
					if (scrollerPosition <= maxOffset) {
						$(settings.navDown).addClass(settings.navDisabledClass);
						return false;
					}
				});
			}
			e.preventDefault();
		});		
		//end click function
	});
		
	return this; //return this for chaining
 };

})(jQuery);