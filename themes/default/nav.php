<?php
	$c = get_controller_name();
	$d = get_action_name();	
	$currentnav = $c . '_' . $d;
?>
<style>
	.nav-pills > #<?php echo $currentnav; ?> > a {
	  color: #ff8522;
	  background-color: #f5f5f5;
	  /*font-weight: bold;*/	
	}
</style>

<ul class="nav nav-pills nav-stacked">
  <li class="active"><a href="#"><span class="glyphicon glyphicon-user"></span> User Management</a></li>
  <li id="user_index"><a href="?c=user">User List</a></li>
  <li id="user_add"><a href="?c=user&d=add">Add New</a></li>
</ul>

<ul class="nav nav-pills nav-stacked">
  <li class="active"><a href="#"><span class="glyphicon glyphicon-th-large"></span> Role Management</a></li>
  <li id="role_index"><a href="?c=role">Role List</a></li>
  <li id="role_add"><a href="?c=role&d=add">Add New</a></li>
</ul>