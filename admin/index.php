<?php 


require_once("../includes/inc_files.php"); 
require_once("../includes/classes/admin.class.php");

if(!$session->is_logged_in()) {redirect_to("../login.php");}

$admin = User::find_by_id($_SESSION['masdyn']['answers']['user_id']);

$current_page = "";
$active_page = "dashboard";

?>

<?php $page_title = "Administration Dashboard"; require_once("../includes/themes/".THEME_NAME."/admin_header.php"); ?>

<div class="row-fluid">
	<?php require_once("../includes/global/admin_nav.php"); ?>
</div>
<div class="row-fluid">
<div class="span12">
	<div class="title">
		<h1><?php echo $page_title; ?></h1>
	</div>
	
	<div id="message"><?php echo output_message($message); ?></div>
	
    <!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">

      // Load the Visualization API and the piechart package.
      google.load('visualization', '1.0', {'packages':['corechart']});

      // Set a callback to run when the Google Visualization API is loaded.
      google.setOnLoadCallback(drawChart);

      // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawChart() {

        // Create the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Gender');
        data.addColumn('number', 'Count');
        data.addRows([
          ['Male', <?php echo Admin::count_users('gender','Male'); ?>],
          ['Female', <?php echo Admin::count_users('gender','Female'); ?>]
        ]);

        // Set chart options
        var options = {'title':'User Gender',
                       'width':500,
                       'height':400};

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>

    <div id="chart_div" style="width: 400px;float: left;"></div>

    <script type="text/javascript">

      // Load the Visualization API and the piechart package.
      google.load('visualization', '1.0', {'packages':['corechart']});

      // Set a callback to run when the Google Visualization API is loaded.
      google.setOnLoadCallback(drawChart);

      // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawChart() {

        // Create the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Status');
        data.addColumn('number', 'Count');
        data.addRows([
          ['Active', <?php echo Admin::count_users('activated',1); ?>],
          ['Inactive', <?php echo Admin::count_users('activated',0); ?>],
			 ['Suspended', <?php echo Admin::count_users('suspended',1); ?>]
        ]);

        // Set chart options
        var options = {'title':'User Status Overview',
                       'width':500,
                       'height':400};

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.PieChart(document.getElementById('chart_div2'));
        chart.draw(data, options);
      }
    </script>

	 <div id="chart_div2" style="width: 400px;float: left;"></div>

	<div class="clear"><!-- --></div>

	<div class="row-fluid">
		<div class="span4">
			<h2>User Genders</h2><br />
			<table class="table table-striped">
			  <tbody>
			    <tr>
			      <td>Male</td>
					<td><?php echo Admin::count_users('gender','Male'); ?></td>
			    </tr>
				<tr>
			      <td>Female</td>
					<td><?php echo Admin::count_users('gender','Female'); ?></td>
			    </tr>
				<tr>
			      <td><strong>Total:</strong></td>
					<td><?php echo Admin::count_all_users(); ?></td>
			    </tr>
			  </tbody>
			</table>
		</div><!--/span-->
		<div class="span4">
			<h2>User Account Statistics</h2><br />
			<table class="table table-striped">
			  <tbody>
			    <tr>
			      <td>Active</td>
					<td><?php echo Admin::count_users('activated',1); ?></td>
			    </tr>
				<tr>
			      <td>Inactive</td>
					<td><?php echo Admin::count_users('activated',0); ?></td>
			    </tr>
				<tr>
			      <td>Suspended</td>
					<td><?php echo Admin::count_users('suspended',1); ?></td>
			    </tr>
				<tr>
			      <td><strong>Total:</strong></td>
					<td><?php echo Admin::count_all_users(); ?></td>
			    </tr>
			  </tbody>
			</table>
		</div><!--/span-->
		<div class="span4">
			<h2>Question Statistics</h2><br />
			<table class="table table-striped">
			  <tbody>
			    <tr>
			      <td>Answered</td>
					<td><?php echo $answered = Admin::count_questions(1); ?></td>
			    </tr>
				<tr>
			      <td>Unanswered</td>
					<td><?php echo $unanswered = Admin::count_questions(0); ?></td>
			    </tr>
				<tr>
			      <td><strong>Total:</strong></td>
					<td><?php echo $answered + $unanswered; ?></td>
			    </tr>
			  </tbody>
			</table>
		</div><!--/span-->
	</div><!--/row-->
	
	<div class="row-fluid">
		<?php $latest = Question::get_latest("-",10); if($latest){ ?>
		
		<div class="span12">
			<h2>Latest Questions</h2><br />
			<table class="table table-striped">
			  <tbody>
				<?php foreach($latest as $question){ ?>
			    <tr>
					<td><a href="<?php echo WWW."question.php?id=".$question->id; ?>"><?php echo $question->title ?> <span> - <?php echo ago_time($question->created); ?> - <span style="color: rgb(103, 177, 23);"><?php echo $question->thumbs_up; ?> Up</span> - <span style="color: rgb(207, 0, 0);"><?php echo $question->thumbs_down; ?> Down</span></a></td>
			    </tr>
				<?php } ?>
			  </tbody>
			</table>
		</div><!--/span-->
		
		<?php } ?>
		
	</div><!--/row-->
	

</div><!--/span-->

</div>

<?php require_once("../includes/themes/".THEME_NAME."/footer.php"); ?>