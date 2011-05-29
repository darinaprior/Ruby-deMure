			<?php 
			/* Ready-made collections */
			$sql	= "select CollectionID, Title from Collection where Current is not null order by Priority desc";
			$rset	= mysql_query($sql, $cnRuby);
			$aCollections	= array();
			while ($records = mysql_fetch_array($rset))
			{
				$aCollections[$records['CollectionID']] = $records['Title'];
			}
				
			/* Product types */
			$sql	= "select ProdTypeID, IfNull(NavName, Description) as Name from ProductType where exists (select ProdID from Product where ProductTypeID = ProdTypeID);";
			$rset	= mysql_query($sql, $cnRuby);
			$aProdTypes	= array();
			while ($records = mysql_fetch_array($rset))
			{
				$aProdTypes[$records['ProdTypeID']] = $records['Name'];
			}
				
			/* Product colours */
			$sql	= "select t1.ColourID, t1.Description 
						from 
						(
							select ColourID, Description 
							from Colour c 
							where exists (select ColourID from ProductColour pc where pc.ColourID = c.ColourID)
						) t1 
						inner join
						(
							select ColourID, COUNT(*) as NumProds 
							from ProductColour 
							group by ColourID
						) t2 
						on t1.ColourID = t2.ColourID 
						order by t2.NumProds desc 
						limit 10;";
			$rset	= mysql_query($sql, $cnRuby);
			$aColours	= array();
			while ($records = mysql_fetch_array($rset))
			{
				$aColours[$records['ColourID']] = $records['Description'];
			}
				
			/* Product shapes */
			$sql	= "select t1.ShapeID, t1.Description 
						from 
						(
							select ShapeID, Description 
							from Shape s 
							where exists (select ShapeID from Product p where p.ShapeID = s.ShapeID)
						) t1 
						inner join
						(
							select ShapeID, COUNT(*) as NumProds 
							from Product 
							group by ShapeID
						) t2 
						on t1.ShapeID = t2.ShapeID 
						order by t2.NumProds desc 
						limit 10;";
			$rset	= mysql_query($sql, $cnRuby);
			$aShapes	= array();
			while ($records = mysql_fetch_array($rset))
			{
				$aShapes[$records['ShapeID']] = $records['Description'];
			}
				
			/* Product sizes */
			$sql	= "select SizeID, Name from Size s where exists (select ProdID from ProductSize ps where ps.SizeID = s.SizeID) order by `Order`";
			$rset	= mysql_query($sql, $cnRuby);
			$aSizes	= array();
			while ($records = mysql_fetch_array($rset))
			{
				$aSizes[$records['SizeID']] = $records['Name'];
			}
				
			/* Product materials */
			$sql	= "select t1.MaterialID, t1.Name 
						from
						(
							select MaterialID, Name 
							from Material m 
							where exists (select MaterialID from ProductMaterial pm where pm.MaterialID = m.MaterialID) 
							and IncludeInNav = 1
						) t1 
						inner join
						(
							select MaterialID, COUNT(*) as NumProds 
							from ProductMaterial 
							group by MaterialID
						) t2 
						on t1.MaterialID = t2.MaterialID 
						order by t2.NumProds desc 
						limit 10;";
			$rset	= mysql_query($sql, $cnRuby);
			$aMaterials	= array();
			while ($records = mysql_fetch_array($rset))
			{
				$aMaterials[$records['MaterialID']] = $records['Name'];
			}

			/* Publicity types */
			$sql	= "select PublicityTypeId, Type from PublicityType where Priority >= 0 order by Priority desc";
			$rset	= mysql_query($sql, $cnRuby);
			$publicityTypes = array();
			while ($records = mysql_fetch_array($rset))
			{
				$publicityTypes[$records['PublicityTypeId']] = $records['Type'];
			}
				
			/* Link categories */
			$sql	= "select LinkCategoryID, Category from LinkCategory where Priority is not null order by Priority desc";
			$rset	= mysql_query($sql, $cnRuby);
			$aLinkCategories= array();
			while ($records = mysql_fetch_array($rset))
			{
				$aLinkCategories[$records['LinkCategoryID']] = $records['Category'];
			}
			?>
			
			<table class="tblNav">
				<tr>
					<td align="center" class="tdNav">
						<ul id="nav">
							<li><a href="/">Home</a></li>
							<li>
								<a href="#">Shop</a>
								<ul>
									<li><a href="bespoke.php">Bespoke Work</a></li>
									<li><a href="off-the-rack.php">Ready-Made</a>
										<ul>
											<li><a href="off-the-rack.php">All Collections</a></li>
											<?php 
											foreach ($aCollections as $colId => $colTitle)
											{
												?><li><a href="collection.php?cid=<?php echo $colId ?>"><?php echo $colTitle ?></a></li><?php 
											}
											?>
										</ul>
									</li>
									<li><a href="vouchers.php">Gift Vouchers</a></li>
									<li>
										<a href="off-the-rack.php">Browse by</a>
										<ul>
											<li><a href="#">Product Type</a>
												<ul>
													<?php 
													foreach ($aProdTypes as $id => $name)
													{
														?><li><a href="browse.php?type=1&val=<?php echo $id ?>"><?php echo $name ?></a></li><?php 
													}
													?>
												</ul>
											</li>
											<li><a href="#">Colour</a>
												<ul>
													<?php 
													foreach ($aColours as $id => $name)
													{
														?><li><a href="browse.php?type=2&val=<?php echo $id ?>"><?php echo $name ?></a></li><?php 
													}
													?>
												</ul>
											</li>
											<li><a href="#">Shape</a>
												<ul>
													<?php 
													foreach ($aShapes as $id => $name)
													{
														?><li><a href="browse.php?type=3&val=<?php echo $id ?>"><?php echo $name ?></a></li><?php 
													}
													?>
												</ul>
											</li>
											<li><a href="#">Size</a>
												<ul>
													<?php 
													foreach ($aSizes as $id => $name)
													{
														?><li><a href="browse.php?type=4&val=<?php echo $id ?>"><?php echo $name ?></a></li><?php 
													}
													?>
												</ul>
											</li>
											<li><a href="#">Material</a>
												<ul>
													<?php 
													foreach ($aMaterials as $id => $name)
													{
														?><li><a href="browse.php?type=5&val=<?php echo $id ?>"><?php echo $name ?></a></li><?php 
													}
													?>
												</ul>
											</li>
										</ul>
									</li>
								</ul>
							</li>
							<li>
								<a href="press.php">Publicity</a>
								<ul>
									<?php 
									foreach ($publicityTypes as $publicityTypeId => $publicityType)
									{
										?><li><a href="press.php?typeid=<?php echo $publicityTypeId ?>"><?php echo $publicityType ?></a></li><?php 
									}
									?>
								</ul>
							</li>
							<li>
								<a href="links.php">Community</a>
								<ul>
									<?php 
									foreach ($aLinkCategories as $catId => $catName)
									{
										?><li><a href="links.php?catid=<?php echo $catId ?>"><?php echo $catName ?></a></li><?php 
									}
									?>
								</ul>
							</li>
							<li>
								<a href="#">Information</a>
								<ul>
									<li><a href="faq.php">FAQ</a></li>
									<li><a href="testimonials.php">Testimonials</a></li>
									<li><a href="sizing.php">Sizing Chart</a></li>
									<li><a href="about.php">About Ruby</a></li>
									<li><a href="contact.php">Contact</a></li>
								</ul>
							</li>
						</ul>
					</td>
				</tr>
			</table>