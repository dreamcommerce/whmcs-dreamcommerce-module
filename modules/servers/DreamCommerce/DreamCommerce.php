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
/**
 * Config options are the module settings defined on a per product basis.
 * 
 * @return array
 */
function DreamCommerce_ConfigOptions($loadValuesFromServer = true) {
      
}

/**
 * This function is called when a new product is due to be provisioned.
 * 
 * @param array $params
 * @return string
 */
function DreamCommerce_CreateAccount($params) {
      
}

/**
 * FUNCTION proxmoxVPS_ChangePackage
 * This function is used for upgrading and downgrading of products.
 * @param array $params
 * @return string
 */
function DreamCommerce_ChangePackage($params) {
      
}
/**
 * Change Password
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
