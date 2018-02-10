<?php
error_reporting(E_ALL); 
ini_set('display_errors', 1);

if(isset($_GET['locationId']))
{
	$locationId = $_GET['locationId'];
	include 'db.php';
	//BRING INFO ABOUT THE PASSED
	if($locationId == 0)
	{
		$passedCheck = 1;
		//$passedParent = $arrayinfo['parentId'];
		$passedName = 'Rwanda';
		$passedId = 0;
	}else
	{
		$sqlinfo = $db->query("SELECT  CHAR_LENGTH(parentId) PassCount, parentId, name, id  FROM locations WHERE id = '$locationId' LIMIT 1");
		while($arrayinfo = mysqli_fetch_array($sqlinfo))
		{
			$passedCheck = $arrayinfo['PassCount'];
			$passedParent = $arrayinfo['parentId'];
			$passedName = $arrayinfo['name'];
			$passedId = $arrayinfo['id'];
		}
	}
	//END BRING INFO
	// CHECK IF THERE IS ANY LEVEL DOWN//
	$sqlcheck = $db->query("SELECT CHAR_LENGTH(parentId) nowCount FROM locations ORDER BY parentId DESC LIMIT 1");
	$arraycheck = mysqli_fetch_array($sqlcheck);
	$lastparent = $arraycheck['nowCount'];
	
	// END CHECK
	if($lastparent > $passedCheck)
	{	
		if($passedId == 0){
			$prov = '<option value="0">-- Select A province --</option>';
		}elseif($passedCheck == 5){
			$prov = '<option value="0">-- Select A Village --</option>';
		}else{
			$prov ="";
		}
		$sql = $db->query("SELECT * FROM locations WHERE parentId = '$locationId'");
		echo ''.$passedName.'
		<select class="form-control" name="locationId" id="locationId" onchange="changelocation()">
				'.$prov.'';
		while($row = mysqli_fetch_array($sql))
			{ 
				echo'<option value="'.$row['id'].'">'.$row['name'].'</option>';
			}
		echo'</select></br>';
	}
	else
	{
		echo'
		<input type="hidden" name="locationId" value="'.$passedId.'">
		<select class="form-control" disabled>
			<option >'.$passedName.' Village </option>
		</select>';
		?>
		
		<!-- <script>
		(function getShipPrice() 
		{
			var vilageId = <?php echo $passedId;?>;
			$.ajax({
				type : "GET",
				url : "cart3.php",
				dataType : "html",
				cache : "false",
				data : {
					vilageId : vilageId,
				},
				success : function(html, textStatus){
					$("#shipPrices").html(html);
				},
				error : function(xht, textStatus, errorThrown){
					alert("There is Error : " + errorThrown);
				}
			});
		})();
		</script> -->
		<?php
		}
}
?>