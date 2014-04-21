<?php

function required($reqparams) {
	if (!isset($reqparams['required']))
		return '';

	$type = $reqparams['validation'];
	$msg = $reqparams['reqmsg'];

	$patterns['numeric'] = '[0-9]';
	$patterns['email'] = '*@-.-';
	$patterns['url'] = 'https?://.+';
	$patterns['creditcard'] = '[0-9]{13,16}';
	$patterns['text'] = '*[a-zA-Z0-9-_.';
	$str = " required='required' pattern='{$patterns[$type]}' vmsg='$msg'";
	return $str;
}

function is_valid_email($email, $skipDNS = true) {
	$isValid = true;
	if (!is_string($email))
		return false;
	$atIndex = strrpos($email, "@");
	if (is_bool($atIndex) && !$atIndex) {
		$isValid = false;
	} else {
		$domain = substr($email, $atIndex + 1);
		$local = substr($email, 0, $atIndex);
		$localLen = strlen($local);
		$domainLen = strlen($domain);
		if ($localLen < 1 || $localLen > 64) {
			// local part length exceeded
			$isValid = false;
		} else if ($domainLen < 1 || $domainLen > 255) {
			// domain part length exceeded
			$isValid = false;
		} else if ($local[0] == '.' || $local[$localLen - 1] == '.') {
			// local part starts or ends with '.'
			$isValid = false;
		} else if (preg_match('/\\.\\./', $local)) {
			// local part has two consecutive dots
			$isValid = false;
		} else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
			// character not valid in domain part
			$isValid = false;
		} else if (preg_match('/\\.\\./', $domain)) {
			// domain part has two consecutive dots
			$isValid = false;
		} else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local))) {
			// character not valid in local part unless
			// local part is quoted
			if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\", "", $local))) {
				$isValid = false;
			}
		}

		if (!$skipDNS) {
			if ($isValid && !(checkdnsrr($domain, "MX") || checkdnsrr($domain, "A"))) {
				// domain not found in DNS
				$isValid = false;
			}
		}
	}
	return $isValid;
}

function e($var, $def = "") {
	return ( isset($var) ? $var : $def );
}

function ph($str) {
	return "placeholder='{$str}' ";
}


/**
 * 
 * @param type $formsetting Contains form data and fields info. Basically a definition of the form's structure.
 *								- Which field takes which type of input
 *								- If a field is required
 *								- What message to show if not filled
 *								- Field label
 * @param type $field_defs Predefined structure of each of the field types. Serves the HTML used to render each field
 *								- Definition of field types
 *								- HTML for field types
 * @return type 
 *				array(
 *					array( strings containing HTML of the form partitions, each partion in a separate index ),
 *					array( strings conaining each breadcrumb for individual form parts )
 *				)
 */
function paginate_form($formsetting, $field_defs) {
	$formsetting_raw = $formsetting;
	$forms_view = $formsetting_raw['fields'];
	$forms_pref = $formsetting_raw['fieldsinfo'];


	$formfields = new formfields();
	$advancedfields = new advancedfields();
	$commonfields = $field_defs['fields_common'];
	$generic_fields = $field_defs['fields_generic'];
	$advanced_fields = $field_defs['fields_advanced'];
	$form_parts_html = array();
	$part_number = 0;
	$part_html = "<div style='display: block' id='form_part_{$part_number}'>";
	$form_parts_names = array("form_part_{$part_number}" => 'Start');
	foreach ($forms_view as $id => $type) {

		$cur_pref = $forms_pref[$id];
		$cur_pref['id'] = $id;
		if ($type == 'pageseparator') {
			$prev_part = $part_number - 1;
			$part_name = $advancedfields->$advanced_fields[$forms_view[$id]]['type']($cur_pref);
			$parent_part = $part_number;

			$part_number++;
			$back_button_html = "<a id='goto_part_{$prev_part}' data-parent='form_part_{$parent_part}' data-next='form_part_{$prev_part}' class='btn btn-info pull-left change-part'><< Back</a>";
			$next_button_html = "<a id='goto_part_{$part_number}' data-parent='form_part_{$parent_part}' data-next='form_part_{$part_number}' class='btn btn-info pull-right change-part'>Next >></a>";
			$change_part_button_html = ($prev_part < 0 ? "<div class='col-md-12'>" : "<div class='col-md-6'>{$back_button_html}</div><div class='col-md-6'>") . $next_button_html . "</div>";
			$form_parts_html[] = $part_html . "<div class='row'>{$change_part_button_html}</div></div>"; // @todo: Change part button has to be added
			$part_html = "<div style='display: none' id='form_part_{$part_number}'>";
			if (empty($part_name))
				$part_name = 'Untitled';
			$form_parts_names["form_part_{$part_number}"] = $part_name;
			continue;
		}

		$part_html .= "<div class='form-group'>";
		if ($type != 'password')
			$part_html .= "<label for='field_' style='display: block;clear: both'>{$cur_pref['label']}</label>";

		if (isset($commonfields[$forms_view[$id]]))
			$part_html .= $formfields->$commonfields[$forms_view[$id]]['type']($cur_pref);
		else if (isset($advanced_fields[$forms_view[$id]]))
			$part_html .= $advancedfields->$advanced_fields[$forms_view[$id]]['type']($cur_pref);
		else
			$part_html .= $formfields->$forms_view[$id]($cur_pref);
		$part_html .= "</div>";
	}
	if (!empty($part_html)) {
		$prev_part = $part_number - 1;
		$parent_part = $part_number;
		$back_button_html = "<a id='goto_part_{$prev_part}' data-parent='form_part_{$parent_part}' data-next='form_part_{$prev_part}' class='btn btn-info pull-left change-part'><< Back</a>";
		$submit_button_html = "<button type='submit' id='submit' class='submit-btn btn btn-info pull-right change-part' data-parent='form_part_{$parent_part}'>" . ((isset($formsetting_raw['buttontext']) == false || $formsetting_raw['buttontext'] == '') ? "Submit" : $formsetting_raw['buttontext']) . "</button>";
		$submit_section_html = "<div class='row'>" . ($part_number > 0 ? "<div class='col-md-6'>{$back_button_html}</div><div class='col-md-6'>" : "<div class='col-md-12'>") . $submit_button_html . "</div>";
		$form_parts_html[] = $part_html . $submit_section_html . "</div>"; // @todo: Final submit button html has to be added
	}

	return array(
		'form_parts_html' => $form_parts_html,
		'form_parts_names' => $form_parts_names
	);
}

function get_currency_symbolised_amount($amount, $currency) {
	$symbol = '';
	switch($currency) {
		case 'USD':
			$symbol = '$';
			break;
		case 'EUR':
			$symbol = '€';
			break;
	}
	return $symbol.$amount;
}

/**
 * Convet and object into an array (Recursively) 
 * @param type $object
 * @return type
 */
function make_array($object) {
	if (!is_object($object) && !is_array($object)) {
		return $object;
	} else {
		$object = get_object_vars($object);
	}
	return array_map('make_array', $object);
}

/**
 * Get the client's IP address
 * 
 */

function get_client_ip() {
     $ipaddress = '';
     if (getenv('HTTP_CLIENT_IP'))
         $ipaddress = getenv('HTTP_CLIENT_IP');
     else if(getenv('HTTP_X_FORWARDED_FOR'))
         $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
     else if(getenv('HTTP_X_FORWARDED'))
         $ipaddress = getenv('HTTP_X_FORWARDED');
     else if(getenv('HTTP_FORWARDED_FOR'))
         $ipaddress = getenv('HTTP_FORWARDED_FOR');
     else if(getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
     else if(getenv('REMOTE_ADDR'))
         $ipaddress = getenv('REMOTE_ADDR');
     else
         $ipaddress = 'UNKNOWN';

     return $ipaddress; 
}



if ( ! function_exists( 'my_pagination' ) ) :
	function my_pagination() {
		global $wp_query;

		$big = 999999999; // need an unlikely integer
		
		echo paginate_links( array(
			'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format' => '?paged=%#%',
			'current' => max( 1, get_query_var('paged') ),
			'total' => $wp_query->max_num_pages
		) );
	}
endif;

?>