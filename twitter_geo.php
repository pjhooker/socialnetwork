<!DOCTYPE html>
<html lang="it-IT" prefix="og: http://ogp.me/ns#">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Natural trees MAP | MapIT</title>
	<link rel='stylesheet' id='style-css'  href='http://www.cityplanner.it/natural_tree/wp-content/themes/flat-theme/style.css?ver=3.9.1' type='text/css' media='all' /> 

</head><!--/head-->

<body>

<?php




?>
<?php


    $connessione = mysqli_connect("HOST", "USER", "PASSWORD","DBNAME")
        or die("Connessione non riuscita: " . mysqli_error());
    print ("Connesso con successo<br>");


$pageid=$_GET["id"];

$result = mysqli_query($connessione,"SELECT * FROM grid WHERE `id`=$pageid");

while($row = mysqli_fetch_array($result)) {
  $lat=$row['lat'];
  $lng=$row['lng'];
  echo"lat: $lat | lng: $lng";
}


ini_set('display_errors', 1);
require_once('TwitterAPIExchange.php');

/** Set access tokens here - see: https://dev.twitter.com/apps/ **/
$settings = array(
    'oauth_access_token' => "YOURTOKEN",
    'oauth_access_token_secret' => "YOURTOKEN",
    'consumer_key' => "YOURTOKEN",
    'consumer_secret' => "YOURTOKEN"
);

/** URL for REST request, see: https://dev.twitter.com/docs/api/1.1/ **/
$url = 'https://api.twitter.com/1.1/blocks/create.json';
$requestMethod = 'POST';

/** POST fields required by the URL above. See relevant docs as above **/
$postfields = array(
    'screen_name' => 'usernameToBlock', 
    'skip_status' => '1'
);

/** Perform a POST request and echo the response **/
$twitter = new TwitterAPIExchange($settings);
echo $twitter->buildOauth($url, $requestMethod)
             ->setPostfields($postfields)
             ->performRequest();

/** Perform a GET request and echo the response **/
/** Note: Set the GET field BEFORE calling buildOauth(); **/
$url = 'https://api.twitter.com/1.1/search/tweets.json';
$getfield = '?q=&geocode='.$lat.','.$lng.',0.5km&lang=eu&locale=it&result_type=recent&count=100';
$requestMethod = 'GET';

$twitter = new TwitterAPIExchange($settings);
$response = $twitter->setGetfield($getfield)
    ->buildOauth($url, $requestMethod)
    ->performRequest();


$json_a=json_decode($response,true);

//$json_a=json_decode($response);
foreach($json_a['statuses'] as $data2) { 
	echo"<div style='postition:relative;border:1px;'>";
	$count++;
	$val0=$data2['id'];
	$val1=$data2['user']['screen_name'];
	$string_post="https://twitter.com/".$data2['user']['screen_name']."/status/".$data2['id'];
	echo "<a href='$string_post'>https://twitter.com/".$data2['user']['screen_name']."/status/".$data2['id']."</a>,";
	$coo=0;
	$val2='0';
	$val3='0';
	foreach($data2['geo']['coordinates'] as $data) {
		$coo++;
		//$thumbnail = $data2['geo']['coordinates'];
		if ($coo==1){$tcoo='lat';$val2=$data;}else{$tcoo='lng';$val3=$data;}
		echo"$tcoo ".$data."|";
	}
	echo"
		0
	";

mysqli_query($connessione,"
INSERT INTO `tb_twtter01` (
`id` ,
`user` ,
`lat` ,
`lng`
)
VALUES (
'$val0', '$val1', '$val2', '$val3'
);
	");

	echo"
		 (inserito)</div>
	";

}

    mysqli_close($connessione);
    $pageidnext=$pageid+1;
	if ($pageid<122){
	//if ($pageid<0){
	echo'<META http-equiv="refresh" content="0;URL=http://www.cityplanner.it/experiment_host/php/twitter01/index.php?id='.$pageidnext.'">';
	}
	else{echo "STOP";}
?>





          
	</body>
</html>
