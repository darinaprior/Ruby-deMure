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
});