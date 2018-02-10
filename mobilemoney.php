<?php 
if(isset($_GET['amount2']))
{
	$url = 'http://51.141.48.174:9000/requestpayment/';
	
	$data = array();
	$data["agentName"] = "UPLUS";
	$data["agentId"] = "0784848236";
	$data["phone"] = $_GET['phone1'];
    $data["phone2"] = $_GET['phone2'];
	$data["amount"] = $_GET['amount2'];
    $options = array(
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'POST',
			'content' => http_build_query($data)
		)
	);
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);
	if ($result === FALSE) 
	{ 
		var_dump($result);	
	}
	ECHO $server_output = $result;
	


	// PUT THE RESPONSE IN SESSION SO THAT I CAN CALL IT'S STATUS
	session_start();
	$_SESSION["server_output"] = $server_output;
	// FROM JSON TO PHP
	$firstcheck = json_decode($server_output);
	$status = $firstcheck->{'information'};
	// 1ST STATUS CONNECTED TO THE INTEGRATOR API WAITNING FOR MTN/TIGO RESPONSE
	echo'<div id="returning">';
			echo $status;
	echo'</div>';
	// FIRE THE RECURRING CALL AFTER 7 SEC TO CHECK THE STATUS AND DESTROY THAT AFTER 2 MIN
	echo'
	<script>
		interval = setInterval(function() { checking();}, 15000);
		interval;
		stopcall = setInterval(
			function() { 
				stopit();
			}, 60000);
		stopcall;
	</script>
	';
}
?>
<!-- 2 AJAX CALL THE STATUS-->
<script>
function checking()
	{
		var check =1;
		//alert('ChecKing Status');
		$.ajax(
			{
				type : "GET",
				url : "backend.php",
				dataType : "html",
				cache : "false",
				data :
					{
						check : check,
					},
				success : function(html, textStatus)
				{
					//aler('Status is back');
					$("#returning").html(html);
				},
				error : function(xht, textStatus, errorThrown)
				{
					alert("Error : " + errorThrown);
				}
			});
	}
function stopit()
	{
		clearInterval(interval);
		document.getElementById('status').innerHTML = 'Canceled.';
	}
</script>

<?php // 3 TRACK THE STATUS, STOP IF DONE OR ERROR
if(isset($_GET['check']))
{
	session_start();
	$server_output = $_SESSION["server_output"];
    

    $data = json_decode($server_output);
	$url = 'http://51.141.48.174:9000/requestpayment/';
	$options = array(
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'POST',
			'content' => http_build_query($data)
		)
	);
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);
	if ($result === FALSE) 
	{ 
		var_dump($result);	
	}
	$server_output = $result;
	

		// CHANGE THE INFORMATION TO RESEND
	$_SESSION["server_output"] = $server_results;
		// DECODE THE RETURNED STATUS FROM PHONE 1 AND 2
	$obj = json_decode($server_results);
		$info1 = $obj->{'information'};
		$info2 = $obj->{'information2'};
		$amount = $obj->{'amount'};
		
		if($info1 == 'REQUESTED')
			{
				echo'<div id="status">Please approve a request on your Phone</br>
					<button class="button" onclick="stopit()">cancel</button></div>';
			}
		elseif($info1 == 'DECLINED')
			{
				echo'<script>clearInterval(interval);</script>
					Sorry you just canceled, but its okay you can try again.</br>
					<button class="button" onclick="pay()">Send</button></div>';
			}
		elseif($info1 == 'APPROVED')
			{
				if($info2 == 'COMPLETE')
					{
						echo'<script>clearInterval(interval);</script> 
							Thanks The money has been received by '.$phone2.'. Status:'.$info2.'';
					}
				elseif($info2 == 'Error sending money.')
					{
						echo'<script>clearInterval(interval);</script>
							'.$info2.'
							<form>
								The mobile destination you provided might not be in mobile money, 
								so we are going to return back your money after some fiew try.  
								Please send us your feadback
								<textarea name="feadback"></textarea><br/>
								<input class="button" type="submit" value="send"/>
							</form>
							';
					}
				else{
						echo'<script>clearInterval(interval);</script>
							'.$info2;
					}
			}
		else
			{
				echo'
					Something is not right. 
					You might not have enough money on your phone
					or, Your Phone might not be registred in eather MTN or TIGO
					'.$info2.' '.$info1.'';
			}
	}
?>