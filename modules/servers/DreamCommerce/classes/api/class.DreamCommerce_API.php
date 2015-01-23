<?php

/* * ********************************************************************
 * DreamCommerce Product developed. (2015-01-20)
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
class DreamCommerce_API  extends MG_CurlApi{

      protected $url;
      protected $debug;
      protected  $login;
      protected  $password;
      
      private $session;
      /**
       * Init object
       * @param sting $url
       * @param sting $login
       * @param sting $password
       * @param sting $debug
       */
      public function __construct($url, $login, $password, $debug) {
           $this->isJson = true;
           $this->url =  $url;
           $this->debug = (boolean) $debug;
           $this->login = $login; 
           $this->password = $password;
           parent::__construct($this->url, $this->login, $this->password, "DreamCommerce", $this->debug);
      }      
      /**
       * Process Request
       * @param object $res
       * @return object
       * @throws DreamCommerce_Exception
       */
      private function processRequest($res){
            if(isset($res->error)&& $res->error){
                  throw new DreamCommerce_Exception((string) $res->error , (int)$res->code);
            }
            return $res;
      }
      
      /**
       * Get Session identifier
       * @return string
       */
      public function getSession(){ return $this->session;}
           
      public function testConnection(){
            $data = array( 
                         "method"  => "testConnection"
                         ,'params' => array()
                );
            return $this->processRequest($this->call($data ));
      }
      /**
       * Allows user authentication
       * 
       * @param string $login
       * @param string $password
       * @return object|null
       */
      public function login($login=null, $password=null){
            if(!$login)
                  $login = $this->login;
            if(!$password)
                  $password = $this->password;
            
            $data = array( 
                         "method"  => "login"
                         ,'params' => array($login, $password)
                );
            $this->session = $this->processRequest($this->call($data));
      }
      /**
       * Returns list of available license packages
       * 
       * @param string $session
       * @return object|null
       */
      public function getPackages($session=null){
            if(!$session) $session= $this->session;
            $data = array( 
                         "method"  => "getPackages"
                         ,'params' => array($session)
                );
            
            return $this->processRequest($this->call($data));
      }
      
      /**
       * Returns list of available license periods
       * 
       * @param string $session
       * @return object|null
       */
      public function getPeriods($session=null){
            if(!$session) $session= $this->session;
            $data = array( 
                         "method"  => "getPeriods"
                         ,'params' => array($session)
                );
            
            return $this->processRequest($this->call($data));
      }
      
       /**
       * Create license
       * 
       * @param string $session session identifier
       * @param string $email user email address
       * @param string $host shop address, e.g. "store001.partnerdomain.com"
       * @param int $type license type (0 - trial, 1 - full version)
       * @param string $package package name
       * @param int $period period (months)
       * @param string $version shop version, if empty latest version will be used
       * @param string $info additional notes
       * @return object|null
       */
      public function createLicense($session=null, $email, $host, $type, $package, $period,$version=null, $info=null){
            if(!$session) $session= $this->session;
            $data = array( 
                           "email" => $email
                          ,"host"  => $host
                          ,"type"  => $type
                          ,"package" => $package
                          ,"period" => $period
            );
            if($version)
                  $data['version'] = $version;
            if($info)
                  $data['info'] = $info;
            
            $data = array( 
                         "method"  => "createLicense"
                         ,'params' => array($session, $data)
                );
            
            return $this->processRequest($this->call($data ));
      }
      /**
       * Return license details
       * 
       * @param string $session
       * @param string $licenseID
       * @return object|null
       */
      function getLicense($session=null, $licenseID){
            if(!$session) $session= $this->session;
            $data = array( 
                         "method"  => "getLicense"
                         ,'params' => array($session, $licenseID)
                );
            return $this->processRequest($this->call($data ));
      }
      
      /**
       * Remove license
       * 
       * @param string $session
       * @param string $licenseID
       * @return object|null
       */
      function removeLicense($session=null, $licenseID){
            if(!$session) $session= $this->session;
            $data = array( 
                         "method"  => "removeLicense"
                         ,'params' => array($session, $licenseID)
                );
            return $this->processRequest($this->call($data ));
      }
      
      /**
       * Return list of domains for license
       * 
       * @param string $session
       * @param string $licenseID
       * @return object|null
       */
      function getLicenseDomains($session=null, $licenseID){
            if(!$session) $session= $this->session;
            $data = array( 
                         "method"  => "getLicenseDomains"
                         ,'params' => array($session, $licenseID)
                );
            return $this->processRequest($this->call($data ));
      }
      
       /**
       * Renew license
       * 
       * @param string $session
       * @param string $licenseID
       * @param int $period renew period (months)
       * @return object|null
       */
      function renewLicense($session=null, $licenseID, $period){
            if(!$session) $session= $this->session;
            $data = array( 
                         "method"  => "renewLicense"
                         ,'params' => array($session, $licenseID, $period)
                );
            return $this->processRequest($this->call($data ));
      }
      
       /**
       * Set new package for license
       * 
       * @param string $session
       * @param string $licenseID
       * @param string $package package name
       * @return object|null
       */
      function changeLicensePackage($session=null, $licenseID, $package){
            if(!$session) $session= $this->session;
            $data = array( 
                         "method"  => "changeLicensePackage"
                         ,'params' => array($session, $licenseID, $package)
                );
            return $this->processRequest($this->call($data ));
      }
      
      /**
       * Suspend license
       * 
       * @param string $session
       * @param string $licenseID
       * @return object|null
       */
      function suspendLicense($session=null, $licenseID){
            if(!$session) $session= $this->session;
            $data = array( 
                         "method"  => "suspendLicense"
                         ,'params' => array($session, $licenseID)
                );
            return $this->processRequest($this->call($data ));
      }
      
       /**
       * Unsuspend license
       * 
       * @param string $session
       * @param string $licenseID
       * @return object|null
       */
      function unsuspendLicense($session=null, $licenseID){
            if(!$session) $session= $this->session;
            $data = array( 
                         "method"  => "unsuspendLicense"
                         ,'params' => array($session, $licenseID)
                );
            return $this->processRequest($this->call($data ));
      }
      
       /**
       * Check availability of the domain
       * 
       * @param string $session
       * @param string $domain
       * @return object|null
       */
      function checkDomainAvailability($session=null, $domain){
            if(!$session) $session= $this->session;
            $data = array( 
                         "method"  => "checkDomainAvailability"
                         ,'params' => array($session, $domain)
                );
            return $this->processRequest($this->call($data ));
      }
      
       /**
       * Add domain for license
       * 
       * @param string $session
       * @param string $licenseID
       * @param string $domain
       * @return object|null
       */
      function addLicenseDomain($session=null, $licenseID, $domain){
            if(!$session) $session= $this->session;
            $data = array( 
                         "method"  => "addLicenseDomain"
                         ,'params' => array($session,$licenseID, $domain)
                );
            return $this->processRequest($this->call($data ));
      }
      
       /**
       * Remove domain for license
       * 
       * @param string $session
       * @param string $licenseID
       * @param string $domain
       */
      function removeLicenseDomain($session=null, $licenseID, $domain){
            if(!$session) $session= $this->session;
            $data = array( 
                         "method"  => "removeLicenseDomain"
                         ,'params' => array($session,$licenseID, $domain)
                );
            return $this->processRequest($this->call($data ));
      }

      /**
       * Add email account for license
       * 
       * @param string $session
       * @param string $licenseID
       * @param string $name
       * @param string $password
       * @param int $quota
       * @return object|null
       */
      function addLicenseMail($session=null, $licenseID, $name, $password, $quota){
            if(!$session) $session= $this->session;
            $data = array(
                          "name" => $name,
                          "password" => $password,
                          "quota"  => $quota
                    
                    );
            $data = array( 
                         "method"  => "addLicenseMail"
                         ,'params' => array($session,$licenseID, $data)
                );
            return $this->processRequest($this->call($data ));
      }
      
      /**
       * Update email account for license
       * 
       * @param string $session
       * @param string $licenseID
       * @param string $name
       * @param string $password
       * @param int $quota
       * @return object|null
       */
      function editLicenseMail($session=null, $licenseID, $name, $password, $quota){
            if(!$session) $session= $this->session;
            $data = array(
                          "name" => $name,
                          "password" => $password,
                          "quota"  => $quota
                    
                    );
            $data = array( 
                         "method"  => "editLicenseMail"
                         ,'params' => array($session,$licenseID, $data)
                );
            return $this->processRequest($this->call($data ));
      }
      
      /**
       * Remove email account for license
       * 
       * @param string $session
       * @param string $licenseID
       * @param string $name
       * @return object|null
       */
      function removeLicenseMail($session=null, $licenseID, $name){
            if(!$session) $session= $this->session;
            $data = array( 
                         "method"  => "removeLicenseMail"
                         ,'params' => array($session,$licenseID, $name)
                );
            return $this->processRequest($this->call($data ));
      }
}
