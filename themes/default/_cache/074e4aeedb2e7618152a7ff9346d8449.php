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
		     <h1>Page Title <small>Subtext for header</small></h1>
		  </div>
		  <div class="h10"></div>
		  
          <h2>Table</h2>
	      <table class="table table-bordered table-striped">
	        <thead>
	          <tr>
	            <th>Tag</th>
	            <th>Description</th>
	          </tr>
	        </thead>
	        <tbody>
	          <tr>
	            <td>
	              AAA
	            </td>
	            <td>
	              This is a template for a simple marketing or informational website
	            </td>
	          </tr>
	          <tr>
	            <td>
	              AAA
	            </td>
	            <td>
	              This is a template for a simple marketing or informational website
	            </td>
	          </tr>
	          <tr>
	            <td>
	              AAA
	            </td>
	            <td>
	              This is a template for a simple marketing or informational website
	            </td>
	          </tr>
	        </tbody>
	      </table>
	      <div class="h10"></div>
	        
		  <h2>Buttons</h2>
		  <table class="table table-bordered table-striped">
		    <thead>
		      <tr>
		        <th>Button</th>
		        <th>class=""</th>
		        <th>Description</th>
		      </tr>
		    </thead>
		    <tbody>
		      <tr>
		        <td><button class="btn btn-default" href="#">Default</button></td>
		        <td><code>btn btn-default</code></td>
		        <td>Standard gray button with gradient</td>
		      </tr>
		      <tr>
		        <td><button class="btn btn-primary" href="#">Primary</button></td>
		        <td><code>btn btn-primary</code></td>
		        <td>Provides extra visual weight and identifies the primary action in a set of buttons</td>
		      </tr>
		      <tr>
		        <td><button class="btn btn-info" href="#">Info</button></td>
		        <td><code>btn btn-info</code></td>
		        <td>Used as an alternative to the default styles</td>
		      </tr>
		      <tr>
		        <td><button class="btn btn-success" href="#">Success</button></td>
		        <td><code>btn btn-success</code></td>
		        <td>Indicates a successful or positive action</td>
		      </tr>
		      <tr>
		        <td><button class="btn btn-warning" href="#">Warning</button></td>
		        <td><code>btn btn-warning</code></td>
		        <td>Indicates caution should be taken with this action</td>
		      </tr>
		      <tr>
		        <td><button class="btn btn-danger" href="#">Danger</button></td>
		        <td><code>btn btn-danger</code></td>
		        <td>Indicates a dangerous or potentially negative action</td>
		      </tr>
		      <tr>
		        <td><button class="btn btn-inverse" href="#">Inverse</button></td>
		        <td><code>btn btn-inverse</code></td>
		        <td>Alternate dark gray button, not tied to a semantic action or use</td>
		      </tr>
		    </tbody>
		  </table>
	      <div class="h10"></div>
	    	  	   
		  <h2>Alerts</h2>
		  <div>
		      <div class="alert alert-success">
		        <button class="close" data-dismiss="alert">&times;</button>
		        <strong>Well done!</strong> You successfully read this important alert message.
		      </div> 		  
		      <div class="alert alert-info">
		        <button class="close" data-dismiss="alert">&times;</button>
		        <strong>Heads up!</strong> This alert needs your attention, but it's not super important.
		      </div>	
		      <div class="alert alert-warning">
		        <button class="close" data-dismiss="alert">&times;</button>
		        <strong>Warning!</strong> Best check yo self, you're not looking too good.
		      </div>			      	  
			  <div class="alert alert-error">
			  	<button class="close" data-dismiss="alert">&times;</button>
				<strong>Oh snap!</strong> Change a few things up and try submitting again.
			  </div>
		  </div>
		  <div class="h10"></div>
		  	             
		  <h2>Modals</h2>
		  <div>
	          <div id="my-modal" class="modal fade" role="dialog" aria-hidden="true">
			  	<div class="modal-dialog">
			       <div class="modal-content">
		            <div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
       				 	<h4 class="modal-title">Modal title</h4>
		            </div>
		            <div class="modal-body">
		              	<h4>Text in a modal</h4>
		              	<p>Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem.</p>
		            </div>
		            <div class="modal-footer">
		              	<a href="#" class="btn btn-default" data-dismiss="modal" >Close</a>
		              	<a href="#" class="btn btn-primary">Save changes</a>
		            </div>
		          </div>
		         </div>
	          </div>
	          <a data-toggle="modal" href="#my-modal" class="btn btn-primary">Launch demo modal</a>
          		  	
		  </div>
		  <div class="h10"></div> 
		  <div class="h10"></div> 


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
