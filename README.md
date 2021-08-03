# Small Pool Promo
Twitter post to promote other small stake pools.

Requirements:

> composer require abraham/twitteroauth

Things you need to do:

1) Create a fonts folder and download the fonts you want to use. 

Edit this section of the code:

> // FONTS
> 
> $font_path 	= dirname(__FILE__) . '/fonts';
> 
> $font_body  = '/source_code/SourceCodePro-SemiBold.ttf';
> 
> $font_title = '/nerko/nerko-regular.ttf';
> 
> $title_color = 'D5302F';
> 
> $body_color  = '2C3336';
> 

2) Add your Twitter credentials to the config.php file.

3) Create a new background image: 1024 x 512, leave it mostly blank for the text.
> // IMAGE BACKGROUND
> 
> $background = dirname(__FILE__) . '/img/twitter_bg.png';

4) Add this script as a cron job to run daily.
