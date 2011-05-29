<p id="searchresults">
<?php
	// PHP5 Implementation - uses MySQLi.
	// mysqli('localhost', 'yourUsername', 'yourPassword', 'yourDatabase');
	$mysqli = new mysqli('localhost', 'root', '', 'darina_test');
	
	if(!$mysqli) {
		// Show error if we cannot connect.
		echo 'ERROR: Could not connect to the database.';
	} else {
		// Is there a posted query string?
		if(isset($_POST['queryString'])) {
			$queryString = $mysqli->real_escape_string($_POST['queryString']);
			
			// Is the string length greater than 0?
			if(strlen($queryString) >0) {
				$query = $mysqli->query('select
    				    p.ProdID,
    				    ProductTypeID,
    				    Title,
    				    Description,
    				    TotalCost,
                        CONCAT(\'http://rubydemure.com/product.php?pid=\', p.ProdID) as LinkUrl,
    				    CONCAT(\'http://rubydemure.com/\', Filepath) as ImageUrl
                    from Product p
                    inner join ProductImage pi on p.DefaultImageID = pi.ProdImageID
                    where Title like \'%'.$queryString.'%\'
                    or Description like \'%'.$queryString.'%\'
                    order by Priority desc, Title
                    limit 5;');
				
				if($query) {
					// While there are results loop through them - fetching an Object.
					
					// Store the category id
					$catid = 0;
					while ($result = $query ->fetch_object()) {
					    /*
						if($result->cat_id != $catid) { // check if the category changed
							echo '<span class="category">'.$result->cat_name.'</span>';
							$catid = $result->cat_id;
						}
						*/
	         			echo '<a href="'.$result->LinkUrl.'">';
	         			$thumbnail = str_replace('images/', 'images/thumbs/', $result->ImageUrl);
	         			echo '<img src="'.$thumbnail.'" alt="" />';
	         			
	         			$name = $result->Title;
	         			if(strlen($name) > 35) {
	         				$name = substr($name, 0, 35) . "...";
	         			}
	         			echo '<span class="searchheading">'.$name.'</span>';
	         			
	         			$description = $result->Description;
	         			if(strlen($description) > 80) {
	         				$description = substr($description, 0, 80) . "...";
	         			}
	         			
	         			echo '<span>'.$description.'</span>';
	         			echo '</a>';
	         		}
	         		echo '<span class="seperator"><a href="http://www.marcofolio.net/sitemap.html" title="Sitemap">Nothing interesting here? Try the sitemap.</a></span><br class="break" />';
				} else {
					echo 'ERROR: There was a problem with the query.';
				}
			} else {
				// Dont do anything.
			} // There is a queryString.
		} else {
			echo 'There should be no direct access to this script!';
		}
	}
?>
</p>