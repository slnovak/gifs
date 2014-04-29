<?php
// reddit/imgur scraper
$section = 'reactiongifs';
$max_pages = 50;

parse_str(implode('&', array_slice($argv, 1)), $_GET);

if( isset( $_GET['section'] ) ){
	$section = $_GET['section'];
}

if( isset( $_GET['pages'] ) ){
	$section = $_GET['pages'];
}

require_once("reddit.class.php");

echo "Fetching pages...";

// scrape the list of posts
$data = $reddit->scrape($section,$max_pages);

// total number of links returned, initialize counter for percentages
$totalitems = count($data); $counter = 1;

echo "Parsing ".$totalitems." total items...\n\n";

// process the links we are left with
foreach($data as $item) {
	if(strstr($item['url'],'imgur.com')) {
		$reddit->processImgurLink($item['url'],'../' . $section . '/', str_replace(' ', '-', $item['title'] ) . '.' . $section . '.gif' );
	}

	// display progress
	$completed = round(($counter/$totalitems)*100);
	echo ($completed<>$last_completion) ? $completed."% complete\n" : '';
	$last_completion = $completed;
	$counter++;

}
