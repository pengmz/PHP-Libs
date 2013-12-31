<script type="text/javascript">
	function context($scope) {
		<?php init_context($this->data()); ?>
	}	
</script>
  
<div ng-controller="context">  
	<div class="page-header">
	   <h1>User List <small></small></h1>
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
	    <tr ng-repeat="user in users">
	      <td>{{user.id}}</td>
	      <td>{{user.username}}</td>
	      <td>{{user.email}}</td>
	      <td>
	      <a class="btn btn-info" href="?c=user&d=edit&id={{user.id}}">Edit</a>
	      <a class="btn btn-info" href="javascript:delete_user({{user.id}});">Delete</a>
	      </td>
	    </tr>
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
</div>

<script type="text/javascript">
	function delete_user(user_id) {
		$('#delete-btn').attr("href", "?c=user&d=delete&id="+user_id);
		$('#delete-modal').modal('show');			
	}	
</script>	

