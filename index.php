<?php

function downloadJson($url) {
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$response = curl_exec($ch);
	curl_close($ch);

	return json_decode($response, true);
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
</head>
<body>
 


<?php
$issues = downloadJson("https://api.github.com/repos/duckduckgo/zeroclickinfo-goodies/issues?page=1&per_page=100");

foreach($issues as $key => $i) {
	if(!$i["pull_request"])
		continue;

	$comments = downloadJson($i["comments_url"]);
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
</body>
</html>
