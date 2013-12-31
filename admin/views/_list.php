  
<div class="page-header">
   <h1><?php echo ucfirst(get_controller_name())?> List <small></small></h1>
</div>
<div class="h10"></div>

<div class="row-fluid">
<div class="pager pull-right">
	<a class="btn btn-primary" href="?c=<?php echo get_controller_name();?>&d=add">Add New</a>
</div>
<?php 
if (! empty($list)) {
	$rows = array();
	$headers = array();
	foreach ($list as $obj) {
		$vars = get_object_vars($obj);
		$data = array_values($vars);
		$data[] = '<a class="btn btn-primary" href="?c=' . get_controller_name() . '&d=edit&id=' . $obj->id . '">Edit</a>'; 
		$rows[] = $data;
		if (empty($headers)) {
			$headers = array_map('ucfirst', array_keys($vars));
			$headers[] = 'Operations';
		}
	}
	HTML::table(array('headers' => $headers, 'rows' => $rows));
}
?>  
</div>
