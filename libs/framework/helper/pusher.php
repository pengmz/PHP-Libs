<?php
define('PUSH_NOTIFICATIONS_TYPE_ID_IOS', 'iphone');
define('PUSH_NOTIFICATIONS_TYPE_ID_ANDROID', 'android');

// Apple Server
define('PUSH_NOTIFICATIONS_APNS_HOST', 'gateway.push.apple.com');
// Apple Server port.
define('PUSH_NOTIFICATIONS_APNS_PORT', 2195);
// Apple Feedback Server, initially set to development server.
define('PUSH_NOTIFICATIONS_APNS_FEEDBACK_HOST', 'feedback.push.apple.com');
// Apple Feedback Server port.
define('PUSH_NOTIFICATIONS_APNS_FEEDBACK_PORT', 2196);
// Size limit for individual payload, in bytes.
define('PUSH_NOTIFICATIONS_APNS_PAYLOAD_SIZE_LIMIT', 255);
// Payload sound
define('PUSH_NOTIFICATIONS_APNS_NOTIFICATION_SOUND', 'default');
// Boolean value to indicate wether Apple's feedback service should be called
// on cron to remove unused tokens from our database.
define('PUSH_NOTIFICATIONS_APNS_QUERY_FEEDBACK_SERVICE', 1);
// Maximum of messages to send per stream context.
define('PUSH_NOTIFICATIONS_APNS_STREAM_CONTEXT_LIMIT', 1);
// Name of certificate, initially set to development certificate.
if (! defined('PUSH_NOTIFICATIONS_APNS_CERTIFICATE')) {
	define('PUSH_NOTIFICATIONS_APNS_CERTIFICATE', 'apns-dev.pem');
	define('PUSH_NOTIFICATIONS_APNS_PASSPHRASE', '123456');
}

// Google Push Notification Types
define('PUSH_NOTIFICATIONS_GOOGLE_TYPE_C2DM', 0);	//Cloud 2 Device Messaging
define('PUSH_NOTIFICATIONS_GOOGLE_TYPE_GCM', 1);	//Google Cloud Messaging
define('PUSH_NOTIFICATIONS_GOOGLE_TYPE', PUSH_NOTIFICATIONS_GOOGLE_TYPE_GCM);
// C2DM Serve
define('PUSH_NOTIFICATIONS_C2DM_CLIENT_LOGIN_ACTION_URL', 'https://www.google.com/accounts/ClientLogin');
define('PUSH_NOTIFICATIONS_C2DM_SERVER_POST_URL', 'https://android.apis.google.com/c2dm/send');
// GCM Server
define('PUSH_NOTIFICATIONS_GCM_SERVER_POST_URL', 'https://android.googleapis.com/gcm/send');
// GCM API KEY Credentials.
if (! defined('PUSH_NOTIFICATIONS_GCM_API_KEY')) {
	define('PUSH_NOTIFICATIONS_GCM_API_KEY', 'AIzaSyDOV18d2SRFt2IXK8SHCKISLdvP6fQsBxM');
}

// Baidu API KEY
if (! defined('PUSH_NOTIFICATIONS_BAIDU_API_KEY')) {
	define('PUSH_NOTIFICATIONS_BAIDU_API_KEY', 'zbtzQyrBjjczyRVKAix0aMuF');
	define('PUSH_NOTIFICATIONS_BAIDU_SECRET_KEY', 'eLG48VcQjjYxyTx8ZzYWdBwu4wo1uN1K');
}

if (! defined('LIB_PATH')) {
	define('DS', DIRECTORY_SEPARATOR);
	define('LIB_PATH', dirname(dirname(dirname(__FILE__))) . DS);
	define('ROOT', dirname(LIB_PATH));
}

@set_time_limit(0);
@ignore_user_abort(true);
		
class Pusher {
	
	/**
	 * Send out push notifications, switch automatically between delivery method.
	 */
	public static function push_notifications($message, $devices = array()) {
		if (! $message || empty($devices)) {
			return FALSE;
		}

		// Shorten the message to 255 characters / 8 bit.
		if (mb_strlen($message) > PUSH_NOTIFICATIONS_APNS_PAYLOAD_SIZE_LIMIT) {
			$message = mb_substr($message, 0, PUSH_NOTIFICATIONS_APNS_PAYLOAD_SIZE_LIMIT);
		}
		
		// Convert the payload into the correct format for delivery.
		$payload = array('alert' => $message);
		
		// Group tokens into types.
		$tokens_ios = array();
		$tokens_android = array();
		foreach($devices as $device) {
			$device_token = $device->device_token;
			if ($device_token) {
				switch($device->device_type) {
					case PUSH_NOTIFICATIONS_TYPE_ID_IOS:
						$tokens_ios[] = $device_token;
						break;
					
					case PUSH_NOTIFICATIONS_TYPE_ID_ANDROID:
						$tokens_android[] = $device_token;
						break;
				}
			}
		}

		// Send payload to iOS.
		if (! empty($tokens_ios)) {
			$result = self::push_notifications_to_apple($tokens_ios, $payload);
		}
		// Send payload to Android.
		if (! empty($tokens_android)) {
			$result = self::push_notifications_by_baidu($tokens_android, $payload);
			//$result = self::push_notifications_to_android($tokens_android, $payload);
		}
		return $result;
	}
	
	public static function push_notifications_to_apple($tokens, $payload) {
		// Convert the payload into the correct format for APNS.
		$payload_apns = array('aps' => $payload);
		return self::push_notifications_by_apns($tokens, $payload_apns);
	}
	
	public static function push_notifications_to_android($tokens, $payload) {
		switch(PUSH_NOTIFICATIONS_GOOGLE_TYPE) {
			case PUSH_NOTIFICATIONS_GOOGLE_TYPE_C2DM:
				$result = self::push_notifications_by_c2dm($tokens, $payload);
				break;
			
			case PUSH_NOTIFICATIONS_GOOGLE_TYPE_GCM:
				$result = self::push_notifications_by_gcm($tokens, $payload);
				break;
		}		
		return $result;
	}
	
	public static function push_notifications_by_baidu($tokens, $payload) {
		include_once LIB_PATH . 'httpclient/http_client.php';
		
		$http_method = 'POST';
		$url = 'http://channel.api.duapp.com/rest/2.0/channel/channel';
		$messages = json_encode(array('title' => '河海MBA', 'description' => $payload['alert'], 'open_type' => 2));
		$data = array(
			'method'	=>	'push_msg',
			'apikey'	=>	PUSH_NOTIFICATIONS_BAIDU_API_KEY,
			'push_type'	=>	'3',
			'device_type'	=>	'3',
			'message_type'	=>	'1',
			'messages'	=>	$messages,
			'msg_keys'	=>	md5(json_encode($tokens) . time()),
			'timestamp'	=>	time(),
		);
		
		ksort($data);
		$data_str = '';
		foreach ($data as $k => $v) {
			$data_str .= $k . '=' . $v;
		}
		$sign = md5(urlencode($http_method . $url . $data_str . PUSH_NOTIFICATIONS_BAIDU_SECRET_KEY)); 
		$data['sign'] = $sign;
		
		$request = new HttpClient();
		$request->post($url, $data);
		//echo $response->status();
		//echo $response->body();
		$request->close();		
		
		return true;
	}
		
	/**
	 * Send out push notifications through APNS.
	 *
	 * @param $tokens
	 *   Array of iOS tokens
	 * @param $payload
	 *   Payload to send. Minimum requirement
	 *   is a nested array in this format:
	 *   $payload = array(
	 *     'aps' => array(
	 *       'alert' => 'Push Notification Test',
	 *     );
	 *   );
	 */
	public static function push_notifications_by_apns($tokens, $payload) {
		if (empty($tokens) || empty($payload)) {
			return FALSE;
		}
		
		$payload_apns = array();
		
		// Allow for inclusion of custom payloads.
		foreach($payload as $key => $value) {
			if ($key != 'aps') {
				$payload_apns[$key] = $value;
			}
		}
		
		// Add the default 'aps' key for the payload.
		$payload_apns['aps'] = $payload['aps'];
		
		// Set the default sound if no sound was set.
		if (! isset($payload_apns['aps']['sound'])) {
			$payload_apns['aps']['sound'] = PUSH_NOTIFICATIONS_APNS_NOTIFICATION_SOUND;
		}
		
		// JSON-encode the payload.
		$payload_apns = json_encode($payload_apns);
		
		$result = 0;
		
		// Send a push notification to every recipient.
		$stream_counter = 0;
		foreach($tokens as $token) {
			// Open an apns connection, if necessary.
			if ($stream_counter == 0) {
				$apns = self::apns_open_connection();
				if (! $apns) {
					Log::error('[Push][APNS] Check to make sure you are using a valid certificate file.');
					return $result;
				}
			}
			$stream_counter ++;
			
			$apns_message = chr(0) . chr(0) . chr(32) . pack('H*', $token) . chr(0) . chr(strlen($payload_apns)) . $payload_apns;
			//$apns_message = chr(0) . pack('n', 32) . pack('H*', $token) . pack('n', strlen($payload_apns)) . $payload_apns;
			
			// Write the payload to the currently active streaming connection.
			$success = fwrite($apns, $apns_message);
			if ($success) {
				$result ++;
			} elseif ($success == 0 || $success == FALSE || $success < strlen($apns_message)) {
				Log::error('[Push][APNS] APNS message could not be sent.');
			}
			
			// Reset the stream counter if no more messages should
			// be sent with the current stream context.
			// This results in the generation of a new stream context
			// at the beginning of this loop.
			if ($stream_counter >= PUSH_NOTIFICATIONS_APNS_STREAM_CONTEXT_LIMIT) {
				$stream_counter = 0;
			}
		}
		
		// Close the apns connection.
		fclose($apns);
		
		Log::debug('[Push][APNS] Successfully push messages.');
		return $result;
	}
	
	/**
	 * Send out push notifications through GCM.
	 *
	 * @link http://developer.android.com/guide/google/gcm/index.html
	 */
	public static function push_notifications_by_gcm($tokens, $payload) {
		if (empty($tokens) || empty($payload)) {
			return FALSE;
		}
		
		if (is_null(PUSH_NOTIFICATIONS_GCM_API_KEY)) {
			return FALSE;
		}
		
		// Define an array of result values.
		$result = 0;
		
		// Define the header.
		$headers = array();
		$headers[] = 'Content-Type:application/json';
		$headers[] = 'Authorization:key=' . PUSH_NOTIFICATIONS_GCM_API_KEY;
		
		// Check of many token bundles can be build.
		$token_bundles = ceil(count($tokens) / 1000);
		
		// Send a push notification to every recipient.
		for($i = 0; $i < $token_bundles; $i ++) {
			// Create a token bundle.
			$bundle_tokens = array_slice($tokens, $i * 1000, 1000, FALSE);
			
			// Convert the payload into the correct format for C2DM payloads.
			// Prefill an array with values from other modules first.
			$data = array();
			foreach($payload as $key => $value) {
				if ($key != 'alert') {
					$data['data'][$key] = $value;
				}
			}
			// Fill the default values required for each payload.
			$data['registration_ids'] = $bundle_tokens;
			$data['collapse_key'] = (string) time();
			$data['data']['message'] = $payload['alert'];
			
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, PUSH_NOTIFICATIONS_GCM_SERVER_POST_URL);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($curl, CURLOPT_POST, TRUE);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
			$response_raw = curl_exec($curl);
			$info = curl_getinfo($curl);
			curl_close($curl);
			
			$response = FALSE;
			if (isset($response_raw)) {
				$response = json_decode($response_raw);
			}
			
			// If Google's server returns a reply, but that reply includes an error,
			// log the error message.
			if ($info['http_code'] == 200 && (! empty($response->failure))) {
				Log::error('[Push][GCM] Google\'s Server returned an error: ' . $response_raw);
				
				// Analyze the failure.
				foreach($response->results as $token_index => $message_result) {
					if (! empty($message_result->error)) {
						// If the device token is invalid or not registered (anymore because the user
						// has uninstalled the application), remove this device token.
						if ($message_result->error == 'NotRegistered' || $message_result->error == 'InvalidRegistration') {
							Log::error('[Push][GCM] GCM token not valid anymore. Removing token ' . $bundle_tokens[$token_index]);
						}
					}
				}
			}
			
			// Count the successful sent push notifications if there are any.
			if ($info['http_code'] == 200 && ! empty($response->success)) {
				$result += $response->success;
			}
		}
		
		Log::debug('[Push][GCM] Successfully push messages.');
		return $result;
	}
	
	/**
	 * Send out push notifications through C2DM.
	 */
	public static function push_notifications_by_c2dm($tokens, $payload) {
		if (empty($tokens) || empty($payload)) {
			return FALSE;
		}
		
		if (is_null(PUSH_NOTIFICATIONS_C2DM_USERNAME) || is_null(PUSH_NOTIFICATIONS_C2DM_PASSWORD)) {
			return FALSE;
		}
		
		// Determine an updated authentication token.
		// Google is very vague about how often this token changes,
		// so we'll just get a new token every time.
		$auth_token = self::c2dm_get_token();
		if (! $auth_token) {
			Log::error('[Push][C2DM] Google C2DM Server did not provide an authentication token. . Check your C2DM credentials.');
			return FALSE;
		}
		
		// Define an array of result values.
		$result = 0;
		
		// Define the header.
		$headers = array();
		$headers[] = 'Authorization: GoogleLogin auth=' . $auth_token;
		
		// Send a push notification to every recipient.
		foreach($tokens as $token) {
			
			// Convert the payload into the correct format for C2DM payloads.
			// Prefill an array with values from other modules first.
			$data = array();
			foreach($payload as $key => $value) {
				if ($key != 'alert') {
					$data['data.' . $key] = $value;
				}
			}
			// Fill the default values required for each payload.
			$data['registration_id'] = $token;
			$data['collapse_key'] = time();
			$data['data.message'] = $payload['alert'];
			
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, PUSH_NOTIFICATIONS_C2DM_SERVER_POST_URL);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($curl, CURLOPT_POST, TRUE);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			$response = curl_exec($curl);
			$info = curl_getinfo($curl);
			curl_close($curl);
			
			// If Google's server returns a reply, but that reply includes an error, log the error message.
			if ($info['http_code'] == 200 && (isset($response) && preg_match('/Error/', $response))) {
				Log::error('[Push][C2DM] Google\'s Server returned an error: ' . $response);
				
				// If the device token is invalid or not registered (anymore because the user
				// has uninstalled the application), remove this device token.
				if (preg_match('/InvalidRegistration/', $response) || preg_match('/NotRegistered/', $response)) {
					Log::error('[Push][C2DM] C2DM token not valid anymore. Removing token ' . $token);
				}
			}
			
			// Success if the http response status is 200 and the response
			// data does not containt the word "Error".
			if ($info['http_code'] == 200 && (isset($response) && ! preg_match('/Error/', $response))) {
				$result ++;
			}
		}
		
		Log::debug('[Push][C2DM] Successfully push messages.');
		return $result;
	}
	
	/**
	 * Determine the auth string from C2DM server.
	 */
	private static function c2dm_get_token() {
		$data = array('Email' => PUSH_NOTIFICATIONS_C2DM_USERNAME, 'Passwd' => PUSH_NOTIFICATIONS_C2DM_PASSWORD, 'accountType' => 'HOSTED_OR_GOOGLE', 'source' => 'Company-AppName-Version', 'service' => 'ac2dm');
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, PUSH_NOTIFICATIONS_C2DM_CLIENT_LOGIN_ACTION_URL);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_POST, TRUE);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		$response = curl_exec($curl);
		curl_close($curl);
		
		// Get the auth token.
		preg_match("/Auth=([a-z0-9_\-]+)/i", $response, $matches);
		$auth_token = $matches[1];
		
		return $auth_token;
	}
	
	/**
	 * Open an APNS connection.
	 * Should be closed by calling fclose($connection) after usage.
	 */
	private static function apns_open_connection() {
		// Determine the absolute path of the certificate.
		// @see http://stackoverflow.com/questions/809682
		$apns_cert = self::apns_get_certificate();
		
		if (! file_exists($apns_cert)) {
			Log::error('[Push][APNS Connect] Cannot find apns certificate file at @path' . $apns_cert);
			return FALSE;
		}
		
		// Create a stream context.
		$stream_context = stream_context_create();
		// Set options on the stream context.
		stream_context_set_option($stream_context, 'ssl', 'local_cert', $apns_cert);
		
		if (PUSH_NOTIFICATIONS_APNS_PASSPHRASE) {
			stream_context_set_option($stream_context, 'ssl', 'passphrase', PUSH_NOTIFICATIONS_APNS_PASSPHRASE);
		}
		
		// Open an Internet socket connection.
		$apns = stream_socket_client('ssl://' . PUSH_NOTIFICATIONS_APNS_HOST . ':' . PUSH_NOTIFICATIONS_APNS_PORT, $error, $error_string, 2, STREAM_CLIENT_CONNECT, $stream_context);
		if (! $apns) {
			Log::error('[Push][APNS Connect] Connection to Apple Notification Server failed.' . $error . ': ' . $error_string);
			return FALSE;
		} else {
			return $apns;
		}
	}
	
	/**
	 * Get the full path to the APNS certificate.
	 *
	 * @return string
	 *   The path to the certificate file on the server.
	 */
	private static function apns_get_certificate() {
		$path = ROOT . DS . 'configs' . DS . 'certificates' . DS . PUSH_NOTIFICATIONS_APNS_CERTIFICATE;
		return $path;
	}
	
	/**
	 * Connect to Apple's feedback server to get unused device tokens.
	 * @see http://stackoverflow.com/questions/4774681/php-script-for-apple-push-notification-feedback-service-gets-timeout-every-time
	 * @see http://stackoverflow.com/questions/1278834/php-technique-to-query-the-apns-feedback-server/2298882#2298882
	 */
	private static function apns_feedback_service() {
		$tokens = array();

		$stream_context = stream_context_create();
		$apns_cert = self::apns_get_certificate();
		stream_context_set_option($stream_context, 'ssl', 'local_cert', $apns_cert);
		$apns = stream_socket_client('ssl://' . PUSH_NOTIFICATIONS_APNS_FEEDBACK_HOST . ':' . PUSH_NOTIFICATIONS_APNS_FEEDBACK_PORT, $error, $error_string, 2, STREAM_CLIENT_CONNECT, $stream_context);
		
		if ($apns) {
			while(! feof($apns)) {
				$data = fread($apns, 38);
				if (strlen($data)) {
					$tokens[] = unpack("N1timestamp/n1length/H*devtoken", $data);
				}
			}
			fclose($apns);
		}
		
		return $tokens;
	
	}
	
	/**
	 * Check size of a push notification payload.
	 * Payload can't exceed PUSH_NOTIFICATIONS_APNS_PAYLOAD_SIZE_LIMIT.
	 */
	private static function check_payload_size($payload = '') {
		if ($payload == '') {
			return FALSE;
		}
		
		// JSON-encode the payload.
		$payload = json_encode($payload);
		
		// Verify that the payload doesn't exceed limit.
		$payload_size = mb_strlen($payload, '8bit');
		$size_valid = ($payload_size > PUSH_NOTIFICATIONS_APNS_PAYLOAD_SIZE_LIMIT) ? FALSE : TRUE;
		return $size_valid;
	}

}

//Pusher::push_notifications_by_baidu(array(), array('alert' => '测试1102'));

//$devices = array();
//$device = new stdClass();
//$device->device_token = 'c9ae659fc95f9bc3809f0b1e91a0e0f55d40c7808fa1510a23bbff0124e849c4';
//$device->device_type = 'iphone';
//$devices[] = $device;
//$device2 = new stdClass();
//$device2->device_token = 'f375b832901bccdad61424b880d66c833ba874f845e9a61569f8b7d404de7275';
//$device2->device_type = 'iphone';
//$devices[] = $device2;
//Pusher::push_notifications('test push22', $devices);
