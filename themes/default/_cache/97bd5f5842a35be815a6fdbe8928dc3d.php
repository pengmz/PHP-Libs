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
		include_theme_css('/style.css');
		//include_theme_script('/js/jquery.min.js');
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

    <div ng-app>
        <div class="col-md-3 pull-left" style="max-width:280px;">
        	<div class="h10"></div>
			<?php include_file('nav');?>		    
        </div>
        <div class="col-md-9" style="min-height:456px">
			<?php include_file('message');?>	
   			<?php 
	$c = get_controller_name();
	$d = get_action_name();
	$action = '?c=' . $c;
	if ($d == 'edit') {
		$action .= '&d=update';	
	} else {
		$action .= '&d=save';	
	}
			
?>	
<div class="page-header">
   	<h2>User Form</h2>
</div>

<div class="row-fluid">	  	

   <form id="form1" name="form1" method="post" action="<?php echo $action; ?>" onsubmit="return validateData()">
   		<input type="hidden" id="id" name="id" value="<?php echo $request->id; ?>" />
   		
   		<label for="username">Username<span style="color:red">*</span></label>
   		<input id="username" name="username" value="<?php echo $request->username; ?>" type="text"/>
   		<div class="h10"></div>

   		<label for="password">Password</label>
   		<input id="password" name="password" value="<?php echo $request->password; ?>" type="password" />
   		<div class="h10"></div>
   		
   		<label for="email">Email</label>
   		<input id="email" name="email" value="<?php echo $request->email; ?>" type="text" /><br/>
   		<div class="h10"></div>
   		
		<button class="btn btn-info btn-lg" id="saveForm" type="submit" style="width:180px">
			保存
		</button>	    		
    </form>
    <div class="h10"></div>

</div>

<script type="text/javascript">
	$("#username").focus();
	
	function validateData(){
	    $(".text-error").hide();
	    if (!jQuery.trim($("#username").val())){
	        $("#username_is_required").show();
	        $("#username").focus();
	        return false;
	    }
	    return true;
	}
</script>	

        </div>
   		<div class="clear h10"></div>      
    </div>
    <hr/>

    <footer>
    	<p class="pull-right">&copy; Company 2013 &nbsp;&nbsp;&nbsp;</p>
    </footer>
        
	<?php 
		include_theme_script('/js/jquery.min.js');
		include_theme_script('/js/bootstrap.min.js');
		//include_theme_script('/js/angular/angular.min.js');
		//include_theme_script('/js/angular/angular-resource.min.js');
	?>

  </body>
</html>
