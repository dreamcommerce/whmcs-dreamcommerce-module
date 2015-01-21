<?php
/* * ********************************************************************
 * CloudLinux Licenses Product developed. (2014-06-04)
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
 * @author Pawel Kopec <pawelk@modulesgarden.com>
 */
/**
 * Client Area controller
 */
class DreamCommerce extends MG_Clientarea {
      /**
       *
       * @var DreamCommerce_API
       */
      protected $api;
      /**
       *
       * @var DreamCommerce_Config
       */
      protected $config;
      
      private $accountID;
      
	public function init($params) {
             $this->lang   = $this->getLang($params);
             $config       = DreamCommerce_ConfigOptions(false);
             $this->config = new DreamCommerce_Config($config, $params);
             $this->api    = new DreamCommerce_API($this->config->host, $this->config->username, $this->config->password, $this->config->debugMode);
             $this->accountID = $params['customfields']['accountID'];
       }

	public function indexAction($params) {
             
             try{
                   $this->api->testConnection();
                   $this->api->login();
                   $info = $this->api->getLicense(null, $this->accountID);
             } catch (Exception $ex) {
                   $this->addError($ex->getMessage());
             }
             return array(
                           "accountID" => $this->accountID,
                           "domainsManagement" => $this->config->domainsManagement,
                           "info" => $info,
                           "errors" => $this->getErrors(),
                           "infos" => $this->getInfos(),
                  );

	}
       
       public function domainsManagementAction($params) {
             
             try{
                   $this->api->testConnection();
                   $this->api->login();
                   try{
                         if(isset($_POST['act'])){
                               if($_POST['act']=="addDomain"){
                                     $this->api->addLicenseDomain(null, $this->accountID, $_POST['licenseDomain']['domain']);
                                     $this->addInfo(sprintf($this->lang['domainsManagement']['domainAdded'], $_POST['licenseDomain']['domain']));
                                     $this->redToCurrentPage();
                               }
                         }
                          if(isset($_GET['delete'])){
                                $this->api->removeLicenseDomain(null, $this->accountID, $_GET['delete']);
                                $this->addInfo(sprintf($this->lang['domainsManagement']['domainDeleted'], $_GET['delete']));
                                $this->redToCurrentPage();
                          }
                         
                   } catch (Exception $ex) {
                         $this->addError($ex->getMessage());
                         $this->redToCurrentPage();
                   }
                   $licenseDomains = $this->api->getLicenseDomains(null,  $this->accountID);
                            
             } catch (Exception $ex) {
                   $this->addError($ex->getMessage());
             }
             return array(
                         "licenseDomains" => $licenseDomains,
                           "errors" => $this->getErrors(),
                           "infos" => $this->getInfos(),
                  );

	}
       
      
        public function ajaxAction($params){
		try {

			switch ($_POST['subaction']){					
				case 'details':
					$res = array(
						'result' => '1',
						'data' => 'test',
					);
					break;
                             break;
				default: throw new Exception('Action not supported');
			}
			
			if (!isset($res)){
				$res = array(
					'result' => '1',
					'msg' => $msg,
				);
			}
		} catch (Exception $e){
			$res = array(
				'result'=> '0',
				'msg'	=> $e->getMessage()
			);
		}
		echo json_encode($res);
		die();
	}
       

}
