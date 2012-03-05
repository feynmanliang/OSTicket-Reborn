<?php
if(!defined('OSTCLIENTINC') || !is_object($thisclient) || !$thisclient->isValid()) die('Kwaheri');

//Get ready for some deep shit.
$qstr='&'; //Query string collector
$status=null;
$sstring=null;
if($_REQUEST['status']) { //Query string status has nothing to do with the real status used below.
    $qstr.='status='.urlencode($_REQUEST['status']);
    //Status we are actually going to use on the query...making sure it is clean!
    switch(strtolower($_REQUEST['status'])) {
     case 'open':
     case 'closed':
        $status=$_REQUEST['status'];
        break;
     default:
        $status=''; //ignore
    }
}

//Restrict based on email of the user...STRICT!
$qwhere =' WHERE email='.db_input($thisclient->getEmail());

//STATUS
if($status){
    $qwhere.=' AND status='.db_input($status);    
}
//Search subject
$sstring = $_REQUEST['sstring'];
if ($sstring) $qwhere .= " AND subject like '%".db_input($sstring,false)."%'";

//Admit this crap sucks...but who cares??
$sortOptions=array('date'=>'ticket.created','ID'=>'ticketID','pri'=>'priority_id','dept'=>'dept_name');
$orderWays=array('DESC'=>'DESC','ASC'=>'ASC');

//Sorting options...
if($_REQUEST['sort']) {
        $order_by =$sortOptions[$_REQUEST['sort']];
}
if($_REQUEST['order']) {
    $order=$orderWays[$_REQUEST['order']];
}
if($_GET['limit']){
    $qstr.='&limit='.urlencode($_GET['limit']);
}

$order_by =$order_by?$order_by:'ticket.created';
$order=$order?$order:'DESC';
$pagelimit=$_GET['limit']?$_GET['limit']:PAGE_LIMIT;
$page=($_GET['p'] && is_numeric($_GET['p']))?$_GET['p']:1;

$qselect = 'SELECT ticket.ticket_id,ticket.ticketID,ticket.dept_id,ispublic,subject,name,email,dept_name,status,source,priority_id ,ticket.created ';
$qfrom=' FROM '.TICKET_TABLE.' ticket LEFT JOIN '.DEPT_TABLE.' dept ON ticket.dept_id=dept.dept_id ';
//Pagenation stuff....wish MYSQL could auto pagenate (something better than limit)
$total=db_count('SELECT count(*) '.$qfrom.' '.$qwhere);
$pageNav=new Pagenate($total,$page,$pagelimit);
$pageNav->setURL('view.php',$qstr.'&sort='.urlencode($_REQUEST['sort']).'&order='.urlencode($_REQUEST['order']));

//Ok..lets roll...create the actual query
$qselect.=' ,count(attach_id) as attachments ';
$qfrom.=' LEFT JOIN '.TICKET_ATTACHMENT_TABLE.' attach ON  ticket.ticket_id=attach.ticket_id ';
$qgroup=' GROUP BY ticket.ticket_id';
$query="$qselect $qfrom $qwhere $qgroup ORDER BY $order_by $order LIMIT ".$pageNav->getStart().",".$pageNav->getLimit();
//echo $query;
$tickets_res = db_query($query);
$showing=db_num_rows($tickets_res)?$pageNav->showing():"";
$results_type=($status)?ucfirst($ctlang->docatch($status)).$ctlang->dot('tlist_002'):$ctlang->dot('tlist_003');
$negorder=$order=='DESC'?'ASC':'DESC'; //Negate the sorting..
?>
    <?if($errors['err']) {?>
        <p class="errormessage"><?=$ctlang->docatch($errors['err'])?></p>
    <?}elseif($msg) {?>
        <p class="infomessage"><?=$ctlang->docatch($msg)?></p>
    <?}elseif($warn) {?>
        <p class="warnmessage"><?=$ctlang->docatch($warn)?></p>
    <?}?>

<div class="box">
  <h2><?=$ctlang->dot('tlist_001')?></h2>
  <div class="boxContent">

 <table width="100%">
    <tr>
        <td width="60%"><b><?=$showing?>&nbsp;&nbsp;<?=$results_type?></b></td>
        <td nowrap align="right">
            <a href="view.php?status=open"><?=$ctlang->dot('tlist_004')?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
            <a href="view.php?status=closed"><?=$ctlang->dot('tlist_005')?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
            <a href="view.php"><?=$ctlang->dot('tlist_006')?></a>
        </td>
    </tr>
 </table>

  </div>
</div>

<!-- tickets table -->
<div class="box">
  <div class="boxContent">

 <table width="100%">
    <tr><td>
     <table width="100%">
        <tr>
	    <th width="70" nowrap>
                <a href="view.php?<?=urlencode("sort=ID&order=$negorder$qstr")?>" title="<?=$ctlang->dot('tlist_015')?> <?=$negorder?>"><?=$ctlang->dot('tlist_007')?></a></th>
	    <th width="80">
                <a href="view.php?<?=urlencode("sort=date&order=$negorder$qstr")?>" title="<?=$ctlang->dot('tlist_016')?> <?=$negorder?>"><?=$ctlang->dot('tlist_008')?></a></th>
            <th width="60"><?=$ctlang->dot('tlist_009')?></th>
            <th width="240"><?=$ctlang->dot('tlist_010')?></th>
            <th width="150">
                <a href="view.php?<?=urlencode("sort=dept&order=$negorder$qstr")?>" title="<?=$ctlang->dot('tlist_017')?> <?=$negorder?>"><?=$ctlang->dot('tlist_011')?></a></th>
            <th width="150"><?=$ctlang->dot('tlist_012')?></th>
        </tr>
        <?
        $class = "rowA";
        $total=0;
        if($tickets_res && ($num=db_num_rows($tickets_res))):
            $defaultDept=Dept::getDefaultDeptName();
            while ($row = db_fetch_array($tickets_res)) {
                $dept=$row['ispublic']?$row['dept_name']:$defaultDept; //Don't show hidden/non-public depts.
                ?>
            <tr class="<?=$class?> " id="cttid<?=$row['ticketID']?>">
                <td align="center" title="<?=$row['email']?>" nowrap>
                    <a class="Icon <?=strtolower($row['source'])?>Ticket" title="<?=$row['email']?>" href="view.php?id=<?=$row['ticketID']?>">
                        <b><?=$row['ticketID']?></b></a></td>
                <td nowrap align="center">&nbsp;<?=Format::db_date($row['created'])?></td>
                <td align="center">&nbsp;<?=ucfirst($ctlang->docatch($row['status']))?></td>
                <td>&nbsp;<a href="view.php?id=<?=$row['ticketID']?>"><?=Format::truncate($row['subject'],32)?></a>
                    &nbsp;<?=$row['attachments']?"<span class='Icon file'>&nbsp;</span>":''?></td>
                <td nowrap>&nbsp;<?=Format::truncate($dept,30)?></td>
                <td>&nbsp;<?=Format::truncate($row['email'],32)?></td>
            </tr>
            <?
            $class = ($class =='rowB') ?'rowA':'rowB';
            } //end of while.
        else: //no tickets found!! ?> 
            <tr class="<?=$class?>"><td colspan=7><b><?=$ctlang->dot('tlist_013')?></b></td></tr>
        <?
        endif; ?>
     </table>
    </td></tr>
    <tr><td>
    <?
    if($num>0 && $pageNav->getNumPages()>1){ //if we actually had any tickets returned?>
     <tr><td style="text-align:left;padding-left:20px"><?=$ctlang->dot('tlist_014')?><?=$pageNav->getPageLinks()?>&nbsp;</td></tr>
    <?}?>
 </table>

  </div>
</div>

