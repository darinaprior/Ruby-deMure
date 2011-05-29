<?php // Adapted from http://www.marcofolio.net/ ?>

<p id="searchresults">
<?php
$db = new mysqli('localhost', 'rubydemu_ruby', 'thangi8F', 'rubydemu_dbRubydemure');

if(!$db) {
	// Show error if we cannot connect.
	echo 'ERROR: We apologise for the inconvenience.  Please try again later.';
} else {
	// Is there a posted query string?
	if(isset($_POST['queryString'])) {
	
		// Is the string length greater than 0?
		if(strlen($_POST['queryString']) >0) {
			$prevCategory = '';

			// Run the query
			$sql = 'SELECT sc.name, s.title, s.description, s.url, s.image_filepath
				FROM search s
				INNER JOIN search_category sc on s.category_id = sc.id
				WHERE s.title LIKE ?
				ORDER BY sc.name, s.title
				LIMIT 6';
			$stmt = $db->prepare($sql);
			if ($stmt) {
				$stmt->bind_param(
					's',
					$db->real_escape_string('%'.$_POST['queryString'].'%')
				);
				$stmt->bind_result($category, $title, $desc, $url, $img);
				$stmt->execute();
				while ($result = $stmt->fetch()) {
					
					if($category != $prevCategory) { // check if the category changed
						echo '<span class="category">'.$category.'</span>';
						$prevCategory = $category;
					}
					
		 			echo '<a href="'.$url.'">';
		 			echo '<img src="'.$image_filepath.'" alt="" />';
		 			
		 			$name = $title;
		 			if(strlen($name) > 35) { 
		 				$name = substr($name, 0, 35) . "...";
		 			}	         			
		 			echo '<span class="searchheading">'.$name.'</span>';
		 			
		 			$description = $desc;
		 			if(strlen($description) > 80) { 
		 				$description = substr($description, 0, 80) . "...";
		 			}
		 			
		 			echo '<span>'.$description.'</span></a>';
				}//while
				$stmt->close();
		 		echo '<span class="seperator"><a href="http://www.rubydemure.com/browse.php?type=1&val=1" title="Browse products">Didn\'t find what you were looking for?<br/>Try browsing - click here.</a></span><br class="break" />';
			} else {
				echo 'ERROR: There was a problem with the query.';
			}//if $stmt
		}//if strlen($_POST['queryString'])
	}//if $_POST['queryString']
}//if $db
?>
</p>