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
   			
<div class="page-header">
   <h2>User List <small></small></h2>
</div>
<div class="h10"></div>
  
<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th width="32">ID</th>
      <th>Username</th>
      <th>Email</th>
      <th width="200">Operations</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($users as $user){ ?>	
    <tr>
      <td><?php echo $user->id; ?></td>
      <td><?php echo $user->username; ?></td>
      <td><?php echo $user->email; ?></td>
      <td>
      <a class="btn btn-primary" href="?c=user&d=edit&id=<?php echo $user->id; ?>">Edit</a>
      <a class="btn btn-primary" href="javascript:delete_user(<?php echo $user->id; ?>);">Delete</a>
      </td>
    </tr>
    <?php } ?>	
  </tbody>
</table>
<div class="h10"></div> 
<div id="delete-modal" class="modal fade" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
    	<div class="modal-content">
        	<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
 				<h4 class="modal-title">删除</h4>
        	</div>
	        <div class="modal-body">
	          	<p>确定要删除这个用户吗?</p>
	        </div>
	        <div class="modal-footer">
	          	<a id="delete-btn" href="#" class="btn btn-info">删除</a>
	          	<a href="#" class="btn btn-default" data-dismiss="modal" >取消</a>
	        </div>
      </div>
	</div>
</div>
          	      
<script type="text/javascript">
	function delete_user(user_id) {
		$('#delete-btn').attr("href", "?c=user&d=delete&id="+user_id);
		$('#delete-modal').modal('show');			
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
