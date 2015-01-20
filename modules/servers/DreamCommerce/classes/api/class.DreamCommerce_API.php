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
class DreamCommerce_API  extends MG_CurlApi{

      protected $url;
      protected $debug;
      protected  $login;
      protected  $password;
      
      private $session;

      public function __construct($url, $login, $password, $debug) {
           $this->isJson = true;
           $this->url =  $url;
           $this->debug = (boolean) $debug;
           $this->login = $login; 
           $this->password = $password;
           parent::__construct($this->url, $this->login, $this->password, "DreamCommerce", $this->debug);
      }      

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
      
      /**
       * Creates license
       * 
       * @param string $session session identifier
       * @param string $email user email address
       * @param string $host shop address, e.g. "store001.partnerdomain.com"
       * @param int $type license type (0 - trial, 1 - full version)
       * @param string $package package name
       * @param int $period period (months)
       * @param string $version shop version, if empty latest version will be used
       * @param string $info additional notes
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
            
      }
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
       * @return object
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
       * @return object
       */
      public function getPeriods($session=null){
            if(!$session) $session= $this->session;
            $data = array( 
                         "method"  => "getPeriods"
                         ,'params' => array($session)
                );
            
            return $this->processRequest($this->call($data));
      }
}
