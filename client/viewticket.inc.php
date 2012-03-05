<?php
if(!defined('OSTCLIENTINC') || !is_object($thisclient) || !is_object($ticket)) die('Kwaheri'); //bye..see ya
//Double check access one last time...
if(strcasecmp($thisclient->getEmail(),$ticket->getEmail())) die('Access Denied');

$info=($_POST && $errors)?Format::htmlchars($_POST):array(); //Re-use the post info on error...savekeyboards.org

$dept = $ticket->getDept();
//Making sure we don't leak out internal dept names
$dept=($dept && $dept->isPublic())?$dept:$cfg->getDefaultDept();
//We roll like that...
?>

<div class="box">
  <h2><a class="Icon refresh" href="view.php?id=<?=$ticket->getExtId()?>"><?=$ctlang->dot('tview_001')?> <?=$ticket->getExtId()?></a></h2>
  <div class="boxContent">

<table width="100%">
    <tr>
       <td width=50%>	
        <table align="center" width="100%">
	        <tr>
				<th width="100" ><?=$ctlang->dot('tview_002')?></th>
				<td><?=ucfirst($ctlang->docatch($ticket->getStatus()))?></td>
			</tr>
            <tr>
                <th><?=$ctlang->dot('tview_003')?></th>
                <td><?=Format::htmlchars($dept->getName())?></td>
            </tr>
			<tr>
                <th><?=$ctlang->dot('tview_004')?></th>
                <td><?=Format::db_datetime($ticket->getCreateDate())?></td>
            </tr>
		</table>
	   </td>
	   <td width=50% valign="top">
        <table align="center" width="100%">
            <tr>
                <th width="100"><?=$ctlang->dot('tview_005')?></th>
                <td><?=Format::htmlchars($ticket->getName())?></td>
            </tr>
            <tr>
                <th width="100"><?=$ctlang->dot('tview_006')?></th>
                <td><?=$ticket->getEmail()?></td>
            </tr>
            <tr>
                <th><?=$ctlang->dot('tview_007')?></th>
                <td><?=Format::phone($ticket->getPhone())?></td>
            </tr>
        </table>
       </td>
    </tr>
</table>

  </div>
</div>

    <?if($errors['err']) {?>
        <p class="errormessage"><?=$ctlang->docatch($errors['err'])?></p>
    <?}elseif($msg) {?>
        <p class="infomessage"><?=$ctlang->docatch($msg)?></p>
    <?}?>

<div class="box">
  <h2><?=$ctlang->dot('tview_008')?> <?=Format::htmlchars($ticket->getSubject())?></h2>
  <div class="boxContent">

    <span class="Icon thread"><?=$ctlang->dot('tview_009')?></span>
        <?
	    //get messages
        $sql='SELECT msg.*, count(attach_id) as attachments  FROM '.TICKET_MESSAGE_TABLE.' msg '.
            ' LEFT JOIN '.TICKET_ATTACHMENT_TABLE.' attach ON  msg.ticket_id=attach.ticket_id AND msg.msg_id=attach.ref_id AND ref_type=\'M\' '.
            ' WHERE  msg.ticket_id='.db_input($ticket->getId()).
            ' GROUP BY msg.msg_id ORDER BY created';
	    $msgres =db_query($sql);
	    while ($msg_row = db_fetch_array($msgres)):
		    ?>
		    <table align="center" class="message" cellspacing="0" cellpadding="1" width="100%" border=0>
		        <tr><th><?=Format::db_daydatetime($msg_row['created'])?></th></tr>
                <?if($msg_row['attachments']>0){ ?>
                <tr class="header"><td><?=$ticket->getAttachmentStr($msg_row['msg_id'],'M')?></td></tr> 
                <?}?>
                <tr class="info">
                    <td><?=Format::display($msg_row['message'])?></td></tr>
		    </table>
            <?
            //get answers for messages
            $sql='SELECT resp.*,count(attach_id) as attachments FROM '.TICKET_RESPONSE_TABLE.' resp '.
                ' LEFT JOIN '.TICKET_ATTACHMENT_TABLE.' attach ON  resp.ticket_id=attach.ticket_id AND resp.response_id=attach.ref_id AND ref_type=\'R\' '.
                ' WHERE msg_id='.db_input($msg_row['msg_id']).' AND resp.ticket_id='.db_input($ticket->getId()).
                ' GROUP BY resp.response_id ORDER BY created';
            //echo $sql;
		    $resp =db_query($sql);
		    while ($resp_row = db_fetch_array($resp)) {
                $respID=$resp_row['response_id'];
                ?>
    		    <table align="center" class="response" cellspacing="0" cellpadding="1" width="100%" border=0>
    		        <tr>
    			        <th><?=Format::db_daydatetime($resp_row['created'])?>&nbsp;-&nbsp;<?=Format::htmlchars($resp_row['staff_name'])?></th></tr>
                    <?if($resp_row['attachments']>0){ ?>
                    <tr class="header">
                        <td><?=$ticket->getAttachmentStr($respID,'R')?></td></tr>
                                    
                    <?}?>
			        <tr class="info">
				        <td> <?=Format::display($resp_row['response'])?></td></tr>
		        </table>
		    <?
		    } //endwhile...response loop.
            $msgid =$msg_row['msg_id'];
        endwhile; //message loop.
     ?>
    </div>
</div>

        <?if($_POST && $errors['err']) {?>
            <p class="errormessage"><?=$ctlang->docatch($errors['err'])?></p>
        <?}elseif($msg) {?>
            <p class="infomessage"><?=$ctlang->docatch($msg)?></p>
        <?}?>

<div class="box">
        <?if($ticket->isClosed()) {?>
        <h3><?=$ctlang->dot('tview_010')?></h3>
        <?}?>
  <div class="boxContent">
        <form action="view.php?id=<?=$id?>#reply" name="reply" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?=$ticket->getExtId()?>" />
            <input type="hidden" name="respid" value="<?=$respID?>" />
            <input type="hidden" name="a" value="postmessage" />
            <div align="left">
                <?=$ctlang->dot('tview_011')?> <font class="error">*&nbsp;<?=$ctlang->docatch($errors['message'])?></font><br/>
                <textarea name="message" id="message" cols="60" rows="7"><?=$info['message']?></textarea>
            </div>
            <? if($cfg->allowOnlineAttachments()) {?>
            <div align="left">
                <?=$ctlang->dot('tview_012')?><br /><input type="file" name="attachment" id="attachment" style="size: 30px;" value="<?=$info['attachment']?>" /> 
                    <font class="error">&nbsp;<?=$ctlang->docatch($errors['attachment'])?></font>
            </div>
            <?}?>
            <div align="left"  style="padding:10px 0 10px 0;">
                <input class="button" type='submit' value='<?=$ctlang->dot('tview_013')?>' />
                <input class="button" type='reset' value='<?=$ctlang->dot('tview_014')?>' />
                <input class="button" type='button' value='<?=$ctlang->dot('tview_015')?>' onClick='window.location.href="view.php"' />
            </div>
        </form>
    </div>
</div>
<br />
