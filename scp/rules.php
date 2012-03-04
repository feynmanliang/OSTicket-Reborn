<?php
require('staff.inc.php');

$page='';
$answer=null; //clean start.

$nav->setTabActive('rules');
$nav->addSubMenu(array('desc'=>'Rules','href'=>'rules.php','iconclass'=>'premade'));
$nav->addSubMenu(array('desc'=>'New Rule','href'=>'add_rule.php','iconclass'=>'newPremade'));
require_once(STAFFINC_DIR.'header.inc.php');

$query=sprintf("SELECT * FROM ost_ticket_rules;");
$result=mysql_query($query);

// Count table rows
$count=mysql_num_rows($result) or die(mysql_error());;
print "$count rules found";
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <form name="form1" method="POST" action="rules.php">
    <tr>
    <td>
    <table width="100%" border="0" cellspacing="0" cellpadding="5">

    <tr>
    <!--<td align="center"><strong>ID</strong></td>-->
    <td align="center"><strong>Delete</strong></td>
    <td align="center"><strong>Enabled</strong></td>
    <td align="center"><strong>Category</strong></td>
    <td align="center"><strong>Contains</strong></td>
    <td align="center"><strong>Assign To</strong></td>
    <td align="center"><strong>Department</strong></td>
    <td align="center"><strong>Staff</strong></td>
    <td align="center"><strong>Last Updated</strong></td>
    </tr>
    <?php
    $class = 'row1';
$counter = 0;
while($rows=mysql_fetch_array($result)){
    ?>
    <tr class="<?=$class?>">
        <!--<td align="center"><?=$rows['id']; ?></td>-->
                                                       <td align="center"><input type="checkbox" name="deleteRule[]" value="<?=$rows['id']; ?>"></td>
                                                       <td align="center"><select name="isenabled[]"><option value="on" <?if($rows['isenabled'] == "on"){print "SELECTED";}?> >On</option><option value="off" <?if($rows['isenabled'] == "off"){print "SELECTED";}?>>Off</option></select></td>
    <td align="center">
                                                                                                                                                                                                                                                                         <select name="Category[]"><option value="subject" <? if($rows['Category'] == "subject"){print "SELECTED";}?>>Subject</option><option value="email" <? if($rows['Category'] == "email"){print "SELECTED";}?>>Email</option></select>

    </td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         <td align="center"><input name="Criteria[]" type="text" id="Criteria" value="<? echo $rows['Criteria']; ?>"></td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         <td align="center">

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         <select name="Action[]"><option value=""> </option><option value="deptId" <? if($rows['Action'] == "deptId"){print "SELECTED";}?>>Department</option><option value="staffId" <? if($rows['Action'] == "staffId"){print "SELECTED";}?>>Staff</option></select>

    </td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   <td align="center">

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   <select name="Department[]">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   <option value=0> </option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   <?
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   $depts= db_query('SELECT dept_id,dept_name FROM '.DEPT_TABLE.'');
    while (list($deptId,$deptName) = db_fetch_row($depts)){?>
        <option value="<?=$deptId?>" <? if($deptId == $rows['Department']){print SELECTED;}?>><?=$deptName?></option>
        <?}?>
    </select>

          </td>

          <td align="center">

          <select id="Staff" name="Staff[]" >
          <option value="0" selected="selected"> </option>
          <?
          $sql=' SELECT staff_id,CONCAT_WS(", ",lastname,firstname) as name FROM '.STAFF_TABLE.
          ' WHERE isactive=1 AND onvacation=0 ';
    $depts= db_query($sql.' ORDER BY lastname,firstname ');
    while (list($staffId,$staffName) = db_fetch_row($depts)){?>
        <option value="<?=$staffId?>" <? if($staffId == $rows['Staff']){print SELECTED;}?>><?=$staffName?></option>
        <?}?>
    </select>
          </td>

          <td align="center"><? echo $rows['updated']; ?></td>
                                                               </tr>
                                                               <?php
                                                               $class = ($class =='row2') ?'row1':'row2';
    $counter = $counter +1;
}
?>
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
$DELETE = $_POST['deleteRule'];

// Check if button name "Submit" is active, do this

if(isset($_POST['Submit'])){
    for($i=0;$i<$count;$i++){
        $sql1="UPDATE ost_ticket_rules SET isenabled='$ISENABLED[$i]', Category='$CATEGORY[$i]', Criteria='$CRITERIA[$i]', Action='$ACTION[$i]', Department='$DEPARTMENT[$i]', Staff='$STAFF[$i]', updated=NOW() WHERE id='$id[$i]'";
        $result=mysql_query($sql1);
        if(isset($DELETE[$i])){
            $sql2="DELETE from ost_ticket_rules WHERE id='$DELETE[$i]'";
            $result2=mysql_query($sql2);
        }
    }

    ?>
    <SCRIPT language="JavaScript">
         <!--
         window.location="rules.php";
    //-->
    </SCRIPT><?
          }

mysql_close();
require_once(STAFFINC_DIR.'footer.inc.php');
?>