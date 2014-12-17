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
class DreamCommerce_API  extends Mg_CurlApi{

      protected $url;
      protected $debug;
      protected $userId = 0;
      protected $apiKey = '';
      private static $categories = array(
          'colocation',
          'dedicatedserver',
          'gameserver',
          'voiceserver',
          'logging'
      );
      const colocation='colocation';
      const dedicatedserver = 'dedicatedserver';
      const gameserver = 'gameserver';
      const voiceserver = 'voiceserver';
      
      private static $jsonRequest = true;

      public function __construct() {

      }
      /**
       * 
       */
      public function setWHMCSConnection(array $params){

            $host = $params['serverip'] ? $params['serverip'] : $params['serverhostname'];
            if ($host == null)
                  $host = 'http://internalbeta.i3d.net/api/rest/';
            $this->url =  $host;
            $this->userId = $params['serverusername'];
            $this->apiKey = $params['serverpassword'];
            $this->debug = (boolean) $params['debug_mode'];
            parent::__construct($this->url, $this->userId, $this->apiKey, "i3D", $this->debug);
      }

      /**
       * 
       * @param type $category
       * @param type $action
       * @param array $option
       * @return type
       * @throws Exception
       */
      public function doRequest($category, $action, array $option = null) {
            // Validate some vars
            if ($this->userId < 1 || strlen($this->apiKey) != 20)
                  throw new Exception("Invalid parameters");

            if (!in_array($category, self::$categories))
                  throw new Exception("Invalid category");

            // Create POST data
            $data = array(
                'userId' => $this->userId,
                'apiKey' => $this->apiKey,
                'action' => $action
            );
            
            if ($option) {
                  $data = array_merge($data, $option);
            }
            self::$jsonRequest ?     $request = json_encode($data): $request = http_build_query($data);
            $headers = array('Content-type: application/json',  //x-www-form-urlencoded
                                         "Content-Length: ".strlen($request));
            
            $url = $this->url . $category;
            $response = $this->call( $request, $headers, $url);
            if($response)
                  return $this->processRequest($response);
      }
      
      public function testConnection(){
//            $result = $this->doRequest(I3D_API::dedicatedserver, 'getAll');
//            if($result->status !="Success")
//                  throw new Exception ($result->msg);
//            return true;
      }
      
      private function processRequest($response){
            $res = json_decode($response);
            if(!$res && strpos($response, 'ERROR')!== false)
                    throw new Exception((string) $response);
            if($res->status !="Success" &&  $res->msg)
                  throw new Exception((string) $res->msg);
            
            if($res->data->result && strtolower($res->data->result)=="error")
                  throw new Exception((string) $res->data->message);
            
            if($res->data->updates[0]->result &&  strtolower($res->data->updates[0]->result)=="error")
                  throw new Exception((string) $res->data->updates[0]->msg);
            return $res;
      }
      

}
