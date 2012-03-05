<?
// full url of your theme
define('CTHEME_URL','./client/');

// not sure why this is here :P
define('CTHEME_PATH',CLIENTINC_DIR);

// show ticked ID input as password instead of text?
// set it to 0 if you want osTicket default behaviour
define('CTHEME_TICKETPWD',0);

// default language
// %file.lang.php should exist in root foolder in order to work
// view the default language files in order to create a new one ;)
define('CTHEME_LANG','en');

// default language 'catching'
// for more info read the ctheme-lang.inc.php file
define('CTHEME_LANGC',1);

// include language class and create the object
include_once(CLIENTINC_DIR.'ctheme-lang.inc.php');
include_once(CLIENTINC_DIR.CTHEME_LANG.'.lang.php');
$ctlang = new ctlang;

// in your css folder you have 2 files, default.php and grey.php
// grey.php is the default value, however you might change/create
// a new one with your stuff and use it here
define('CTHEME_CSS','grey.php');

// set it to 1 if you want to enable captcha, read the README file first!
// USE this captcha or osTicket's, not both :)
define('CTHEME_CAPTCHA',0);
?>
