<?php

/* * ********************************************************************
 * Custom developed. (2014-09-05)
 * *
 *
 *  CREATED BY MODULESGARDEN       ->       http://modulesgarden.com
 *  CONTACT                        ->       contact@modulesgarden.com
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
 */
abstract class MG_Abstract_Product {

	//Product ID
	public $id;
	//Product Configuration. Should be created in child
	public $defaultConfig = array();
	//Default table
	protected $_tableName = '';
	protected $_config = null;

	public function __construct($id, array $data = array()) {
		//Get child name
		$child = get_class($this);

		//Set up default table name
		$this->_tableName = strtolower('mg_' . $child);

		//Copy Params
		foreach ($data as $k => $v) {
			$this->$k = $v;
		}
		$this->id = (int) $id;
              if($id && empty($data)){
                    $this->load();
              }
	}
       /**
        * Auto Config
        */
	public function runAutoConfiguration() {
		//Generate Custom Fields
		if ($_REQUEST['modaction'] == 'generate_custom_fields') {
			ob_clean();
			$ret = $this->generateDefaultCustomField();
			$json = array();
			if ($ret) {
				$json['status'] = 1;
				$json['message'] = 'Custom Fields Generated';
			} else {
				$json['status'] = 0;
				$json['message'] = 'Custom Fields Already Generated';
			}

			echo json_encode($json);
			die();
		}
		//Generate Configurable Options
		elseif ($_REQUEST['modaction'] == 'generate_configurable_options') {
			ob_clean();
			$ret = $this->generateDefaultConfigurableOptions();
			$json = array();
			if ($ret) {
				$json['status'] = 1;
				$json['message'] = 'Configurable Options Generated';
			} else {
				$json['status'] = 0;
				$json['message'] = 'Configurable Options Already Generated';
			}

			echo json_encode($json);
			die();
		}
	}

	/**
	 *  Save Product Configuration
	 * @param type $customconfigoption
	 */
	public function saveConfigOptions($customconfigoption) {
		$this->clearConfig();

		foreach ($customconfigoption as $k => $v) {
			// hidden field for checkbox -> save checkbox value
			if (strpos($k, 'hidden_') === 0 && isset($this->defaultConfig[substr($k, 7)])){
				$this->saveConfig(substr($k, 7), $v);
			} elseif (isset($this->defaultConfig[$k]['type']) && $this->defaultConfig[$k]['type'] == 'checkbox'){
				continue;
			} else {
				$this->saveConfig($k, $v);
			}
		}
	}

	/**
	 *  Generate Default Custom Fieds
	 * @return boolean
	 */
	public function generateDefaultCustomField() {
		foreach ($this->defaultCustomField as $key => $field) {
			$q = mysql_safequery("SELECT id, relid, fieldname
			FROM tblcustomfields
			WHERE
				relid = " . $this->id . "
				AND type = 'product'
				AND (fieldname = '" . $key . "' OR fieldname LIKE '" . $key . "|%') ");

			if (mysql_num_rows($q)) {
				return false;
			}

			switch ($field['type']) {
				case 'text':
					mysql_safequery('INSERT INTO tblcustomfields(type,relid,fieldname,fieldtype,description,fieldoptions,regexpr,adminonly,required,showorder,showinvoice,sortorder)
			VALUES("product", ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
						$this->id,
						$key . '|' . $field['title'],
						"text",
						$field['description'] ? $field['description'] : '',
						$field['fieldoptions'] ? $field['fieldoptions'] : '',
						$field['regexpr'] ? $field['regexpr'] : '',
						$field['adminonly'] ? 'on' : '',
						$field['required'] ? 'on' : '',
						$field['showorder'] ? 'on' : '',
						$field['showinvoice'] ? 'on' : '',
						$field['sortorder'] ? ' on' : ''
					));
					break;
				case 'quantity':
					mysql_safequery('INSERT INTO tblproductconfigoptions(gid,optionname,optiontype,qtyminimum,qtymaximum,`order`,hidden) VALUES(?,?,4,?,?,0,0)', array(
						$group_id,
						$field_key . '|' . $field['title'],
						$field['min'],
						$field['max'],
					));
					break;
				default:
					die('Unsupported type!');
			}
		}

		return true;
	}

	/**
	 * Generate Default Configurable Options
	 */
	public function generateDefaultConfigurableOptions() {
		if ($this->hasConfigurableOptions()) {
			return false;
		}

		foreach ($this->defaultConfigurableOptions as $group) {
			//Create Group
			mysql_safequery('INSERT INTO tblproductconfiggroups(name,description) VALUES(?, ?)', array(
				$group['title'],
				$group['description']
			));
			$group_id = mysql_insert_id();

			//Assign to product
			mysql_safequery('INSERT INTO tblproductconfiglinks(gid,pid) VALUES(?,?)', array($group_id, $this->id));

			foreach ($group['fields'] as $field_key => $field) {
				switch ($field['type']) {
					case 'select': case 1: case 'dropdown':
						$field_type = 1;
						break;
					case 'radio': case 2:
						$field_type = 2;
						break;
					case 'yesno': case 3:
						$field_type = 3;
						break;
					case 'quantity': case 4:
						$field_type = 4;
						break;
					default:
						continue;
				}

				mysql_safequery('INSERT INTO tblproductconfigoptions(gid,optionname,optiontype,qtyminimum,qtymaximum,`order`,hidden) VALUES(?,?,?,?,?,0,0)', array(
					$group_id,
					$field_key . '|' . $field['title'],
					$field_type,
					isset($field['qtyminimum']) ? (int) $field['qtyminimum'] : 0,
					isset($field['qtymaximum']) ? (int) $field['qtymaximum'] : 0,
				));

				$config_id = mysql_insert_id();
				$currencyDefault = mysql_fetch_assoc(mysql_safequery("SELECT * FROM  `tblcurrencies`  WHERE `default`=1 LIMIT 1", array()));

				//Insert options
				foreach ($field['options'] as $option) {
					mysql_safequery("INSERT INTO tblproductconfigoptionssub(configid,optionname,sortorder,hidden) VALUES(?,?,0,0)", array(
						$config_id,
						$option['value'] . '|' . $option['title'],
						isset($field['sortorder']) ? (int) $field['sortorder'] : 0,
						isset($field['hidden']) ? 'on' : '',
					));
					$suboption_id = mysql_insert_id();

					if (isset($field['options']['pricing'])) {
						foreach ($field['options']['pricing'] as $currency_id => $values) {
							mysql_safequery('INSERT INTO `tblpricing` (`type`,`currency`,`relid`,`msetupfee`,`qsetupfee`,`ssetupfee`,`asetupfee`,`bsetupfee`,`tsetupfee`,`monthly`,`quarterly`,`semiannually`,`annually`,`biennially`,`triennially`) VALUES("configoptions",?,?,?,?,?,?,?,?,?,?,?,?,?,?)', array(
								$currency_id,
								$suboption_id,
								isset($values['msetupfee']) ? (float) $values['msetupfee'] : 0,
								isset($values['qsetupfee']) ? (float) $values['qsetupfee'] : 0,
								isset($values['ssetupfee']) ? (float) $values['ssetupfee'] : 0,
								isset($values['asetupfee']) ? (float) $values['asetupfee'] : 0,
								isset($values['bsetupfee']) ? (float) $values['bsetupfee'] : 0,
								isset($values['tsetupfee']) ? (float) $values['tsetupfee'] : 0,
								isset($values['monthly']) ? (float) $values['monthly'] : 0,
								isset($values['quarterly']) ? (float) $values['quarterly'] : 0,
								isset($values['semiannually']) ? (float) $values['semiannually'] : 0,
								isset($values['annually']) ? (float) $values['annually'] : 0,
								isset($values['biennially']) ? (float) $values['biennially'] : 0,
								isset($values['triennially']) ? (float) $values['triennially'] : 0,
							));
						}
					} else {
						mysql_safequery("INSERT INTO `tblpricing` ( `type` , `currency` , `relid` , `msetupfee` , `qsetupfee` , `ssetupfee` , `asetupfee` , `bsetupfee` , `tsetupfee` , `monthly` , `quarterly` , `semiannually` , `annually` , `biennially` , `triennially`)
							VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", array("configoptions", $currencyDefault['id'], $suboption_id,"0.00","0.00","0.00","0.00","0.00","0.00","0.00","0.00","0.00","0.00","0.00","0.00"));
					}
				}
			}
		}

		return true;
	}
       /**
        * Update DefaultConfigurable Options
        * @return boolean
        */
       public function updateDefaultConfigurableOptions(){
             
            $conif = mysql_fetch_assoc(mysql_safequery('SELECT * FROM tblproductconfiglinks WHERE pid = ?', array($this->id)));
            if (!$conif)
                  return false;
            
            foreach ($this->defaultConfigurableOptions as $group) {
			$group_id = $conif['gid'];


			foreach ($group['fields'] as $field_key => $field) {
				switch ($field['type']) {
					case 'select': case 1: case 'dropdown':
						$field_type = 1;
						break;
					case 'radio': case 2:
						$field_type = 2;
						break;
					case 'yesno': case 3:
						$field_type = 3;
						break;
					case 'quantity': case 4:
						$field_type = 4;
						break;
					default:
						continue;
				}

                            $configoptions = mysql_fetch_assoc(mysql_safequery("SELECT * FROM  `tblproductconfigoptions`  WHERE `gid`=?  AND `optionname` LIKE ?  LIMIT 1", array($group_id, "{$field_key}|%")));
				$config_id     = $configoptions['id'];
				$currencyDefault = mysql_fetch_assoc(mysql_safequery("SELECT * FROM  `tblcurrencies`  WHERE `default`=1 LIMIT 1", array()));
                            
                            mysql_safequery("DELETE FROM tblproductconfigoptionssub WHERE `relid` IN (SELECT id FROM tblproductconfigoptionssub WHERE configid=?)", array($config_id));
                            mysql_safequery("DELETE FROM tblproductconfigoptionssub WHERE configid=?", array($config_id));
                            
				//Insert options
				foreach ($field['options'] as $option) {
					mysql_safequery("INSERT INTO tblproductconfigoptionssub(configid,optionname,sortorder,hidden) VALUES(?,?,0,0)", array(
						$config_id,
						$option['value'] . '|' . $option['title'],
						isset($field['sortorder']) ? (int) $field['sortorder'] : 0,
						isset($field['hidden']) ? 'on' : '',
					));
					$suboption_id = mysql_insert_id();

					if (isset($field['options']['pricing'])) {
						foreach ($field['options']['pricing'] as $currency_id => $values) {
							mysql_safequery('INSERT INTO `tblpricing` (`type`,`currency`,`relid`,`msetupfee`,`qsetupfee`,`ssetupfee`,`asetupfee`,`bsetupfee`,`tsetupfee`,`monthly`,`quarterly`,`semiannually`,`annually`,`biennially`,`triennially`) VALUES("configoptions",?,?,?,?,?,?,?,?,?,?,?,?,?,?)', array(
								$currency_id,
								$suboption_id,
								isset($values['msetupfee']) ? (float) $values['msetupfee'] : 0,
								isset($values['qsetupfee']) ? (float) $values['qsetupfee'] : 0,
								isset($values['ssetupfee']) ? (float) $values['ssetupfee'] : 0,
								isset($values['asetupfee']) ? (float) $values['asetupfee'] : 0,
								isset($values['bsetupfee']) ? (float) $values['bsetupfee'] : 0,
								isset($values['tsetupfee']) ? (float) $values['tsetupfee'] : 0,
								isset($values['monthly']) ? (float) $values['monthly'] : 0,
								isset($values['quarterly']) ? (float) $values['quarterly'] : 0,
								isset($values['semiannually']) ? (float) $values['semiannually'] : 0,
								isset($values['annually']) ? (float) $values['annually'] : 0,
								isset($values['biennially']) ? (float) $values['biennially'] : 0,
								isset($values['triennially']) ? (float) $values['triennially'] : 0,
							));
						}
					} else {
						mysql_safequery("INSERT INTO `tblpricing` ( `type` , `currency` , `relid` , `msetupfee` , `qsetupfee` , `ssetupfee` , `asetupfee` , `bsetupfee` , `tsetupfee` , `monthly` , `quarterly` , `semiannually` , `annually` , `biennially` , `triennially`)
							VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", array("configoptions", $currencyDefault['id'], $suboption_id,"0.00","0.00","0.00","0.00","0.00","0.00","0.00","0.00","0.00","0.00","0.00","0.00"));
					}
				}
			}
		}

		return true;
       }

	/**
	 * Load Product Configuration
	 * @throws Exception
	 */
	public function load() {
		$q = mysql_safequery('SELECT * FROM tblproducts WHERE id = ' . (int) $this->id);
		$row = mysql_fetch_assoc($q);
		if (!empty($row)) {
			foreach ($row as $k => $v) {
				$this->$k = $v;
			}
		} else {
			throw new Exception('No product to load');
		}
	}

	/**
	 *  Load product configuration by service it
	 * @param type $serviceid
	 */
	public function setIdByServiceId($serviceid) {
		$q = mysql_safequery('SELECT packageid FROM tblhosting WHERE id = ' . (int) $serviceid);
		$row = mysql_fetch_assoc($q);
		$this->id = (int) $row['packageid'];
	}

	/**
	 * Update product details
	 * @param array $values
	 * @return type
	 */
	public function update(array $values) {
		$sets = array();
		foreach ($values as $k => $v) {
			$v = is_numeric($v) ? $v : '"' . mysql_real_escape_string($v) . '"';
			$sets[] = $k . '=' . $v;
		}
		return mysql_safequery('UPDATE tblproducts SET ' . implode(',', $sets) . ' WHERE id = ' . (int) $this->id);
	}

	/**
	 * Has Configurable Options?
	 * @return type
	 */
	public function hasConfigurableOptions() {
		$q = mysql_safequery('SELECT * FROM tblproductconfiglinks WHERE pid = ?', array($this->id));
		return (bool) mysql_num_rows($q);
	}

	/**
	 * Has Assigned Server Group
	 * @return boolean
	 */
	public function hasAssignedServerGroup() {
		$q = mysql_safequery('SELECT servergroup FROM tblproducts WHERE id = ?', array($this->id));
		$row = mysql_fetch_assoc($q);
		return isset($row['servergroup']) ? (int) $row['servergroup'] : false;
	}

	/**
	 * Get server params
	 * @return array
	 */
	public function getParams() {
		$result = mysql_safequery("
			SELECT
				s.ipaddress AS serverip, s.hostname AS serverhostname, s.username AS serverusername, s.password AS serverpassword, s.secure AS serversecure, s.accesshash AS serveraccesshash,
				configoption1,configoption2,configoption3,configoption4,configoption5,configoption6,configoption7,configoption8,configoption9
			FROM tblservers AS s
			JOIN tblservergroupsrel AS sgr ON sgr.serverid = s.id
			JOIN tblservergroups AS sg ON sgr.groupid = sg.id
			JOIN tblproducts AS p ON p.servergroup = sg.id
			WHERE p.id = ?
			ORDER BY s.active DESC
			LIMIT 1", array($this->id)
		);
		$row = mysql_fetch_assoc($result);
		// old whmcs
		if (!function_exists('decrypt') && file_exists(ROOTDIR . DS . 'includes' . DS . 'functions.php'))
			include_once ROOTDIR . DS . 'includes' . DS . 'functions.php';
		if (!empty($row['serverpassword']))
			$row['serverpassword'] = decrypt($row['serverpassword']);
		return $row;
	}

	// ========================================
	// ============ CUSTOM CONFIG =============
	// ========================================
       /**
        * 
        * @param string $name
        * @return string
        */
	public function getConfig($name) {
		$this->loadConfig();
		return isset($this->_config[$name]) ? $this->_config[$name] : null;
	}
       /**
        * Isse tConfig
        * @param string $name
        * @return boolean
        */
	public function issetConfig($name) {
		$this->loadConfig();
		return isset($this->_config[$name]);
	}
       /**
        * Load Config
        * @return array
        */
	public function loadConfig() {
		if ($this->_config !== null)
			return $this->_config;

		$this->setupDbTable();
		$q = mysql_query_safe('SELECT * FROM ' . $this->_tableName . ' WHERE product_id = ' . (int) $this->id);
		while ($row = mysql_fetch_assoc($q)) {
			if (json_decode($row['value']) !== NULL)
				$row['value'] = json_decode($row['value']);

			$this->_config[$row['setting']] = $row['value'];
		}
		return $this->_config;
	}
       /**
        * Save Config
        * @param string $name
        * @param string $value
        * @return boolean
        */
	public function saveConfig($name, $value) {
		$this->setupDbTable();
		if (is_array($value))
			$value = json_encode($value);
		return mysql_query_safe('INSERT INTO ' . $this->_tableName . '(setting,product_id,value) VALUES(?,?,?) ON DUPLICATE KEY UPDATE value = ?', array(
			$name,
			(int) $this->id,
			($value == '-- not specified --' ? '' : $value),
			$value
		));
	}
       /**
        * Clear Config
        * @return boolean
        */
	public function clearConfig() {
		return mysql_query_safe('DELETE FROM ' . $this->_tableName . ' WHERE product_id = ' . (int) $this->id);
	}
       /**
        * Render Config Options
        * @param string $scripts
        * @param string $moduleWikiUrl
        * @param string $moduleVersion
        * @param string $moduleLogoSrc
        * @return string
        */
	public function renderConfigOptions($scripts = '', $moduleWikiUrl = 'http://www.docs.modulesgarden.com/Main_Page', $moduleVersion = '1.0.0', $moduleLogoSrc = ''){
		$scripts .= '
			<style type="text/css">
				td.configoption_group {background-color:silver;font-weight:bold;text-align:left;}
				.fieldlabel.mg, .fieldarea.mg {width:25%;}
				.fielddescription {font-size: 10px;color: gray;display: inline;}
				.mgContact {
					float: right;
					margin: 0;
					background-color: #1c4b8c;
					-moz-border-radius: 5px;
					-webkit-border-radius: 5px;
					-o-border-radius: 5px;
					border-radius: 5px;
					position: relative;
					top: -5px;
					width: 400px;
					height: 30px;
					padding: 3px;
					text-align: center;
					position: relative;
					-webkit-border-radius: 5px;
					-moz-border-radius: 5px;
					border-radius: 5px;
					background-color: #1c4b8c;
				}
			</style>
			<script type="text/javascript">
			jQuery(document).ready(function(){
                            var moduleWikiUrl = "'.$moduleWikiUrl.'";
                            var moduleLogoSrc = "'.$moduleLogoSrc.'";
                            if(moduleWikiUrl && moduleLogoSrc){
                                  jQuery("input[name^=customconfigoption]:first").closest(".form").before(\'<p id="created-by-mg" style="text-align: left; margin-bottom: 5px; margin-top: 0;">Created by <a href="http://www.modulesgarden.com" target="_blank">ModulesGarden</a>. For more information visit our <a href="'.$moduleWikiUrl.'" target="_blank">Wiki</a>. <small>Version '.$moduleVersion.'</small></p>\');
                                  $("form[name=packagefrm]").prev().before(\'<div class="mgContact"><a href="http://www.modulesgarden.com" target="_blank" title="ModulesGarden - Custom Software Development"><span style="margin-top: 0px; float: right; height: 27px; width: 160px; margin: 2px 10px 0px 0px; display: inline-block; text-align: center;background: url('.$moduleLogoSrc.') no-repeat;"></span></a><a href="http://www.modulesgarden.com/customers/support" target="_blank" title="Open A Support Ticket"><small style="display: inline-block; line-height: 30px; font-size: 11px; font-weight: normal; color: #fff;">We are here to help you, just click!</small></a></div>\');
                            }
			});
			</script>
		';

		$str = '';
		$options = array();
		$groups = array();
		$i = 0;
		foreach ($this->defaultConfig as $k => $config) {
			// group html
			if (is_string($config)) {
				$groups[$i] = $config;
				continue;
			}

			if ($k == 'generate_configurable_options') {
				$options[] = '
					<td class="fieldlabel mg">Configurable Options</td>
					<td class="fieldarea mg"><a href="#" class="generate_configurable_options">Generate</a></td>';

				$scripts .= '<script type="text/javascript">
					jQuery(function(){
					   jQuery(".generate_configurable_options").click(function(event){
							event.preventDefault();
							jQuery.post(window.location.href, {"modaction":"generate_configurable_options", "productid":' . $this->id . '}, function(res){
								alert(res.message);
								if(res.status){
									window.location.href = "configproducts.php?action=edit&id=' . $this->id . '&tab=4";
								}
							}, "json");
						});
					});
				 </script>';
			} elseif ($k == 'genereate_custom_field') {
				$options[] = '
					<td class="fieldlabel mg">Custom Field</td>
					<td class="fieldarea mg"><a href="#" class="generate_custom_fields">Generate</a></td>';

				$scripts .= '<script type="text/javascript">
						jQuery(function(){
						   jQuery(".generate_custom_fields").click(function(event){
								event.preventDefault();
										jQuery.post(window.location.href, {"modaction":"generate_custom_fields", "productid":' . $this->id . '}, function(res){
										alert(res.message);
										if(res.status)
										{
											window.location.href = "configproducts.php?action=edit&id=' . $this->id . '&tab=3";
										}
								}, "json");
							});
						});
					 </script>';
			} else {
				$options[] = '
					<td class="fieldlabel mg">' . $config['title'] . '</td>
					<td class="fieldarea mg">
						' . $this->renderConfigOptionInput(
								$k, $config['type'], isset($config['default']) ? $config['default'] : '', isset($config['options']) ? $config['options'] : array(), isset($config['useOptionsKeys']) && $config['useOptionsKeys']
						) . '
						' . (isset($config['description']) && $config['description'] ? '<div class="fielddescription"><img src="../images/help.gif" title="' . $config['description'] . '" /></div>' : ''. (isset($config['html']) && $config['html']) ?  $config['html'] : '') .
                                          '
					</td>';
			}
			$i++;
		}
		$countFields = 0;
		foreach ($options as $k => $option) {
			if ($countFields == 0 && $k != 0)
				$str .= '<tr>';

			if (isset($groups[$k])) {
				if ($countFields == 1)
					$str .= '<td></td><td></td>';
				$str .= '</tr><tr><td colspan="4" class="configoption_group">' . $groups[$k] . '</td></tr><tr>';
				$countFields = 0;
			}
			$str .= $option;

			$countFields++;
			if ($countFields == 2)
				$str .= '</tr>';
			if ($countFields > 1)
				$countFields = 0;
		}
		if ($countFields != 0)
			$str .= '</tr>';
		return $scripts . $str;
	}
       /**
        * Render Config Option Input
        * @param string $name
        * @param string $type
        * @param string $default
        * @param array $options
        * @param string $optionsValuesFromKeys
        * @return string
        * @throws Exception
        */
	public function renderConfigOptionInput($name, $type, $default, array $options = array(), $optionsValuesFromKeys = false) {
		$value = $this->getConfig($name) ? $this->getConfig($name) : ($this->issetConfig($name) ? '' : $default);
		switch ($type) {
			case 'multiselect':
				$str = '<select name="customconfigoption[' . $name . '][]" multiple style="width:160px;">';
				foreach ($options as $k => $option) {
					$str .= '<option value="' . ($optionsValuesFromKeys ? $k : $option) . '" ' . (is_array($value) && in_array(($optionsValuesFromKeys ? $k : $option), $value) ? 'selected' : '') . '>' . $option . '</option>';
				}
				$str .= '</select>';
				return $str;

			case 'select':
				$str = '<select name="customconfigoption[' . $name . ']" style="width:160px;">';
				foreach ($options as $k => $option) {
					$str .= '<option value="' . ($optionsValuesFromKeys ? $k : $option) . '" ' . ($value == ($optionsValuesFromKeys ? $k : $option) ? 'selected' : '') . '>' . $option . '</option>';
				}
				$str .= '</select>';
				return $str;

			case 'text':
				return '<input type="text" name="customconfigoption[' . $name . ']" style="width:150px;" value="' . $value . '" />';

			case 'password':
				return '<input type="password" name="customconfigoption[' . $name . ']" style="width:150px;" value="' . $value . '" />';

			case 'textarea':
				return '<textarea name="customconfigoption[' . $name . ']" style="width:100%">' . $value . '</textarea>';

			case 'radio':
				$str = '';
				foreach ($options as $option)
					$str .= '<input type="radio" name="customconfigoption[' . $name . ']" value="' . $option . '" /> ' . $option;
				return $str;

			case 'checkbox':
				return '
					<input type="checkbox"  name="customconfigoption[' . $name . ']" value="1"  ' . ($value ? ' checked="checked" ' : '') . ' />
					<input type="hidden"  name="customconfigoption[hidden_' . $name . ']" value="' . ($value ? '1' : '0') . '" /> ' .
					$option;

			case 'empty':
				return '';
		}
		// NO CHECKBOX
		throw new Exception('Config Option "'.$name.'" type "'.$type.'" not supported');
	}
       /**
        * Setup Db Table
        * @return boolean
        */
	public function setupDbTable() {
		return mysql_query_safe('CREATE TABLE IF NOT EXISTS `' . $this->_tableName . '` (
			`setting` varchar(100) NOT NULL,
			`product_id` int(10) unsigned NOT NULL,
			`value` text NOT NULL,
			PRIMARY KEY (`setting`,`product_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
	}

}
