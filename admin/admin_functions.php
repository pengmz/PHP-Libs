<?php

	function is_admin(){
		return is_loggedin() && (get_current_username()=='admin');
	}

	function is_manager(){
		return is_loggedin() && (get_current_userid() <= 1000);
	}
	
	function is_loggedin(){
		return !is_null(get_current_userid());
	}
	
	function get_current_userid() {
		return get_session('_current_userid');
	}
	
	function get_current_username() {
		return get_session('_current_username');
	}
	
	function save_current_username($user_id, $username) {
		set_session('_current_userid', $user_id);
		set_session('_current_username', $username);
	}
		
	function save_current_appid($app_id) {
		set_session('_current_appid', $app_id);
	}	

	function get_current_appid() {
		return get_session('_current_appid');
	}

?>