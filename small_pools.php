<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// TWITTER KEYS
//

require __DIR__ . '/config.php';

// COMPOSER AUTOLOAD DEPENDENCIES
//

require __DIR__ . '/vendor/autoload.php';

use Abraham\TwitterOAuth\TwitterOAuth;
 
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_SECRET);


// CONFIGURATIONS

// my ticker
$myticker = "COCO";


// log file
$log_path = "/home/twitter/logs/small_pools.log";

// FONTS
$font_path 	= dirname(__FILE__) . '/fonts';
$font_body  = '/source_code/SourceCodePro-SemiBold.ttf';
$font_title = '/nerko/nerko-regular.ttf';
$title_color = 'D5302F';
$body_color  = '2C3336';

// IMAGE BACKGROUND
$background = dirname(__FILE__) . '/img/twitter_bg.png';

// TITLE TEXT
$title = 'POOLS WITH LESS THAN 10 MILLION OF ACTIVE STAKE';

// POOL STAKE MAXIMUM
$pool_stake = 10000000000000;

// ID (dont change)
$id = random_int(100, PHP_INT_MAX);

// NEW IMAGE PATH
$new_image = dirname(__FILE__) . '/img/small_pools/'. $id .'.png';


/**
 * Slightly modified version of http://www.geekality.net/2011/05/28/php-tail-tackling-large-files/
 * @author Torleif Berger, Lorenzo Stanco
 * @link http://stackoverflow.com/a/15025877/995958
 * @license http://creativecommons.org/licenses/by/3.0/
 */

function tailCustom($filepath, $lines = 1, $adaptive = true) {

	// Open file
	$f = @fopen($filepath, "rb");
	if ($f === false) return false;

	// Sets buffer size, according to the number of lines to retrieve.
	// This gives a performance boost when reading a few lines from the file.
	if (!$adaptive) $buffer = 4096;
	else $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));

	// Jump to last character
	fseek($f, -1, SEEK_END);

	// Read it and adjust line number if necessary
	// (Otherwise the result would be wrong if file doesn't end with a blank line)
	if (fread($f, 1) != "\n") $lines -= 1;
	
	// Start reading
	$output = '';
	$chunk = '';

	// While we would like more
	while (ftell($f) > 0 && $lines >= 0) {

		// Figure out how far back we should jump
		$seek = min(ftell($f), $buffer);

		// Do the jump (backwards, relative to where we are)
		fseek($f, -$seek, SEEK_CUR);

		// Read a chunk and prepend it to our output
		$output = ($chunk = fread($f, $seek)) . $output;

		// Jump back to where we started reading
		fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);

		// Decrease our line counter
		$lines -= substr_count($chunk, "\n");

	}

	// While we have too many lines
	// (Because of buffer size we might have read too many)
	while ($lines++ < 0) {

		// Find first newline and remove all text before that
		$output = substr($output, strpos($output, "\n") + 1);

	}

	// Close file and return
	fclose($f);
	return trim($output);

}

// QUERY ADAPOOLS FOR POOL DATA
function poolQuery() {

	$poolURL 	= "https://js.adapools.org/pools.json";
	$poolINIT 	= curl_init($poolURL);
	
	curl_setopt($poolINIT, CURLOPT_URL, $poolURL);
	curl_setopt($poolINIT, CURLOPT_RETURNTRANSFER, true);
	
	$poolStats 	= curl_exec($poolINIT);
	
	curl_close($poolINIT);

	return $poolStats;
}
//
// DATA
//



a:

// POOL DATA FROM ADAPOOLS
$pools = json_decode(poolQuery(), true);

// INITIALIZE EMPTY TICKERS ARRAY
$tickers = [];

// GO THROUGH THE POOL DATA
foreach ($pools as $pool => $stats) {
	
	// TICKER	
	$ticker 	= $stats['db_ticker'];
	
	// TWITTER HANDLE
	$twitter 	= $stats['handles']['tw'];

	// ACTIVE STAKE
	$stake = $stats['active_stake'];

		
		// 1) FIND POOLS WITH ACTIVE STAKE OF LESS THAT $POOL_STAKE
		if ($stake <= $pool_stake) {
			

			// 2) FIND POOLS WITH TWITTER META DATA
			if ($twitter) {
				
				// BUILD AN ARRAY FOR EACH POOL WITH ITS TICKER AND TWITTER HANDLE
				$sp = array('ticker' => $ticker, 'twitter' => $twitter, 'stake' => $stake);
				
				
				array_push($tickers, $sp);

	

			}
		}
	   

}

//
// DISPLAY
//
// NUMBER OF POOLS TO SHOWCASE
$small_pools = 10;



// TOTAL NUMBER OF SMALL POOLS WITH TWITTER IN THEIR METADATA
$total_pools = count($tickers);

$the_card = [];

for ($i=0; $i < $small_pools; $i++) { 
	// fixed width
	$the_pool = rand(0,($total_pools -1));
	array_push($the_card, $the_pool);

}

// create the new image for the small pool post

$image = new \NMC\ImageWithText\Image($background);

// TITLE TEXT
$title = new \NMC\ImageWithText\Text($title, 10, 60);
$title->align = 'left';
$title->color = $title_color;
$title->font = $font_path . $font_title;
$title->lineHeight = 20;
$title->size = 46;
$title->startX = 40;
$title->startY = 40;
$image->addText($title);

// DEFAULT POSTIONS IN THE IMAGE FOR TEXT
$x = 40;
$y = 35;


// TWITTER HANDLES ARRAY
$tags = [];

foreach ($the_card as $card) {

	$tw_handle 	= strip_tags(str_replace("@", "", $tickers[$card]['twitter']));
	$tw_stake 	= number_format(($tickers[$card]['stake'] / 1000000));
	$tw_ticker  =  $tickers[$card]['ticker'];

	// UPDATE TWITER HANDLE ARRAY
	array_push($tags, $tw_handle);

	// IMAGE CONTENT
	$img_content = 'TICKER: ' . $tw_ticker . ' --- ' . $tw_stake . ' ADA --- @' . $tw_handle;
	
	$card = new \NMC\ImageWithText\Text($img_content, 5, 150);
	$card->align = 'left';
	$card->color = $body_color;
	$card->font = $font_path . $font_body;
	$card->lineHeight = 36;
	$card->size = 28;
	$card->startX += $x + 0; 
	$card->startY += $y + 40; 

	$x = $card->startX;
	$y = $card->startY;

	$image->addText($card);

	// LOG FILE
	$log_file = fopen($log_path, "a+") or die("Unable to open file!");
	$log_data = $tw_ticker . "\n";

	fwrite($log_file, $log_data);

	fclose($log_file);

}

// Render image
$image->render($new_image);



// get the last post from the log
$history 	= 200;
$last_post	= array(tailCustom($log_paty, $history));

if (in_array($tickers, $last_post) == $tickers) {
	// THIS CARD HAS BEEN SEEN LATELY
	goto a;
}

else  {

	// UPLOAD THE IMAGE TO TWITTER
	$content_media = $new_image;
	$media_upload  = $connection->upload('media/upload', ['media' => $content_media]);

	// THE PARAMETERS FOR THE TWITER POST

	$content = $myticker . 'has less than 10m and so do these pools #stakecoco #cardano #stakepool @' . $tags[1] . ' @' . $tags[2] .' @' . $tags[3] .' @' . $tags[4] .' @' . $tags[5] .' @' . $tags[6] .' @' . $tags[7] .' @' . $tags[8] .' @' . $tags[9];

	$parameters = [
	'status' => $content,
	'media_ids' => implode(',', [$media_upload->media_id_string])
	];

	// POST TO TWITTER
	$result = $connection->post('statuses/update', $parameters);

}


