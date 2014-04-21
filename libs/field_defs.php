<?php

function set_commonfields() {
	$fields = array();
	$fields['name'] = array('type' => 'text', 'label' => 'Name');
	$fields['email'] = array('type' => 'text', 'label' => 'Email');
	$fields['subject'] = array('type' => 'text', 'label' => 'Subject');
	$fields['message'] = array('type' => 'textarea', 'label' => 'Message');

	return $fields;
}

add_filter("common_fields", "set_commonfields");

function set_genericfields() {
	$generic_fields = array();
	$generic_fields = array(
		'text' => array(
			'type' => 'text',
			'label' => 'Text'
		),
		'password' => array(
			'type' => 'password',
			'label' => 'Password'
		),
		'radio' => array(
			'type' => 'radio',
			'label' => 'Radio',
			'options' => true
		),
		'checkbox' => array(
			'type' => 'checkbox',
			'label' => 'Checkbox',
			'options' => true
		),
		'select' => array(
			'type' => 'select',
			'label' => 'Select',
			'options' => true
		),
		'textarea' => array(
			'type' => 'textarea',
			'label' => 'Textarea'
		)
	);
	return $generic_fields;
}

add_filter("generic_fields", "set_genericfields");

function set_advancedfields() {
	$advanced_fields = array();
	$advanced_fields = array(
		'file' => array(
			'type' => 'file',
			'label' => 'File Upload',
			'template' => 'file'
		),
		'captcha' => array(
			'type' => 'captcha',
			'label' => 'Captcha'
		),
		'fullname' => array(
			'type' => 'fullname',
			'label' => 'Full name'
		),
		'address' => array(
			'type' => 'address',
			'label' => 'Address'
		),
		'pageseparator' => array(
			'type' => 'pageseparator',
			'label' => 'Page Separator',
			'template' => 'separator'
		),
		'payment' => array(
			'type' => 'payment',
			'label' => 'Payment methods',
			'template' => 'payment'
		)
	);
	return $advanced_fields;
}

add_filter("advanced_fields", "set_advancedfields");

function set_methods() {
	$methods_set = array();
	$methods_set = array(
		'paypal' => 'PayPal',
		'2checkout' => '2Checkout',
		'skrill' => 'Skrill',
		'payza' => 'PayZa',
		'authorizenet' => 'Authorize.net'
	);
	return $methods_set;
}

add_filter("method_set", "set_methods");

function currencies() {
	$currencies_list = array(
		'USD' => 'Dollar',
		'EUR'	=> 'Euro'
	);
	return $currencies_list;
}
?>