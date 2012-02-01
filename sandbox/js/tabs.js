<!-- jQuery for the product detail tabs -->
$jq(document).ready(function(){

	//  When user clicks on tab, this code will be executed
	$jq("#tabs li").click(function() {
		
		//  First remove class "active" from currently active tab
		$jq("#tabs li").removeClass('active');
		
		//  Now add class "active" to the selected/clicked tab
		$jq(this).addClass("active");
		
		//  Hide all tab content
		$jq(".tab_content").hide();
		
		//  Here we get the href value of the selected tab
		var selected_tab = $jq(this).find("a").attr("href");
		
		//  Show the selected tab content
		$jq(selected_tab).fadeIn();
		
		//  At the end, return false so that the click on the link is not executed
		return false;
	});
	
	// For the "Colours & Sizes" tab...
	var enter_text = 'Click the colour combinations below to filter images on the left';
        
	// Add the "click below" text to a HIDDEN span that we'll never display (this is a precautionary measure)
	// Then repeat every 8 seconds
	$jq("#colour_click_hidden").text('');
	$jq("#colour_click_hidden").writeTextCharByChar(enter_text);
	setInterval(
		function() {
			$jq("#colour_click_hidden").text('');
			$jq("#colour_click_hidden").writeTextCharByChar(enter_text);
		},
		8000/*8 secs*/
	);
	
	// As long as the contents of the HIDDEN span aren't gobbledy-gook, then copy them to the displayed
	// span every half a second.  This deals with issues when the user moves away to another
	// browser tab/window or another application and comes back
	setInterval(
		function() {
			var new_text = '';
			var hidden_text = $jq("#colour_click_hidden").text();		// get the current "hidden" value
			hidden_text.replace(/^\s\s*/, '').replace(/\s\s*$/, '');	// trim whitespace
			var text_ok = enter_text.indexOf(hidden_text);			// compare with our desired string
			if (text_ok != -1)  {
				new_text = hidden_text;
			}
			$jq("#colour_click").text(new_text);
		},
		50/*half a second*/
	);
});

// Function to fill the text of the given object with the given string, one character at a time
(function(x) {
	x.fn.writeTextCharByChar = function(content) {
		var contentArray = content.split(""), current = 0, elem = this;
		setInterval(
			function() {
				if(current < contentArray.length) {
					elem.text(elem.text() + contentArray[current++]);
				}
			},
			50/*half a second*/
		);
		
	};
    
})($jq);