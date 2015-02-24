<?php

/* * ********************************************************************
 * Customization Services by ModulesGarden.com
 * Copyright (c) ModulesGarden, INBS Group Brand, All Rights Reserved 
 * (2014-03-10, 12:06:40)
 * 
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
 */

class DreamCommerce_Product extends MG_Abstract_Product {

	public $defaultConfig = array();       
	
	public $defaultCustomField = array(
		'accountID' => array(
			'type'			=> 'text',
			'title'			=> 'Account',
			'description'	=> 'Custom Field required for DreamCommerce module',
			'adminonly'		=> 'on'
		),
           
           
	);
	
	public $defaultConfigurableOptions = array(
		'group1' => array(
			'title'			=> 'Configurable Options for i3D product',
			'description'	=> 'Auto generated',
			'fields'		=> array(
				'cpu_type' => array(
					'type'		=> 'dropdown',
					'title'		=> 'CPU Type',
					'options'	=> array(
						array('value' => '10', 'title' => '10 GB'),
                                          array('value' => '20', 'title' => '20 GB')
                                       
					)
				),
				'memory' => array(
					'type'		=> 'dropdown',
					'title'		=> 'Memory',
					'options'	=> array(
                                       
					)
				),
				'hard_disk' => array(
					'type'		=> 'dropdown',
					'title'		=> 'Hard Disk',
					'options'	=> array(
                                       
					)
				),
                            'ip_addresses' => array(
					'type'		=> 'dropdown',
					'title'		=> 'IP Addresses',
					'options'	=> array(
                                       
					)
				),
                           'operating_system' => array(
					'type'		=> 'dropdown',
					'title'		=> 'Operating System',
					'options'	=> array(
                                       
					)
				),
                         
			)
		)
	);

       /**
        * Remove Group Config
        * @param int $key
        * @return boolean
        */
	public function removeGroupConfig($key){
             if(isset($this->defaultConfig[$key])){
                   $removeConf = false;
                   foreach($this->defaultConfig as $k => $v){
                         if($k == $key){
                               $removeConf = true;
                               unset($this->defaultConfig[$key]); 
                               continue;
                         }
                         if($removeConf && is_array($v)){
                               unset($this->defaultConfig[$k]); 
                         }elseif($removeConf && is_string($v)){
                               break;
                         }
                   }
                   return true;
             }
             return false;
       }
       /**
        * Get User Permission
        * @return array
        */
       public function getUserPermission(){
             $conf = $this->loadConfig();
             $perm = array();
             foreach( $conf as $k => $v){
                   if(strpos($k, 'per_')!== false){
                         $perm[$k] = $v;
                   }
             }    
             return $perm;
       }
       
}
