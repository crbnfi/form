<?php

if(!defined('IN_APPLICATION')) die('<h1>Access denied</h1>');

$config = array(
	
	'save_folder' 		=> '../form-data/',
	
	'mysql_host' 		=> '',
	'mysql_user' 		=> 'carbonfi_db',
	'mysql_password' 	=> '',
	'mysql_database' 	=> 'carbonfi_db',
	'mysql_prefix' 		=> 'form_',

	'email_to'      => 'isko@carbon.fi, juri@carbon.fi',
	'email_from'    => 'no-reply@carbon.fi',
	'email_subject' => 'Test subject',
	'email_message' => '',
	
);

// Form sets

/**
 * Example:
 *
 * 	$config['test-form'] = array(
 * 		'save' => array('file', 'mysql'),
 * 		'fields' => array(
 * 			'field-1' => array(
 * 				'required' => TRUE,
 * 				'validation' => 'email'
 * 			),
 * 			'field-2' => array(
 * 				'required' => FALSE,
 * 				'validation' => 'digit'
 * 			)
 * 		)
 * 	);
*/

$config['feedback'] = array(
	
	// Save the data as file
	'save' => array('mysql', 'file', 'email'),

	'email_message' => 'Lorem ipsum dolor sit amet',
	
	'fields' => array(
		
		'name' => array(
			'pretty' => 'Name',
			'required' => TRUE
		),
		
		'email' => array(
			'pretty' => 'E-mail',
			'required' => FALSE,
			'validation' => 'email'
		),
		
		'type' => array(
			'pretty' => 'Type',
			'required' => TRUE
		),
		
		'feedback' => array(
			'pretty' => 'Feedback',
			'required' => TRUE
		),
		
		'contact-me' => array(
			'pretty' => 'Contact me',
			'required' => FALSE
		)
		
	)
	
);