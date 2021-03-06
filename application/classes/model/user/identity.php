<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Model for User Identities (RiverID)
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.swiftly.org
 * @subpackage Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Model_User_Identity extends ORM {
	
	/**
	 * An identity belongs to a user
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'user' => array()
		);

	/**
	 * Rules for the user identity.
	 * @return array Rules
	 */
	public function rules ()
	{
		return array(
			'user_id' => array(
				array('not_empty'), 
				array('numeric')
			), 
			'provider' => array(
				array('not_empty'), 
				array('max_length', array(':value', 255) )
			), 
			'identity' => array(
				array('not_empty'), 
				array('max_length', array(':value', 255) ), 
				array(array($this, 'unique_identity'), array(':validation', ':field') ) 
			)
		);
	}

	/**
	 * Triggers error if identity exists.
	 * Validation callback.
	 *
	 * @param	Validation	Validation object
	 * @param	string	  field name
	 * @return	void
	 */
	public function unique_identity (Validation $validation, $field)
	{
		$identity_exists = (bool) DB::select(array('COUNT("*")', 'total_count'))
			->from($this->_table_name)
			->where('identity', '=', $validation['identity'])
			->and_where('provider', '=', $validation['provider'])
			->execute($this->_db)
			->get('total_count');
		if ($identity_exists)
		{
			$validation->error($field, 'identity_available', array($validation[$field]));
		}
	}
}