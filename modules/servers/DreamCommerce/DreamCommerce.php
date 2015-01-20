<?php

/**********************************************************************
 * Custom developed. (2014-12-17)
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
 **********************************************************************/

/**
 * @author Pawel Kopec <pawelk@modulesgarden.com>
 */

if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'functions.php';
include_once dirname(__FILE__) .DIRECTORY_SEPARATOR.'DreamCommerce_Loader.php';
        error_reporting(E_ALL);
ini_set('display_errors', '1');
/**
 * Config options are the module settings defined on a per product basis.
 * 
 * @return array
 */
function DreamCommerce_ConfigOptions($loadValuesFromServer = true) {
 
        $config                 =   array 
        (
          'lmsPartner'          =>  array
        (
            "FriendlyName" => "LMS Partner",
            'Type'          =>  'text',
            'Size'          =>  '25',
        ),
        'username'          =>  array
        (
           "FriendlyName" => "Username",
            'Type'          =>  'text',
            'Size'          =>  '25',
        ),
        'password'          =>  array
        (
            "FriendlyName" => "Password",
            'Type'          =>  'password',
            'Size'          =>  '25'
        ),
          'host'          =>  array
        (
            "FriendlyName" => "Host",
            'Type'          =>  'text',
            'Size'          =>  '45',
        ),
          'package'          =>  array(
            "FriendlyName" => "Package",
            'Type'          =>  'dropdown',
            "Options" => "",
        ),
          'period'        => array(
            "FriendlyName" => "Period",
            'Type'          =>  'dropdown',
            "Options" => "",
        ),
            'hostPrefix'          =>  array
        (
           "FriendlyName" => "Store Host Prefix",
            'Type'          =>  'text',
            'Size'          =>  '25',
           'Description'   =>  'Used when domain is not ordered. Eg. store'
        ),
            'nextStoreID'          =>  array
        (
           "FriendlyName" => "Next Store ID",
            'Type'          =>  'text',
            'Size'          =>  '25',
           'Description'   =>  'Used for hostname. Eg. 1'
        ),
         'licenseType'        => array(
            "FriendlyName" => "License Type",
            'Type'          =>  'dropdown',
            "Options" => "Trial,Full",
        ), 
            'shopVersion' =>  array
        (
           "FriendlyName" => "Shop Version",
            'Type'          =>  'text',
            'Size'          =>  '25',
           'Description'   =>  'If empty latest version will be used'
        ),
            "info" =>  array
        (
           "FriendlyName" => "Additional Notes",
            'Type'          =>  'textarea',
            'Size'          =>  '25',
           'Description'   =>  ''
        ),
          "debugMode"   =>  array
        (
            "FriendlyName" => "Debug Mode",
            'Type'          =>  'yesno',
            'Description'   =>  'Logs on "Module Log"'
        ),
    );
            
    if(basename($_SERVER["SCRIPT_NAME"]) == 'configproducts.php' && $loadValuesFromServer ===true){
            
            $params = mysql_get_row("SELECT * FROM tblproducts WHERE id =? LIMIT 1", array($_REQUEST['id']));
            $config = DreamCommerce_ConfigOptions(false);
            $dcConfig = new DreamCommerce_Config($config, $params);
            if(!empty($dcConfig->host) && !empty($dcConfig->username) && !empty($dcConfig->password)){
                  try{
                        $api = new DreamCommerce_API($dcConfig->host, $dcConfig->username, $dcConfig->password, $dcConfig->debugMode);
                        $api->testConnection();
                        $api->login();
                        
                        $temp = array();
                        foreach($api->getPackages() as $package){
                            $temp[] = "{$package->name}";
                        }
                        $config['package']['Options'] =implode(",", $temp);
                        
                        $temp = array();
                        foreach($api->getPeriods() as $period){
                            $temp[] = "{$period->id} | {$period->name}";
                        }
                        $config['period']['Options'] = implode(",", $temp);
                        unset($temp);
                        
                  } catch (DreamCommerce_Exception $ex) {
                        echo '<div class="errorbox"><span class="title">'.$ex->getMessage().'</span></div>';
                  } catch (Exception $ex){
                        echo "<div class=\"errorbox\"><span class=\"title\">ERROR: {$ex->getMessage()} File: {$ex->getFile()} Line: {$ex->getLine()}</span></div>";
                  }
            }else{
                  unset($config['package'], $config['period']);
            }
      }
    return $config;
}

/**
 * This function is called when a new product is due to be provisioned.
 * 
 * @param array $params
 * @return string
 */
function DreamCommerce_CreateAccount($params) {
      if ($params['customfields']['accountID'])
		return 'Custom Field /Account/ is not empty';
      try{
            $config = DreamCommerce_ConfigOptions(false);
            $dcConfig = new DreamCommerce_Config($config, $params);
            $api = new DreamCommerce_API($dcConfig->host, $dcConfig->username, $dcConfig->password, $dcConfig->debugMode);
            $product = new DreamCommerce_Product($params['pid']);
            $hosting = new DreamCommerce_Hosting($params['serviceid']);
            if(!isset($params['customfields']['accountID'])){
                 $product->generateDefaultCustomField(); 
            }
            $api->testConnection();
            $api->login();
            
            $email = $params['clientsdetails']['email'];
            $dcConfig->nextStoreID = (int) $dcConfig->nextStoreID;
            $host  = $params['domain'] ? $params['domain'] : $dcConfig->hostPrefix.$dcConfig->nextStoreID;
            $type  = $dcConfig->licenseType =="Full" ? 1 : 0;
            $version = $dcConfig->shopVersion;
            $package = $dcConfig->package;
            $period  = 0;
            foreach($api->getPeriods() as $p){
                  if($dcConfig->getPeriodID() == $p->id){
                        $period = $p->months;
                        break;
                  }
            }
            $info = $dcConfig->info;
            $result = $api->createLicense(null, $email, $host, $type, $package, $period,$version, $info);
            $hosting->setCustomField("accountID", (string)$result->account);
            $hosting->update(array("domain"=> (string)$result->host, "password" => encrypt((string)$result->password ) ));
            
            if(empty($params['domain'] )){
                  $optionKey = $dcConfig->getOptionKey('nextStoreID', $params);
                  $product->update(array($optionKey => $dcConfig->nextStoreID +=1 ));
            }
            
            return 'success';         
      }
      catch (DreamCommerce_Exception $ex) {
            return "ERROR: {$ex->getMessage()} Code: {$ex->getCode()}";
      }
      catch (Exception $ex) {
            return "ERROR: {$ex->getMessage()} File: {$ex->getFile()} Line: {$ex->getLine()}";
      }
}

/**
 * This function is used for upgrading and downgrading of products.
 * 
 * @param array $params
 * @return string
 */
function DreamCommerce_ChangePackage($params) {
      
}
/**
 * Change Password
 * 
 * @param array $params
 * @return string
 */
function DreamCommerce_ChangePassword($params) {
      
}
/**
 * This function is called when a termination is requested.
 * 
 * @param array $params
 * @return string
 */
function DreamCommerce_TerminateAccount($params) {
      
}

/**
 * This function is called when a suspension is requested.
 * 
 * @param array $params
 * @return string
 */
function DreamCommerce_SuspendAccount($params) {
      
}

/**
 * This function is called when an unsuspension is requested.
 * 
 * @param array $params
 * @return string
 */
function DreamCommerce_UnsuspendAccount($params) {
      
}

/**
 * This function is used to define additional fields
 * @param array $params
 * @return string
 */
function DreamCommerce_AdminServicesTabFields($params) {
      
}

/**
 * FUNCTION DreamCommerce_TestConnection
 * Test connection
 * @param array $params
 * @return array
 */
function DreamCommerce_TestConnection($params) {
      
}

/**
 * This function should be called automatically
 * @param array $params
 * @return string
 */
function DreamCommerce_Renew($params) {
      
}

// =========================== CLIENT AREA ==================================

function DreamCommerce_ClientAreaCustomButtonArray() {
	return array(
		"Management" => "management",
	);
}

/**
 * This function is used on Client Area
 * 
 * @param array $params
 * @return array
 */
function DreamCommerce_ClientArea($params) {
      $module =  basename(dirname(__FILE__)); //Eg. module name is DreamCommerce
      try {
             $clientArea = new $module($params['serviceid']);
             return $clientArea->run('index', $params);
      } catch (Exception $ex) {
            return array(
               'templatefile' => 'templates/index',
               'breadcrumb'   => ' > <a href="#">Management</a>',
               'vars'         => array("errors" => array( $ex->getMessage()))
            );
      }
}

/**
 * Management this function is used on Client Area
 * 
 * @param array $params
 * @return array
 */
function DreamCommerce_management($params) {
      $module =  basename(dirname(__FILE__));
      try {
            if ($module::$run) return false;
            $clientArea = new $module($params['serviceid']);
            return $clientArea->run($_GET['act'], $params);
      } catch (Exception $ex) {
           return array("vars" => array("modulecustombuttonresult" => $ex->getMessage()));
      }
      
}