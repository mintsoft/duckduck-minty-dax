<?php

function curl_get($url, array $get = NULL, array $options = array())
{   
    $defaults = array(
        CURLOPT_URL => $url,
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_TIMEOUT => 4,
        CURLOPT_USERAGENT => "DuckDuck-Minty-Dax"
    );
   
    $ch = curl_init();
    curl_setopt_array($ch, ($options + $defaults));

    if( ! $result = curl_exec($ch))
    {
        trigger_error(curl_error($ch));
    }

    curl_close($ch);
    return json_decode($result, true);
} 

?>
<!DOCTYPE html>
<html lang="en">
<head>
</head>
<body>
 
<table>
<thead>
</thead>
<tbody>

<?php
$issues = curl_get("https://api.github.com/repos/duckduckgo/zeroclickinfo-goodies/issues?page=1&per_page=100");
var_dump($issues);

exit;
foreach($issues as $key => $i) {
	if(!$i["pull_request"])
		continue;

#	$comments = curl_get($i["comments_url"]);
	$author = $i["user"]["login"];
	$title = $i["title"];

	$latest_comment = array(
		created_at => '1970-01-01T00:00:00Z',
		updated_at => '1970-01-01T00:00:00Z',
	);

	foreach($comments as $k => $comment) {
		if($latest_comment["created_at"] < $comment["created_at"]) {
			$latest_comment = $comment;
		}
	}	

}
?>
</tbody>
</table>
</body>
</html>
