<?php  

	if (isset($_POST['location'])) {
		$location = $_POST['location'];
		$cityLat = $_POST['cityLat'];
		$cityLong = $_POST['cityLong'];
		$_SESSION['Latitude'] = $cityLat;
		$_SESSION['Longitude'] = $cityLong;
		$_SESSION['Adress'] = $location;
	}

?>