/*
	Courtesy (with some alterations):
	http://www.kriesi.at/archives/create-simple-tooltips-with-css-and-jquery
*/
function fnRmTooltip(target_items, name)
{
	$jq(target_items).each(function(i){
		if($jq(this).attr("title") != "")	// checks if there is a title
		{
			$jq("body").append("<div class='"+name+"' id='"+name+i+"'><p>"+$jq(this).attr('title')+"</p></div>");
			var tip = $jq("#"+name+i);
			$jq(this).removeAttr("title");
			$jq(this).mouseover(function(){tip.css({opacity:0.9, display:"none"}).fadeIn(400);});
			$jq(this).mousemove(function(kmouse){tip.css({left:kmouse.pageX+10, top:kmouse.pageY-20});})
			$jq(this).mouseout(function(){tip.fadeOut(400);});
		}//if
	});//each
}//function

