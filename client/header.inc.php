<?php
$title=($cfg && is_object($cfg))?$cfg->getTitle():'osTicket :: Support Ticket System';
include_once(CLIENTINC_DIR.'config.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?=Format::htmlchars($title)?></title>
<link href="<?=CTHEME_URL?>css/<?=CTHEME_CSS?>?ctheme_url=<?=CTHEME_URL?>" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="outer">
	<div id="header">
		<h1><a href="index.php"><?=$ctlang->dot('head_001')?></a></h1>

		<h2><?=$ctlang->dot('head_002')?></h2>
	</div>
	<div id="menu">
		<ul>
			<li class="first"><a href="index.php" accesskey="1"><?=$ctlang->dot('head_003')?></a></li>
			<li><a href="open.php" accesskey="2"><?=$ctlang->dot('head_004')?></a></li>
			<?
			if(is_object($thisclient) && $thisclient->isValid()) {?>
			<li><a href="view.php" accesskey="3"><?=$ctlang->dot('head_005')?></a></li>
			<li><a href="logout.php" accesskey="4"><?=$ctlang->dot('head_006')?></a></li>
			<?}else {?>
			<li><a href="view.php" accesskey="3"><?=$ctlang->dot('head_007')?></a></li>
			<?}?>
		</ul>
	</div>
	<div id="content">
		<div id="primaryContentContainer">
			<div id="primaryContent">
