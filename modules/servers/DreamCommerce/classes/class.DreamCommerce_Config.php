<?php

/**********************************************************************
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
 **********************************************************************/

/**
 * @author Pawel Kopec <pawelk@modulesgarden.com>
 * Product configuration
 */
class DreamCommerce_Config {

      private $config = array();
      
      public $lmsPartner, $username, $password, $host, $package, $period, $debugMode, $hostPrefix, $nextStoreID, $licenseType, $shopVersion, $domainsManagement;
      /**
       * Construct
       * @param array $config
       * @param array $params
       */
      public function __construct($config, $params) {
            $this->config = $config;
            $this->loadConfig($params);
      }
      /**
       * Set Config
       * @param array $config
       */
      public function setConfig($config){
            $this->config = $config;
      }
      /**
       * Get Option Key
       * @param string $optionName
       * @param array $params
       * @return string
       */
      public function getOptionKey($optionName, $params){
             $i = 1;
            foreach ($this->config as $key => $value) {
                  if ($optionName==$key && isset($params['configoption' . $i]) ) {
                        return 'configoption' . $i;
                  }
                  $i++;
            }
      }
      /**
       * Load Config
       * @param array $params
       */
      private function loadConfig($params) {

            $i = 1;
            foreach ($this->config as $key => $value) {
                  if ( isset($params['configoption' . $i])) {
                        $this->$key = $params['configoption' . $i];
                  }
                  $i++;
            }
      }
      /**
       * Get Period ID
       * @return int
       */
      public function getPeriodID(){
            return (int) $this->period;
      }

}
