
<div class="page-header">
   	<h2><?php echo ucfirst(get_action_name())?> Form</h2>
</div>

<div class="row-fluid">
<?php 
	$c = get_controller_name();
	$d = get_action_name();
	$action = '?c=' . $c;
	if ($d == 'edit') {
		$action .= '&d=update';	
	} else {
		$action .= '&d=save';	
	}
	
	HTML::form($action, $fields);
?>	
</div>
