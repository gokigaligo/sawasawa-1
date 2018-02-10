<?PHP require('db.php');?>
<?php
if(isset($_POST['locationId'])){
	 $locationId = $_POST['locationId'];
	 $cartPrice = $_POST['cartPrice'];
} 
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
		<a class="btn btn-danger" href="shipping.php"><i class="fa fa-arrow-left"></i> Back to Shipping</a>
		';
		$steps.='<li class="current-step"><span>01. Summary</span></li>
                                <li class="current-step"><span>02. Shipping</span></li>
                                <li class="current-step"><span>03. Payment Options</span></li>';
	}else{
		$link.='<a class="next-btn" href="login.php?page=cart.php">Sign in</a> <a class="next-btn" href="register.php?page=cart.php">Register</a>'; // filter everything but numbers and letters
		$steps.='<li class="current-step"><span>01. Summary</span></li>
                                <li class="current-step"><span>02. Sign in</span></li>
                                <li><span>03. Payment Options</span></li>
                                <li><span>04. Shipping</span></li>
                                <li><span>05. Payment</span></li>';
	}
?>
<?php
// Script Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');
?>
<?php 
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//       Section 5  (render the cart for the user to view on the page)
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$cartOutput = "";
$cartTotal = "";
$i = 0;
$pp_checkout_btn = '';
$product_id_array = '';
if (!isset($_SESSION["cart_array"]) || count($_SESSION["cart_array"]) < 1) 
{
	$cartOutput = "";
}
else
	{
	// Start PayPal Checkout Button
	$pp_checkout_btn .= '<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
    <input type="hidden" name="cmd" value="_cart">
    <input type="hidden" name="upload" value="1">
    <input type="hidden" name="business" value="'.$doneBy.'@sawasawa.com">';
	// Start the For Each loop
	$i = 0; 
   foreach ($_SESSION["cart_array"] as $each_item) 
   { 
		$item_id = $each_item['item_id'];
		$sql = $db->query("SELECT * FROM items1 WHERE itemId='$item_id' LIMIT 1");
		while ($row = mysqli_fetch_array($sql)) {
			$product_name = $row["itemName"];
			$price = $row["unityPrice"];
			$details = $row["description"];
		}
		$pricetotal = $price * $each_item['quantity'];
		//$cartTotal = $cartPrice;
		$cartTotal = $pricetotal + $cartTotal;
		setlocale(LC_MONETARY, "en_US");
       // $pricetotal = money_format("%10.2n", $pricetotal);
		// Dynamic Checkout Btn Assembly
		$x = $i + 1;
		$pp_checkout_btn .= '<input type="hidden" name="item_name_' . $x . '" value="' . $product_name . '">
        <input type="hidden" name="amount_' . $x . '" value="' . $price/840 . '">
        <input type="hidden" name="quantity_' . $x . '" value="' . $each_item['quantity'] . '">  ';
		// Create the product array variable
		$product_id_array .= "$item_id-".$each_item['quantity'].","; 
		// Dynamic table row assembly
		
		$i++; 
    } 
	
	$pp_checkout_btn .= '<input type="hidden" name="item_name_' . $x . '" value="' . $product_name . '">
        <input type="hidden" name="amount_' . $x . '" value="200">';
		
	$pp_checkout_btn .= '<input type="hidden" name="custom" value="' . $product_id_array . '">
	<input type="hidden" name="notify_url" value="https://www.yoursite.com/storescripts/my_ipn.php">
	<input type="hidden" name="return" value="https://www.yoursite.com/checkout_complete.php">
	<input type="hidden" name="rm" value="2">
	<input type="hidden" name="cbt" value="Return to The Store">
	<input type="hidden" name="cancel_return" value="https://www.yoursite.com/paypal_cancel.php">
	<input type="hidden" name="lc" value="US">
	<input type="hidden" name="currency_code" value="USD">
	<input type="image" src="http://www.paypal.com/en_US/i/btn/x-click-but01.gif" name="submit" alt="Make payments with PayPal - its fast, free and secure!">
	</form>';
}

if($i>0)
{
	$itemsarethere = "Your shopping cart contains:  <span>".$i." Product</span>";
}
else
{
	$itemsarethere = "Your shopping cart is empty";
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
                        <h3 class="title">Shopping cart <?php echo $locationId;?></h3>
                    </div>
                    <div class="row-none">
                        <!-- Main content -->
                        <div class="page-order">
                            <ul class="step clearfix"><?php echo $steps;?>
                            </ul>
                            <div class="heading-counter warning">Your Payment is : <?php echo number_format($cartTotal);?> Rwf + <?php echo number_format($cartPrice - $cartTotal); ?> Rwf Shipping fee = <?php echo number_format($cartPrice);?> Rwf
                            </div>
                            <div class="order-detail-content">
                                <table class="table table-bordered table-responsive cart_summary">
                                    <thead>
                                        <tr>
                                            <th class="cart_product">Pay through</th>
                                            <th>Convresion</th>
                                            <th>Rate</th>
                                            <th>Charges</th>
                                            <th>Total</th>
                                            <th  class="action">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<?php 
										$sql2 = $db->query("SELECT * FROM `paysystems1` order by id desc limit 2");
												
										while($row = mysqli_fetch_array($sql2))
													{
														$cartTotal = $cartPrice;
														$payee = $row['pSystem'];
														if($payee == 'paypal'){
														echo'<tr>
														<td>'.$row['pSystem'].'</td>
														<td>'.$row['pConverstion'].'</td>
														<td>'.$row['pRate'].'</td>
														<td>'.$row['pCharges'].'%</td>
														<td>'.round(($cartPrice / $row['pRate'])+($cartPrice / $row['pRate'])* $row['pCharges'] / 100 , 2) .' '.$row['pConverstion'].'</td>
														<td><div class="cart_navigation">'.$pp_checkout_btn.'</div></td></tr>';
														}else{
														echo'<tr>
														<td>'.$row['pSystem'].'</td>
														<td>'.$row['pConverstion'].'</td>
														<td>'.$row['pRate'].'</td>
														<td>'.$row['pCharges'].'%</td>
														<td>'.number_format(($cartPrice / $row['pRate'])+($cartPrice / $row['pRate'])* $row['pCharges'] / 100 , 0) .' '.$row['pConverstion'].'</td>
														<td><div class="cart_navigation"><a href="javascript:void()" class="btn btn-success btn-sm dialog-button"> Chose</a></div></td></tr>';
														}
													}
													?>
									</tbody>
                                </table>
								<?php echo $link;?>
                            </div>
                        </div>
                        <!-- End Main content -->
                    </div>
                </div>
            </div>
            <!-- end part -->
        </div>
        <a href="order.html#" class="scroll_top" title="Scroll to Top" style="display: inline;">Scroll</a>
    </div>
	<dialog id="dialog" class="mdl-dialog" style="padding:0px;">
		<div class="mdl-dialog__actions" style="padding: 10px; text-align: center; display: block;font-size: 20px;    background: #2196F3; color: #fff;">
			Mobile Money Transfer
		</div>
		<div class="mdl-dialog__content" id="result" style="background: #ccc; padding:10px; text-align: center; border-bottom: solid #ccc 0.1px;" id="contBody">
			<input type="number" id="phone1" style="background: #fff; font-size: 20px;
    border-radius: 4px;
    padding: 2px 8px;" value="2507">
		</div>
		<div class="mdl-dialog__actions" id="actionbc" style="padding:10px; display: block;">
			<button type="button" class="btn btn-danger">Close</button>
			<button style="float: right;" onclick="pay()" class="btn btn-success">Pay</button>
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
<script>
    function pay(){
        var amount2 = <?php echo $cartPrice;?>;
        var phone1 = document.getElementById('phone1').value;
        var realphone1 = phone1.substring(phone1.indexOf("7"));
        var realphone2 = '';
        //alert(realphone1);
        //alert(realphone2);
        document.getElementById('result').innerHTML = '<div style="color: red; font-size:20px; padding: 10px">Contacting MTN...</div>';
        $.ajax({
            type : "GET",
            url : "mobilemoney.php",
            dataType : "html",
            cache : "false",
            data : {

                amount2 : amount2,
                phone1 : realphone1,
                phone2 : realphone2,
            },
            success : function(html, textStatus){
                $("#result").html(html);

            },
            error : function(xht, textStatus, errorThrown){
                alert("Error : " + errorThrown);
            }
        });
    }
</script>
</body>
</html>