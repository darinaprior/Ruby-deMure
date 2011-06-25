/*
* Author:      Marco Kuiper (http://www.marcofolio.net/)
*/
google.setOnLoadCallback(function()
{
	// Safely inject CSS3 and give the search results a shadow
	var cssObj = { 'box-shadow' : '#888 5px 10px 10px', // Added when CSS3 is standard
		'-webkit-box-shadow' : '#888 5px 10px 10px', // Safari
		'-moz-box-shadow' : '#888 5px 10px 10px'}; // Firefox 3.5+
	jQuery("#suggestions").css(cssObj);
	
	// Fade out the suggestions box when not active
	 jQuery("input").blur(function(){
	 	jQuery('#suggestions').fadeOut();
	 });
});

function lookup(inputString) {
	if(inputString.length == 0) {
		jQuery('#suggestions').fadeOut(); // Hide the suggestions box
	} else {
		jQuery.post("Include/search.php", {queryString: ""+inputString+""}, function(data) { // Do an AJAX call
			jQuery('#suggestions').fadeIn(); // Show the suggestions box
			jQuery('#suggestions').html(data); // Fill the suggestions box
		});
	}
}