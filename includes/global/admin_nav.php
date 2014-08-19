<div class="span12">
	<ul class="nav nav-pills" style="margin-bottom: -10px;">
		<li<?php if($active_page == "dashboard"){echo " class='active'";} ?>><a href="index.php">Dashboard</a></li>
		<li<?php if($active_page == "users"){echo " class='active'";} ?>><a href="users.php">Users</a></li>
		<li<?php if($active_page == "questions"){echo " class='active'";} ?>><a href="questions.php">Questions</a></li>
		<li<?php if($active_page == "reported"){echo " class='active'";} ?>><a href="reported.php">Reported</a></li>
		<li<?php if($active_page == "categories"){echo " class='active'";} ?>><a href="categories.php">Categories</a></li>
		<li<?php if($active_page == "settings"){echo " class='active'";} ?>><a href="settings.php">Settings</a></li>
	</ul>
	<hr style="margin-bottom: 9px;" />
</div>