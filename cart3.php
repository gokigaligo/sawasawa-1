<?PHP require('db.php');
 if(isset($_GET['vilageId'])){$mylocation = $_GET['vilageId'];}
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
<?php
// Script Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', '0');
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
			$itemCompanyCode = $row["itemCompanyCode"];
			$sqlCompany = $db->query("SELECT location FROM company1 WHERE companyId='$itemCompanyCode' LIMIT 1");
			$rowCompany = mysqli_fetch_array($sqlCompany);
			$itemLocation = $rowCompany['location'];
			
			if($itemLocation == $mylocation){$shipPirce = $villagePrice;}
			else
			{
				$sqlCellSaler = $db->query("SELECT parentId FROM locations WHERE id='$itemLocation'");
				$rowCellSaler = mysqli_fetch_array($sqlCellSaler);
				$cellSalerId = $rowCellSaler['parentId'];
				$sqlCellBuyer = $db->query("SELECT parentId FROM locations WHERE id='$mylocation'");
				$rowCellBuyer = mysqli_fetch_array($sqlCellBuyer);
				$cellBuyerId = $rowCellBuyer['parentId'];
				if($cellSalerId == $cellBuyerId){$shipPirce = $cellPrice;}
				else
				{
					$sqlSectorSaler = $db->query("SELECT parentId FROM locations WHERE id='$cellSalerId'");
					$rowSectorSaler = mysqli_fetch_array($sqlSectorSaler);
					$sectorSalerId = $rowSectorSaler['parentId'];
					
					$sqlSectorBuyer = $db->query("SELECT parentId FROM locations WHERE id='$cellBuyerId'");
					$rowSectorBuyer = mysqli_fetch_array($sqlSectorBuyer);
					$SectorBuyerId = $rowSectorBuyer['parentId'];
					if($sectorSalerId == $SectorBuyerId){$shipPirce = $sectorPrice;}
					else
					{
						$sqlDistrictSaler = $db->query("SELECT parentId FROM locations WHERE id='$sectorSalerId'");
						$rowDistrictSaler = mysqli_fetch_array($sqlDistrictSaler);
						$districtSalerId = $rowDistrictSaler['parentId'];
						
						$sqlDistrictBuyer = $db->query("SELECT parentId FROM locations WHERE id='$SectorBuyerId'");
						$rowDistrictBuyer = mysqli_fetch_array($sqlDistrictBuyer);
						$districtBuyerId = $rowDistrictBuyer['parentId'];
						if($districtSalerId == $districtBuyerId){$shipPirce = $districtPrice;}
						else
						{
							$sqlProvinceSaler = $db->query("SELECT parentId FROM locations WHERE id='$districtSalerId'");
							$rowProvinceSaler = mysqli_fetch_array($sqlProvinceSaler);
							$provinceSalerId = $rowProvinceSaler['parentId'];
							
							$sqlProvinceBuyer = $db->query("SELECT parentId FROM locations WHERE id='$districtBuyerId'");
							$rowProvinceBuyer = mysqli_fetch_array($sqlProvinceBuyer);
							$provinceBuyerId = $rowProvinceBuyer['parentId'];
							if($provinceSalerId == $provinceBuyerId){$shipPirce = $provincePrice;}
							else
							{
								$sqlCountrySaler = $db->query("SELECT parentId FROM locations WHERE id='$provinceSalerId'");
								$rowCountrySaler = mysqli_fetch_array($sqlCountrySaler);
								//echo
								$countrySalerId = $rowCountrySaler['parentId'];
								//echo '<hr/>';
								$sqlCountryBuyer = $db->query("SELECT parentId FROM locations WHERE id='$provinceBuyerId'");
								$rowCountryBuyer = mysqli_fetch_array($sqlCountryBuyer);
								//echo
								$countryBuyerId = $rowCountryBuyer['parentId'];
								if($countrySalerId == $countryBuyerId){$shipPirce = $country;}
								else{$shipPirce = 'error';}
							}
						}
					}
				}
			}
			
		}
		$pricetotal = $price * $each_item['quantity'];
		$Gtotal = $shipPirce + $pricetotal;
		$cartTotal = $Gtotal + $cartTotal;
		setlocale(LC_MONETARY, "en_US");
       // $pricetotal = money_format("%10.2n", $pricetotal);
		// Dynamic Checkout Btn Assembly
		$x = $i + 1;
		// Create the product array variable
		$product_id_array .= "$item_id-".$each_item['quantity'].","; 
		// Dynamic table row assembly
		
		$cartOutput .= "<tr>";
		$cartOutput .= '
		<td class="cart_product">
		<a href="shop-single-product.html"><img src="products/'.$item_id.'.jpg" alt="Product"></a>
		</td>';
		$cartOutput .= '<td class="cart_description">
                                                <p class="product-name"><a href="post.php?postId='.$item_id.'">'.$product_name.'</a></p>
                                                <small><a href="post.php?postId='.$item_id.'">'.$details.'</a> </small><br>
                                            </td>';
		$cartOutput .= '<td>' . $price . ' Rwf</td>';
		
		$cartOutput .= '<td class="price"><span>' . $pricetotal . ' Rwf</span></td>';
		$cartOutput .= '<td>'.$shipPirce.' Rwf</td>';
		$cartOutput .= '<td>'.$Gtotal.' Rwf</td>';
		$cartOutput .= '</tr>';
		$i++; 
    } 
	//setlocale(LC_MONETARY, "en_US");
   // $cartTotal = money_format("%10.2n", $cartTotal);
	//$cartTotal = "".$cartTotal." Rwf";
    // Finish the Paypal Checkout Btn
	
	
	//$pp_checkout_btn .= '
	//<form action="peyment.php" method="post">
	//<input type="text" name="itemCode" value="' . $item_id . '">
	//<input type="text" name="quantity" value="' . $each_item['quantity'] . '">
	//</form>';
	
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
<table class="table table-bordered table-responsive cart_summary">
                                    <thead>
                                        <tr>
                                            <th class="cart_product">Product</th>
                                            <th>Description</th>
                                            <th>Unit price</th>
                                            <th>Total</th>
                                            <th>Shipping</th>
                                            <th>GTotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<?php echo $cartOutput;?>
									</tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" rowspan="2"></td>
                                            <td colspan="3">Total products</td>
                                            <td colspan="2"><?php echo number_format($cartTotal);?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3"><strong>Total</strong></td>
                                            <td colspan="2"><strong><?php echo number_format($cartTotal);?></strong></td>
                                        </tr>
                                    </tfoot>    
                                </table>
								
									<div class="cart_navigation">
										<?php echo $link;?>
									</div>
								<input name="cartPrice" value="<?php echo $cartTotal;?>">