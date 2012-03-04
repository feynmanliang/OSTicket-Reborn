<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php

require('staff.inc.php');
 
$page='';
$answer=null; //clean start.
 
$nav->setTabActive('Reports');
$nav->addSubMenu(array('desc'=>'Reports','href'=>'reports.php','iconclass'=>''));
if($thisuser->isAdmin()){
$nav->addSubMenu(array('desc'=>'Report Settings','href'=>'reports_admin.php','iconclass'=>'')); 
}

require_once(STAFFINC_DIR.'header.inc.php');

if(isset($_POST['submit'])){

?><p align="center" id="infomessage">Preferences Updated</p><?

$threeD = $_POST['3d'];
$graphWidth = $_POST['graphWidth'];
$graphHeight = $_POST['graphHeight'];
$resolution = $_POST['resolution'];
$viewable = $_POST['viewable'];

$update="UPDATE ".REPORTS_TABLE." SET 3d=$threeD,graphWidth=$graphWidth,graphHeight=$graphHeight,resolution='$resolution',viewable='$viewable' WHERE 1";
$result = mysql_query($update) or die( "An error has occured: " .mysql_error (). ":" .mysql_errno ());  
//echo $update;

} // Close "if submitted" 

$query = "SELECT 3d,graphWidth,graphHeight,resolution,viewable from ".REPORTS_TABLE." LIMIT 1";
$result = mysql_query($query) or die(mysql_error());
while($row = mysql_fetch_array($result)){
?>

<form method="POST" name="reportForm" action="reports_admin.php">
 <table name="formTable" cellpadding="2" class="tform" style="width: 100%;">
<tr class="header"><td colspan="2">Report Settings</td></tr>
  <tr>
   <!-- Set 3D on or off -->
  <th>3D graphs</th><td><input type="radio" name="3d" value="1" <? if($row['3d']=='1'){ echo checked; }?> /> On<input type="radio" name="3d" value="0" <? if($row['3d']=='0'){ echo checked; }?>/> Off</td>
  </tr>
  <tr>
   <!-- Graph Dimensions -->
  <th>Graph Width</th><td><input type="text" name="graphWidth" value="<?=$row['graphWidth']?>"/></td> 
  </tr>
  <tr>
  <th>Graph Height</th><td><input type="text" name="graphHeight" value="<?=$row['graphHeight']?>"/></td>
  </tr>
  <tr>
   <!-- X to resolution -->
  <th>Time to Resolution</th><td><input type="radio" name="resolution" value="days" <? if($row['resolution']=='days'){ echo checked; }?>/> Days<input type="radio" name="resolution" value="hours" <? if($row['resolution']=='hours'){ echo checked; }?>/> Hours</td>
  </tr>
  <tr>
   <!-- Reports viewable by -->
  <th>Reports viewable</th><td><input type="radio" name="viewable" value="admins" <? if($row['viewable']=='admins'){ echo checked; }?>/> Admins<br>
                               <input type="radio" name="viewable" value="managers" <? if($row['viewable']=='managers'){ echo checked; }?>/> Managers<br>
                               <input type="radio" name="viewable" value="staff" <? if($row['viewable']=='staff'){ echo checked; }?>/> Staff</td>
  </tr>
 </table>
   <input class="button" name='submit' type="submit" value="Save changes" /><input class="button" type="reset" value="Reset Changes" />
</form>

<?

} // Close while row results
mysql_close();
require_once(STAFFINC_DIR.'footer.inc.php');
?>
