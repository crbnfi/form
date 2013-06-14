<?php

/**
 * Carbon Form Utility
 *
 * @package		Carbon Utilities
 * @author		Carbon Oy, Juri Saltbacka
 * @copyright	Copyright (c) 2011, Carbon Oy.
 * @link		http://carbon.fi
 * @version		Version 0.1
 */

define('IN_APPLICATION', TRUE);
$mysql_connected = FALSE;

require_once('common.php');
require_once('config.php');

$request = explode('/', ($_SERVER['PATH_INFO'] ? $_SERVER['PATH_INFO'] : $_SERVER['ORIG_PATH_INFO']));
$output = array();
$errors = array('required' => array(), 'invalid' => array());
$data = array();

if(isset($request[1]))
{
	// Set the form set
	$set = preg_replace('[^A-Za-z0-9-_]', '', $request[1]);
	$output['set'] = $set;
	
	// Fetch the config for current set
	if(isset($config[$set]))
	{
		foreach($config[$set]['fields'] as $field => $rules)
		{
			// Check if the field is required
			if($rules['required'] == TRUE)
			{
				if(empty( $_REQUEST[$field] )) {
					$errors['required'][] = $field;
					continue;
				}
			}
			
			// Validate
			if(!empty( $_REQUEST[$field] ) && isset($rules['validation']) && isset($validators[$rules['validation']]))
			{
				if(!preg_match($validators[$rules['validation']], $_REQUEST[$field])) {
					$errors['invalid'][] = $field;
					continue;
				}
			}
			
			$data[$field] = isset($_REQUEST[$field]) ? clean($_REQUEST[$field]) : 0;
		}
		
		if(count( $errors['required'] ) > 0 || count( $errors['invalid'] ) > 0)
		{
			$output['status'] = 'error';
			$output['error'] = 'invalid_form_data';
			$output['errors'] = $errors;
		}
		else
		{
			if(!is_array($config[$set]['save'])) $config[$set]['save'] = array($config[$set]['save']);
			
			foreach($config[$set]['save'] as $method) {
				switch($method)
				{
					case 'mysql':
						
						if(!$mysql_connected) {
							if(!$config['mysql_host'] || !$config['mysql_user'] || !$config['mysql_password'] || !$config['mysql_database']) {
								$output['status'] = 'error';
								$output['error'] = 'mysql_config_error';
								exit (json_output($output));
							}
							
							if(!@mysql_connect($config['mysql_host'], $config['mysql_user'], $config['mysql_password'])) {
								$output['status'] = 'error';
								$output['error'] = 'mysql_connection_error';
								$output['mysql_error'] = mysql_error();
								exit (json_output($output));
							}
							
							if(!@mysql_select_db($config['mysql_database'])) {
								$output['status'] = 'error';
								$output['error'] = 'mysql_database_error';
								$output['mysql_error'] = mysql_error();
								exit (json_output($output));
							}
							
							$mysql_connected = TRUE;
						}
						
						$fields = array();
						$values = array();
						
						foreach($data as $k => $v) {
							$fields[] = $k;
							$values[] = cleanSQL($v);
						}
						
						$qs = "INSERT INTO {$config['mysql_prefix']}{$set} (`" . join("`, `", $fields) . "`) VALUES ('" . join("', '", $values) . "')";
						
						if(!@mysql_query($qs)) {
							$output['status'] = 'error';
							$output['error'] = 'mysql_error';
							$output['query_string'] = $qs;
							$output['mysql_error'] = mysql_error();
							exit (json_output($output));
						}
						
					break;
					case 'file':
					case 'csv':
					default:
						if($fp = @fopen($config['save_folder'] . $set . '.csv', 'a+')) {
							fwrite($fp, '"' . join('";"', $data) . "\"\n");
							fclose($fp);
						}
						else {
							$output['status'] = 'error';
							$output['error'] = 'file_access_error';
							exit (json_output($output));
						}
					break;
					case 'email':

						$message = (isset($config[$set]['email_message']) $config[$set]['email_message'] ? $config['email_message']) . "\n\n";

						foreach($data as $k => $v) {
							$message .= (isset($config[$set]['fields'][$k]['pretty']) ? $config[$set]['fields'][$k]['pretty'] : $k) . ": " . $v . "\n";
						}

						if(!@mail($config['email_to'], $config['email_subject'], $message, 'From: ' . $config['email_from'])) {
							$output['status'] = 'error';
							$output['error'] = 'sending_mail';
							exit (json_output($output));
						}


					break;
				}
			}
			
			$output['status'] = 'success';
			$output['data'] = $data;
		}
	}
	else
	{
		$output['status'] = 'error';
		$output['error'] = 'invalid_form_set';
	}
}
else
{
	$output['status'] = 'error';
	$output['error'] = 'invalid_request';
}

exit (json_output($output));