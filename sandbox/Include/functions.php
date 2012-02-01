<?php
/** THIS FILE CONTAINS COMMON FUNCTIONS **/

/**
 * Takes an array of colour names and formats them into a human-readable string
 * @param	array	$colours		- one-dimensional array of colour names
 * @param	bool	$suppressTitleCase	- OPTIONAL (default:false)
 				- should capitalisation of the first char be suppressed?
 * @return	string	- the formatted string
 * @example	formatColourString(array('Black','WHITE','red','yELloW')) => 'Black, white, red and yellow'
 * @example	formatColourString(array('BLACK')) => 'Black'
 * @example	formatColourString(array('BLACK'), true) => 'black'
 * @author	Darina Prior
 */
function formatColourString($colours, $suppressTitleCase=false)
{
	$colourString = '';
	
	// Split away the last colour so we can insert
	// the word "and" (if necessary)
	$lastColour = $colours[COUNT($colours)-1];
	unset($colours[COUNT($colours)-1]);
	
	// Put the string together
	if (COUNT($colours) > 0) {
		$colourString .= implode(', ', $colours).' and ';
	}
	$colourString .= $lastColour;
	
	// Make the sentence title case
	$colourString = strtolower($colourString);
	if (!$suppressTitleCase) {
		$colourString = ucfirst($colourString);
	}
	
	// Return the string
	return $colourString;
}