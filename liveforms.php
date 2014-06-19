<?php
/*
  Plugin Name: Live Form
  Plugin URI: http://liveform.org
  Description: Live Form - Drag and Drop Form Builder For WordPress.
  Author: Shaon
  Version: 1.1.2
  Author URI: http://liveform.org
 */


define("LF_BASE_DIR", dirname(__FILE__) . "/");
define("LF_BASE_URL", plugins_url("/liveforms/"));
define("LF_UPLOAD_PATH", WP_CONTENT_DIR . '/uploads/');
define('LF_ACTIVATED', true);

// Include libraries
include LF_BASE_DIR . '/libs/advanced-fields.class.php';
include LF_BASE_DIR . '/libs/field_defs.php';
include LF_BASE_DIR . '/libs/form-fields.class.php';
include LF_BASE_DIR . '/libs/functions.php';
include LF_BASE_DIR . '/libs/liveforms-reqlist-paginator.class.php';

class liveforms {

	public $fields_common;
	public $fields_generic;
	public $fields_advaced;
	public $set_methods;

	public static function getInstance() {
		static $instance;
		if ($instance == null) {
			$instance = new self;
		}
		return $instance;
	}

	/**
	 * Constructor function
	 */
	private function __construct() {
		// Public view shortcodes
		add_shortcode('liveform', array($this, 'view_showform'));
		add_shortcode('liveform_agent', array($this, 'view_agent'));
		add_shortcode('liveform_query', array($this, 'view_public_token'));

		// Deploy installer
		register_activation_hook(__FILE__, array($this, 'install'));

		// Activate init hooks
		add_action('init', array($this, 'form_post_type_init'));
		add_action('init', array($this, 'ajax_get_request_list'));
		add_action('init', array($this, 'ajax_submit_reply'));
		add_action('init', array($this, 'ajax_action_submit_form'));
		add_action('init', array($this, 'ajax_submit_change_request_state'));
		add_action('init', array($this, 'show_captcha_image'));

		// Custom UI elements 
		add_action('admin_menu', array($this, 'register_custom_menu_items'));
		add_action('add_meta_boxes', array($this, 'add_meta_box'));
		add_filter('post_row_actions', array($this, 'add_option_showreqs'), 10, 2);
		add_filter('manage_form_posts_columns', array($this, 'add_columns_to_form_list'));
		add_action('manage_form_posts_custom_column', array($this, 'populate_form_list_custom_columns'), 10, 2);
		add_filter("liveform_submitform_thankyou_message", array($this, 'liveform_submitform_thankyou_message'), 10, 1);

		// Liveform bindings
		add_action('save_post', array($this, 'action_save_form'));
		add_action("wp_ajax_get_reqlist", array($this, "action_get_reqlist"));
		add_filter("the_content", array($this, "form_preview"));


		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
		add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
		$this->setup_fields();
	}

	/*
	 * Installer script to create
	 * - Necessary custom tables
	 * - Add additional roles
	 */

	function install() {
		// Invoke wordpress Database object
		global $wpdb;

		// SQLs for creating custom tables
		// Create table to save the "contact requests"/"form entries"
		$sqls[] = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}liveforms_conreqs` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`fid` int(11) NOT NULL,
			`uid` int(11) NOT NULL,
			`data` text NOT NULL,
			`reply_for` int(11) NOT NULL,
			`status` varchar(20) NOT NULL,
			`token` varchar(20) NOT NULL,
			`time` int(11) NOT NULL,
			`agent_id` int(11) NOT NULL,
			`replied_by` varchar(500) NOT NULL,
			PRIMARY KEY (`id`)
        )";

		$sqls[] = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}liveforms_stats` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`fid` int(11) NOT NULL,
			`action` varchar(20) NOT NULL,
			`ip` varchar(30) NOT NULL,
			`time` int(11) NOT NULL,
			PRIMARY KEY (`id`)
        )";


		// Add necessary roles
		// Agent role that helps "agent" users to manage
		// the forms that have been assigned to them
		$agent_caps = array('subscriber');
		add_role('agent', 'Agent', $agent_caps);

		// Execute the SQLs
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		foreach ($sqls as $sql) {
			dbDelta($sql);
		}
	}

	/**
	 * @function        setup_fields
	 * @uses            Add the field definitions
	 *                    - Common Field types
	 *                    - Advanced Field types
	 *                    - Generic Field types
	 *                    - Method Set
	 */
	function setup_fields() {
		$this->fields_common = apply_filters("common_fields", $this->fields_common);
		$this->fields_generic = apply_filters("generic_fields", $this->fields_generic);
		$this->fields_advanced = apply_filters("advanced_fields", $this->fields_advaced);
		$this->set_methods = apply_filters("method_set", $this->set_methods);
	}

	// Custom menu items for the Admin UI
	/**
	 * @function    register_custom_menu_items
	 * @uses        Adds various additional menu and list items to wordpress
	 * @global type $submenu to modify the wordpress menu items
	 */
	function register_custom_menu_items() {
		// Submenu item in the "Forms" menu item
		add_submenu_page('edit.php?post_type=form', __('Form entries'), __('Form entries'), 'manage_options', 'form-entries', array($this, 'admin_view_submitted_forms'));
		add_submenu_page('edit.php?post_type=form', __('Statistics'), __('Statistics'), 'manage_options', 'statistics', array($this, 'admin_view_global_stats'));
		global $submenu;
		// Agent creation panel
		$submenu['edit.php?post_type=form'][501] = array(__('Add agents'), 'manage_options', admin_url('user-new.php'));
	}

	/**
	 * @function add_option_showreqs
	 * @param array $actions Add link to 'Entries' list for a form
	 * @param type $post Get the details of the 'Form'
	 * @return string
	 * @uses Add a link to all the 'Entries' that have been posted through a 'Form'. This link
	 *        is added to the Forms list in the Administration backend
	 */
	function add_option_showreqs($actions, $post) {
		if ($post->post_type == 'form') {
			// Entries finder item for the "Forms" list
			$actions['showreqs'] = "<a class='submitdelete' title='" . esc_attr(__('List all entries')) . "' href='" . admin_url("edit.php?section=requests&post_type=form&page=form-entries&form_id={$post->ID}") . "'>" . __('Entries') . "</a>";
			$actions['showstats'] = "<a class='submitdelete' title='" . esc_attr(__('Statistics')) . "' href='" . admin_url("edit.php?post_type=form&page=statistics&form_id={$post->ID}&ipp=5&paged=1") . "'>" . __('Statistics') . "</a>";
		}
		return $actions;
	}

	/**
	 * @functino is_ajax
	 * @uses Library fucntion to check if an ajax request
	 * is being handled
	 * @return type boolean
	 */
	function is_ajax() {
		return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
				strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
	}

	/**
	 * @function enqueue_scripts
	 * @uses Add the JS and CSS dependencies for loading on the public accessible pages
	 *
	 */
	function enqueue_scripts() {
		wp_enqueue_style("lf_bootstrap_css", LF_BASE_URL . "views/css/bootstrap.min.css");
		wp_enqueue_style("lf_fontawesome_css", LF_BASE_URL . "views/css/font-awesome.min.css");
                wp_enqueue_style("lf_style_css", LF_BASE_URL . "views/css/front.css");
		wp_enqueue_style("lf_breadcrumbs_css", LF_BASE_URL . "views/css/bread-crumbs.css");
		wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
		wp_enqueue_script("jquery");
		wp_enqueue_script('jquery-form');
		wp_register_script('jquery-validation-plugin', 'http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js', array('jquery'));
		wp_enqueue_script('jquery-validation-plugin');
		wp_enqueue_script("lf_bootstrap_js", LF_BASE_URL . "views/js/bootstrap.min.js");
		wp_enqueue_script("lf_mustache_js", LF_BASE_URL . "views/js/mustache.js");
		wp_enqueue_script("jquery-ui-core");
		wp_enqueue_script("jquery-ui-sortable");
		wp_enqueue_script("jquery-ui-datepicker");
		wp_enqueue_script("jquery-ui-draggable");
		wp_enqueue_script("jquery-ui-droppable");
	}

	/**
	 * @function admin_enqueue_scripts
	 * @uses Add the JS and CSS dependencies for loading on the admin accessible sections
	 */
	function admin_enqueue_scripts() {
        $post_type = isset($_GET['post_type'])?$_GET['post_type']:get_post_type();
        if($post_type!='form') return;
		wp_enqueue_style("lf_bootstrap_css", LF_BASE_URL . "views/css/bootstrap.min.css");
		wp_enqueue_style("lf_style_css", LF_BASE_URL . "views/css/style.css");
		wp_enqueue_style("lf_fontawesome_css", LF_BASE_URL . "views/css/font-awesome.min.css");
		wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
		wp_enqueue_script("jquery");
		wp_enqueue_script('jquery-form');
		wp_register_script('jquery-validation-plugin', 'http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js', array('jquery'));
		wp_enqueue_script('jquery-validation-plugin');
		wp_enqueue_script("lf_bootstrap_js", LF_BASE_URL . "views/js/bootstrap.min.js");
		wp_enqueue_script("lf_mustache_js", LF_BASE_URL . "views/js/mustache.js");
		wp_enqueue_script("jquery-ui-core");
		wp_enqueue_script("jquery-ui-sortable");
		wp_enqueue_script("jquery-ui-datepicker");
		wp_enqueue_script("jquery-ui-draggable");
		wp_enqueue_script("jquery-ui-droppable");
	}

	/**
	 * @function add_meta_box
	 * @uses Adds the metaboxes in the 'Form' creation
	 *        section of the Administration dashboard
	 *        -- Form creation panel
	 *        -- Agent selection panel
	 */
	public function add_meta_box($post_type) {
		
                //if (in_array($post_type, $post_types)) {
		// Add the 'Form' creation panel
		add_meta_box(
				'createnew'
				, __("Form builder", 'liveforms')
				, array($this, 'view_createnew')
				, 'form'
				, 'advanced'
				, 'high'
		);
		// Add the 'Agent selection panel'
		add_meta_box(
				'agents'
				, __('Assign agents', 'liveforms')
				, array($this, 'view_list_agents')
				, 'form'
				, 'side'
				, 'high'
		);
	}

	/**
	 * @function form_post_type_init
	 * @uses Initiate the custom post type
	 */
	function form_post_type_init() {
		$form_post_type_labels = array(
			'name' => _x('Forms', 'post type general name', 'liveforms'),
			'singular_name' => _x('Form', 'post type singular name', 'liveforms'),
			'menu_name' => _x('Forms', 'admin menu', 'liveforms'),
			'name_admin_bar' => _x('Form', 'add new on admin bar', 'liveforms'),
			'add_new' => _x('Add New', 'book', 'liveforms'),
			'add_new_item' => __('Add New Form', 'liveforms'),
			'new_item' => __('New Form', 'liveforms'),
			'edit_item' => __('Edit Form', 'liveforms'),
			'view_item' => __('View Form', 'liveforms'),
			'all_items' => __('All Forms', 'liveforms'),
			'search_items' => __('Search Forms', 'liveforms'),
			'parent_item_colon' => __('Parent Forms:', 'liveforms'),
			'not_found' => __('No forms found.', 'liveforms'),
			'not_found_in_trash' => __('No forms found in Trash.', 'liveforms')

		);

		$form_post_type_args = array(
			'labels' => $form_post_type_labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'query_var' => true,
			'rewrite' => array('slug' => 'form'),
			'capability_type' => 'page',
			'has_archive' => true,
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array('title'),
            'menu_icon' => 'dashicons-feedback'
		);

		register_post_type('form', $form_post_type_args);
	}

	/**
	 * @function action_save_form
	 * @uses Save the form after creation through the 'Form' creation panel
	 */
	function action_save_form($post_id) {
		$formadata = isset($_REQUEST['contact'])?$_REQUEST['contact']:null;
		if (count($formadata) > 0 && get_post_type() == 'form') {
			$prev_data = get_post_meta($post_id, 'form_data', $single = true);
			$prev_agent_id = $prev_data['agent'];
			if ((empty($formadata['agent']) && !empty($prev_agent_id)) || (!empty($formadata['agent']) && $formadata['agent'] != $prev_agent_id)) {
				$prev_agent_forms = get_user_meta($user_id = $prev_agent_id, $meta_key = 'form_ids', $single = true);
				if (!empty($prev_agent_forms)) {
					$prev_agent_forms = $prev_agent_forms;
					foreach ($prev_agent_forms as $key => $value) {
						if ($value == $post_id) {
							unset($prev_agent_forms[$key]);
						}
					}
				}
				update_user_meta($user_id = $prev_agent_id, $meta_key = 'form_ids', $meta_value = $prev_agent_forms);
			}

			update_post_meta($post_id, 'form_data', $formadata);
			// Add form to agent's formlist
			if (!empty($formadata['agent'])) {
				$agent_id = $formadata['agent'];
				$prev_forms = get_user_meta($user_id = $agent_id, $meta_key = 'form_ids', $single = true);
				if (empty($prev_forms)) {
					$prev_forms = array($post_id);
				} else {
					$prev_forms = $prev_forms;
					foreach ($prev_forms as $key => $value) {
						if ($value == $post_id) {
							unset($prev_forms[$key]);
						}
					}
					$prev_forms[] = $post_id;
				}
				update_user_meta($user_id = $agent_id, $meta_key = 'form_ids', $meta_value = $prev_forms);
			}
		}
	}

	

	/**
	 * @function ajax_get_request_list
	 * @uses Respond to ajax request for list of "List of entry replies"
	 * @return string HTML output for the table of requests
	 */
	function ajax_get_request_list() {
		if ($this->is_ajax() && isset($_REQUEST['section']) && $_REQUEST['section'] == 'stat_req') {
			$ajax_html = $this->action_get_reqlist($args = array(
				'form_id' => $_REQUEST['form_id'],
				'status' => $_REQUEST['status'],
				'template' => 'showreqs_ajax'
			));
			echo $ajax_html;
			die();
		}
	}

	/**
	 * @function ajax_submit_reply
	 * @uses Respond to ajax request for list of "List of entry replies"
	 * @return string HTML output for the recent reply
	 */
	function ajax_submit_reply() {
		if ($this->is_ajax() && isset($_REQUEST['section']) && $_REQUEST['section'] == 'reply') {
			// Add reply to DB
			$reply_id = $this->handle_replies();
			global $wpdb;
			$reply = $wpdb->get_row("select * from {$wpdb->prefix}liveforms_conreqs where `id`='{$reply_id}'", ARRAY_A);

			if ($reply_id) {
				$image_code = base64_encode($reply['icon']);
				$reply_time = date('Y-m-d H:m', $reply['time']);
				$reply['user_name'] = $_REQUEST['user_name'];
				$ajax_html = " <div class='media thumbnail'><div class='pull-left'>
									<img src='http://www.gravatar.com/avatar/{$image_code}' />
								</div>
								<div class='media-body'>
									<h3 class='media-heading'>{$reply['user_name']}</h3>
									({$reply_time})
									<p>{$reply['data']}</p>
								</div>
							</div>";
				echo $ajax_html;
			} else {
				echo "<<div class='media thumbnail'><div class='pull-left'>"
				. "Sorry!"
				. "</div>"
				. "<div class='media-body'>"
				. "<h3 class='media-heading'>Failed</h3>"
				. "<p>The reply could not be saved</p>"
				. "</div></div>";
			}
			die();
		}
	}

	/**
	 * @function ajax_submit_change_request_state
	 * @uses Respond to ajax request to change the state of a request
	 * @global type $wpdb Wordpress databse object
	 */
	function ajax_submit_change_request_state() {
		if ($this->is_ajax() && isset($_REQUEST['action']) && $_REQUEST['action'] == 'change_req_state') {
			if (isset($_REQUEST['ids'])) {
				$ids = implode(",", $_REQUEST['ids']);
			}
			$args = array();

			if (isset($_REQUEST['status'])) {
				global $wpdb;
				$status = $_REQUEST['status'];
				$query_status = $_REQUEST['query_status'];
				$args['status'] = $query_status;
				$query = '';
				switch ($status) {
					case "delete":
						$query = "delete from {$wpdb->prefix}liveforms_conreqs where `id` in ({$ids})";
						break;
					default:
						$query = "update {$wpdb->prefix}liveforms_conreqs set `status`='{$status}' where `id` in ({$ids})";
				}
				$wpdb->query($query);

				// Get counts
				$get_count_query = "select * from {$wpdb->prefix}liveforms_conreqs where `status`='{$query_status}'";
				$request_count = $wpdb->query($get_count_query, ARRAY_A);
			}

			if (isset($_REQUEST['form_id'])) {
				$form_id = $_REQUEST['form_id'];
				$args['form_id'] = $form_id;
			}

			$args['template'] = 'showreqs_ajax';
			$ajax_html = $this->action_get_reqlist($args);

			$data = array(
				'count' => $request_count,
				'html' => $ajax_html,
				'changed' => isset($_REQUEST['ids']) ? count($_REQUEST['ids']) : 0
			);
			echo json_encode($data);
			die();
		}
	}

	/**
	 * @function view_public_token
	 * @uses Render view for Token/Query entry page
	 * @return type string(html)
	 */
	function view_public_token() {
		$html = '<a href="http://liveform.org/pricing/">Available in pro only</a>';

		return $html;
	}

	/**
	 * @function view_agent
	 * @uses Render view for the agent
	 * @return type string HTML
	 */
	function view_agent() {
		$html = '<a href="http://liveform.org/pricing/">Available in pro only</a>';


		return $html;
	}

	/**
	 * @function view_list_agent
	 * @uses Render view for the list of agents
	 *        in the metabox for 'Agent selection' of a form
	 * @return type string HTML
	 */
	function view_list_agents($post) {
		$formdata = get_post_meta($post->ID, 'form_data', true);
		$html_data = array(
			'agents' => array(),
			'agent_id' => isset($formdata['agent']) ? $formdata['agent'] : null
		);
		$html = $this->get_html('list_agents', $html_data);
		echo $html;
	}

	/**
	 * @function view_agent_requests
	 * @uses Render view for the list of requests
	 *        that are accessilble for the current
	 *        logged in agent user
	 * @return type string HTML
	 */
	function view_agent_requests() {
		$html = "<div class='w3eden'>";
		$html .= '<div class="wrap">';
		if (current_user_can('agent')) {
			$html .= '<div id="icon-tools" class="icon32">'
					. '</div> ';
			if (isset($_REQUEST['form_id'])) {
				$args = array(
					'form_id' => $_REQUEST['form_id'],
					'template' => 'showreqs'
				);
				$html .= $this->action_get_reqlist($args);
			} else {
				$html .= 'You cannot manage this form';
			}
		} else {
			$html .= 'You are not an agent';
		}

		$html .= '</div></div>';

		return $html;
	}

	/**
	 * @function admin_view_submitted_forms
	 * @uses Render view for the list of requests
	 *        for the Admin
	 * @return type string HTML
	 */
	function admin_view_submitted_forms() {
		$forms_list = query_posts('post_type=form');
        $html = '';
		$select_html = "<div class='w3eden'>";
		$select_html .= "<div class='container-fluid'><div class='row row-bottom-buffer'><form class='form' method='post' action='' >";
		$select_html .= '<input type="hidden" name="section" value="requests" />';
		$select_html .= "<div class='col-md-11'><select class='form-control' name='form_id'><option>Select a form</option>";

		foreach ($forms_list as $form) {
			if (isset($_REQUEST['form_id']) && $_REQUEST['form_id'] == $form->ID) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
			$select_html .= "<option {$selected} value='{$form->ID}'>{$form->post_title}</option>";
		}

		$select_html .= '</select></div>';
		$select_html .= "<div class='col-md-1 text-right'><button class='btn btn-primary' type='submit'>Go!</button></div>";
		$select_html .= "</form></div></div>";

		$html .= '<div class="wrap">'
				. '<div id="icon-tools" class="icon32">'
				. '</div> '
				. $select_html;

		if (isset($_REQUEST['section'])) {
			$section = $_REQUEST['section'];
			if ($section == 'requests' && isset($_REQUEST['form_id'])) {
				$args = array(
					'form_id' => $_REQUEST['form_id'],
					'template' => 'admin_showreqs'
				);
				
				$html .= $this->action_get_reqlist($args);
			}
			if ($section == 'request' && isset($_REQUEST['req_id'])) {
				$html .= $this->view_get_request_data($args = array(
					'fid' => $_REQUEST['form_id'],
					'reply_for' => $_REQUEST['req_id'],
					'template' => 'admin_reply_req'
				));
			}
			if ($section == 'reply') {
				$this->handle_replies();
				$html .= $this->view_get_request_data($args = array(
					'fid' => $_REQUEST['form_id'],
					'reply_for' => $_REQUEST['req_id'],
					'template' => 'admin_reply_req'
				));
			}
		}
		$html .= '</div></div>';
		echo $html;
	}

	function admin_view_global_stats() {
		global $wpdb;

		$form_query = 'post_type=form';

		$forms_list = query_posts('post_type=form');
		$max_views = -1;
		$max_submits = -1;

		$max_viewed_form = null;
		$max_submitted_form = null;
		$form_ids = array();

		foreach ($forms_list as $form) {
			$form_ids[$form->ID] = $form->post_title;
			$query = "SELECT * FROM {$wpdb->prefix}liveforms_stats where `fid`='{$form->ID}'";
			$results = $wpdb->get_results($query, ARRAY_A);
			$view_count = get_post_meta($form->ID, 'view_count', true);
			$submit_count = get_post_meta($form->ID, 'submit_count', true);

			if ($view_count > $max_views) {
				$max_views = $view_count;
				$max_viewed_form = array(
					'label' => $form->post_title,
					'value' => $form->ID
				);
			}
			if ($submit_count > $max_submits) {
				$max_submits = $submit_count;
				$max_submitted_form = array(
					'label' => $form->post_title,
					'value' => $form->ID
				);
			}

			foreach ($results as $result) {
				if ($result['action'] == 'v') {
					$view_count_stats[$form->ID][] = array(
						'ip' => $result['ip'],
						'time' => array(
							'second' => date('Y-m-d H:m:s', $result['time']),
							'minute' => date('Y-m-d H:m', $result['time']),
							'hour' => date('Y-m-d h', $result['time']),
							'day' => date('Y-m-d', $result['time']),
							'month' => date('Y-m', $result['time']),
							'year' => date('Y', $result['time'])
						)
					);
				} else {
					$submit_count_stats[$form->ID][] = array(
						'ip' => $result['ip'],
						'time' => array(
							'second' => date('Y-m-d H:m:s', $result['time']),
							'minute' => date('Y-m-d H:m', $result['time']),
							'hour' => date('Y-m-d h', $result['time']),
							'day' => date('Y-m-d', $result['time']),
							'month' => date('Y-m', $result['time']),
							'year' => date('Y', $result['time'])
						)
					);
				}
			}
		}

		$stats = array(
			'max_submitted_form' => array(
				'label' => 'Most submitted form',
				'value' => $max_submitted_form
			),
			'max_viewed_form' => array(
				'label' => 'Most viewed form',
				'value' => $max_viewed_form
			),
			'total_forms' => array(
				'label' => 'Total number of forms',
				'value' => array(
					'label' => 'Total forms',
					'value' => count($forms_list)
				)
			)
		);

		// If a single form was requested
		if (isset($_REQUEST['form_id'])) {
			$selected_form_id = $_REQUEST['form_id'];
		} else {
			$selected_form_id = 'none';
		}

		$html_data = array(
			'views' => json_encode($view_count_stats),
			'submits' => json_encode($submit_count_stats),
			'form_ids' => $form_ids,
			'selected_form_id' => $selected_form_id,
			'stats' => $stats
		);

		

		$html = $this->get_html('stats_global', $html_data);

		echo $html;
	}

	/** View callers * */
	function view_createnew($post) {
		$formdata = get_post_meta($post->ID, 'form_data', $single = true);
		$html_data = array(
			'commonfields' => $this->fields_common,
			'generic_fields' => $this->fields_generic,
			'advanced_fields' => $this->fields_advanced,
			'methods_set' => $this->set_methods,
			'form_post_id' => $post->ID
		);
		if (!empty($formdata)) {
			$html_data['form_data'] = $formdata;
		}
		$view = $this->get_html("createnew", $html_data);
		echo $view;
	}

	function view_showform($params) {
		$form_id = $params['form_id'];
		$formdata = get_post_meta($form_id, 'form_data', $single = true);
		if (!empty($formdata)) {
			$paginated_form = paginate_form($formdata, array(
				'fields_common' => $this->fields_common,
				'fields_generic' => $this->fields_generic,
				'fields_advanced' => $this->fields_advanced
			));
			$html_data = array_merge($paginated_form, array('form_id' => $form_id));
			$view = $this->get_html("showform", $html_data);

			// Record the view
			$this->record_view_stat($form_id, get_client_ip());
		} else {
			$view = "No forms defined";
		}
		return $view;
	}

	/** Action callers * */
	public function ajax_action_submit_form() {
		if ($this->is_ajax() && isset($_REQUEST['action']) && $_REQUEST['action'] == 'submit_form') {
			$form_id = $_REQUEST['form_id'];

			// Update the submit count for this form
			$this->record_submission_stat($form_id, get_client_ip());

			$file_paths = array();
			if (count($_FILES)) {
				foreach ($_FILES['upload']['name'] as $file_index => $file_name) {
					$prepend_key = uniqid("liveforms_", $more_entropy = true) . '_';
					$new_path = LF_UPLOAD_PATH . $prepend_key . $file_name;
					move_uploaded_file($_FILES['upload']['tmp_name'][$file_index], $new_path);
					$file_paths[$file_index] = $new_path;
				}
			}


			$data = isset($_REQUEST['submitform']) ? $_REQUEST['submitform'] : array();
			$form_data = get_post_meta($form_id, 'form_data', $single = true);


			if (count($file_paths)) {
				$data = array_merge($data, $file_paths);
			}
			$data = serialize($data);
			$token = uniqid();
			$emails = $this->entry_has_emails(maybe_unserialize($data));
			$form_entry = array('data' => $data, 'fid' => $form_id, 'status' => 'new', 'token' => $token, 'time' => time());

			$form_entry = apply_filters("liveform_before_form_submit", $form_entry);
                                        
                       
			global $wpdb;

			// Insert the request into the database
			$wpdb->insert(
					"{$wpdb->prefix}liveforms_conreqs", $form_entry
			);

			$submission_id = $wpdb->insert_id;

			do_action("liveform_after_form_submitted", $form_entry, $submission_id);

			//Preparing Email
			//Fetching user infos for email
                                        
			$from_email = $form_data['email'];
			$from_name = $form_data['from'];

			// Prepare entry data for email template injection
			$email_template_data = array_merge(array('fid' => $form_id, 'status' => 'new', 'token' => $token), maybe_unserialize($data));

			//to user
			$site_name = get_bloginfo('name');
			$user_email_data['subject'] = "[{$site_name}] Thanks for contacting with us";
			$user_email_data['message'] = "Thanks for your visit to {$site_name}. We are glad that you contacted with us. To gain further access to your submitted request, use this token: [ {$token} ]";
			$user_email_data['to'] = $emails;
			$user_email_data['from_email'] = $from_email;
			$user_email_data['from_name'] = $from_name;

			$user_email_data = apply_filters('user_email_data', $user_email_data, $form_id, maybe_unserialize($email_template_data));
			$headers = "{$user_email_data['from_name']} <{$user_email_data['from_email']}>\r\n";
			$headers .= "Content-type: text/html";
			if (isset($user_email_data['subject']) || isset($user_email_data['message'])) {
				foreach ($user_email_data['to'] as $email) {
					wp_mail($email, $user_email_data['subject'], $user_email_data['message'], $headers);
				}
			}

			//to form admin
			$admin_email_data['subject'] = "[{$site_name}] Form submitted";
			$admin_email_data['message'] = "New form submission on you site {$site_name}.\n";
			foreach (maybe_unserialize($data) as $field_name => $entry_value) {
				$admin_email_data['message'] .= "{$field_name}: {$entry_value}\n";
			}
			$admin_email_data['to'] = $from_email;
			$admin_email_data['from_email'] = $from_email;
			$admin_email_data['from_name'] = $from_name;
			$admin_email_data = apply_filters('admin_email_data', $admin_email_data, $form_id, maybe_unserialize($email_template_data));
			$headers = "{$admin_email_data['from_name']} <{$admin_email_data['from_email']}>\r\n";
			$headers .= "Content-type: text/html";
			wp_mail($admin_email_data['to'], $admin_email_data['subject'], $admin_email_data['message'], $headers);

                                        

			// Increment the form submit count by 1
			// $this->form_submit_count($form_id);

			$data = maybe_unserialize($data);
                                        

            $return_data = array();
            $return_data['message'] = apply_filters("liveform_submitform_thankyou_message",stripslashes($form_data['thankyou']));
            $return_data['action'] = 'success';
            echo json_encode($return_data);
			die();
		}
	}



	/** Library to get template * */
	function get_html($view, $html_data) {
		if (empty($view))
			return null;
		extract($html_data);
		ob_start();
		include(LF_BASE_DIR . "views/{$view}.php");
		$data = ob_get_clean();
		return $data;
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

	function view_get_request_data($args = array()) {
		global $wpdb;
		// initialize view output

		if (!$args || count($args) == 0) {
			return "No requests found";
		}

		if (isset($args['template'])) {
			$template_name = $args['template'];
			unset($args['template']);
		}
		$html = '';
		// Build the query
		$request_data_query = "select * from {$wpdb->prefix}liveforms_conreqs where ";
		$tmp = array();
		foreach ($args as $key => $value) {
			$tmp[] = "`{$key}`='{$value}'";
		}
		$args_query = implode(" and ", $tmp);
		$request_data_query .= $args_query;


		// Check if token was used to access the response
		// If token is used then fetch the reqply_history using the id of the token
		if (isset($args['token'])) {
			$reply_db_fetch = $wpdb->get_row($request_data_query, ARRAY_A);

			// Terminate further execution since token enquiry is invalid
			if (count($reply_db_fetch) < 1) {
				return "No requests found";
			}

			$args = array(); // rebuild args for second query
			$args['fid'] = $reply_db_fetch['fid'];
			$args['reply_for'] = $reply_db_fetch['id'];
			$request_data_query = "select * from {$wpdb->prefix}liveforms_conreqs where ";
			$tmp = array();
			foreach ($args as $key => $value) {
				$tmp[] = "`{$key}`='{$value}'";
			}
			$args_query = implode(" and ", $tmp);
			$request_data_query .= $args_query;
		}

		$request_data_query .= " order by `time` desc";
		$reply_db_fetch = $wpdb->get_results($request_data_query, ARRAY_A);

		$req_data = $wpdb->get_row("select * from {$wpdb->prefix}liveforms_conreqs where `id`='{$args['reply_for']}'", ARRAY_A);
		$form_data = get_post_meta($post_id = $args['fid'], $meta_key = 'form_data', $single = true);
		$field_values = unserialize($req_data['data']);
		$reply_user_name = '';
		foreach ($form_data['fields'] as $key => $field) {
			if ($field == 'name') {
				$reply_user_name = $field_values[$key];
			}
		}

		if (!isset($_REQUEST['token'])) {
			$current_user = wp_get_current_user();
			$user_name = $current_user->user_login;
		}

		$reply_history = array();
		foreach ($reply_db_fetch as $reply) {
			if ($reply['replied_by'] == 'user') {
				if ($reply['uid'] == -1) {
					$tmp_user = null;
				} else {
					$tmp_user = get_userdata(intval($reply['uid']));
				}
			} else {
				$tmp_user = get_userdata(intval($reply['agent_id']));
			}
			$tmp_reply = $reply;
			$tmp_reply['username'] = $tmp_user != null ? $tmp_user->user_login : $reply_user_name;
			$tmp_reply['icon'] = md5(strtolower(trim($tmp_user->user_email)));
			$reply_history[] = $tmp_reply;
		}
		$html_data = array();
		$html_data['reply_history'] = $reply_history;
		$html_data['form_fields'] = $form_data['fieldsinfo'];
		$html_data['field_values'] = $field_values;
		$html_data['req_data'] = $req_data;
		$html_data['current_user_name'] = isset($_REQUEST['token']) ? $reply_user_name : $user_name;

		$html .= $this->get_html(isset($template_name) ? $template_name : 'reply_req', $html_data);
		return $html;
	}

	function handle_replies() {
		global $wpdb;
		$user_id = is_user_logged_in() ? get_current_user_id() : -1;
		$reply_data = array();
		if (!current_user_can('agent') && !current_user_can('manage_options')) {
			$reply_data['uid'] = $user_id;
			$reply_data['replied_by'] = "user";
		} else {
			$reply_data['agent_id'] = $user_id;
			$reply_data['replied_by'] = "agent";
		}
		$reply_data['data'] = $_REQUEST['reply_msg'];
		$reply_data['reply_for'] = $_REQUEST['req_id'];
		$reply_data['fid'] = $_REQUEST['form_id'];
		$reply_data['time'] = time();

		if ($_REQUEST['req_status'] == "new") { // no previous replies have been issued
			$request_status_update_query = "update {$wpdb->prefix}liveforms_conreqs set `status`='inprogress' where `id`='{$_REQUEST['req_id']}'";
			$wpdb->query($request_status_update_query);
		}
		$sql_part = '';
		$tmp_sqls_parts = array();
		foreach ($reply_data as $key => $value) {
			$tmp_sqls_parts[] = "`{$key}`='{$value}'";
		}
		$sql_part = implode(", ", $tmp_sqls_parts);
		$reply_add_query = "insert into {$wpdb->prefix}liveforms_conreqs set {$sql_part}";
		$wpdb->query($reply_add_query);

		return $wpdb->insert_id;
	}

	function action_get_reqlist($args) {
		global $wpdb;

		$form_id = $args['form_id'];

		if (!isset($args['fid'])) {
			$args['fid'] = $form_id;
			unset($args['form_id']);
		}

		if (isset($args['template'])) {
			$template_name = $args['template'];
			unset($args['template']);
		}

		
		$count_query_prefix = "select count(*) from {$wpdb->prefix}liveforms_conreqs where ";
		$query_prefix = "select * from {$wpdb->prefix}liveforms_conreqs where ";
		$query_args = array();

		foreach ($args as $key => $value) {
			$query_args[] = "`{$key}` = '{$value}'";
		}

		$query_suffix = implode(" and ", $query_args);
		if (!isset($args['token'])) {
			$query_suffix .= " and `token` != ''";
		}
		$query = $query_prefix . $query_suffix;
		$count_query = $count_query_prefix . $query_suffix;
		$req_count = $wpdb->get_row($count_query, ARRAY_A);

		// Counting query states [new, inprogress, onhold, resolved]
		$new_request_query = $count_query_prefix . " `status`='new'";
		$new_request_count = $wpdb->get_row($new_request_query, ARRAY_A);
		$inprogress_request_query = $count_query_prefix . " `status`='inprogress'";
		$inprogress_request_count = $wpdb->get_row($inprogress_request_query, ARRAY_A);
		$onhold_request_query = $count_query_prefix . " `status`='onhold'";
		$onhold_request_count = $wpdb->get_row($onhold_request_query, ARRAY_A);
		$resolved_request_query = $count_query_prefix . " `status`='resolved'";
		$resolved_request_count = $wpdb->get_row($resolved_request_query, ARRAY_A);

		//Pagination
		$items_per_page = isset($_REQUEST['ipp']) ? $_REQUEST['ipp'] : 5;
		$page_id = isset($_REQUEST['paged']) ? intval($_REQUEST['paged']) - 1 : 0 ;
		$starting_item = intval($page_id) * intval($items_per_page);
		$query .= " limit {$starting_item}, {$items_per_page}";

		$form_meta = get_post_meta($form_id, 'form_data', true);
		$form_title = get_post_field('post_title', $form_id);

		$reqlist = $wpdb->get_results($query, ARRAY_A);

		$form = array(
			'id' => $form_id,
			'title' => $form_title
		);

		$counts = array(
			'inprogress' => $inprogress_request_count['count(*)'],
			'new' => $new_request_count['count(*)'],
			'resolved' => $resolved_request_count['count(*)'],
			'onhold' => $onhold_request_count['count(*)']
		);
		
		if (empty($reqlist)) {
			return 'No requests found';
		}

		$html_data = array(
			'form' => $form,
			'form_fields' => $form_meta['fieldsinfo'],
			'reqlist' => $reqlist,
			'counts' => $counts,
			'total_request' => $req_count['count(*)'],
		);
		$form_html = $this->get_html(isset($template_name) ? $template_name : 'showreqs', $html_data);
		return $form_html;
	}

	function add_columns_to_form_list($column) {
		$column['form_id'] = 'Shortcode';
		$column['view_count'] = 'Views';
		$column['submit_count'] = 'Submissions';

		return $column;
	}

	function populate_form_list_custom_columns($column_name, $post_id) {
		$custom_field = get_post_custom($post_id);
		$view_count = get_post_meta($post_id, 'view_count', true) == '' ? 0 : get_post_meta($post_id, 'view_count', true);
		$submit_count = get_post_meta($post_id, 'submit_count', true) == '' ? 0 : get_post_meta($post_id, 'submit_count', true);
		switch ($column_name) {
			case 'form_id':
				echo "<input type='text' readonly='readonly' value='[liveform form_id={$post_id}]'/>";
				break;
			case 'view_count':
				echo $view_count;
				break;
			case 'submit_count':
				echo $submit_count;
				break;
			default:
		}
	}

	function form_preview($content) {
		if (get_post_type() != "form")
			return $content;
		return do_shortcode("[liveform form_id='" . get_the_ID() . "']");
	}

	function show_captcha_image() {
		if (isset($_REQUEST['show_captcha'])) {
			$coj = new SimpleCaptcha();
			$coj->CreateImage();
			die();
		}
	}

	function payment_fields($submission) {
		$payment_fields = array();
		foreach ($submission as $key => $value) {
			if (strstr($key, 'payment_')) {
				$payment_fields = array('field' => $key,
					'method' => $submission[$key]
				);
				return $payment_fields;
			}
		}

		return null;
	}

	function has_payment_fields($submission) {
		foreach ($submission as $key => $value) {
			if (strstr($key, 'payment_')) {
				return true;
			}
		}

		return false;
	}

	function record_view_stat($form_id, $ip = 'not acquired') {
		global $wpdb;

		$view_count = get_post_meta($form_id, 'view_count', true);
		if ($view_count == '') {
			$view_count = 0;
		}
		update_post_meta($form_id, 'view_count', $view_count + 1);

       	$current_time = time();
		$wpdb->query("INSERT into {$wpdb->prefix}liveforms_stats SET `fid`='{$form_id}', `action`='v', `ip`='{$ip}', `time`='{$current_time}' ");
	}

	function record_submission_stat($form_id, $ip = 'not acquired') {
		global $wpdb;

		$submit_count = get_post_meta($form_id, 'submit_count', true);
		if ($submit_count == '') {
			$submit_count = 0;
		}
		update_post_meta($form_id, 'submit_count', $submit_count + 1);

		$current_time = time();
		$wpdb->query("INSERT into {$wpdb->prefix}liveforms_stats SET `fid`='{$form_id}', `action`='s', `ip`='{$ip}', `time`='{$current_time}' ");
	}

	function liveform_submitform_thankyou_message($message) {
		return $message;
	}

}

/** Initialize * */
//new liveforms();

liveforms::getInstance();
