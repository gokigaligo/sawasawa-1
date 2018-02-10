<?php

	include ("../db.php");
?>
<?php // Destry session if it hasn't been used for 15 minute.
session_start();
?>
<?php
// get the subcategory list
if(isset($_GET['catId']))
{
	$catId = $_GET['catId'];
	$catoption="";
	$sql = $db->query("SELECT * FROM `productsubcategory` WHERE CatCode = '$catId' ");
	while($row = mysqli_fetch_array($sql))
	{
		$catoption.='<option value="'.$row['subCatId'].'">'.$row['subCatName'].'</option>
		';
	}echo'<select onchange="get_prod()" id="subCatId">
	<option></option>
	'.$catoption.'
	</select>
	';
}
// get the product list
if(isset($_GET['subCatId']))
{
	$subCatId = $_GET['subCatId'];
	include ("../db.php");
	$catoption="";
	$sql = $db->query("SELECT * FROM `products` WHERE subCatCode = '$subCatId' ");
	while($row = mysqli_fetch_array($sql))
	{
		$catoption.='<option value="'.$row['productId'].'">'.$row['productName'].'</option>
		';
	}echo'<select onchange="new_post()" id="productId">
	<option></option>
	'.$catoption.'
	</select>
	';
}
// get the form to post a new post
if(isset($_GET['productId']))
{
	$productId = $_GET['productId'];
	include ("../db.php");
	$sql = $db->query("SELECT * FROM `products` WHERE productId = '$productId'");
	while($row = mysqli_fetch_array($sql))
	{
//,,,,,,postedBy,,		
		
		echo'<h3>Post in '.$row['productName'].'</h3>
		<form method="post" action="user.php" enctype="multipart/form-data">
		
		<table width="100%" >
			<tr>
				<td>
				Title:<input  type="text" name="postTitle">
				<input  type="text" name="productCode" value="'.$row['productId'].'" hidden/>				
				</td>
			</tr>
			<tr>
				<table>
					<tr>
						<td>Price:<input type="number" name="price"></td>
						<td>Status:
							<select name="priceStatus">
								<option>Fixed</option>
								<option>Negotiable</option>
							</select>
						</td>
						<td>Quantity:<input type="number" name="quantity"></td>
					</tr>
				</table>
			</tr>
			<tr>
				<td  border="1">
					Description:<textarea name="postDesc"></textarea>
					<input type="file" name="fileField" id="fileField"/>
			
				</td>
			</tr>
			<tr>
				<table>
					<tr>
						<td>Deadline:<input type="date" name="postDeadline"></td>
						<td>location:<input type="text" name="productLocation"></td>
						<td><input type="submit" value="add" name="addpst"/></td>
					</tr>
				</table>
			</tr>		
		</table>
		
		</form>
		';
	}
}
?>

<?php
// MODIFY ITEM
if(isset($_GET['modifyPostTitle']))
{
	$PostTitle = $_GET['modifyPostTitle'];
	$Price = $_GET['modifyPrice'];
	$Quantity = $_GET['modifyQuantity'];
	$ProductLocation = $_GET['modifyProductLocation'];
	$PostDesc = $_GET['modifyPostDesc'];
	$PriceStatus = $_GET['modifyPriceStatus'];
	$PostId = $_GET['modifyPostId'];
	
	$sql = $db->query("UPDATE `posts` SET postTitle='$PostTitle',
	quantity='$Quantity',price='$Price',priceStatus='$PriceStatus',
	postDesc='$PostDesc',productLocation='$ProductLocation' WHERE postId = '$PostId'")
	or die(mysqli_error());
	$sql2 = $db->query("SELECT * FROM posts WHERE postId = '$PostId'");
	while($row = mysqli_fetch_array($sql2))
	{
		$postTitle = $row['postTitle'];
		$quantity = $row['quantity'];
		$price = $row['price'];
		$priceStatus = $row['priceStatus'];
		$postDesc = $row['postDesc'];
		$postedDate = $row['postedDate'];
		$postedBy = $row['postedBy'];
		$productLocation = $row['productLocation'];
	}
	echo'<style> .notif{font-family: Arial, Helvetica, sans-serif;
	font-size: 14px;
	color: #fe2c2c;}></style><div class="notif">Post modifiyed succesfully <a href="userPost.php?postId='.$PostId.'">Close</a></div><table>
			<tr>
				<td>Name: </td>
				<td><input id="postTitle" value="'.$postTitle.'">
				<input id="postId" value="'.$PostId.'" hidden></td>
			</tr>
			<tr>
				<td>Price: </td>
				<td><input id="price" value="'.$price.'"> Rwf, 
				<select id="priceStatus">
					<option value="'.$priceStatus.'">'.$priceStatus.'</option>
					<option value="Negociable">Negociable</option>
					<option value="Fixed">Fixed</option>
				</select>
				</td>
			</tr>
			<tr>
				<td>Quantity: </td>
				<td><input id="quantity" value="'.$quantity.'"></td>
			</tr>
			<tr>
				<td>Owner: </td>
				<td><input id="postedBy" value="'.$postedBy.'" disabled></td>
			</tr>
			<tr>
				<td>Located: </td>
				<td><input id="productLocation" value="'.$productLocation.'"></td>
			</tr>
			<tr>
				<td>More Info: </td>
				<td><textarea id="postDesc">'.$postDesc.'</textarea></td>
			</tr>
			<tr>
				<td>Was here since: </td>
				<td><input id="postedDate" value="'.$postedDate.'" disabled></td>
			</tr>
		</table>
		';
	}
?>

<?php
// reply box
if(isset($_GET['commentId']))
{
	$commentId = $_GET['commentId'];
	$postCode = $_GET['postCode'];
	if (isset($_SESSION["username"])) {
$username = preg_replace('#[^A-Za-z0-9]#i', '', $_SESSION["username"]);
	echo'<br/><textarea id="replyNote" placeholder="your comment Plz!"></textarea>
	<input id="replyBy" value="'.$username.'" hidden/>
	<input id="postCode" value="'.$postCode.'" hidden/>
	<input id="commentCode" value="'.$commentId.'" hidden/><br/>
	<select id="visibilityStatus">
		<option value=""></option>
		<option value="Private">Private</option>
		<option value="Public">Public</option>
		</select>
	<button onclick="replyComment()">Comment</button>
	';
}else{
	echo'please first <a href="login.php">sign</a> in or <a href="../register.php">register</a> to submit a comment.';
}
}
if(isset($_GET['replyNotes']))
{
	$replyNotes = $_GET['replyNotes'];
	$replyBy = $_GET['replyBy'];
	$postCode = $_GET['postCode'];
	$commentCode = $_GET['commentCode'];
	$visibilityStatus = $_GET['visibilityStatus'];
	
	
	$sql = $db->query("INSERT INTO `commentreplies`(replyNotes, replyBy, visibilityStatus, commentCode) 
	VALUES ('$replyNotes', '$replyBy', '$visibilityStatus', '$commentCode')")or (mysqli_error());
	echo'your reply has been successfully submited! <a href="userPost.php?postId='.$postCode.'">Click Here</a>
	<br/>
	<br/>
	';
}
?>