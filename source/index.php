<?php

$input = explode("?", substr(filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_STRING), 1));
$code = $input[0];
if($code === ''){
	echo 'hcawn.com is a URL shortener website and nothing else, go away please.';
}
elseif($code === 'admin'){
	echo 'admin mode entered';
	include('includes/dbconnect.php');
	################ get codes ################
	$sql = "SELECT * FROM `links` ORDER BY entry;";
	if ( ! $query = mysqli_query($connection, $sql) ) {
		echo mysqli_error($connection);
		die;
	};
	?>
	<form class="addNew" action="" method="post" style="text-align: center;" >
		<input type="text" name="code" placeholder="code" tabindex="1">
		<input type="text" name="URL" placeholder="URL" tabindex="2">
		<input type="submit" name="newCode" value="Create new code" tabindex="3">
	</form>	
	<?php
	if(isset($_POST['newCode'])) {
		$code = strtolower(filter_input(INPUT_POST, 'code', FILTER_SANITIZE_STRING));
		$URL = strtolower(filter_input(INPUT_POST, 'URL', FILTER_SANITIZE_STRING));
		$baseHexr = "K9VQJNPC4LSD8YF2R01ZMG63WT75XB";
		$insertsql = "INSERT INTO `links` (created, modified, code, URL) VALUES ('".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."', '".$code."', '".$URL."');";
		echo $insertsql;
		if (!mysqli_multi_query($connection, $insertsql)) {
			echo mysqli_error($connection);
			die;				
		};
	};
	$list = array();
	echo "<table><tbody><tr><td>entry</td><td>code</td><td>URL</td><td>created</td><td>modified</td></tr><tr><td colspan=5 style='border-bottom: 1px solid #000;'></td></tr>";
	for ($x = 0; $x < mysqli_num_rows($query); $x++) {
		$list[] = mysqli_fetch_array($query, MYSQLI_ASSOC);
		echo "<tr>";
		echo "<tr><td>".$list[$x]['entry']."</td><td>".$list[$x]['code']."</td><td>".$list[$x]['URL']."</td><td>".$list[$x]['created']."</td><td>".$list[$x]['modified']."</td>";
		echo "";
		echo "</tr>";
	};
	echo "</tbody></table>";
	################ get codes ################
	################ get usage ################
	$sql = "SELECT * FROM `linkUsage` ORDER BY entry DESC;";
	if ( ! $query = mysqli_query($connection, $sql) ) {
		echo mysqli_error($connection);
		die;
	};
	$usagelist = array();
	echo "<hr><table><tbody><tr><td>entry</td><td>code</td><td>created</td><td>IP</td><td>agent</td><td>referrer</td><td>extra</td></tr><tr><td colspan=7 style='border-bottom: 1px solid #000;'></td></tr>";
	for ($x = 0; $x < mysqli_num_rows($query); $x++) {
		$usagelist[] = mysqli_fetch_array($query, MYSQLI_ASSOC);
		echo "<tr><td>".$usagelist[$x]['entry']."</td><td>".$usagelist[$x]['code']."</td><td>".$usagelist[$x]['created']."</td><td>".$usagelist[$x]['IP']."</td><td>".$usagelist[$x]['agent']."</td><td>".$usagelist[$x]['referrer']."</td><td>".$usagelist[$x]['extra']."</td></tr>";
	};
	echo "</tbody></table>";
	################ get usage ################

}
else {
	include('includes/dbconnect.php');
	$sql = "SELECT * FROM `links` WHERE  code = '".$code."' OR entry = '".$code."';";
	if ( ! $query = mysqli_query($connection, $sql) ) {
		echo mysqli_error($connection);
		die;
	};
	$result = mysqli_fetch_array($query, MYSQLI_ASSOC);
	if($result != ""){
		#save to DB
		$referrer = "";
		if($_SERVER['HTTP_REFERER']!=""){
			$referrer = $_SERVER['HTTP_REFERER'];
		};
		$extra = "";
		if($input[1]!=""){
			$extra = $input[1];
		};		
		$insertsql = "INSERT INTO `linkUsage` (created, code, ip, agent, referrer, extra) VALUES ('".date("Y-m-d H:i:s")."', '".$code."', '".$_SERVER['REMOTE_ADDR']."', '".$_SERVER['HTTP_USER_AGENT']."', '".$referrer."', '".$extra."');";
		echo $insertsql;
		if (!mysqli_multi_query($connection, $insertsql)) {
			echo mysqli_error($connection);
			die;				
		};
		while(mysqli_more_results($connection)) {
			mysqli_next_result($connection);
		};		
		#save to DB
		header("Location: ".$result['URL']);
		die();	
	}
	else {
		header("Location: https://hcawn.com");
		die();	
	}
};
?>