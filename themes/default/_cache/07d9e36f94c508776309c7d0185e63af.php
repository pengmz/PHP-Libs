<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Admin</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> 
	<?php 
		include_theme_css('/css/bootstrap.min.css');
		include_theme_css('/css/bootstrap-responsive.min.css');
		//include_theme_css('/css/bootstrap-theme.min.css');
		include_theme_css('/style.css');
		include_theme_script('/js/jquery.min.js');
	?>
    <!--[if lt IE 9]>
      <?php 
      	include_theme_script('/js/html5shiv.js'); 
      	include_theme_script('/js/respond.min.js'); 
      ?>
    <![endif]-->	
  </head>

  <body>

    <div class="navbar navbar-default navbar-fixed-top">
    	<div class="navbar-inner">
		  <div>
            <ul class="navbar-nav">
              <li><a href="#"><span class="glyphicon glyphicon-home"></span>&nbsp; Home</a></li>
              <li><a href="#about">About</a></li>
              <li><a href="#contact">Contact</a></li>
            </ul>
          </div>          
          <div class="navbar-nav navbar-right">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
              <span class="glyphicon glyphicon-user"></span> Username
              <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
              <li><a href="?d=logout">Sign Out</a></li>
            </ul>
          </div> 
       </div>         
    </div>

    <div>
        <div class="col-md-3 pull-left" style="max-width:280px;">
        	<div class="h10"></div>
			<?php $this->import('nav');?>		    
        </div>
        <div class="col-md-9" style="min-height:456px">
			<?php $this->import('message');?>	
   			  
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

        </div>
   		<div class="clear h10"></div>      
    </div>
    <hr/>

    <footer>
    	<p class="pull-right">&copy; Company 2013 &nbsp;&nbsp;&nbsp;</p>
    </footer>
        
	<?php 
		//include_theme_script('/js/jquery.min.js');
		include_theme_script('/js/bootstrap.min.js');
	?>

  </body>
</html>
