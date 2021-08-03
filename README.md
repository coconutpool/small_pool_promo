# Small Pool Promo
Twitter post to promote other small skate pools.

Requirements:

> composer require abraham/twitteroauth

Things you need to do:

1) Create a fonts folder and download the fonts you want to use. 

Edit this section of the code:

> // FONTS
> $font_path 	= dirname(__FILE__) . '/fonts';
> $font_body  = '/source_code/SourceCodePro-SemiBold.ttf';
> $font_title = '/nerko/nerko-regular.ttf';
> $title_color = 'D5302F';
> $body_color  = '2C3336';

2) Add your Twitter credentials to the config.php file.

3) Add this script as a cron job to run daily.
