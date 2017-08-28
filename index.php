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
    $return_result = json_decode($result, true);
    if(isset($return_result['message']))
    {
        echo "JSON error returned: ".$return_result['message'];
        throw new Exception("JSON error returned: ".$return_result['message']);
    }
    return $return_result;
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
<style>
    .highlight {
        background: #efb4a5;
    }
</style>
</head>
<body>
<form method='post'>
<p> Github token <input type='text' name='token' value='' /> </p>
<p> <input type='submit' /> </p>
</form> 
<table>
<thead>
    <th>Number</th>
    <th>Created At</th>
    <th>Updated At</th>
    <th>Author</th>
    <th>Latest Comment Updated At</th>
    <th>Latest Comment Author</th>
</thead>
<tbody>

<?php
$comment_warning_threshold = 7;
$timeago = gmdate("Y-m-d\TH:i:s", time() - $comment_warning_threshold * 86400)."Z";

$options = array();

if(!empty($_POST['token'])) {
    $token = $_POST['token'];
    $options = array("Authorization", "token $token");
}

$issues = curl_get("https://api.github.com/repos/duckduckgo/zeroclickinfo-goodies/issues?page=1&per_page=100", NULL, $options);
$index = 1;
foreach($issues as $key => $i) {

	if(!isset($i["pull_request"]))
		continue;
    
	$comments = array();
	$comments = curl_get($i["comments_url"], NULL, $options);
	$author = $i["user"]["login"];
	$title = $i["title"];
	$url = $i["html_url"];

	$latest_comment = array(
		"created_at" => '1970-01-01T00:00:00Z',
        "updated_at" => '1970-01-01T00:00:00Z',
        "user" => array("login"=> "")
	);

	foreach($comments as $k => $comment) {
		if($latest_comment["created_at"] < $comment["created_at"]) {
			$latest_comment = $comment;
		}
    }
    
    $className = $latest_comment["updated_at"] < $timeago ? "highlight" : "";
    echo "<tr class='$className'>";
        echo "<td><a href='$url'>".htmlentities($i["number"])."</a></td>";
        echo "<td>".htmlentities($i["created_at"])."</td>";
        echo "<td>".htmlentities($i["updated_at"])."</td>";
        echo "<td>".htmlentities($author)."</td>";
        echo "<td>".htmlentities($latest_comment["updated_at"])."</td>";
        echo "<td>".htmlentities($latest_comment["user"]["login"])."</td>";
    echo "</tr>";
}
?>
</tbody>
</table>
</body>
</html>
