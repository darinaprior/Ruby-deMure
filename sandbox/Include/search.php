<?php // Adapted from http://www.marcofolio.net/ ?>

<p id="searchresults">
<?php
require_once 'connection.php';
require_once 'functions.php';

if(!$mysqli) {
	// Show error if we cannot connect.
	echo 'ERROR: We apologise for the inconvenience.  Please try again later.';
} else {
	// Is there a posted query string?
	if(isset($_POST['queryString'])) {
	
		// Is the string length greater than 0?
		if(strlen($_POST['queryString']) >0) {
			$prevCategory = '';
                        
                        // Split the string into keywords to search for
                        // e.g. search for "crystal" and "pink"
                        // rather than "crystal pink"
                        $keywords = explode(' ', $_POST['queryString']);
                        // TODO - explode by space OR comma
                        
                        // We'll need to set up a dynamic prepared statement
                        // The number of parameters depends on the number of keywords
                        $sqlUnionBlock = '';
                        $sqlUnionSelects = array();
                        $bindParamTypes = '';
                        $bindParamValues = array();
                        foreach ($keywords as $word) {
                            // Validate
                            if (isset($word) && strlen($word) > 1) {
                                // Clean up and format each keyword
                                $word = stripslashes(trim($word));
                                $word = '%'.$word.'%';
                                // Add to the sql query
                                $sqlUnionSelects[] = 'SELECT CONVERT(? USING utf8) AS CompareKey';
                                // Add a parameter type to bind
                                $bindParamTypes .= 's';
                                // Add the parameter
                                $bindParamValues[] = $word;
                            }//if
                        }//foreach $keywords
                        $sqlUnionBlock = implode(' UNION ', $sqlUnionSelects);
                        
                        // Only display ANYTHING if there are some actual
                        // keywords i.e. not just spaces or single letters!
                        if (COUNT($bindParamValues) > 0) {
                            // Put together the SQL
                            $sql = 'SELECT
                                        COUNT(*) AS NumKeywordsMatched,
                                        sc.name,
                                        s.category_id,
                                        s.product_category_id,
                                        s.title,
                                        s.description,
                                        s.url,
                                        s.image_filepath
                                    FROM search s
                                    INNER JOIN search_category sc on s.category_id = sc.id
                                    INNER JOIN
                                    (
                                        '.$sqlUnionBlock.'
                                    ) tKeys
                                    ON (s.title LIKE tKeys.CompareKey OR s.description LIKE tKeys.CompareKey)
                                    GROUP BY s.id
                                    ORDER BY sc.name, NumKeywordsMatched desc, s.title
                                    LIMIT 6';                                    
                                    
                            // Prepare the statement
                            $stmt = $mysqli->prepare($sql);
                            if ($stmt) {
                                // Use call_user_func_array to dynamically bind params
                                $bind_names[] = $bindParamTypes;
                                for ($i=0; $i<count($bindParamValues); $i++)  {
                                    $bind_name = 'bind'.$i;
                                    $$bind_name = $bindParamValues[$i];
                                    $bind_names[] = &$$bind_name;
                                }
                                $return = call_user_func_array(array($stmt,'bind_param'),$bind_names);

                                $stmt->bind_result(
                                	$numKeywordsMatched,
                                	$category,
                                	$categoryId,
                                	$productCategoryId,
                                	$title,
                                	$desc,
                                	$url,
                                	$imagePath
                                );
                                $stmt->execute();
                                // Loop through the results
                                while ($result = $stmt->fetch()) {

                                        if($category != $prevCategory) { // check if the category changed
                                                echo '<span class="category">'.stripslashes($category).'</span>';
                                                $prevCategory = $category;
                                        }

                                        echo '<a href="'.$url.'">';
                                        
					// Print out the search image, if one is set
					if (isset($imagePath) && $imagePath != '') {
						echo '<img src="'.$imagePath.'" alt="" />';
					}

                                        $name = $title;
                                        if(strlen($name) > 35) { 
                                                $name = substr($name, 0, 35) . "...";
                                        }	         			
                                        echo '<span class="searchheading">'.stripslashes($name).'</span>';

                                        $description = $desc;
                                        if(strlen($description) > 120) { 
                                                $description = substr($description, 0, 120) . "...";
                                        }

                                        echo '<span>'.stripslashes($description).'</span></a>';
                                }//while
                                $stmt->close();
                                echo '<span class="alternative"><a href="http://www.rubydemure.com/browse.php?type=1&val=1" title="Browse products">Didn\'t find what you were looking for?<br/>Try browsing - click here.</a></span><br class="break" />';
                            } else {
                                echo 'ERROR: There was a problem with the query.';
                            }//if $stmt
                        }//if COUNT($bindParamValues)
		}//if strlen($_POST['queryString'])
	}//if $_POST['queryString']
}//if $mysqli
?>
</p>