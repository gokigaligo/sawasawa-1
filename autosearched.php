<?php
	include 'db.php';
	if (isset($_GET['search']) && $_GET['search']!='') {
		$pro = $_GET['search'];
		$sql = $db -> query("SELECT * FROM items1 WHERE itemName LIKE ('%$pro%')");
		$count = mysqli_num_rows($sql);
		if ($count < 1) {
			echo '
				<br> <p><i>'.$pro.' Not avairable</i></p>
			';
		}
		else {
		?>
		<br>
			<table style="
			    width: 100%;
			    font-size: 130%;
			    text-transform: capitalize;
			    text-align: left;
			    font-weight: bold;">
					<?php
						while ( $query = mysqli_fetch_array($sql) ) {
							$id = $query['itemId'];
							?>
							<tr>
								<td>
									<a href="post.php?postId=<?php echo $query['itemId'];?>"><?php echo $query['itemName'];?></a>
								</td>
							</tr>
							<?php
						}	
					?>
			</table>
		<?php
		}
	}

?>