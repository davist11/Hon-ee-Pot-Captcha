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
  var $version          = '0.2';
  var $description      = 'Adds honey pot captcha functionality to the Freeform addon. You will not be able to submit the form with the captcha field filled in.';
  var $settings_exist   = 'y';
  var $docs_url         = 'https://github.com/davist11/Hon-ee-Pot-Captcha';
	var $settings         = array();
	var $settings_default = array(
	  'honeepot_field' => 'honeepot',
	  'honeepot_error' => 'Sorry, but we think you might be a robot.'
	);
	
	function __construct($settings='')
  {
    $this->EE =& get_instance();
    $this->settings = $settings;
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
    return $settings;
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
    $honeepot_field = $this->EE->input->post($this->settings['honeepot_field']);
    if($honeepot_field !== '')
    {
      $errors[] = $this->settings['honeepot_error'];
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
    $honeepot_field = $this->EE->input->post($this->settings['honeepot_field']);
    if($honeepot_field !== '')
    {
      return $this->EE->output->show_user_error('submission', $this->settings['honeepot_error']);
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