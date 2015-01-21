<?php

/* * ********************************************************************
 *  DirectVPS (2013-09-13)
 * *
 *
 *  CREATED BY MODULESGARDEN       ->        http://modulesgarden.com
 *  CONTACT                        ->       contact@modulesgarden.com
 *
 *
 *
 *
 * This software is furnished under a license and may be used and copied
 * only  in  accordance  with  the  terms  of such  license and with the
 * inclusion of the above copyright notice.  This software  or any other
 * copies thereof may not be provided or otherwise made available to any
 * other person.  No title to and  ownership of the  software is  hereby
 * transferred.
 *
 *
 * ******************************************************************** */

/**
 * @author Grzegorz Draganik <grzegorz@modulesgarden.com>
 * @author Mariusz Miodowski <mariusz@modulesgarden.com>
 */

abstract class MG_Abstract_Hosting {

	//Hosting ID
	public $id = 0;
       public $hosting_details = array();
	//product Detaild
	public $product_details = array();
	//custom fields
	public $custom_fields = array();
	//configurable options
	public $configurable_options = array();
	//server configuration
	public $server_details = array();

	public function __construct($id) {
		$this->id = $id;

              $this->load();
	}

	public function update(array $values) {
		$sets = array();
		foreach ($values as $k => $v) {
			$v = is_numeric($v) ? $v : '"' . mysql_real_escape_string($v) . '"';
			$sets[] = $k . '=' . $v;
		}

		return mysql_query('UPDATE tblhosting SET ' . implode(',', $sets) . ' WHERE id = ' . (int) $this->id);
	}

	public function load() {
		//Get Hosting Details
		$q = mysql_get_row("SELECT * FROM tblhosting WHERE id = ?", array($this->id));
		foreach ($q as $key => &$val) {
			$this->hosting_details[$key] = $val;
		}
		$this->hosting_details['password'] = decrypt($this->hosting_details['password']);

		//Get Custom fields
		$q = mysql_get_array('SELECT cf.id, cf.fieldname, cfv.value
                    FROM tblcustomfields AS cf
                    JOIN tblcustomfieldsvalues AS cfv ON cfv.fieldid = cf.id
                    WHERE cf.type = "product" AND cfv.relid = ' . (int) $this->id . '');
		foreach ($q as $key => &$val) {
			if (strpos($val['fieldname'], '|')) {
				$this->custom_fields[substr($val['fieldname'], 0, strpos($val['fieldname'], '|'))] = $val['value'];
			} else {
				$this->custom_fields[$val['fieldname']] = $val['value'];
			}
		}

		//Get Server Configuration
		if ($this->hosting_details['server']) {
			$q = mysql_get_row("SELECT * FROM tblservers WHERE id = ?", array($this->hosting_details['server']));
			foreach ($q as $key => &$val) {
				$this->server_details[$key] = $val;
			}
		}

		//Get Configurable Options
		$config_options = mysql_get_array('
			SELECT
				co.optionname AS config_option_name,
				cos.optionname AS option_name
			FROM tblhostingconfigoptions AS coh
			JOIN tblproductconfigoptions AS co
			JOIN tblproductconfigoptionssub AS cos
			WHERE coh.relid = ' . (int) $this->id);

		foreach ($config_options as $key => &$val) {
			$this->configurable_options[self::getFirstAndLastName($val['config_option_name'])] = self::getFirstAndLastName($val['option_name']);
		}
	}

	/** ******************************
	 *          GETTERS
	 * ***************************** */

	/**
	 * Get Hosting Details
	 * @param type $key
	 * @return boolean
	 */
	public function getDetails($key = null) {
		if (isset($this->hosting_details[$key])) {
			return $this->hosting_details[$key];
		}

		return false;
	}

	/**
	 * Get Custom Field
	 * @param type $key
	 * @return boolean
	 */
	public function getCustomField($key) {
		if (isset($this->custom_fields[$key])) {
			return $this->custom_fields[$key];
		}

		return false;
	}

	public function setCustomField($fieldname, $value){
		$customField = mysql_fetch_assoc(mysql_query_safe('
			SELECT f.id
			FROM tblcustomfields AS f
			JOIN tblproducts AS p ON f.type = "product" AND f.relid = p.id
			WHERE p.id = ? AND (f.fieldname = ? OR f.fieldname LIKE ?)
		', array(
			$this->hosting_details['packageid'],
			$fieldname,
			$fieldname.'|%',
		)));
		
		if (empty($customField))
			return false;
		
		mysql_query_safe('DELETE FROM tblcustomfieldsvalues WHERE fieldid = ? AND relid = ?', array($customField['id'], $this->id));
		return mysql_query_safe('INSERT INTO tblcustomfieldsvalues(fieldid,relid,value) VALUES(?,?,?)', array(
			$customField['id'], $this->id, $value
		));
	}
	
	public function getCustomFields($fieldname = null){
		$fields = array();
		$q = mysql_query('
			SELECT cf.id, cf.fieldname, cfv.value
			FROM tblcustomfields AS cf
			JOIN tblcustomfieldsvalues AS cfv ON cfv.fieldid = cf.id
			WHERE cf.type = "product" AND cfv.relid = '.(int)$this->id.'
		');
		while ($row = mysql_fetch_assoc($q)){
                    if( strpos($row['fieldname'], '|')!== false){
                          $ex = explode("|", $row['fieldname']);
                          $row['fieldname'] = current( $ex );
                    }
		     $fields[$row['fieldname']] = $row;
		}
		return $fields;
	}

	public static function getFirstAndLastName($str, $first = true) {
		$pos = strpos($str, '|');
		if ($pos) {
			return $first ? substr($str, 0, $pos) : substr($str, $pos);
		} else {
			return $str;
		}
	}

}
