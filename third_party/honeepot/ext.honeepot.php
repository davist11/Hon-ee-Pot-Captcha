<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Hon-ee Pot Captcha Extension Class for ExpressionEngine 2
 *
 * @package     ExpressionEngine
 * @subpackage  Hon-ee Pot Captcha
 * @category    Extensions
 * @author      Trevor Davis
 * @link        http://trevordavis.net/
 */

class Honeepot_ext {

	var $name             = 'Hon-ee Pot Captcha';
	var $version          = '0.15';
	var $description      = 'Adds honey pot captcha functionality to the Freeform addon, comments, register, login, Zoo Visitor Registration addon, ProForm addon, Safecraker addon, and Solspace User addon. You will not be able to submit the form with the captcha field filled in.';
	var $settings_exist   = 'y';
	var $docs_url         = 'https://github.com/davist11/Hon-ee-Pot-Captcha';
	var $settings         = array();
	var $settings_default = array(
		'honeepot_field' => 'honeepot',
		'honeepot_error' => 'Sorry, but we think you might be a robot.',
		'honeepot_log_rejections' => 'n'
	);
	var $package		  = 'honeepot';


	function __construct($settings='')
	{
		$this->EE =& get_instance();
		$this->settings = $settings;

		//Allow for config overrides
		$this->apply_config_overrides();
	}

	/**
	 * Settings
	 *
	 * This function returns the settings for the extensions
	 *
	 * @return settings array
	 */
	function settings()
	{
		$settings['honeepot_field'] = $this->settings_default['honeepot_field'];
		$settings['honeepot_error'] = $this->settings_default['honeepot_error'];
		$settings['honeepot_log_rejections'] = array('r', array('y' => 'Yes', 'n' => 'No'), $this->settings_default['honeepot_log_rejections']);

		return $settings;
	}

	/**
	 * Config Overrides
	 *
	 * This function will merge with config overrides
	 *
	 * @return void
	 */
	function apply_config_overrides()
	{
		// init
		$config_items = array();

		if($this->EE->config->item('honeepot_field')) {
			$config_items['honeepot_field'] = $this->EE->config->item('honeepot_field');
		}

		if($this->EE->config->item('honeepot_error')) {
			$config_items['honeepot_error'] = $this->EE->config->item('honeepot_error');
		}

		if($this->EE->config->item('honeepot_log_rejections')) {
			$config_items['honeepot_log_rejections'] = $this->EE->config->item('honeepot_log_rejections');
		}

		if(is_array($this->settings)) {
			$this->settings = array_merge($this->settings, $config_items);
		}
	}

	/**
	 * Freeform Validation
	 *
	 * If the hon-ee pot field is filled in on a Freeform form, this will return an error
	 *
	 * @return errors array
	 */
	function validate($errors)
	{
		$honeepot_field = $this->EE->input->post($this->settings['honeepot_field'], TRUE);

		if($honeepot_field !== '' && $honeepot_field !== FALSE)
		{
			$errors[] = $this->settings['honeepot_error'];

			$this->_log('Solspace Freeform');
		}

		return $errors;
	}


	/**
	 * Comment form Validation
	 *
	 * If the hon-ee pot field is filled in on a comment form, this will return an error
	 *
	 * @return void
	 */
	function validate_comment()
	{
		$honeepot_field = $this->EE->input->post($this->settings['honeepot_field'], TRUE);

		if($honeepot_field !== '' && $honeepot_field !== FALSE)
		{
			$this->_log('Comment');

			return $this->EE->output->show_user_error('submission', $this->settings['honeepot_error']);
		}
	}


	/**
	 * Safecracker form Validation
	 *
	 * If the hon-ee pot field is filled in on a safecracker form, this will return an error
	 *
	 * @return errors array
	 */
	function validate_safecracker($safecracker)
	{
		$honeepot_field = $this->EE->input->post($this->settings['honeepot_field'], TRUE);

		if($honeepot_field !== '' && $honeepot_field !== FALSE)
		{
			$safecracker->errors[] = $this->settings['honeepot_error'];

			$this->_log('Safecracker');
		}

		return $safecracker;
	}


	/**
	 * Zoo Visitor form Validation
	 *
	 * If the hon-ee pot field is filled in on the zoo visitor registration form, this will return an error
	 *
	 * @return errors array
	 */
	function validate_zoo_visitor($errors)
	{
		$honeepot_field = $this->EE->input->post($this->settings['honeepot_field'], TRUE);

		if($honeepot_field !== '' && $honeepot_field !== FALSE)
		{
			$errors['captcha'] = $this->settings['honeepot_error'];

			$this->_log('Zoo Visitor');
		}

		return $errors;
	}


	/**
	 * ProForm Validation
	 *
	 * If the hon-ee pot field is filled in on the pro form form, this will return an error
	 *
	 * @return array with form object and form session
	 */
	function validate_proform($module, $form_obj, $form_session)
	{
		$honeepot_field = $this->EE->input->post($this->settings['honeepot_field'], TRUE);

		if($honeepot_field !== '' && $honeepot_field !== FALSE)
		{
			$this->EE->output->show_user_error('submission', $this->settings['honeepot_error']);

			$this->_log('ProForm');
		}

		return array($form_obj, $form_session);
	}


	/**
	 * Member Registration Validation
	 *
	 * If the hon-ee pot field is filled in on the member registration form, this will return an error
	 *
	 * @return void
	 */
	function validate_member_register($member)
	{
		$honeepot_field = $this->EE->input->post($this->settings['honeepot_field'], TRUE);

		if($honeepot_field !== '' && $honeepot_field !== FALSE) {
			$member->errors[] = $this->settings['honeepot_error'];

			$this->_log('Member Registration');
		}
	}

	/**
	 * Member Login Validation
	 *
	 * If the hon-ee pot field is filled in on the member login form, this will return an error
	 *
	 * @return void
	 */
	function validate_member_login()
	{
		$honeepot_field = $this->EE->input->post($this->settings['honeepot_field'], TRUE);

		if($honeepot_field !== '' && $honeepot_field !== FALSE) {
			$this->EE->output->show_user_error('submission', $this->settings['honeepot_error']);

			$this->_log('Member Login');
		}
	}

	/**
	* Solspace User Register form Validation
	*
	* If the hon-ee pot field is filled in on a User register form, this will return an error
	*
	* @return errors array
	*/
	function validate_user_register()
	{
		$honeepot_field = $this->EE->input->post($this->settings['honeepot_field'], TRUE);

		if($honeepot_field !== '' && $honeepot_field !== FALSE) {
			$errors[] = $this->settings['honeepot_error'];

			$this->_log('Solspace User Registration');
		}

		return $errors;
	}

	/**
	* Log to the developer log if the setting is turned on
	*
	* @return void
	*/
	function _log($type)
	{
		if($this->settings['honeepot_log_rejections'] === 'y')
		{
			$this->EE->load->library('logger');
			$this->EE->load->library('user_agent');

			$this->EE->logger->developer('Honeepot rejected submission on ' . $type . ' form at ' . $this->EE->agent->referrer() . ' from ' . $_SERVER['REMOTE_ADDR']);
		}
	}


	/**
	 * Activate Extension
	 *
	 * This function enters the extension into the exp_extensions table
	 *
	 * @see http://codeigniter.com/user_guide/database/index.html for
	 * more information on the db class.
	 *
	 * @return void
	 */
	function activate_extension()
	{
		$data = array(
			'class'       => __CLASS__,
			'hook'        => 'freeform_module_validate_end',
			'method'      => 'validate',
			'settings'    => serialize($this->settings()),
			'priority'    => 10,
			'version'     => $this->version,
			'enabled'     => 'y'
		);

		// insert in database
		$this->EE->db->insert('extensions', $data);

		$data = array(
			'class'       => __CLASS__,
			'hook'        => 'insert_comment_start',
			'method'      => 'validate_comment',
			'settings'    => serialize($this->settings()),
			'priority'    => 10,
			'version'     => $this->version,
			'enabled'     => 'y'
		);

		// insert in database
		$this->EE->db->insert('extensions', $data);

		$data = array(
			'class'       => __CLASS__,
			'hook'        => 'safecracker_submit_entry_start',
			'method'      => 'validate_safecracker',
			'settings'    => serialize($this->settings()),
			'priority'    => 10,
			'version'     => $this->version,
			'enabled'     => 'y'
		);

		// insert in database
		$this->EE->db->insert('extensions', $data);

		$data = array(
			'class'       => __CLASS__,
			'hook'        => 'zoo_visitor_register_validation_start',
			'method'      => 'validate_zoo_visitor',
			'settings'    => serialize($this->settings()),
			'priority'    => 10,
			'version'     => $this->version,
			'enabled'     => 'y'
		);

		// insert in database
		$this->EE->db->insert('extensions', $data);

		$data = array(
			'class'       => __CLASS__,
			'hook'        => 'proform_validation_start',
			'method'      => 'validate_proform',
			'settings'    => serialize($this->settings()),
			'priority'    => 10,
			'version'     => $this->version,
			'enabled'     => 'y'
		);

		// insert in database
		$this->EE->db->insert('extensions', $data);

		$data = array(
			'class'       => __CLASS__,
			'hook'        => 'member_member_register_errors',
			'method'      => 'validate_member_register',
			'settings'    => serialize($this->settings()),
			'priority'    => 10,
			'version'     => $this->version,
			'enabled'     => 'y'
		);

		// insert in database
		$this->EE->db->insert('extensions', $data);

		$data = array(
			'class'       => __CLASS__,
			'hook'        => 'member_member_login_start',
			'method'      => 'validate_member_login',
			'settings'    => serialize($this->settings()),
			'priority'    => 10,
			'version'     => $this->version,
			'enabled'     => 'y'
		);

		// insert in database
		$this->EE->db->insert('extensions', $data);

		$data = array(
			'class'       => __CLASS__,
			'hook'        => 'user_register_error_checking',
			'method'      => 'validate_user_register',
			'settings'    => serialize($this->settings()),
			'priority'    => 10,
			'version'     => $this->version,
			'enabled'     => 'y'
		);

		// insert in database
		$this->EE->db->insert('extensions', $data);
	}


	/**
	 * Update Extension
	 *
	 * This function performs any necessary db updates when the extension
	 * page is visited
	 *
	 * @return 	mixed	void on update / false if none
	 */
	function update_extension($current = '')
	{
		if ($current == '' || $current == $this->version)
		{
			return FALSE;
		}

		if ($current < '0.1')
		{
			// Update to version 1.0
		}

		$this->EE->db->where('class', __CLASS__);
		$this->EE->db->update(
			'extensions',
			array('version' => $this->version)
		);
	}


	/**
	 * Disable Extension
	 *
	 * This method removes information from the exp_extensions table
	 *
	 * @return void
	 */
	function disable_extension()
	{
		$this->EE->db->where('class', __CLASS__);
		$this->EE->db->delete('extensions');
	}

}

/* End of file ext.honeepot.php */
/* Location: ./system/expressionengine/third_party/honeepot/ext.honeepot.php */