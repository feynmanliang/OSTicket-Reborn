<?
define('CTHEME_CAPTCHA_PATH','');
//include_once(CTHEME_CAPTCHA_PATH.'config.php');
include_once(CTHEME_CAPTCHA_PATH.'php-captcha.inc.php');
$aFonts = array(CTHEME_CAPTCHA_PATH.'fonts/VeraBd.ttf',CTHEME_CAPTCHA_PATH.'fonts/VeraIt.ttf',CTHEME_CAPTCHA_PATH.'fonts/Vera.ttf');
// create new image
$oPhpCaptcha = new PhpCaptcha($aFonts, 200, 50);
$oPhpCaptcha->UseColour(true);
$oPhpCaptcha->Create();
?>

