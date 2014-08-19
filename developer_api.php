<?php require_once("includes/inc_files.php"); 


$current_page = "home";

?>

<?php $page_title = "Account Overview"; require_once("includes/themes/".THEME_NAME."/header.php"); ?>

		
	<h1>Developer Api</h1>
	<hr />
	
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Request</th>
				<th>Required Input</th>
				<th>Will Return</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>user_details</td>
				<td>username</td>
				<td>username, first_name, last_name, gender, last_login, country, questions_posted, questions_answered</td>
			</tr>
			<tr>
				<td>user_questions</td>
				<td>username</td>
				<td>id, title, description, created, view_count, category_id, thumbs_up, thumbs_down, answer_count (will return all projects for this user)</td>
			</tr>
			<tr>
				<td>question_details</td>
				<td>id</td>
				<td>id, title, description, created, view_count, category_id, thumbs_up, thumbs_down, answer_count</td>
			</tr>
		</tbody>
	</table>
	
	<hr />	
	<p>The api allows you to request all public information on a user using the get variable. All requests will return in JSON.</p>
	<hr />
	Example of a request to our Api:<br />
	<pre><?php echo WWW; ?>api.php?request=user_details&amp;username=admin</pre>
	Here is the data our Api returned though the above request:<br />
	<pre>{
  "api_version":"1.0",
  "data":
   {
     "username":"admin",
     "first_name":"Admin",
     "last_name":"Account",
     "gender":"Male",
     "last_login":"2013-02-03 22:55:03",
     "country":"United Kingdom",
     "questions_posted":"0",
     "questions_answered":"0"
   }
}</pre>
	Here is an example of how to decode the json data sent from our Api:<br />
	<pre>$json = file_get_contents("<?php echo WWW; ?>api.php?request=user_details&amp;username=admin");
$data = json_decode($json);
preprint($data);
</pre>
	
	The above code will output the following:
	<pre>
stdClass Object
(
    [api_version] => 1.0
    [data] => stdClass Object
        (
            [username] => admin
            [first_name] => Admin
            [last_name] => Account
            [gender] => Male
            [last_login] => 2013-02-03 22:55:03
            [country] => United Kingdom
            [questions_posted] => 0
            [questions_answered] => 0
        )

)
</pre>

Here is an example of how to get the api version and the username from the above object:
<pre>
echo "API Version: ".$data->api_version." | Username: ".$data->data->username;
</pre>

Output of above example:
<pre>
API Version: 1.0 | Username: admin
</pre>

</div>

<?php require_once("includes/themes/".THEME_NAME."/footer.php"); ?>