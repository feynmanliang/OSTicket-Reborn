<?php
if(!defined('OSTCLIENTINC')) die('Kwaheri rafiki wangu?'); //Say bye to our friend..

$info=($_POST && $errors)?Format::htmlchars($_POST):array(); //on error...use the post data
?>
    <?if($errors['err']) {?>
        <p class="errormessage"><?=$ctlang->docatch($errors['err'])?></p>
    <?}elseif($msg) {?>
        <p class="infomessage"><?=$ctlang->docatch($msg)?></p>
    <?}elseif($warn) {?>
        <p class="warnmessage"><?=$ctlang->docatch($warn)?></p>
    <?}?>

<div class="box">
  <h3><?=$ctlang->dot('open_001')?></h3>
  <div class="boxContent">

<b><?=$ctlang->dot('open_002')?></b><br/><br/>
<form action="open.php" method="post" enctype="multipart/form-data">
<table align="left" cellpadding=2 cellspacing=1 width="90%">
    <tr>
        <th width="20%"><?=$ctlang->dot('open_003')?></th>
        <td>
            <?if ($thisclient && ($name=$thisclient->getName())) {
                ?>
                <input type="hidden" name="name" value="<?=$name?>" /><?=$name?>
            <?}else {?>
                <input type="text" name="name" size="25" value="<?=$info['name']?>" />
	        <?}?>
            &nbsp;<font class="error">*&nbsp;<?=$ctlang->docatch($errors['name'])?></font>
        </td>
    </tr>
    <tr>
        <th nowrap ><?=$ctlang->dot('open_004')?></th>
        <td>
            <?if ($thisclient && ($email=$thisclient->getEmail())) {
                ?>
                <input type="hidden" name="email" size="25" value="<?=$email?>" /><?=$email?>
            <?}else {?>             
                <input type="text" name="email" size="25" value="<?=$info['email']?>" />
            <?}?>
            &nbsp;<font class="error">*&nbsp;<?=$ctlang->docatch($errors['email'])?></font>
        </td>
    </tr>
<!--    <tr>
        <th nowrap >Domain Name:</th>
        <td>
             <input type="text" name="domain" size="25" value="" />
        </td>
    </tr> -->
    <tr>
        <th><?=$ctlang->dot('open_005')?></th>
        <td><input type="text" name="phone" size="25" value="<?=$info['phone']?>" />&nbsp;<font class="error">&nbsp;<?=$ctlang->docatch($errors['phone'])?></font></td>
    </tr>
    <tr style="height:2px;"><td align="left" colspan=2 >&nbsp;</td</tr>
    <tr>
        <th><?=$ctlang->dot('open_006')?></th>
        <td>
            <select name="topicId">
                <option value="" selected ><?=$ctlang->dot('open_007')?></option>
                <?
                 $services= db_query('SELECT topic_id,topic FROM '.TOPIC_TABLE.' WHERE isactive=1 ORDER BY topic');
                 while (list($topicId,$topic) = db_fetch_row($services)){
                    $selected = ($info['topicId']==$topicId)?'selected':''; ?>
                    <option value="<?=$topicId?>"<?=$selected?>><?=$topic?></option>
                <?
                 }?>
                <option value="0" ><?=$ctlang->dot('open_008')?></option>
            </select>
            &nbsp;<font class="error">*&nbsp;<?=$ctlang->docatch($errors['topicId'])?></font>
        </td>
    </tr>
    <tr>
        <th><?=$ctlang->dot('open_009')?></th>
        <td>
            <input type="text" name="subject" size="35" value="<?=$info['subject']?>" />
            &nbsp;<font class="error">*&nbsp;<?=$ctlang->docatch($errors['subject'])?></font>
        </td>
    </tr>
    <tr>
        <th valign="top"><?=$ctlang->dot('open_010')?></th>
        <td>
            <? if($errors['message']) {?> <font class="error"><b>&nbsp;<?=$ctlang->docatch($errors['message'])?></b></font><br/><?}?>
            <textarea name="message" cols="35" rows="8" style="width:85%"><?=$info['message']?></textarea></td>
    </tr>
    <?
    if($cfg->allowPriorityChange() ) {
      $sql='SELECT priority_id,priority_desc FROM '.TICKET_PRIORITY_TABLE.' WHERE ispublic=1 ORDER BY priority_urgency DESC';
      if(($priorities=db_query($sql)) && db_num_rows($priorities)){ ?>
      <tr>
        <th><?=$ctlang->dot('open_011')?></th>
        <td>
            <select name="pri">
              <?
                $info['pri']=$info['pri']?$info['pri']:$cfg->getDefaultPriorityId(); //use system's default priority.
                while($row=db_fetch_array($priorities)){ ?>
                    <option value="<?=$row['priority_id']?>" <?=$info['pri']==$row['priority_id']?'selected':''?> ><?=$row['priority_desc']?></option>
              <?}?>
            </select>
        </td>
       </tr>
    <? }
    }?>

    <?if(($cfg->allowOnlineAttachments() && !$cfg->allowAttachmentsOnlogin())  
                || ($cfg->allowAttachmentsOnlogin() && ($thisclient && $thisclient->isValid()))){
        
        ?>
    <tr>
        <th><?=$ctlang->dot('open_012')?></th>
        <td>
            <input type="file" name="attachment" /><font class="error">&nbsp;<?=$ctlang->docatch($errors['attachment'])?></font>
        </td>
    </tr>
    <?}?>
<?
if(CTHEME_CAPTCHA) {
?>
    <tr>
        <th><?=$ctlang->dot('open_013')?></th>
        <td>
            <img src="<?=CTHEME_URL?>secure-img.php?<?=md5(rand(1000,9999))?>" /><br />
            <input type="text" name="captcha_img" size="35" value="" />&nbsp;<font class="error">*&nbsp;<?=$errors['ctcaptcha']?></font>
        </td>
    </tr>
<?
}
?>

<?
//osTicket Captcha
if($cfg && $cfg->enableCaptcha() && (!$thisclient || !$thisclient->isValid())) {
        if($_POST && $errors && !$errors['captcha'])
            $errors['captcha']='Please re-enter the text again';
        ?>
    <tr>
        <th valign="top">Captcha Text:</th>
        <td><img src="captcha.php" border="0" align="left">
        <span>&nbsp;&nbsp;<input type="text" name="captcha" size="7" value="">&nbsp;<i>Enter the text shown on the image.</i></span><br/>
                <font class="error">&nbsp;<?=$errors['captcha']?></font>
        </td>
    </tr>
<?}?>



    <tr style="height:2px;"><td align="left" colspan=2 >&nbsp;</td</tr>
    <tr>
        <td></td>
        <td>
            <input class="button" type="submit" name="submit_x" value="<?=$ctlang->dot('open_014')?>" />
            <input class="button" type="reset" value="<?=$ctlang->dot('open_015')?>" />
            <input class="button" type="button" name="cancel" value="<?=$ctlang->dot('open_016')?>" onClick='window.location.href="index.php"' />
        </td>
    </tr>
</table>
</form>

  </div>
</div>
