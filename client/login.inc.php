<?php
if(!defined('OSTCLIENTINC')) die('Kwaheri');

$e=Format::htmlchars($_POST['lemail']?$_POST['lemail']:$_GET['e']);
$t=Format::htmlchars($_POST['lticket']?$_POST['lticket']:$_GET['t']);
?>

    <?if($errors['err']) {?>
        <p class="errormessage"><?=$ctlang->docatch($errors['err'])?></p>
    <?}elseif($warn) {?>
        <p class="warnmessage"><?=$ctlang->docatch($warn)?></p>
    <?}?>

<div class="box">
  <h2><?=$ctlang->dot('login_001')?></h2>
  <div class="boxContent">
    <p align="center">
	<?=$ctlang->dot('login_002')?>
    </p>
    <p class="errormessage"><?=$ctlang->docatch($loginmsg)?></p>
    <form action="login.php" method="post">
    <table cellspacing="1" cellpadding="5" border="0" align="center">
        <tr>
            <td><?=$ctlang->dot('login_003')?></td><td><input type="text" name="lemail" size="25" value="<?=$e?>" /></td>
	</tr>
	<tr>
            <td><?=$ctlang->dot('login_004')?></td><td><input type="<?=(CTHEME_TICKETPWD)?'password':'text'?>" name="lticket" size="25" value="<?=$t?>" /></td>
            <td><input class="button" type="submit" value="<?=$ctlang->dot('login_005')?>" /></td>
        </tr>
    </table>
    </form>

  </div>
</div>
