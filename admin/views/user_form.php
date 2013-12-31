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

   <form id="form1" name="form1" method="post" action="${action}" onsubmit="return validateData()">
   		<input type="hidden" id="id" name="id" value="${request->id}" />
   		
   		<label for="username">Username<span style="color:red">*</span></label>
   		<input id="username" name="username" value="${request->username}" type="text"/>
   		<div class="h10"></div>

   		<label for="password">Password</label>
   		<input id="password" name="password" value="${request->password}" type="password" />
   		<div class="h10"></div>
   		
   		<label for="email">Email</label>
   		<input id="email" name="email" value="${request->email}" type="text" /><br/>
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
