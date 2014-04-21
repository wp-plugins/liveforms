<?php

/**
 * A Class to handle payment operations from a submitted form
 */
// Dependencies
class Liveforms_Payment {

	var $params;

	function __construct() {
		// hook into init payment_verification
	}

	public function pay($params) {
		$payment_class = ucwords($params['method']);
		$pay_object = new $payment_class();
		return $pay_object->ShowPaymentForm($params, 1);
	}

	function payment_verification() {
		if (isset($_REQUEST['paymethod']) && class_exists($_REQUEST['paymethod'])) {
			$payment = new $_REQUEST['paymethod']();
			$submission_id = $payment->GetExtraParams();
			$submission_method_custom_params = $payment->GetCustomVars();
			$creds_data = $this->fetch_form_data($submission_id);
			$verified_state = $payment->VerifyNotification($submission_method_custom_params);

			
			if ($verified_state) {
				$this->send_emails($creds_data);
			}
			die('ok');
		}
	}

	function fetch_form_data($submit_id, $table_name = 'liveforms_conreqs') {
		global $wpdb;
		$query = "select * from {$wpdb->prefix}{$table_name} where `id`='{$submit_id}'";
		$submission = $wpdb->get_row($query, ARRAY_A);

		$form_id = $submission['fid'];
		$form_settings = get_post_meta($form_id, 'form_data', true);

		$form_creds = array(
			'submit_id' => $submit_id,
			'form_id' => $form_id,
			'submission_details' => $submission,
			'form_data' => $form_settings,
		);

		return $form_creds;
	}

	public function send_emails($form_creds) {
		$form_id = $form_creds['form_id'];
		$form_data = $form_creds['form_data'];

		//Preparing Email
		//Fetching user infos for email
		$form_agent_id = $form_data['agent'];
		$form_agent_info = get_userdata($form_agent_id);
		$form_agent_email = $form_agent_info->user_email;
		$from_email = $form_data['email'];
		$from_name = $form_data['from'];

		$token = $form_creds['submission_details']['token'];
		$data = maybe_unserialize($form_creds['submission_details']['data']);
		$emails = $this->entry_has_emails(maybe_unserialize($data));



		// Prepare entry data for email template injection
		$email_template_data = array_merge(array('fid' => $form_id, 'status' => 'new', 'token' => $token), maybe_unserialize($data));

		//to user
		$site_name = get_bloginfo('name');
		$user_email_data['subject'] = "[{$site_name}] Thanks, your payment was successful";
		$user_email_data['message'] = "Thanks for your payment to {$site_name}. To gain further access to your submitted request, use this token: [ {$token} ]";
		$user_email_data['to'] = $emails;
		$user_email_data['from_email'] = $from_email;
		$user_email_data['from_name'] = $from_name;

		$user_email_data = apply_filters('user_email_payment_data', $user_email_data, $form_id, maybe_unserialize($email_template_data));
		$headers = "{$user_email_data['from_name']} <{$user_email_data['from_email']}>\r\n";
		$headers .= "Content-type: text/html";
		if (isset($user_email_data['subject']) || isset($user_email_data['message'])) {
			foreach ($user_email_data['to'] as $email) {
				wp_mail($email, $user_email_data['subject'], $user_email_data['message'], $headers);
			}
		}

		//to form admin
		$admin_email_data['subject'] = "[{$site_name}] Payment succeeded";
		$admin_email_data['message'] = "New payment succeeded from you site {$site_name}.<br/>";
		foreach (maybe_unserialize($data) as $field_name => $entry_value) {
			$admin_email_data['message'] .= "{$field_name}: {$entry_value}<br/>";
		}
		$admin_email_data['to'] = $from_email;
		$admin_email_data['from_email'] = $from_email;
		$admin_email_data['from_name'] = $from_name;
		$admin_email_data = apply_filters('admin_email_payment_data', $admin_email_data, $form_id, maybe_unserialize($email_template_data));
		$headers = "{$admin_email_data['from_name']} <{$admin_email_data['from_email']}>\r\n";
		$headers .= "Content-type: text/html";
		wp_mail($admin_email_data['to'], $admin_email_data['subject'], $admin_email_data['message'], $headers);

		//to form agent
		if ($form_agent_id) {
			$agent_email_data['subject'] = "[{$site_name}] Payment recieved";
			$agent_email_data['message'] = "Succesfully recieved payment to {$site_name} through a form you have been assigned to. Please check back.<br/>";
			foreach (maybe_unserialize($data) as $field_name => $entry_value) {
				$agent_email_data['message'] .= "{$field_name}: {$entry_value}<br/>";
			}
			$agent_email_data['to'] = $form_agent_email;
			$agent_email_data['from_email'] = $from_email;
			$agent_email_data['from_name'] = $from_name;
			$agent_email_data = apply_filters('agent_email_payment_data', $agent_email_data, $form_id, maybe_unserialize($email_template_data));
			$headers = "{$agent_email_data['from_name']} <{$agent_email_data['from_email']}>\r\n";
			$headers .= "Content-type: text/html";
			wp_mail($agent_email_data['to'], $agent_email_data['subject'], $agent_email_data['message'], $headers);
		}
	}

	function entry_has_emails($data) {
		$emails = array();
		if (!is_array($data))
			return $emails;
		foreach ($data as $value) {
			if (is_valid_email($value))
				$emails[] = $value;
		}
		return $emails;
	}

}
