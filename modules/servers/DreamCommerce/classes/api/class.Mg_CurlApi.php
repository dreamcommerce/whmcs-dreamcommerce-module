<?php
/**********************************************************************
 * Custom developed. (2014-07-01)
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

class Mg_CurlApi{
      
      protected $url;
      protected $username;
      protected $password;
      private $debug;
      protected $module;
      private $isJson;
      private $isXML;
      
      /**
       * Construct
       * @param string $url
       * @param string $username
       * @param string $password
       * @param string $module
       * @param string $debug
       */
      public function __construct($url, $username, $password, $module, $debug = false) {
            $this->url = $url;
            $this->username = $username;
            $this->password = $password;
            $this->debug = (boolean) $debug;
            $this->module = $module;
      }
      
      /**
       * FUNCTION call
       * Call to  API
       * @param string $request
       * @return boolean|string
       */
      public function call($request, $headers = array(), $url=null) {

   
            if(!$url)
                  $url  = $this->url;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
            curl_setopt($ch, CURLOPT_TIMEOUT, 50);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            if(!empty($headers)){
                 curl_setopt($ch, CURLOPT_HTTPHEADER,  $headers); 
            }
            $data = curl_exec($ch);
            if ($this->debug && function_exists('logModuleCall')) {
                  logModuleCall(
                          $this->module, $url, print_r($request, true), '', print_r($data, true), array($this->username, $this->password)
                  );
            }
            if ($data === false || $data == "") {
                  $err = ucwords(curl_error($ch)) ? ucwords(curl_error($ch)) : "Unable connect to: " . $url;
                  curl_close($ch);
                  throw new Exception("CURL Error: ".$err, 0);
            }
            curl_close($ch);
            return $data;
      }
      
      /**
       * FUNCTION parseXML
       * @param string $xml
       * @return boolean|object
       */
      protected function parseXML($xml) {

            if (!$xml) {
                  throw new Exception("Parsing XML failed");
            }
            $a = simplexml_load_string($xml);
            if ($a === FALSE) {
                  throw new Exception("Parsing XML failed");
                  ;
            } else {
                  return $a;
            }
      }
}