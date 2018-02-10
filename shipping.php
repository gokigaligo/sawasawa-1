<?PHP require('db.php');
 $mylocation = '201010101';
	$sqlShipPrice = $db->query("SELECT * FROM shipping ORDER BY id DESC LIMIT 1");
	$rowShipPrice = mysqli_fetch_array($sqlShipPrice);
	$country = $rowShipPrice['country'];
	$provincePrice = $rowShipPrice['province'];
	$districtPrice = $rowShipPrice['district'];
	$sectorPrice = $rowShipPrice['sector'];
	$cellPrice = $rowShipPrice['cell'];
	$villagePrice = $rowShipPrice['village'];
			

?>
<?php 
// This file is www.developphp.com curriculum material
// Written by Adam Khoury January 01, 2011
$link='';
$steps='';
session_start(); // Start session first thing in script<?php // Session user
// GIVE ME THE SIGNED IN USER IN CASE I NEED TO USE HIM/HER
if (isset($_SESSION["username"])) 
	{
		$username = preg_replace('#[^A-Za-z0-9]#i', '', $_SESSION["username"]);
		$getuserName = $db->query("SELECT * FROM users WHERE loginId='$username'");
		WHILE($row= mysqli_fetch_array($getuserName))
			{
				$doneBy = $row['names'];
			}
		$link.='
		<a class="btn btn-danger" href="cart.php"><i class="fa fa-arrow-left"></i> Back to Cart</a>
		
		<button style="float: right;" type="submit" class="btn btn-success"><i class="fa fa-arrow-right"></i> Proceed to Pay</button>';
		$steps.='<li class="current-step"><span>01. Summary</span></li>
                                <li class="current-step"><span>02. Shipping</span></li>
                                <li><span>03. Payment Options</span></li>';
	}else{
		$link.='<a class="next-btn" href="login.php?page=cart.php">Sign in</a> <a class="next-btn" href="register.php?page=cart.php">Register</a>'; // filter everything but numbers and letters
		$steps.='<li class="current-step"><span>01. Summary</span></li>
                                <li class="current-step"><span>02. Sign in</span></li>
                                <li><span>03. Payment Options</span></li>
                                <li><span>04. Shipping</span></li>
                                <li><span>05. Payment</span></li>';
	}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="assets/lib/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/lib/font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/lib/Linearicons/css/demo.css" />
    <link rel="stylesheet" type="text/css" href="assets/lib/select2/css/select2.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/lib/jquery.bxslider/jquery.bxslider.css" />
    <link rel="stylesheet" type="text/css" href="assets/lib/owl.carousel/owl.carousel.css" />
    <link rel="stylesheet" type="text/css" href="assets/lib/fancyBox/jquery.fancybox.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/animate.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/reset.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/index2.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/quick-view.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/responsive2.css" />
    <title>Ssawasawa_Cart</title>
	<link rel="stylesheet" href="css/popup-polyfill.min.css">
	<link rel="stylesheet" href="css/bootstrap.css">
	<link rel="stylesheet" href="css/styles.css">
	
</head>
<body class="onsale-product">
    <div class="wrapper">
        <div class="main-page">
            <div class="breadcrumb clearfix">
                <div class="container">
                    <ul class="list-breadcr">
                        <li class="home"><a href="index.php" title="Back to Home">Home</a></li>
                        <li><span>Your shopping cart</span></li>
                    </ul>
                </div>
            </div>
            <div class="page-content">
                <!-- Column left -->
                <div class="container">
                    <div class="page-title">
                        <h3 class="title">Shopping cart</h3>
                    </div>
                    <div class="row-none">
                        <!-- Main content -->
                        <div class="page-order">
                            <ul class="step clearfix"><?php echo $steps;?>
                            </ul>
							<form action="payment.php" method="post">                               
								 
								<div style="text-align: center;" class="heading-counter warning">
								<div id="locationsDiv" style="text-align: center;    max-width: 200px;margin: 0 auto;">
									Please chose your location
									  
											<select class="form-control" name="locationId" id="locationId" onchange="changelocation()">
												<option value="0">-- Select Country --</option>
												<option value="0">Rwanda</option>
											</select>
										</div>
									
								</div>
								<div id="shipPrices" class="order-detail-content">
									
								</div>
							</form>	
                        <!-- End Main content -->
                    </div>
                </div>
            </div>
            <!-- end part -->
        </div>
        <a href="order.html#" class="scroll_top" title="Scroll to Top" style="display: inline;">Scroll</a>
    </div>
	<dialog id="dialog" class="mdl-dialog" style="padding:0px;">
		<div class="mdl-dialog__actions" style="padding:2px;text-align: center; display: block;font-size: 20px;    background: #2196F3; color: #fff;">
			Money Transfer
		</div>
		<div class="mdl-dialog__content" style="padding:0px;    border-bottom: solid #ccc 0.1px;" id="contBody">
			<input placeholder="Tigo phone">
		</div>
		<div class="mdl-dialog__actions" id="actionbc" style="padding:0px; display: block;">
			<button type="button" class="mdl-button btn-danger">Close</button>
		</div>
	</dialog>
<!-- Script-->
<script type="text/javascript" src="assets/lib/jquery/jquery-1.11.2.min.js"></script>
<script type="text/javascript" src="assets/lib/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="assets/lib/select2/js/select2.min.js"></script>
<script type="text/javascript" src="assets/lib/jquery.bxslider/jquery.bxslider.min.js"></script>
<script type="text/javascript" src="assets/lib/owl.carousel/owl.carousel.min.js"></script>
<script type="text/javascript" src="assets/lib/jquery.countdown/jquery.countdown.min.js"></script>
<script type="text/javascript" src="assets/lib/fancyBox/jquery.fancybox.js"></script>
<script type="text/javascript" src="assets/lib/jquery.elevatezoom.js"></script>
<script type="text/javascript" src="assets/js/theme-script.js"></script>
<script type="text/javascript" src="assets/js/equalheight.js"></script>

<script src="js/popup.js"></script>	
<script type="text/javascript">
	function changelocation() {
		var locationId = document.getElementById('locationId').value;
		//alert(locationId);
		$.ajax({
			type : "GET",
			url : "locations.php",
			dataType : "html",
			cache : "false",
			data : {
				locationId : locationId,
			},
			success : function(html, textStatus){
				$("#locationsDiv").html(html);
			},
			error : function(xht, textStatus, errorThrown){
				alert("Error : " + errorThrown);
			}
		});
	}
</script>
</body>
</html>