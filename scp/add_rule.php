<?php
require('staff.inc.php');

$page='';
$answer=null; //clean start.

$nav->setTabActive('rules');
$nav->addSubMenu(array('desc'=>'Rules','href'=>'rules.php','iconclass'=>'premade'));
$nav->addSubMenu(array('desc'=>'New Rule','href'=>'add_rule.php','iconclass'=>'newPremade'));
require_once(STAFFINC_DIR.'header.inc.php');

?>
<table width="100%" border="0" cellspacing="1" cellpadding="0">
    <form name="form1" method="POST" action="add_rule.php">
    <tr>
    <td>
    <table width="100%" border="0" cellspacing="1" cellpadding="5">

    <tr>
    <!--<td align="center"><strong>ID</strong></td>-->
    <td align="center"><strong>Enabled</strong></td>
    <td align="center"><strong>Category</strong></td>
    <td align="center"><strong>Contains</strong></td>
    <td align="center"><strong>Assign To</strong></td>
    <td align="center"><strong>Department</strong></td>
    <td align="center"><strong>Staff</strong></td>
    </tr>
    <tr>
    <td width="50" align="center"><select name="isenabled"><option value="on">On</option><option value="off">Off</option></select></td>

    <td align="center">
    <select name="Category"><option value="subject">Subject</option><option value="email">Email</option></select>
    </td>

    <td align="center"><input name="Criteria" type="text" id="Criteria"></td>

    <td align="center"><select name="Action"><option value=""> </option><option value="deptId">Department</option><option value="staffId" >Staff</option></select></td>

    <td align="center">

    <select name="Department">
    <option value=0> </option>
    <?
    $depts= db_query('SELECT dept_id,dept_name FROM '.DEPT_TABLE.'');
while (list($deptId,$deptName) = db_fetch_row($depts)){?>
    <option value="<?=$deptId?>" ><?=$deptName?></option>
        <?}?>
</select>

</td>

<td align="center">

    <select id="Staff" name="Staff" >
    <option value="0" selected="selected"> </option>
    <?
    $sql=' SELECT staff_id,CONCAT_WS(", ",lastname,firstname) as name FROM '.STAFF_TABLE.
    ' WHERE isactive=1 AND onvacation=0 ';
$depts= db_query($sql.' ORDER BY lastname,firstname ');
while (list($staffId,$staffName) = db_fetch_row($depts)){?>
    <option value="<?=$staffId?>" ><?=$staffName?></option>
        <?}?>
</select>
</td>

<tr>
<td colspan="7" align="right"><input type="submit" name="Submit" class="button" value="Submit"></td>
    </tr>
    </table>
    </td>
    </tr>
    </form>
    </table>
    <?php
    $ISENABLED = $_POST['isenabled'];
$CATEGORY = $_POST['Category'];
$CRITERIA = $_POST['Criteria'];
$ACTION = $_POST['Action'];
$DEPARTMENT = $_POST['Department'];
$STAFF = $_POST['Staff'];

// Check if button name "Submit" is active, do this

if(isset($_POST['Submit'])){
    $sql="INSERT INTO ost_ticket_rules (isenabled, Category, Criteria, Action, Department, Staff, updated, created)
VALUES ('$_POST[isenabled]','$_POST[Category]','$_POST[Criteria]','$_POST[Action]','$_POST[Department]','$_POST[Staff]', NOW(), NOW())";
    if (!mysql_query($sql))
        {
            die('Error: ' . mysql_error());
        }
    echo "Rule added";
}

?>
<!--<SCRIPT language="JavaScript">
    <!--
    window.location="rules.php";
//-->
</SCRIPT><?

mysql_close();
require_once(STAFFINC_DIR.'footer.inc.php');
?>