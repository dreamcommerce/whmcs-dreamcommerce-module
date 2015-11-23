<?php

/**********************************************************************
 * DreamCommerce product developed. (2014-12-17)
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
function DreamCommerce_ConfigOptions($prameters = array()) {
  
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
            "FriendlyName" => "API End Point",
            'Type'          =>  'text',
            'Size'          =>  '45',
        ),
          'package'          =>  array(
            "FriendlyName" => "Package",
            'Type'          =>  'dropdown',
            "Options" => "",
        ),
          'period'        => array(
            "FriendlyName" => "Default Period",
            'Type'          =>  'dropdown',
            "Options" => "",
            "Description" => "Period is calculate automatically based on the product billing cycle."
        ),
            'hostPrefix'          =>  array
        (
           "FriendlyName" => "Store Host Prefix",
            'Type'          =>  'text',
            'Size'          =>  '25',
           'Description'   =>  'Used when domain is not ordered. Eg. "store" or {$order_number}',
            "Default" => "store",
        ),
            'nextStoreID'          =>  array
        (
           "FriendlyName" => "Next Store ID",
            'Type'          =>  'text',
            'Size'          =>  '25',
            'Description'   =>  'Used for hostname. Eg. 1',
            "Default" => "1",
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
            "domainsManagement" =>  array
        (
           "FriendlyName" => "Domains Management",
            'Type'          =>  'yesno',
           'Description'   =>  'Enable "Domains Management" In Client Area'
        ),

          "debugMode"   =>  array
        (
            "FriendlyName" => "Debug Mode",
            'Type'          =>  'yesno',
            'Description'   =>  'Logs on "Module Log"'
        ),
    ); 

    if((empty($prameters) || $prameters['action'] =='ConfigOptions')&& basename($_SERVER["SCRIPT_NAME"]) == 'configproducts.php'   ){

            $params = mysql_get_row("SELECT * FROM tblproducts WHERE id =? LIMIT 1", array($_REQUEST['id']));
            $config = DreamCommerce_ConfigOptions(array(1));
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
          $additionalData = array(
              'whmcs_service' => $params['serviceid'],
          );
          $configoptions = array_values($params['configoptions']);
          if (isset($configoptions[0])) {
              $additionalData['whmcs_promocode'] = $configoptions[0];
          }

          if (isset($params['clientsdetails']['phonenumber'])) {
              $additionalData['phone'] = $params['clientsdetails']['phonenumber'];
          }

            $config = DreamCommerce_ConfigOptions(array(1));
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
            $dcConfig->nextStoreID = empty($dcConfig->nextStoreID )? null: (int) $dcConfig->nextStoreID;
            if(trim($dcConfig->hostPrefix)=='{$order_number}'){
                  $order = mysql_get_row("select * from `tblorders` where id =?", array($hosting->hosting_details['orderid']));
                  $dcConfig->hostPrefix = str_replace('{$order_number}', $order['ordernum'], $dcConfig->hostPrefix);
                  $host  = $params['domain'] ? $params['domain'] : $dcConfig->hostPrefix.$dcConfig->nextStoreID;
            }else{
                 $host  = $params['domain'] ? $params['domain'] : $dcConfig->hostPrefix.$dcConfig->nextStoreID;
            }

            $type  = $dcConfig->licenseType =="Full" ? 1 : 0;
            $version = $dcConfig->shopVersion;
            $package = $dcConfig->package;
            $defaultPeriod  = 0;
            $period = 0;
            $hostingPeriod  = $hosting->getMonthlyCycle();
            foreach($api->getPeriods() as $p){
                  if($dcConfig->getPeriodID() == $p->id){
                        $defaultPeriod = $p->months;
                  }
                  if($hostingPeriod && $p->months == $hostingPeriod){
                      $period =   $p->months;
                      break;
                  }
            }
            $period = $period? $period: $defaultPeriod;
            $info = $dcConfig->lmsPartner;
            $api->checkDomainAvailability(null, $host);
            $result = $api->createLicense(null, $email, $host, $type, $package, $period,$version, $info, $additionalData);
            $hosting->setCustomField("accountID", (string)$result->account);
            $hosting->update(array("domain"=> (string)$result->host, "password" => encrypt((string)$result->password ), "username" =>"admin" ));

            if(empty($params['domain'] ) && !empty($dcConfig->nextStoreID)){
                  $optionKey = $dcConfig->getOptionKey('nextStoreID', $params);
                  $product->update(array($optionKey => $dcConfig->nextStoreID +=1 ));
            }
            return 'success';
      }
      catch (DreamCommerce_Exception $ex) {
            return "ERROR: {$ex->getMessage()}";
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
      if (!$params['customfields']['accountID'])
		return 'Custom Field /Account/ is empty';
      try {
            $config = DreamCommerce_ConfigOptions(array(1));
            $dcConfig = new DreamCommerce_Config($config, $params);
            $api = new DreamCommerce_API($dcConfig->host, $dcConfig->username, $dcConfig->password, $dcConfig->debugMode);
            $product = new DreamCommerce_Product($params['pid']);
            $hosting = new DreamCommerce_Hosting($params['serviceid']);
            if (!isset($params['customfields']['accountID'])) {
                  $product->generateDefaultCustomField();
            }
            $api->testConnection();
            $api->login();
            $info = $api->getLicense(null,$params['customfields']['accountID']);
                     
            if($info->package_name != $dcConfig->package ){
                $api->changeLicensePackage(null, $params['customfields']['accountID'], $dcConfig->package);
            }

            if($info->type !== 1 &&  $dcConfig->licenseType=="Full"){
                $api->upgradeLicense(null, $params['customfields']['accountID']);
            }
            return 'success';
      } catch (DreamCommerce_Exception $ex) {
            return "ERROR: {$ex->getMessage()} Code: {$ex->getCode()}";
      } catch (Exception $ex) {
            return "ERROR: {$ex->getMessage()} File: {$ex->getFile()} Line: {$ex->getLine()}";
      }
}

/**
 * This function is called when a termination is requested.
 *
 * @param array $params
 * @return string
 */
function DreamCommerce_TerminateAccount($params) {
      if (!$params['customfields']['accountID'])
		return 'Custom Field /Account/ is empty';
      try {
            $config = DreamCommerce_ConfigOptions(array(1));
            $dcConfig = new DreamCommerce_Config($config, $params);
            $api = new DreamCommerce_API($dcConfig->host, $dcConfig->username, $dcConfig->password, $dcConfig->debugMode);
            $product = new DreamCommerce_Product($params['pid']);
            $hosting = new DreamCommerce_Hosting($params['serviceid']);
            $api->testConnection();
            $api->login();
            $api->removeLicense(null, $params['customfields']['accountID']);
            $hosting->setCustomField("accountID", "");
            $order = mysql_get_row("select * from `tblorders` where id =?", array($hosting->hosting_details['orderid']));
            if(trim($dcConfig->hostPrefix)=='{$order_number}' &&  strpos($params['domain'], $order['ordernum'])!== false){
                   $hosting->update(array("domain"=> ""));
            }
            return 'success';
      } catch (DreamCommerce_Exception $ex) {
            return "ERROR: {$ex->getMessage()} Code: {$ex->getCode()}";
      } catch (Exception $ex) {
            return "ERROR: {$ex->getMessage()} File: {$ex->getFile()} Line: {$ex->getLine()}";
      }

}

/**
 * This function is called when a suspension is requested.
 *
 * @param array $params
 * @return string
 */
function DreamCommerce_SuspendAccount($params) {
      if (!$params['customfields']['accountID'])
		return 'Custom Field /Account/ is empty';
      try {
            $config = DreamCommerce_ConfigOptions(array(1));
            $dcConfig = new DreamCommerce_Config($config, $params);
            $api = new DreamCommerce_API($dcConfig->host, $dcConfig->username, $dcConfig->password, $dcConfig->debugMode);
            $product = new DreamCommerce_Product($params['pid']);
            $hosting = new DreamCommerce_Hosting($params['serviceid']);
            if (!isset($params['customfields']['accountID'])) {
                  $product->generateDefaultCustomField();
            }
            $api->testConnection();
            $api->login();
            $api->suspendLicense(null, $params['customfields']['accountID']);
            return 'success';
      } catch (DreamCommerce_Exception $ex) {
            return "ERROR: {$ex->getMessage()} Code: {$ex->getCode()}";
      } catch (Exception $ex) {
            return "ERROR: {$ex->getMessage()} File: {$ex->getFile()} Line: {$ex->getLine()}";
      }
}

/**
 * This function is called when an unsuspension is requested.
 *
 * @param array $params
 * @return string
 */
function DreamCommerce_UnsuspendAccount($params) {
      if (!$params['customfields']['accountID'])
		return 'Custom Field /Account/ is empty';
      try {
            $config = DreamCommerce_ConfigOptions(array(1));
            $dcConfig = new DreamCommerce_Config($config, $params);
            $api = new DreamCommerce_API($dcConfig->host, $dcConfig->username, $dcConfig->password, $dcConfig->debugMode);
            $product = new DreamCommerce_Product($params['pid']);
            $hosting = new DreamCommerce_Hosting($params['serviceid']);
            if (!isset($params['customfields']['accountID'])) {
                  $product->generateDefaultCustomField();
            }
            $api->testConnection();
            $api->login();
            $api->unsuspendLicense(null, $params['customfields']['accountID']);
            return 'success';
      } catch (DreamCommerce_Exception $ex) {
            return "ERROR: {$ex->getMessage()} Code: {$ex->getCode()}";
      } catch (Exception $ex) {
            return "ERROR: {$ex->getMessage()} File: {$ex->getFile()} Line: {$ex->getLine()}";
      }
}
/**
 * This function should be called automatically
 * @param array $params
 * @return string
 */
function DreamCommerce_Renew($params) {
     if (!$params['customfields']['accountID'])
		return 'Custom Field /Account/ is empty';
     try {
            $config = DreamCommerce_ConfigOptions(array(1));
            $dcConfig = new DreamCommerce_Config($config, $params);
            $api = new DreamCommerce_API($dcConfig->host, $dcConfig->username, $dcConfig->password, $dcConfig->debugMode);
            $product = new DreamCommerce_Product($params['pid']);
            $hosting = new DreamCommerce_Hosting($params['serviceid']);
            $api->testConnection();
            $api->login();
            $defaultPeriod  = 0;
            $period = 0;
            $hostingPeriod  = $hosting->getMonthlyCycle();
            foreach($api->getPeriods() as $p){
                  if($dcConfig->getPeriodID() == $p->id){
                        $defaultPeriod = $p->months;
                  }
                  if($hostingPeriod && $p->months == $hostingPeriod){
                      $period =   $p->months;
                      break;
                  }
            }
            $period = $period? $period: $defaultPeriod;

            $api->renewLicense(null, $params['customfields']['accountID'],  $period);
            return 'success';
      } catch (DreamCommerce_Exception $ex) {
            return "ERROR: {$ex->getMessage()} Code: {$ex->getCode()}";
      } catch (Exception $ex) {
            return "ERROR: {$ex->getMessage()} File: {$ex->getFile()} Line: {$ex->getLine()}";
      }
}

/**
 * This function is used to define additional fields
 * @param array $params
 * @return string
 */
function DreamCommerce_AdminServicesTabFields($params) {
      if (!$params['customfields']['accountID'])
		return;
      try {
            $config = DreamCommerce_ConfigOptions(array(1));
            $dcConfig = new DreamCommerce_Config($config, $params);
            $api = new DreamCommerce_API($dcConfig->host, $dcConfig->username, $dcConfig->password, $dcConfig->debugMode);
            $product = new DreamCommerce_Product($params['pid']);
            $hosting = new DreamCommerce_Hosting($params['serviceid']);
            $clientArea = new DreamCommerce($params['serviceid']);
            $lang = $clientArea->getLang($params);
            $api->testConnection();
            $api->login();
            if(isset($_REQUEST['DreamCommerce_ajax'])){
                  try{
                        switch ($_POST['action']){
				case 'addDomain':
                                  $api->addLicenseDomain(null,$params['customfields']['accountID'], $_REQUEST['domain']);
					$res = array(
						'result' => '1',
						'msg' => sprintf($lang['domainsManagement']['domainAdded'], $_REQUEST['domain'])
					);
                             break;
                            case 'deleteDomain':
                                  $api->removeLicenseDomain(null,$params['customfields']['accountID'], $_REQUEST['domain']);
                                  $res = array(
						'result' => '1',
						'msg' => sprintf($lang['domainsManagement']['domainDeleted'], $_REQUEST['domain'])
					);
                             break;
				default: throw new Exception('Action not supported');
			 }
                  } catch (Exception $ex) {
                        $res = array(
				'result'=> '0',
				'msg'	=> $ex->getMessage()
			   );
                  }
                  ob_clean();
                  echo json_encode($res);
                  die();
            }
            $info = $api->getLicense(null,$params['customfields']['accountID'] );
            $data = array();
            global $templates_compiledir;
            $servicePageUrl = "clientsservices.php?userid={$_GET['userid']}&id={$_GET['id']}&productid={$params['packageid']}";

            $adminTemplateDir = dirname(__FILE__).DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'admin';
            $sm = new Smarty();
            $sm->compile_dir = $templates_compiledir;
            $sm->force_compile = 1;
            $sm->assign("servicePageUrl",   $servicePageUrl);
            $sm->assign("lang",   $lang);
            $sm->assign("info",   $info);
            $data['License Info'] = $sm->fetch($adminTemplateDir.DIRECTORY_SEPARATOR.'licenseInfo.tpl');

            $licenseDomains = $api->getLicenseDomains(null,  $params['customfields']['accountID']);
            $sm->assign("licenseDomains",   $licenseDomains);
            $data['Domains Management'] = $sm->fetch($adminTemplateDir.DIRECTORY_SEPARATOR.'domainsManagement.tpl');

            return $data;
      } catch (DreamCommerce_Exception $ex) {
            echo '<div class="errorbox"><strong>ERROR</strong><br/>' .$ex->getMessage().' Code: '.$ex->getCode(). '</div>';
      } catch (Exception $ex) {
            echo '<div class="errorbox"><strong>ERROR</strong><br/>' .$ex->getMessage().' File: '.$ex->getFile(). '  Line: '.$ex->getLine().'</div>';
      }
      return array();
}

// =========================== CLIENT AREA ==================================

function DreamCommerce_ClientAreaCustomButtonArray($params ) {
      try {
            $clientArea = new DreamCommerce($params['serviceid']);
            $lang = $clientArea->getLang($params);
            $config = DreamCommerce_ConfigOptions(array(1));
            $dcConfig = new DreamCommerce_Config($config, $params);
            if($dcConfig->domainsManagement)
            return array(
                          $lang['domainsManagement']['button'] => "management",
                   );
      } catch (Exception $ex) {
             return array(
                    "Management" => "management",
             );
      }

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
             $clientArea->init($params);
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
            $clientArea = new DreamCommerce($params['serviceid']);
            $clientArea->init($params);
            $act = $_GET['act']? $_GET['act']: 'domainsManagement';
            return $clientArea->run( $act, $params);
      } catch (Exception $ex) {
           return array("vars" => array("modulecustombuttonresult" => $ex->getMessage()));
      }
}
