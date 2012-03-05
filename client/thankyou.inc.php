<?php
if(!defined('OSTCLIENTINC') || !is_object($ticket)) die('Kwaheri rafiki wangu?'); //Say bye to our friend..

//Please customize the message below to fit your organization speak!
?>

    <?if($errors['err']) {?>
        <p class="errormessage"><?=$ctlang->docatch($errors['err'])?></p>
    <?}elseif($msg) {?>
        <p class="infomessage"><?=$ctlang->docatch($msg)?></p>
    <?}elseif($warn) {?>
        <p class="warnmessage"><?=$ctlang->docatch($warn)?></p>
    <?}?>

<div class="box">
  <h3><?=$ctlang->dot('tyou_001')?></h3>
  <div class="boxContent">

    <b><?=Format::htmlchars($ticket->getName())?></b>,<br /><br />
    <p><?=$ctlang->dot('tyou_002')?></p>
          
    <?if($cfg->autoRespONNewTicket()){ ?>
    <p><?=$ctlang->dot('tyou_003')?> <b><?=$ticket->getEmail()?></b>. <?=$ctlang->dot('tyou_004')?></p>
    <p><?=$ctlang->dot('tyou_005')?></p>
    <?}?>
    <p><?=$ctlang->dot('tyou_006')?></p>

  </div>
</div>
<?
unset($_POST); //clear to avoid re-posting on back button??
?>
