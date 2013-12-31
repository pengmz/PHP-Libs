<?php

class Upload {
	
	public static function image($upload_file, $target_dir = 'uploads/', $target_name = null) {
		return Upload::file($upload_file, $target_dir, $target_name, array('gif', 'jpeg', 'jpg', 'png'));
	}
	
	public static function file($upload_file, $target_dir = 'uploads/', $target_name = null, $allowed_exts = array()) {
		if (! $upload_file) {
			Log::error('[Upload] File is empty.');
			return array('error' => 'File is empty.');
		}
		
		$file_name = $upload_file['name'];
		$file_size = $upload_file['size'];
		if ($file_size == 0){
			Log::error('[Upload] File is empty.');
            return array('error' => 'File is empty.');
        }        
	    if (! $file_name){
	    	Log::error('[Upload] File is empty.');
            return array('error' => 'File name empty.');
        }
        $ext = explode('.', $file_name);
        $ext = end($ext);
    	$ext = strtolower($ext);
	    if(! empty($allowed_exts)) {
	    	if (!in_array($ext, $allowed_exts)){
	    		Log::error('[Upload] File has an invalid extension: ' . $ext);
	    		return array('error' => 'File has an invalid extension.');
	    	}
	    }
	    if (! is_writeable($target_dir)){
	    	Log::error('[Upload] Uploads directory isn\'t writable: ' . $target_dir);
            return array('error' => 'Uploads directory isn\'t writable.');
        }	   
	    if (! $target_name){
            $target_name = $file_name;
        } else {
        	$target_name .= '.' . $ext;
        }
		$target_file = $target_dir . $target_name;
				
		if (! move_uploaded_file($upload_file['tmp_name'], $target_file)) {
			Log::error('[Upload] Could not save upload file. ');
			return array('error' => 'Could not save upload file.');
		}			 

		$results = array(
			'success' => true,
			//'uploaded_file' => $target_file,
			'uploaded_file' => basename($target_file)
		);

		Log::debug('[Upload][Success] ' . $target_file);
		
		return $results;
	}
		
}
?>