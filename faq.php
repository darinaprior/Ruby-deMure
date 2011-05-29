<?php
include("Include/connection.php");

// Get all category ids into javascript arrays
?>
<script type="text/javascript">
	var aCategoryIds = new Array();
</script>
<?php
$qAllCats	= "select ID from FAQCategory";
$rsAllCats	= mysql_query($qAllCats, $cnRuby);
while ($recAllCats = mysql_fetch_array($rsAllCats))
{
	?><script type="text/javascript">aCategoryIds.push(<?php echo $recAllCats['ID']; ?>);</script><?php
}

$sPageTitle		= 'Frequently Asked Questions';
$sPageKeywords	= 'faq, frequently asked questions';
include("Include/header.php");
?>

<script type="text/javascript">
	<!--
	$jq(document).ready(function(){
		hideEverything();
	});
	
	function hideEverything()
	{
		for (var i=0; i < aCategoryIds.length; i++)
		{
			$jq("tr[name*=cid_"+aCategoryIds[i]+"]").hide();	// hide all questions AND answers in this category
		}
	}
	function showHideQuestions(catId)
	{
		var isHidden = $jq("tr[name=cid_"+catId+"]").is(":hidden");
		if (isHidden)
		{
			$jq("tr[name=cid_"+catId+"]").show();	// show ONLY the questions in this category (not the answers)
		}
		else
		{
			$jq("tr[name*=cid_"+catId+"]").hide();	// hide all questions AND answers in this category
		}
	}
	function showHideAnswer(faqId)
	{
		$jq("tr[name*=qid_"+faqId+"]").toggle();
	}
	-->
</script>
	

<table class="tblBody" cellpadding="0">
	<tr>
		<td style="
			background-image: url('../images/backgrounds/R4C1.jpg');
			background-repeat:	repeat;
			background-color:	#0F050D;
			width: 75px;"
		>&nbsp;</td>
		<td style="
			background-image: url('../images/backgrounds/R4C2.jpg');
			background-repeat:	repeat;
			background-color:	#0F050D;
			font-family:Verdana, Arial, Helvetica, sans-serif;
			width: 804px;
			vertical-align:top;"
		>
			<table class="tblStdFull">
				<tr>
					<td valign="top">
						<table class="tblStdFullCentre" cellspacing="0">
							<tr>
								<td>
									<table class="tblStdFullCentre" cellspacing="0">
										<tr>
											<td class="tdHeading">Frequently Asked Questions</td>
											<td rowspan="2" align="right">
												<table width="295" cellspacing="0" cellpadding="0">
													<tr>
														<td style="
															background-image:	url('images/hand_bespoke.jpg');
															background-repeat:	no-repeat;
															background-color:	#0F050D;
															text-align:			left;
															width:				100%;
															height:				96px;
															font-size:			10px;
															padding-left:		10px;"
														>
																<font size="2"><b>Lots more on the way!</b></font>
																<br/><br/>If you have a particular
																<br/>question you want answered,
																<br/>contact Ruby by <a href="contact.php"> clicking here</a>
														</td>
													</tr>
												</table>
											</td>
										</tr>
										<tr><td>Click on a category or question to show or hide the information.</td></tr>
										
									</table>
								</td>
							</tr>
							
							<?php
							// Get the categories, questions and answers
							$questions = array();
							$qFaq	= "select f.*, fc.ID as CatID, fc.Name as CatName, fc.Priority as CatPriority from FAQ f inner join FAQCategory fc on f.CategoryID = fc.ID";
							$rsFaq	= mysql_query($qFaq, $cnRuby);
							while ($recFaq = mysql_fetch_array($rsFaq))
							{
								$questions[] = array(
									'ID'			=> $recFaq['ID']
									,'CatID'		=> $recFaq['CatID']
									,'CatName'		=> $recFaq['CatName']
									,'Question'		=> $recFaq['Question']
									,'Answer'		=> $recFaq['Answer']
									,'Priority'		=> $recFaq['Priority']
									,'CatPriority'	=> $recFaq['CatPriority']
								);
							}//while
							
							// We can use array_multisort to sort the multi-dimensional array, but have to store data by column first
							$catPriorityCols	= array();
							$priorityCols		= array();
							$questionCols		= array();
							foreach ($questions as $key => $row)
							{
								$catPriorityCols[$key]	= $row['CatPriority'];
								$priorityCols[$key]		= $row['Priority'];
								$questionCols[$key]		= $row['Question'];
							}//foreach

							// Sort the data by category priority, question priority and question
							// Use $questions as last parameter to store back to that array
							array_multisort($catPriorityCols, $priorityCols, $questionCols, $questions);
							
							// Now print out all of the categories, questions and answers.  Visibility is handled by jQuery
							$prevCatId = '';
							foreach ($questions as $question)
							{
								if ($prevCatId != $question['CatID'])
								{
									?>
									<tr>
										<td align="center" style="cursor:pointer; font-size:14px; font-weight:bold;" onClick="showHideQuestions(<?php echo $question['CatID']; ?>);">
											<br/><?php echo $question['CatName']; ?>
										</td>
									</tr>
									<?php
									$prevCatId = $question['CatID'];
								}
								?>
								
								<tr name="cid_<?php echo $question['CatID']; ?>" onClick="showHideAnswer(<?php echo $question['ID']; ?>);">
									<td align="left" style="cursor:pointer;">
										<b><?php echo $question['Question']; ?></b>
									</td>
								</tr>
								<tr name="cid_<?php echo $question['CatID']; ?>_qid_<?php echo $question['ID']; ?>">
									<td align="left">
										<?php echo $question['Answer']; ?><br/><br/>
									</td>
								</tr>
								<?php
							}//foreach
							?>
							<tr><td><br/></td></tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
		<td style="
			background-image: url('../images/backgrounds/R4C3.jpg');
			background-repeat:	repeat;
			background-color:	#0F050D;
			width: 71px;"
		>&nbsp;</td>
	</tr>
</table>
<?php include("Include/footer.php"); ?>