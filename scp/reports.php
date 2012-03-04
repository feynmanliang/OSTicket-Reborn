<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<link href="reports/style.css" rel="stylesheet" type="text/css">
<?php
$VERSION = 'Version 4.1';
// http://sudobash.net/ostickets-reports 

// Get date for export file
$time = $_SERVER['REQUEST_TIME'];
$file = "Report_" .$time. ".csv";
$file = "reports/$file";

// echo getcwd() . "\n";

require('staff.inc.php');

$page='';
$answer=null; //clean start.

$nav->setTabActive('Reports');
$nav->addSubMenu(array('desc'=>'Reports','href'=>'reports.php','iconclass'=>''));

if($thisuser->isAdmin()){
$nav->addSubMenu(array('desc'=>'Report Settings','href'=>'reports_admin.php','iconclass'=>''));
}
require_once(STAFFINC_DIR.'header.inc.php');

$vquery = "SELECT ostversion from ".CONFIG_TABLE;
$versionCheck = mysql_query($vquery) or die(mysql_error());
while($versionRow = mysql_fetch_array($versionCheck)){
$version=$versionRow['ostversion'];
}

//echo $version;

// If report has not been submitted yet then create a default report.
if(!isset($_POST['submit'])){
$_POST['type'] = 'tixPerDept';
$_POST['submit'] = 'submit';
$_POST['range'] = 'thisWeek';
}


?>
<div style='padding: 15px 0 0 15px; border: 1px solid #ECE8DA; height: 35px; background: #f3f9ff; color: slate; margin: 15px 15px 0 15px;'>

<span style='font-size: 18px; color: #666; font-weight: bold;'>Report Criteria</span>

</div>
<div style='border-left: 1px solid #ECE8DA; border-right: 1px solid #ECE8DA; border-bottom: 1px solid #ECE8DA; margin-left: 15px; margin-right: 15px; padding-bottom: 20px; padding-top: 20px;'>
<!--Load the AJAX API-->
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<?$range = $_POST['range'];?>
<form method="POST" name="reportForm" action="reports.php">
<table name="formTable" style="margin: auto auto;">
  <tr>
   <td>Select date range</td>
  <td>
   <input type="radio" name="dateRange" value="timePeriod" <?if($_POST['dateRange']=='timePeriod'){echo "selected";}?> checked /> 
   <select name="range" onclick="document.reportForm.dateRange[0].checked=true">
    <option value="today" <?if($_POST['range']=='today'){echo "selected";}?>>Today</option>
    <option value="yesterday" <?if($_POST['range']=='yesterday'){echo "selected";}?>>Yesterday</option>
    <option value="thisMonth" <?if($_POST['range']=='thisMonth'){echo "selected";}?>>This Month</option>
    <option value="lastMonth" <?if($_POST['range']=='lastMonth'){echo "selected";}?>>Last Month</option>
    <option value="lastThirty" <?if($_POST['range']=='lastThirty'){echo "selected";}?>>Last 30 days</option>
    <option value="thisWeek" <?if($_POST['range']=='thisWeek'){echo "selected";}?>>This Week (Sun-Sat)</option>
    <option value="lastWeek" <?if($_POST['range']=='lastWeek'){echo "selected";}?>>Last Week (Sun-Sat)</option>
    <option value="thisBusWeek" <?if($_POST['range']=='thisBusWeek'){echo "selected";}?>>This business week (Mon-Fri)</option>
    <option value="lastBusWeek" <?if($_POST['range']=='lastBusWeek'){echo "selected";}?>>Last business week (Mon-Fri)</option>
    <option value="thisYear" <?if($_POST['range']=='thisYear'){echo "selected";}?>>This year</option>
    <option value="lastYear" <?if($_POST['range']=='lastYear'){echo "selected";}?>>Last year</option>
    <option value="allTime" <?if($_POST['range']=='allTime'){echo "selected";}?>>All time</option>
   </select>
  </td>
  <td><input type="radio" name="dateRange" value="timeRange" <?if($_POST['dateRange']=='timeRange'){echo "checked";}?>/></td>
  <td>From <input type="text" name="fromDate" value="<?if($_POST['fromDate']!=''){echo $_POST['fromDate'];}else{echo date("Y-m-d");}?>" onclick="document.reportForm.dateRange[1].checked=true"/> 
      To <input type="text" name="toDate" value="<?if($_POST['toDate']!=''){echo $_POST['toDate'];}else{echo date("Y-m-d");}?>"     onclick="document.reportForm.dateRange[1].checked=true"/></td>
 </tr>
 <tr>
  <td>Report Type</td>
  <td align="right">
   <select name="type">
    <option value="tixPerDept" <?if($_POST['type']=='tixPerDept'){echo "selected";}?>>Tickets per Department</option>
    <option value="tixPerDay" <?if($_POST['type']=='tixPerDay'){echo "selected";}?>>Tickets per Day</option>
    <option value="tixPerMonth" <?if($_POST['type']=='tixPerMonth'){echo "selected";}?>>Tickets per Month</option>
    <option value="tixPerStaff" <?if($_POST['type']=='tixPerStaff'){echo "selected";}?>>Tickets per Staff</option>
    <? if($version == '1.6 ST'){?>
    <option value="tixPerTopic" <?if($_POST['type']=='tixPerTopic'){echo "selected";}?>>Tickets per Help Topic</option>
    <?}?>
    <option value="repliesPerStaff" <?if($_POST['type']=='repliesPerStaff'){echo "selected";}?>>Replies per Staff</option>
    <option value="tixPerClient" <?if($_POST['type']=='tixPerClient'){echo "selected";}?>>Tickets per Client</option>
   </select>
 </tr>
 <tr>
  <td align="right" colspan="4">
   <input type="submit" name="submit" class="button"/><input type="reset" name="reset" class='button'/>
  </td>
</table>
</form>
</div>




<? if(isset($_POST['submit'])){ 

// Get the report options 
$OptionsQuery = "SELECT 3d,graphWidth,graphHeight,resolution,viewable from ".REPORTS_TABLE." LIMIT 1";
$OptionsResult = mysql_query($OptionsQuery) or die(mysql_error());
while($graphOptions = mysql_fetch_array($OptionsResult)){

//// Prepare the select query depending on the report we want.

// Report type department
// Probably not as clean as it could be but.... it works.
// I'm not using the closed count but I'm leaving it here in case anyone wants to easily reference it.

if($_POST['type'] == 'tixPerClient'){
$qselect = "SELECT name,email,
            COUNT(DISTINCT(".TABLE_PREFIX."ticket.ticket_id)) AS number,
            COUNT(DISTINCT(CASE WHEN ".TABLE_PREFIX."ticket.status='open' THEN ".TABLE_PREFIX."ticket.ticket_id END)) as opened,
            COUNT(DISTINCT(CASE WHEN ".TABLE_PREFIX."ticket.status='closed' THEN ".TABLE_PREFIX."ticket.ticket_id END)) as closed
            FROM ".TABLE_PREFIX."ticket";
}
elseif($_POST['type'] == 'repliesPerStaff'){
$qselect = "SELECT
            ".TABLE_PREFIX."staff.lastname,".TABLE_PREFIX."staff.firstname,".TABLE_PREFIX."ticket_response.response_id,
	        ".TABLE_PREFIX."ticket_response.staff_id,".TABLE_PREFIX."ticket_response.created,
            COUNT(DISTINCT(".TABLE_PREFIX."ticket_response.response_id)) as responses FROM ".TABLE_PREFIX."ticket_response
            LEFT JOIN ".TABLE_PREFIX."ticket ON ".TABLE_PREFIX."ticket.ticket_id=".TABLE_PREFIX."ticket_response.ticket_id
            LEFT JOIN ".TABLE_PREFIX."staff ON ".TABLE_PREFIX."staff.staff_id=".TABLE_PREFIX."ticket_response.staff_id ";
}
elseif($_POST['type'] == 'tixPerTopic'){
$qselect = "SELECT                                         
            ".TABLE_PREFIX."ticket.helptopic,
            COUNT(DISTINCT(".TABLE_PREFIX."ticket.ticket_id)) AS number FROM ".TABLE_PREFIX."ticket";
}else{
$qselect = "SELECT
            ROUND(AVG(TIMESTAMPDIFF(HOUR, ".TABLE_PREFIX."ticket.created, ".TABLE_PREFIX."ticket.closed)),2) AS hoursAVG,
            ROUND(AVG(TIMESTAMPDIFF(DAY, ".TABLE_PREFIX."ticket.created, ".TABLE_PREFIX."ticket.closed)),2) AS daysAVG,
            ".TABLE_PREFIX."ticket.dept_id,".TABLE_PREFIX."ticket.staff_id,".TABLE_PREFIX."staff.staff_id,".TABLE_PREFIX."staff.firstname,".TABLE_PREFIX."staff.lastname,
            ".TABLE_PREFIX."ticket.created,".TABLE_PREFIX."ticket.updated,".TABLE_PREFIX."ticket.closed,".TABLE_PREFIX."department.dept_name,
            COUNT(DISTINCT(".TABLE_PREFIX."ticket.ticket_id)) AS number,
            COUNT(DISTINCT(CASE WHEN ".TABLE_PREFIX."ticket.status='open' THEN ".TABLE_PREFIX."ticket.ticket_id END)) as opened,
            COUNT(DISTINCT(CASE WHEN ".TABLE_PREFIX."ticket.status='closed' THEN ".TABLE_PREFIX."ticket.ticket_id END)) as closed   
            FROM ".TABLE_PREFIX."ticket
            LEFT JOIN ".TABLE_PREFIX."ticket_response ON ".TABLE_PREFIX."ticket_response.ticket_id=".TABLE_PREFIX."ticket.ticket_id
            LEFT JOIN ".TABLE_PREFIX."staff ON ".TABLE_PREFIX."ticket.staff_id=".TABLE_PREFIX."staff.staff_id
            LEFT JOIN ".TABLE_PREFIX."department ON ".TABLE_PREFIX."ticket.dept_id=".TABLE_PREFIX."department.dept_id";
}

// Create CSV file column headers

if($_POST['type'] == 'repliesPerStaff'){
 $fh = fopen($file, 'w') or die("Can't open $file");
 $columnHeaders = "Last Name,First Name,Replies";
 fwrite($fh, $columnHeaders);
}
elseif($_POST['type'] == 'tixPerDept'){
 $fh = fopen($file, 'w') or die("Can't open $file");
 $columnHeaders = "Department,Assigned,Tickets Open,Tickets Closed,Time to Resolution";
 fwrite($fh, $columnHeaders);
}
elseif($_POST['type'] == 'tixPerDay'){
 $fh = fopen($file, 'w') or die("Can't open $file");
 $columnHeaders = "Day,Tickets Created,Tickets Open,Tickets Closed";
 fwrite($fh, $columnHeaders);
}
elseif($_POST['type'] == 'tixPerMonth'){
 $fh = fopen($file, 'w') or die("Can't open $file");
 $columnHeaders = "Month,Tickets Created,Tickets Open,Tickets Closed";
 fwrite($fh, $columnHeaders);
}
elseif($_POST['type'] == 'tixPerStaff'){
 $fh = fopen($file, 'w') or die("Can't open $file");
 $columnHeaders = "Staff,Assigned,Tickets,Tickets Open,Tickets Closed,Time to Resolution";
 fwrite($fh, $columnHeaders);
}
elseif($_POST['type'] == 'tixPerTopic'){
 $fh = fopen($file, 'w') or die("Can't open $file");
 $columnHeaders = "Help Topic,Tickets Created";
 fwrite($fh, $columnHeaders);
}
elseif($_POST['type'] == 'tixPerClient'){
 $fh = fopen($file, 'w') or die("Can't open $file");
 $columnHeaders = "Name/Email,Tickets Created,Tickets Open,Tickets Closed";
 fwrite($fh, $columnHeaders);
}

// Now for the time ranges

if($_POST['dateRange'] == 'timePeriod'){

  // Today
  if($_POST['range'] == 'today'){
  $qwhere = "WHERE ".TABLE_PREFIX."ticket.created>=CURDATE() ";
  $report_range = "Today";
  }

  // Yesterday
  if($_POST['range'] == 'yesterday'){     
  $qwhere = "WHERE ".TABLE_PREFIX."ticket.created>=DATE_ADD(CURDATE(), INTERVAL -1 DAY) AND ".TABLE_PREFIX."ticket.created<CURDATE()";
  $report_range = "Yesterday";
  }

  // This month
  if($_POST['range'] == 'thisMonth'){
  $qwhere = "WHERE YEAR(".TABLE_PREFIX."ticket.created) = YEAR(CURDATE()) AND MONTH(".TABLE_PREFIX."ticket.created) >= MONTH(CURDATE())";
  $report_range = "This Month";
  }

  // Last month
  if($_POST['range'] == 'lastMonth'){
  $qwhere = "WHERE YEAR(".TABLE_PREFIX."ticket.created) = YEAR(CURDATE()) AND MONTH(".TABLE_PREFIX."ticket.created) >= MONTH(DATE_ADD(CURDATE(),INTERVAL -1 MONTH)) AND MONTH(".TABLE_PREFIX."ticket.created) < MONTH(CURDATE())";
  $report_range = "Last Month";
  }

  // Last 30 days
  if($_POST['range'] == 'lastThirty'){
  $qwhere = "WHERE ".TABLE_PREFIX."ticket.created>=DATE_ADD(CURDATE(), INTERVAL -30 DAY) ";
  $report_range = "Last 30 Days";
  }

  // This week (Sun-Sat)
  if($_POST['range'] == 'thisWeek'){
  $qwhere = "WHERE ".TABLE_PREFIX."ticket.created>=DATE_ADD(CURDATE(), interval(1 - DAYOFWEEK(CURDATE()) ) DAY) AND ".TABLE_PREFIX."ticket.created<=DATE_ADD(CURDATE(), interval(7 - DAYOFWEEK(CURDATE()) ) DAY)";
  $report_range = "This Week (Sun-Sat)";
  }

  // Last week (Sun-Sat)
  if($_POST['range'] == 'lastWeek'){
  $qwhere = "WHERE ".TABLE_PREFIX."ticket.created>=DATE_ADD(DATE_ADD(CURDATE(), interval(1 - DAYOFWEEK(CURDATE()) ) DAY), INTERVAL - 1 WEEK) AND ".TABLE_PREFIX."ticket.created<=DATE_ADD(DATE_ADD(CURDATE(), interval(7 - DAYOFWEEK(CURDATE()) ) DAY), interval  - 1 week)";               
  $report_range = "Last Week (Sun-Sat)";
  }

  // This Business week (Mon-Fri)
  if($_POST['range'] == 'thisBusWeek'){
  $qwhere = "WHERE ".TABLE_PREFIX."ticket.created>=DATE_ADD(CURDATE(), interval(2 - DAYOFWEEK(CURDATE()) ) DAY) AND ".TABLE_PREFIX."ticket.created<=DATE_ADD(CURDATE(), interval(6 - DAYOFWEEK(CURDATE()) ) DAY)";
  $report_range = "This Week (Mon-Fri)";
  }

  // Last Business week (Mon-Fri)
  if($_POST['range'] == 'lastBusWeek'){
  $qwhere = "WHERE ".TABLE_PREFIX."ticket.created>=DATE_ADD(DATE_ADD(CURDATE(), interval(2 - DAYOFWEEK(CURDATE()) ) DAY), INTERVAL - 1 WEEK) AND ".TABLE_PREFIX."ticket.created<=DATE_ADD(DATE_ADD(CURDATE(), interval(6 - DAYOFWEEK(CURDATE()) ) DAY), interval - 1 week)";
  $report_range = "Last Business Week (Mon-Fri)";
  }

  // This year
  if($_POST['range'] == 'thisYear'){
  $qwhere = "WHERE YEAR(".TABLE_PREFIX."ticket.created) = YEAR(CURDATE()) ";
  $report_range = "This Year";
  }

  // Last year
  if($_POST['range'] == 'lastYear'){
  $qwhere = "WHERE YEAR(".TABLE_PREFIX."ticket.created) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 YEAR)) "; 
  $report_range = "Last Year";
  }

  // All time
  if($_POST['range'] == 'allTime'){
  $qwhere = "";
  $report_range = "All Time";
  }
} // End timePeriod drop down options


// Specified time range
if($_POST['dateRange'] == 'timeRange'){
  $fromDate = $_POST['fromDate'];
  $toDate = $_POST['toDate'];
  $qwhere = "WHERE ".TABLE_PREFIX."ticket.created>=\"$fromDate 00:00:00\" AND ".TABLE_PREFIX."ticket.created<=\"$toDate 23:59:59\" ";
$report_range = $fromDate. " to " .$toDate;
}

// Setup groupings

// By department
if($_POST['type'] == 'tixPerDept'){
$qgroup = "GROUP BY ".TABLE_PREFIX."department.dept_id ORDER BY number DESC";
$report_type = "Tickets Per Department";
}
elseif($_POST['type'] == 'tixPerStaff'){
$qgroup = "GROUP BY ".TABLE_PREFIX."staff.staff_id ORDER BY ".TABLE_PREFIX."staff.lastname ";
$report_type = "Tickets Per Staff";
}
elseif($_POST['type'] == 'repliesPerStaff'){
$qgroup = "GROUP BY ".TABLE_PREFIX."staff.staff_id ORDER BY ".TABLE_PREFIX."staff.lastname ";
$report_type = "Replies Per Staff";
}
elseif($_POST['type'] == 'tixPerDay'){
$qgroup = "GROUP BY DATE_FORMAT(".TABLE_PREFIX."ticket.created, '%d %M %Y') ORDER BY ".TABLE_PREFIX."ticket.created  ";
$report_type = "Tickets Per Day";
}
elseif($_POST['type'] == 'tixPerMonth'){
$qgroup = "GROUP BY DATE_FORMAT(".TABLE_PREFIX."ticket.created, '%M %Y') ORDER BY ".TABLE_PREFIX."ticket.created";
$report_type = "Tickets Per Month";
}
elseif($_POST['type'] == 'tixPerTopic'){
$qgroup = "GROUP BY ".TABLE_PREFIX."ticket.helptopic ORDER BY ".TABLE_PREFIX."ticket.helptopic";
$report_type = "Tickets Per Help Topic";
}
elseif($_POST['type'] == 'tixPerClient'){
$qgroup = "GROUP BY email ORDER BY email";
$report_type = "Tickets Per Client";
}

// Form the entire query
$query="$qselect $qwhere $qgroup";
// echo $query;

// Run the query
$result = mysql_query($query) or die(mysql_error());
$graphResult = mysql_query($query) or die(mysql_error());
// Depending on how many rows we are using lets change our graphic
// Count the rows
$num_rows = mysql_num_rows($graphResult);

if($num_rows>0){ 
?>

<div style='padding: 15px 0 0 15px; border: 1px solid #ECE8DA; height: 35px; background: #f3f9ff; color: slate; margin: 15px 15px 0 15px;'>

<span style='font-size: 18px; color: #666; font-weight: bold;'><?php echo $report_type.": ".$report_range; ?></span>

</div>
<div style='border-left: 1px solid #ECE8DA; border-right: 1px solid #ECE8DA; border-bottom: 1px solid #ECE8DA; margin-left: 15px; margin-right: 15px; margin-bottom: 20px; padding-bottom: 20px; padding-top: 20px;'>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
    
      // Load the Visualization API and the piechart package.
      google.load('visualization', '1', {'packages':['corechart']});
      
      // Set a callback to run when the Google Visualization API is loaded.
      google.setOnLoadCallback(drawChart);
      
      // Callback that creates and populates a data table, 
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawChart() {

      // Create our data table.
      var data = new google.visualization.DataTable();
      data.addColumn('string', 'Department');
      data.addColumn('number', 'Tickets');
      data.addRows([

   // Get the total of each ticket category - start with a 0
<?
   $Total = 0;
   $resolutionTotal = 0;
   $responseTotal = 0;
   $closedTotal = 0;
   $openedTotal = 0;

   while($graphRow = mysql_fetch_array($graphResult)){

   // Now add each new row to the total
   $Total += $graphRow['number'];
   $closedTotal += $graphRow['closed'];
   $openedTotal += $graphRow['opened'];

   if($graphOptions['resolution']=='hours'){
   $resolutionTotal += $graphRow['hoursAVG'];
   }
   elseif($graphOptions['resolution']=='days'){
   $resolutionTotal += $graphRow['daysAVG'];
   }

   $responseTotal += $graphRow['responses'];
   $resolutionAVG = round($resolutionTotal/$num_rows,2);


        if($_POST['type'] == 'tixPerDept'){?>
          ['<?=$graphRow['dept_name']?>', <?=$graphRow['number']?>],
          <?}?>

        <?if($_POST['type'] == 'tixPerTopic'){?>
          <? if($graphRow['helptopic']==NULL){ $graphRow['helptopic']='None'; } ?>
           ['<?=$graphRow['helptopic']?>', <?=$graphRow['number']?>],
          <?}?>

        <?if($_POST['type'] == 'tixPerStaff'){
          if($graphRow['staff_id'] == NULL){
          $graphRow['lastname'] = Unassigned;
          $graphRow['firstname'] = Tickets;
          }?>
          ['<?=$graphRow['lastname']?>, <?=$graphRow['firstname']?>', <?=$graphRow['number']?>],
          <?}?>

      <?if($_POST['type'] == 'tixPerDay'){?>
          ['<?=date("F j Y", strtotime($graphRow['created']));?>', <?=$graphRow['number']?>],   
          <?}?>
 
        <?if($_POST['type'] == 'repliesPerStaff'){
        if(($graphRow['lastname'] == NULL) && ($graphRow['firstname'] == NULL)){                     
        $graphRow['lastname'] = Deleted;     
        $graphRow['firstname'] = Staff; 
          }?>
          ['<?=$graphRow['lastname']?>, <?=$graphRow['firstname']?>', <?=$graphRow['responses']?>],
          <?}?>  

      <?if($_POST['type'] == 'tixPerMonth'){?>
          ['<?=date("F, Y", strtotime($graphRow['created']));?>', <?=$graphRow['number']?>],
          <?}?>

      <?if($_POST['type'] == 'tixPerClient'){
      $graphEmail = $graphRow['email']; 
      preg_match('/(([a-z0-9&*\+\-\=\?^_`{|\}~][a-z0-9!#$%&*+-=?^_`{|}~.]*[a-z0-9!#$%&*+-=?^_`{|}~])|[a-z0-9!#$%&*+-?^_`{|}~]|("[^"]+"))\@([-a-z0-9]+\.)+([a-z]{2,})/im', $graphEmail, $graphMatches);
      $graphEmail = $graphMatches[0];
      if($graphEmail == NULL){
       $graphEmail = $graphRow['name'];
      }?>
       ['<?php echo $graphEmail;?>', <?=$graphRow['number']?>],
    <?}?>  


      <?}?>
      <!-- This next entry has to be here or IE will throw a fit and not show graphs, last entry cannot end with a , -->
      ['', 0]
      ]);

      // Instantiate and draw our chart, passing in some options.
      var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
      <? if($graphOptions['3d']=='1'){ $threeD='true'; }else{ $threeD='false'; }?>
      chart.draw(data, {width: <?=$graphOptions['graphWidth'];?>, height: <?=$graphOptions['graphHeight'];?>, is3D: <?=$threeD;?>, sliceVisibilityThreshold: 1/72000});

    }
    </script>

    <!-- Div that will hold the pie chart -->
    <div id="chart_div"></div>
<div style='float: right; margin-top: -30px; margin-right: 5px;'>
<a href="<?=$file?>" /><img src='images/csv.png' width="50px" height="50px"/></a>
</div>
</div> <!-- end graph div -->
<table id="hor-minimalist-b" width="100%">
<?

// Print out results of query for department reports
 if($graphOptions['resolution']=='hours'){ $time = 'Hours'; }else{ $time = 'Days'; }

if($_POST['type'] == 'tixPerDept'){
 echo "<tr><th>Department</th><th>Assigned</th><th>Tickets Open<th>Tickets Closed</th><th>$time to Resolution (Avg)</th></tr>";
 }
 elseif($_POST['type'] == 'tixPerStaff'){
 echo "<tr><th>Staff</th><th>Assigned</th><th>Tickets Open<th>Tickets Closed</th><th>$time to Resolution (Avg)</th></tr>";
 }
 elseif($_POST['type'] == 'tixPerTopic'){
 echo "<tr><th>Help Topic</th><th>Tickets</th></tr>";
 }
 elseif($_POST['type'] == 'repliesPerStaff'){
 echo "<tr><th>Staff</th><th>Replies</th></tr>";
 }
 elseif($_POST['type'] == 'tixPerDay'){
 echo "<tr><th>Day</th><th>Tickets Created</th><th>Tickets Open<th>Tickets Closed</th></tr>";
 }
 elseif($_POST['type'] == 'tixPerMonth'){
 echo "<tr><th>Month</th><th>Tickets Created</th><th>Tickets Open<th>Tickets Closed</th></tr>";
 }
 elseif($_POST['type'] == 'tixPerClient'){
 echo "<tr><th>Name/Email</th><th>Tickets Created</th><th>Tickets Open<th>Tickets Closed</th></tr>";
}

while($row = mysql_fetch_array($result)){
if($graphOptions['resolution']=='hours'){ $time = $row['hoursAVG']; }elseif($graphOptions['resolution']=='days'){ $time = $row['daysAVG']; }
if($row['open']=='0'){ $row['open']=''; }
if($_POST['type'] == 'tixPerDept'){
echo "<tr style='font-weight: bold;'><td>" . $row['dept_name']. "</td><td>" . $row['number'] ." </td><td>" .$row['opened']. "</td><td>" .$row['closed']. "</td><td>" .$time. "</td></tr> ";

  // Now for the file
 $columnHeaders = "\n" .$row['dept_name']. "," .$row['number']. "," .$row['opened']. "," .$row['closed']. "," .$time;
 fwrite($fh, $columnHeaders);

  }
elseif($_POST['type'] == 'tixPerTopic'){
 if($row['helptopic'] == NULL){
 $row['helptopic'] = None;
}
echo "<tr style='font-weight: bold;'><td>" . $row['helptopic']. "</td><td>" . $row['number'] ." </td></tr> ";;
 
 // Now for the file
 $columnHeaders = "\n" .$row['helptopic']. "," .$row['number'];
 fwrite($fh, $columnHeaders);

 }
elseif($_POST['type'] == 'tixPerStaff'){
 if($row['staff_id'] == NULL){
 $row['lastname'] = Unassigned;
 $row['firstname'] = Tickets;
 }
 echo "<tr style='font-weight: bold;'><td>" . $row['lastname']. ", " .$row['firstname'] . "</td><td>" . $row['number'] ." </td><td>" .$row['opened']. "</td><td>" .$row['closed']. "</td><td>" .$time. "</td></tr> ";

 // Now for the file
 $columnHeaders = "\n" .$row['lastname']. "," .$row['firstname']. "," .$row['number']. "," .$row['opened']. "," .$row['closed']. "," .$time;
 fwrite($fh, $columnHeaders);

  }
elseif($_POST['type'] == 'repliesPerStaff'){
 if($row['lastname'] == NULL){
 $row['lastname'] = Deleted;
 $row['firstname'] = Employee;
 }
 echo "<tr style='font-weight: bold;'><td>" . $row['lastname']. ", " .$row['firstname'] . "</td><td>" . $row['responses'] ." </td></tr> ";

 // Now for the file
 $columnHeaders = "\n" .$row['lastname']. "," .$row['firstname']. "," .$row['responses']; 
 fwrite($fh, $columnHeaders);

  }

elseif($_POST['type'] == 'tixPerClient'){
 $email = $row['email'];
 preg_match('/(([a-z0-9&*\+\-\=\?^_`{|\}~][a-z0-9!#$%&*+-=?^_`{|}~.]*[a-z0-9!#$%&*+-=?^_`{|}~])|[a-z0-9!#$%&*+-?^_`{|}~]|("[^"]+"))\@([-a-z0-9]+\.)+([a-z]{2,})/im', $email, $matches);
 $email = $matches[0];
  if($email == NULL){
 $email = $row['name'];
 }
 echo "<tr style='font-weight: bold;'><td>" . $email. "</td><td>" . $row['number'] ." </td><td>" .$row['opened']. "</td><td>" .$row['closed']. "</td></tr>";

 // Now for the file
 $columnHeaders = "\n" .$email. "," .$row['number']. "," .$row['opened']. "," .$row['closed'];
 fwrite($fh, $columnHeaders);

  }

elseif($_POST['type'] == 'tixPerDay'){
 echo "<tr style='font-weight: bold;'><td>" . date("j F Y", strtotime($row['created'])). "</td><td>" . $row['number'] ." </td><td>" .$row['opened']. "</td><td>" .$row['closed']. "</td></tr> ";

 // Now for the file
 $columnHeaders = "\n" .date("j F Y", strtotime($row['created'])). "," .$row['number']. "," .$row['opened']. "," .$row['closed'];
 fwrite($fh, $columnHeaders);

  }
elseif($_POST['type'] == 'tixPerMonth'){
 echo "<tr style='font-weight: bold;'><td>" . date("F, Y", strtotime($row['created'])). "</td><td>" . $row['number'] ." </td><td>" .$row['opened']. "</td><td>" .$row['closed']. "</td></tr> ";

   // Now for the file
 $columnHeaders = "\n" .date("j F Y", strtotime($row['created'])). "," .$row['number']. "," .$row['opened']. "," .$row['closed'];
 fwrite($fh, $columnHeaders);

  }
 } 
}
if($num_rows>0){
?>
<?if($_POST['type'] == 'tixPerDept'){?>
 <tr style='font-weight: bold; background-color: #f5f5f5;'><td>Total</td><td><?=$Total;?></td><td><?=$openedTotal?></td><td><?=$closedTotal?></td><td><?=$resolutionAVG;?></td></tr>

 <? // Now for the file
 $columnHeaders = "\n" . "Total" . "," .$Total. "," .$openedTotal. "," .$closedTotal. "," .$resolutionAVG;
 fwrite($fh, $columnHeaders);
 ?>

 <? }
elseif($_POST['type'] == 'tixPerTopic'){?>
 <tr style='font-weight: bold; background-color: #F5F5F5;'><td>Total</td><td><?=$Total;?></td></tr>

 <? // Now for the file
 $columnHeaders = "\n" . "Total" . "," .$Total;
 fwrite($fh, $columnHeaders);
 ?>

 <? }
elseif($_POST['type'] == 'tixPerStaff'){?>
 <tr style='font-weight: bold; background-color: #F5F5F5;'><td>Total</td><td><?=$Total;?></td><td><?=$openedTotal?></td><td><?=$closedTotal?></td><td><?=$resolutionAVG;?></td></tr>     

 <? // Now for the file
 $columnHeaders = "\n" . "Total" . ",," .$Total. "," .$openedTotal. "," .$closedTotal. "," .$resolutionAVG;
 fwrite($fh, $columnHeaders);
 ?>

 <? }
elseif($_POST['type'] == 'repliesPerStaff'){?>
 <tr style='font-weight: bold; background-color: #F5F5F5;'><td>Total</td><td><?=$responseTotal;?></td></tr>

 <? // Now for the file    
 $columnHeaders = "\n" . "Total" . ",," .$responseTotal;
 fwrite($fh, $columnHeaders);
 ?>

 <? }
elseif($_POST['type'] == 'tixPerDay'){?>
 <tr style='font-weight: bold; background-color: #F5F5F5;'><td>Total</td><td><?=$Total;?></td><td><?=$openedTotal?></td><td><?=$closedTotal?></td></tr>     

 <? // Now for the file
 $columnHeaders = "\n" . "Total" . "," .$Total. "," .$openedTotal. "," .$closedTotal;
 fwrite($fh, $columnHeaders);
 ?>

  <? }
elseif($_POST['type'] == 'tixPerClient'){?>
 <tr style='font-weight: bold; background-color: #F5F5F5;'><td>Total</td><td><?=$Total;?></td><td><?=$openedTotal?></td><td><?=$closedTotal?></td></tr>

 <? // Now for the file
 $columnHeaders = "\n" . "Total" . "," .$Total. "," .$openedTotal. "," .$closedTotal;
 fwrite($fh, $columnHeaders);
 ?> 

 <? }
elseif($_POST['type'] == 'tixPerMonth'){?>
 <tr style='font-weight: bold; background-color: #F5F5F5;'><td>Total</td><td><?=$Total;?></td><td><?=$openedTotal?></td><td><?=$closedTotal?></td></tr>

 <? // Now for the file
 $columnHeaders = "\n" . "Total" . "," .$Total. "," .$openedTotal. "," .$closedTotal;
 fwrite($fh, $columnHeaders);
 ?>

<?}?>

</table>

<script type="text/javascript">

    function toggle_visibility(id) {
       var e = document.getElementById(id);
       if(e.style.display == 'block')
          e.style.display = 'none';
       else
          e.style.display = 'block';
    }

</script>
<a href="#" onclick="toggle_visibility('legend');" style='margin-left: 15px;'/><span style='color: #666; font-weight: bold;'>Show/Hide Legend</span></a><br /><br />
<div id="legend" style="display:none; margin-left: 15px;">
<b>Department</b><br /> Department tickets are assigned to.<br /><br />
<b>Staff</b><br /> Staff member the ticket is assigned to.<br /><br />
<b>Assigned</b><br /> Number of tickets assigned to this department or staff during the given time period.<br /><br />
<b>Tickets Open</b><br /> Number of tickets that are PRESENTLY open that were created during the given time period.<br /><br />
<b>Tickets Created</b><br /> Number of tickets that were created during given time period.<br /><br />
<b>Tickets Closed</b><br /> Number of tickets that were closed during the given time period.<br /><br />
<b>Days/Hours to Resolution</b><br /> Amount of time in hours or days from ticket creation to ticket being closed.
</div>

<?

fclose($fh); // Close up our csv file
}else{
       echo "<div style='padding: 15px 0 0 15px; border: 1px solid #FF3334; height: 35px; background: #FFCCAA; margin: 15px 15px 0 15px;'><span style='font-size: 18px; color: black; font-weight: bold;'>No data for the selected report</span></div>" ;
     }

} 
} // Close our while loop for getting report options
mysql_close();
require_once(STAFFINC_DIR.'footer.inc.php');





?>
