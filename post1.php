<?php
session_start();
if (isset($_SESSION['Longitude'])) {
    $cityLat = $_SESSION['Latitude'];
    $cityLng = $_SESSION['Longitude'];
}
else {
    if(isset($_GET['postId'])) {
        $postId = $_GET['postId'];
        header("location: clientlocation.php?postId=$postId");
    }
    else {
        header("location: index.php");
    }
}

if(isset($_GET['postId']))
{
	$postId = $_GET['postId'];
	include("db.php");
	$sql = $db->query("
	SELECT I.`itemId`,I.ncpp, I.`itemName`, I.description, I.inDate, I.itemCompanyCode, I.productCode,
	
    IFNULL((SELECT SUM(T.`qty`) FROM `transactions` T WHERE `operation`='In' AND itemCode = '$postId' AND T.`itemCode` = I.`itemId`),0) -
    IFNULL((SELECT SUM(T.`qty`) FROM `transactions` T WHERE `operation`='Out' AND itemCode = '$postId' AND T.`itemCode` = I.`itemId`),0)  Balance
    ,I.`unit`, I.`unityPrice`	
    FROM `items1` I WHERE  itemId = '$postId'");
	while($row = mysqli_fetch_array($sql))
	{
        $postTitle = $row['itemName'];
		$quantity = $row['Balance'];
		$price = $row['unityPrice'];
		$priceStatus = $row['unit'];
		$postDesc = $row['description'];
		$postedDate = $row['inDate'];
		$postedBy = $row['itemCompanyCode'];
		$productCode = $row['productCode'];
		$ncpp = $row['ncpp'];
		$newNncpp = $ncpp+1;
	}
	$sqlncpp = $db->query("UPDATE `items1` SET ncpp=$newNncpp WHERE `itemId` = '$postId'");

	// Related ITEMS
	$relatedItems="";
	$getrelatedItems = $db->query("
		SELECT I.`itemId`, I.`itemName`, I.description, I.inDate, I.itemCompanyCode, I.productCode,
	
		IFNULL((SELECT SUM(T.`qty`) FROM `transactions` T WHERE `operation`='In' AND T.`itemCode` = I.`itemId`),0) -
		IFNULL((SELECT SUM(T.`qty`) FROM `transactions` T WHERE `operation`='Out' AND T.`itemCode` = I.`itemId`),0)  Balance
		,I.`unit`, I.`unityPrice`	
		FROM `items1` I  WHERE `productCode` = '$productCode' AND itemId != '$postId' LIMIT 3");
	$countRelated = mysqli_num_rows($getrelatedItems);
	if($countRelated > 0)
	{
		while($row = mysqli_fetch_array($getrelatedItems))
		{
			$relatedItems.='<li class="item">
								<div class="left-block">
									<a href="post.php?postId='.$row['itemId'].'">
										<img class="img-responsive" alt="'.$row['itemName'].'" src="products/'.$row['itemId'].'.jpg" />
									</a>
								</div>
								<div class="right-block">
									<div class="left-p-info">
										<h5 class="product-name"><a href="post.php?postId='.$row['itemId'].'">'.$row['itemName'].'</a></h5>
										<div class="product-star">
											<i class="fa fa-star-o"></i>
											<i class="fa fa-star-o"></i>
											<i class="fa fa-star-o"></i>
											<i class="fa fa-star-o"></i>
											<i class="fa fa-star-o"></i>
										</div>
									</div>
									<div class="content_price">
										<span class="price product-price">'.number_format($row['unityPrice']).'Rwf/ '.$row['unit'].'</span>
									</div>
								</div>
							</li>
						   ';
		}	
	}else
	{
		$relatedItems.='<li class="item">
				No related Items!
			</li>';
	}
	
	// Company INFO
	$getcompany=$db->query("SELECT * FROM `company1` WHERE `companyId` = '$postedBy'");
	while($row = mysqli_fetch_array($getcompany))
	{
		$companyName = $row['companyName'];
		$companyDescription = $row['companyDescription'];
        $location = $row['location'];
        $companylocation = $row['location'];
        $companylatitude = $row['latitude'];
		$companylongitude = $row['longitude'];
		$businessType = $row['businessType'];
		$dateIn = $row['dateIn'];
	}
	
	// ITEMS IN THE SAME COMPANY
	$samecompany="";
	$getsamecompant = $db->query("
		SELECT I.`itemId`, I.`itemName`, I.description, I.inDate, I.itemCompanyCode, I.productCode,
	
		IFNULL((SELECT SUM(T.`qty`) FROM `transactions` T WHERE `operation`='In' AND `itemCompanyCode` = '$postedBy' AND T.`itemCode` = I.`itemId`),0) -
		IFNULL((SELECT SUM(T.`qty`) FROM `transactions` T WHERE `operation`='Out' AND `itemCompanyCode` = '$postedBy' AND T.`itemCode` = I.`itemId`),0)  Balance
		,I.`unit`, I.`unityPrice`	
		FROM `items1` I  WHERE `itemCompanyCode` = '$postedBy' AND itemId != '$postId' LIMIT 3");
	$countsame = mysqli_num_rows($getsamecompant);
	if($countsame > 0)
	{
		while($row = mysqli_fetch_array($getsamecompant))
		{
			$samecompany.=' <li>
                                            <div class="left-block">
                                                <a href="post.php?postId='.$row['itemId'].'">
                                                    <img class="img-responsive" alt="'.$row['itemName'].'" src="products/'.$row['itemId'].'.jpg" />
                                                </a>
                                                <div class="action">
                                                    <a title="Add to my wishlist" class="heart fa fa-heart" href="post.php?postId='.$row['itemId'].'"></a>
                                                    <a title="Add to compare" class="compare fa fa-retweet" href="post.php?postId='.$row['itemId'].'"></a>
                                                    <a title="Quick view" class="search quickview fa fa-arrows" href="post.php?postId='.$row['itemId'].'"></a>
                                                </div>
                                                <div class="add-to-cart">
                                                    <a title="Add to Cart" class="" href="post.php?postId='.$row['itemId'].'">Add to Cart</a>
                                                </div>
                                                <div class="group-price">
                                                    <span class="product-new">NEW</span>
                                                    <span class="product-sale">Sale</span>
                                                </div>
                                            </div>
                                            <div class="right-block">
                                                <div class="left-p-info">
                                                    <h5 class="product-name"><a href="post.php?postId='.$row['itemId'].'">'.$row['itemName'].'</a></h5>
                                                    <div class="product-star">
                                                        <i class="fa fa-star-o"></i>
                                                        <i class="fa fa-star-o"></i>
                                                        <i class="fa fa-star-o"></i>
                                                        <i class="fa fa-star-o"></i>
                                                        <i class="fa fa-star-o"></i>
                                                    </div>
                                                </div>
                                                <div class="content_price">
                                                    <span class="price old-price">0</span>
                                                    <span class="price product-price">'.number_format($row['Balance']).'Rwf</span>
                                                </div>
                                            </div>
                                        </li>
                                    
						   ';
		}	
	}else
	{
		$relatedItems.='<li class="item">
				No related Items!
			</li>';
	}
	
	// subSubCategory
	$subSubCategory=$db->query("SELECT * FROM `levels` WHERE `id` = '$productCode' LIMIT 1");
	while($row = mysqli_fetch_array($subSubCategory))
	{
		$productName = $row['name'];
		$subCatCode = $row['id'];
		$subCatParent = $row['parentId'];
		// subCategory
		$subCategory=$db->query("SELECT * FROM levels WHERE `id` = '$subCatParent' LIMIT 1");
		while($row = mysqli_fetch_array($subCategory))
		{
			$subCatName = $row['name'];
			$CatCode = $row['id'];
			$CatParent = $row['parentId'];
		
			// category
			$category=$db->query("SELECT * FROM levels WHERE `id` = '$CatParent' LIMIT 1");
			while($row = mysqli_fetch_array($category))
			{
				$catNane = $row['name'];
			}
		}
	}
	
}

?><!DOCTYPE html>
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
    <link rel="stylesheet" type="text/css" href="assets/css/index9.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/quick-view.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/responsive2.css" />
    <title><?php echo $postTitle;?></title>
	  <script src="js/jquery.js"></script>
      <style type="text/css">
        .shipp-card {
            position: relative;
            border: none; 
            margin: 0 0 20px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19); 
            -o-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            border-radius: 5px;
            overflow: hidden;
            background: #fff;
        }
        .modal-body {
            padding: unset;
        }
      </style>
</head>
<body class="shop-single-product detail-page">
    <div class="main-header">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 logo">
                    <a href="#"><img alt="Cavada market" src="assets/images/logo9.png" /></a>
                </div>
                <div class="tool-header">
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 header-search">
                        <span class="toggle-icon"></span>
                        <form class="form-search toggle-mobile" name="_formsearch">
                            <div class="input-search">
                                <input onkeyup="topsearch()" type="text" name="inputsearch"  placeholder="Search everything">
                            </div>
                            
                            <div class="form-category dropdown">
                            <?php
                                include ("db.php");
                                $sql1 = $db->query("SELECT * FROM `productcategory`");
                                echo'<select class="box-category">';
                                while($row = mysqli_fetch_array($sql1)){
                                    $CatID = $row['catId'];
                                    echo'<optgroup label="'.$row['catNane'].'"><option>All Category</option>';
                                    $sql2 = $db->query("SELECT * FROM productsubcategory WHERE CatCode='$CatID'");
                                    while($row = mysqli_fetch_array($sql2))
                                    {
                                        $subCatId = $row['subCatId'];
                                        echo'<option>'.$row['subCatName'].'</option>';
                                        $sql3 = $db->query("SELECT * FROM products WHERE subCatCode='$subCatId'");
                                        while($row = mysqli_fetch_array($sql3)){
                                            echo'<li>'.$row['productName'].'</li>';
                                            }
                                        echo'</ul></li>';
                                    }
                                        echo'</optgroup>';
                                }
                                echo'</select>';

                            ?>
                            </div>
                            <button type="submit" class="btn-search"></button>
                            <div class="getresult" id="getresult"></div>
                        </form>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 right-main-header">
                        
                        <div class="action">
                            <a title="Login" class="compare fa fa-user" href="admin/login.php"></a>
                        </div>
                    </div>
                </div>
            </div>    
        </div>
    </div>
    <div class="wrapper">
        <div class="breadcrumb clearfix">
            <div class="container">
                <ul class="list-breadcr">
                    <li class="home"><a href="index.php" title="Back to Home">Shop</a></li>
                    <li><span><?php echo $postTitle;?></span></li>
                </ul>
            </div>
        </div>
        <div class="page-content" style="background: #e6e6e6; padding: unset;">
            <div class="container" style="    padding-left: 50px;
                padding-right: 50px;
                background: #fff;">
                <div class="row">
                    <!-- Main content -->
                    <div class="col-lg-9 col-md-9 col-sm-12 detail" style="margin-top: 70px;">
                        <div class="primary-box">
                            <!-- product-imge-->
                            <div class="product-image">
                                <div class="product-img-thumb">
                                    <ul class="bxslider">
                                        <li class="item">
                                            <img data-zoom-image="products/<?php echo $postId;?>.jpg"  src="products/<?php echo $postId;?>.jpg" alt=""/> 
                                        </li>
                                        <li class="item">
                                           <img data-zoom-image="products/<?php echo $postId;?>a.jpg" src="products/<?php echo $postId;?>a.jpg" alt=""/> 
                                        </li>
                                        <li class="item">
                                            <img data-zoom-image="products/<?php echo $postId;?>b.jpg" src="products/<?php echo $postId;?>b.jpg" alt=""/> 
                                        </li >
                                        <li class="item">
                                            <img data-zoom-image="products/<?php echo $postId;?>c.jpg" src="products/<?php echo $postId;?>c.jpg" alt=""/> 
                                        </li>
                                        <li class="item">
                                            <img data-zoom-image="products/<?php echo $postId;?>d.jpg" src="products/<?php echo $postId;?>d.jpg" alt=""/> 
                                        </li>
                                    </ul>
                                </div>
                                <div class="product-full">
                                    <img id="product-zoom" src="products/<?php echo $postId;?>.jpg" data-zoom-image="products/<?php echo $postId;?>.jpg" alt="product"/>
                                </div>
                            </div>
                            <!-- product-imge-->
                        </div>
                        <div class="secondary-box">
                            <h3 class="name"><?php echo $postTitle;?></h3>
                            
                            <div class="rating-review">
                                <div class="product-star">
                                    <i class="fa fa-star-o"></i>
                                    <i class="fa fa-star-o"></i>
                                    <i class="fa fa-star-o"></i>
                                    <i class="fa fa-star-o"></i>
                                    <i class="fa fa-star-o"></i>
                                </div>
                                <span class="count-review"><?php $sql5 = $db->query("SELECT * FROM `postscomments` WHERE visibilityStatus='Public' AND postCode='$postId' ORDER by commentId DESC") or die(mysqli_error());
						$countReviews = mysqli_num_rows($sql5);
						
						echo $countReviews;?>
						Customer review</span>
                                
                            </div>

                            <div class="status-product">
                                <p class="price"><?php echo number_format($price);?> Rwf</p>
                                <span class="status"> <?php echo $priceStatus;?></span>
                            </div>

                            <div class="short-text">
                                <p><?php echo $postDesc;?></p>
                            </div>
							<div class="action-detail">
                                <form action="cart.php" method="get">                                   
                                    <div class="qty">
                                        <input class="option-product-qty" name="qty" type="number" max="<?php echo $quantity;?>" min="" value="1" />
                                        <input name="pid" type="hidden" value="<?php echo $postId;?>" />
                                        
                                            <?php echo number_format($quantity);?>
                                    </div>
                                    <div class="action">
                                        <div title="Add to Cart" style="background-color: lightgreen;">
                                            <input type="submit" class="add-to-cart" name="submitItem" value="Add to Cart">
                                        </div>
									</div>
                                </form>

                            </div>
                            <div class="product-data">
                                <p class="product-code">Product code: #<span><?php echo $postId;?></span></p>
                                <p class="product-tags">Product tags: 
                                    <a href=""><span><?php echo $catNane;?>,</span></a>
                                    <a href=""><span><?php echo $subCatName;?>,</span></a>
                                    <a href=""><span><?php echo $productName;?></span></a>
                                </p>
                            </div>
                        </div>
                        <div class="tab-detail">
                            <!-- tab product -->
                            <div class="product-tab">
                                <ul class="nav-tab">
                                    <li class="active">
                                        <a aria-expanded="false" data-toggle="tab" href="shop-single-product.html#description">Description</a>
                                    </li>
                                    <li>
                                        <a aria-expanded="true" data-toggle="tab" href="shop-single-product.html#specification">Company</a>
                                    </li>
                                    <li>
                                        <a data-toggle="tab" href="shop-single-product.html#reviews">reviews</a>
                                    </li>
                                </ul>
                                <div class="tab-container">
                                    <div id="description" class="tab-panel active">
                                        <p><?php echo $postDesc;?>.</p>
                                    </div>
                                    <div id="specification" class="tab-panel">
                                        <table class="table table-bordered">
                                            <tr>
                                                <td>Company Name</td>
                                                <td><?php echo $companyName; ?></td>
                                            </tr>
                                            <tr>
                                                <td>companyDescription</td>
                                                <td><?php echo $companyDescription; ?></td>
                                            </tr>
                                            <tr>
                                                <td>location</td>
                                                <td><?php echo $location; ?></td>
                                            </tr>
											<tr>
                                                <td>businessType</td>
                                                <td><?php echo $businessType; ?></td>
                                            </tr>
											<tr>
                                                <td>dateIn</td>
                                                <td><?php echo $dateIn; ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div id="reviews" class="tab-panel">
                                        <div class="product-comments-block-tab">
                                             <p>
                                                <div id="newComment">
								<a class="add-review" href="javascript:void()" onclick="initiateComment(postCode=<?php echo $postId;?>)">Add your review</a></div>
                                            </p>
						<?php 
						$n=0;
						$sql4 = $db->query("SELECT * FROM `postscomments` WHERE visibilityStatus='Public' AND postCode='$postId' ORDER by commentId DESC") or die(mysqli_error());
						while($row = mysqli_fetch_array($sql4)){
							$n++;
							$commentCode=$row['commentId'];
						echo'<div class="comment row">
                                                <div class="col-sm-3 author">
                                                    <div class="grade">
                                                        <span>'.$n.' Grade</span>
                                                        <div class="product-star">
                                                            <i class="fa fa-star-o"></i>
                                                            <i class="fa fa-star-o"></i>
                                                            <i class="fa fa-star-o"></i>
                                                            <i class="fa fa-star-o"></i>
                                                            <i class="fa fa-star-o"></i>
                                                        </div>
                                                    </div>
                                                    <div class="info-author">
                                                        <span><strong>'.$row['commentBy'].'</strong></span>
                                                        <em>04/08/2015</em>
                                                    </div>
                                                </div>
                                                <div class="col-sm-9 commnet-dettail">
                                                    '.$row['commentNote'].'
												<div id="reply'.$row['commentId'].'">
													<i><a href="javascript:void()" onclick="reply(commentId='.$row['commentId'].', postCode='.$postId.')">Reply</a></i>
												</div>	';
						$f=0;
						$sql5 = $db->query("SELECT * FROM `commentreplies` WHERE visibilityStatus='Public' AND commentCode='$commentCode' ORDER by replyID DESC") or die(mysqli_error());
						while($row = mysqli_fetch_array($sql5))
							{
								$f++;
								echo'.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$f.' <b>'.$row['replyBy'].'</b>: '.$row['replyNotes'].'<br/>';
							}
						echo'</div>
                           </div>';
						}
						?>
				
											
                                        </div>
                                    </div>
                                  </div>
                            </div><!-- end tab product -->
                        </div>
                    </div><!-- End Main content -->
                    <!-- Column left -->
                    <div class="relative-product" style="margin-top: 70px;">
                        <div class="nav-menu custom-menu">
                            <div class="navbar-label">
                                <h3 class="title"><span>Suggested Shipping</span></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-left col-lg-3 col-md-3 col-sm-12 col-xs-12" style="overflow-y: auto;max-height: 500px;">
                        <div class="left-banner">
                            <div>
                                <?php 
                                    $selectshipping = $db->query("SELECT * FROM `shipper`") or die(mysqli_error());

                                    function distbtnshipperandpro($companylatitude, $companylongitude, $shipperlatitude, $shipperlongitude) {
                                        $earthRadius = 6371; // Earth’s mean radius in meter

                                        $latFrom = deg2rad($companylatitude);
                                        $lonFrom = deg2rad($companylongitude);
                                        $latTo = deg2rad($shipperlatitude);
                                        $lonTo = deg2rad($shipperlongitude);

                                        $latDelta = $latTo - $latFrom;
                                        $lonDelta = $lonTo - $lonFrom;

                                        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
                                        return $angle * $earthRadius; // returns the distance
                                    }
                                    while($shipper = mysqli_fetch_array($selectshipping)){
                                        $shipperId = $shipper['shipperId'];
                                        $shipperlat = $shipper['latitude'];
                                        $shipperlong = $shipper['longitude'];

                                        $companylatitude = $companylatitude;
                                        $companylongitude = $companylongitude;

                                        $shipperlatitude = $shipperlat;
                                        $shipperlongitude = $shipperlong;

                                        $clientlatitude = $cityLat;
                                        $clientlongitude = $cityLng;

                                        $distbtnshandpro = distbtnshipperandpro($companylatitude, $companylongitude, $shipperlatitude, $shipperlongitude);

                                        $distbtnproandcli = distbtnshipperandpro($companylatitude, $companylongitude, $clientlatitude, $clientlongitude);

                                        $totalDistance = $distbtnproandcli + $distbtnshandpro;

                                        $selectshipper = $db->query("SELECT * FROM `users` WHERE id = '$shipperId'") or die(mysqli_error());
                                        $shipperInfo = mysqli_fetch_array($selectshipper);
                                       ?>
                                            <div data-toggle="modal" data-target="#<?php echo $shipper['shippingId'];?>" class="shipp-card" title="click for more information">
                                                <div style="text-align: center;"><h2><?php echo $shipper['title'];?></h2></div>
                                                <div style="position: relative; margin-top: 15px;">
                                                    <img src="shipper/<?php echo $shipper['shippingId'];?>.jpg">
                                                </div>
                                                <div class="row" style="margin: 5px -10px;">
                                                    <div class="col-md-3">
                                                        <div style=" height: 35px;width: 35px;border-radius: 50%; background-image: url(assets/images/users/<?php echo $shipperId;?>.jpg); background-repeat: no-repeat;background-attachment: fixed;background-position: center;background-color: #33d09d;"></div>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <?php echo $shipperInfo['names'];?><br>
                                                        <?php echo $shipperInfo['phone'];?>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Modal -->
                                            <div id="<?php echo $shipper['shippingId'];?>" class="modal fade" role="dialog">
                                              <div class="modal-dialog">

                                                <!-- Modal content-->
                                                <div class="modal-content">
                                                  <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    <h4 class="modal-title"><?php echo $shipper['title'];?></h4>
                                                  </div>
                                                  <div class="modal-body">
                                                    <img src="shipper/<?php echo $shipper['shippingId'];?>.jpg">
                                                  </div>
                                                  <div class="modal-footer">
                                                    <div class="col-md-3"><img class="img-circle" src="assets/images/users/<?php echo $shipperId;?>.jpg"></div>
                                                    <div class="col-md-9">
                                                        <p style="text-align:left;">
                                                            <b>Names: </b> <?php echo $shipperInfo['names'];?><br>
                                                            <b>Contact: </b><?php echo $shipperInfo['phone'];?><br>
                                                            <b>Limit: </b> <?php echo $shipper['WeightLimit'];?> Kg<br>
                                                            <b>Price Per Km: </b> <?php echo $shipper['pricepkilo'];?> frw<br>
                                                             <b>Car Address: </b> <?php echo $shipper['address'];?><br>
                                                            <b>car lat: </b> <?php echo $shipper['latitude'];?><br>
                                                            <b>Car long: </b> <?php echo $shipper['longitude'];?><br>
                                                            <b>pro lat: </b> <?php echo $companylatitude;?><br>
                                                            <b>pro long: </b> <?php echo $companylongitude;?><br>
                                                            <b>client lat: </b> <?php echo $clientlatitude;?><br>
                                                            <b>client long: </b> <?php echo $clientlongitude;?><br>
                                                            <b>Dist btn S and P: </b> <?php echo $distbtnshandpro;?> km<br>
                                                            <b>Dist btn P and C: </b> <?php echo $distbtnproandcli;?> km<br>
                                                            <b>Total Distance: </b> <?php echo $totalDistance;?> km<br>
                                                            <b>Amount: </b> <?php echo number_format($totalDistance * $shipper['pricepkilo']) ;?> frw<br>
                                                        </p>
                                                    </div>
                                                    <hr>
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                  </div>
                                                </div>

                                              </div>
                                            </div>
                                        <?php 
                                    }
                                ?>
                            </div>
                        </div>
                        <!-- end Block fillter -->
                    </div><!-- End Column left -->
              </div>
                <div class="row">
                    <div class="popular-tabs control col-lg-12 col-md-12">
                        <div class="navbar nav-menu">
                <!-- Brand and toggle get grouped for better mobile display -->
                            <div class="navbar-label"><h3 class="title"><span>Other Products from <?php echo $companyName; echo $_SESSION['Latitude']; $_SESSION['Longitude'];$_SESSION['Adress'];?></span></h3></div>
                        </div>
                        <div class="tab-container">
                            <div id="tab-1" class="tab-panel active">
                                <ul class="product-list owl-carousel" data-dots="false" data-loop="true" data-nav = "true" data-margin = "30" data-autoplayTimeout="1000" data-autoplayHoverPause = "true" data-responsive='{"0":{"items":1},"500":{"items":2},"800":{"items":3},"1000":{"items":4}}'>
                                   <?php echo $samecompany;?> 
								</ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
         <a href="shop-single-product.html#" class="scroll_top" title="Scroll to Top" style="display: inline;">Scroll</a>
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
    <!-- <script type="text/javascript" src="assets/js/equalheight.js"></script> -->
    <script> 
    <!--1 Load product to Edit-->
    function subshow(subshowid){
    	//alert(subshowid);
    	$.ajax({
    			type : "GET",
    			url : "scripthome.php",
    			dataType : "html",
    			cache : "false",
    			data : {
    				
    				subshowid : subshowid,
    			},
    			success : function(html, textStatus){
    				$("#dynamiclist").html(html);
    			},
    			error : function(xht, textStatus, errorThrown){
    				alert("Error : " + errorThrown);
    			}
    	});
    } 
    <!--2 Load comment box-->
    function initiateComment(postCode)
    {
    	//alert('yeahp');
    	var postCode = postCode;
    	var comment = '1';
    	$.ajax({
    			type : "GET",
    			url : "scripthome.php",
    			dataType : "html",
    			cache : "false",
    			data : {
    				
    				comment : comment,
    				postCode : postCode,
    			},
    			success : function(html, textStatus){
    				$("#newComment").html(html);
    			},
    			error : function(xht, textStatus, errorThrown){
    				alert("Error : " + errorThrown);
    			}
    	});
    }<!--5 Load comment box-->
    <!-- 3 save comment-->
    function saveComment(){
    	var commentBy = document.getElementById('commentBy').value;
    	var commentNote = document.getElementById('commentNote').value;
    	if (commentNote == null || commentNote == "") {
            alert("commentNote must be filled out");
            return false;
        }
    	var visibilityStatus = document.getElementById('visibilityStatus').value;
    	if (visibilityStatus == null || visibilityStatus == "") {
            alert("visibilityStatus must be filled out");
            return false;
        }
    	var postCode = document.getElementById('postCode').value;
    	if (postCode == null || postCode == "") {
            alert("postCode must be filled out");
            return false;
        }
    	$.ajax({
    			type : "GET",
    			url : "scripthome.php",
    			dataType : "html",
    			cache : "false",
    			data : {
    				
    				commentNote : commentNote,
    				commentBy : commentBy,
    				visibilityStatus : visibilityStatus,
    				postCode : postCode,
    			},
    			success : function(html, textStatus){
    				$("#newComment").html(html);
    			},
    			error : function(xht, textStatus, errorThrown){
    				alert("Error : " + errorThrown);
    			}
    	});
    }
    <!--4 Load reply box-->
    function reply(commentId,postCode)
    {
    	var commentId = commentId;
    	var postCode = postCode;
    	$.ajax({
    			type : "GET",
    			url : "scripthome.php",
    			dataType : "html",
    			cache : "false",
    			data : {
    				
    				commentId : commentId,
    				postCode : postCode,
    			},
    			success : function(html, textStatus){
    				$("#reply"+commentId+"").html(html);
    			},
    			error : function(xht, textStatus, errorThrown){
    				alert("Error : " + errorThrown);
    			}
    	});
    }

    <!-- 5 save reply-->
    function replyComment(){
    	var replyBy = document.getElementById('replyBy').value;
    	var postCode = document.getElementById('postCode').value;
    	var replyNotes = document.getElementById('replyNote').value;
    	if (replyNotes == null || replyNotes == "") {
            alert("replyNotes must be filled out");
            return false;
        }
    	var visibilityStatus = document.getElementById('visibilityStatus').value;
    	if (visibilityStatus == null || visibilityStatus == "") {
            alert("visibilityStatus must be filled out");
            return false;
        }
    	var commentCode = document.getElementById('commentCode').value;
    	if (commentCode == null || commentCode == "") {
            alert("commentCode must be filled out");
            return false;
        }
    	$.ajax({
    			type : "GET",
    			url : "scripthome.php",
    			dataType : "html",
    			cache : "false",
    			data : {
    				
    				replyNotes : replyNotes,
    				postCode : postCode,
    				replyBy : replyBy,
    				visibilityStatus : visibilityStatus,
    				commentCode : commentCode,
    			},
    			success : function(html, textStatus){
    				$("#reply"+commentCode+"").html(html);
    			},
    			error : function(xht, textStatus, errorThrown){
    				alert("Error : " + errorThrown);
    			}
    	});
    }
    </script>
</body>
</html>