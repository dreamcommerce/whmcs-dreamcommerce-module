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
 * Controller Client Area
 * @author Pawel Kopec <pawelk@modulesgarden.com>
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
      /**
       * Init Object
       * @param array $params
       */
      public function init($params) {
             $this->lang   = $this->getLang($params);
             $config       = DreamCommerce_ConfigOptions(array(1));
             $this->config = new DreamCommerce_Config($config, $params);
             $this->api    = new DreamCommerce_API($this->config->host, $this->config->username, $this->config->password, $this->config->debugMode);
             $this->accountID = $params['customfields']['accountID'];
       }
       
       /**
        * Client Area Index Action
        * @param array $params
        * @return array
        */
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
                           "domain" => $params['domain'],
                           "username" => $params['username'],
                           "password" => $params['password'],
                           "domainsManagement" => $this->config->domainsManagement,
                           "info" => $info,
                           "errors" => $this->getErrors(),
                           "infos"  => $this->getInfos(),
                  );

	}
       /**
        * Client Area Aomains Management Action
        * @param array $params
        * @return array
        */
       public function domainsManagementAction($params) {
             
             try{
                   $this->api->testConnection();
                   $this->api->login();
                   $domains = mysql_get_array("select id,domain from tbldomains where userid=? and `status`=? order by domain", array($params['clientsdetails']['userid'],"Active"));
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
                   $domains[] = array("domain" =>  $params['domain'] );
                   $temp = array();
                   foreach($domains as $domain){
                      $temp[] =    array("id" => $domain['id'], "name" => $domain['domain']);
                   } 
                   $domains = $temp;
                   unset($temp);
                   $licenseDomains = $this->api->getLicenseDomains(null,  $this->accountID);
             
                   foreach($licenseDomains as $key => $domain){
                         $licenseDomains[$key] =  array("id" => "", "name" => $domain);
                         foreach($domains as $k => $d){
                               if($domain == $d['name'] )
                                     unset($domains[$k]);
                               $licenseDomains[$key]['id'] = $d['id'];
                         }
                   }
             } catch (Exception $ex) {
                   $this->addError($ex->getMessage());
             }
             
             return array(
                          "domains" => $domains,
                         "licenseDomains" => $licenseDomains,
                           "errors" => $this->getErrors(),
                           "infos" => $this->getInfos(),
                  );

	}
           
}
