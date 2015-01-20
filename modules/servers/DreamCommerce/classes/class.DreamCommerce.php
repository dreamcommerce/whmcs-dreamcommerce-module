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
     

	public function init($act, $params, $lang) {
      }

	public function indexAction($params) {
             
             try{
                   $this->addError("ki huj");
                   $this->addError("ki huaj");
             } catch (Exception $ex) {
                   $this->addError($ex->getMessage());
             }
             return array(
                           "errors" => $this->getErrors(),
                           "infos" => $this->getInfos(),
                  );

	}
       
       public function domainsManagementAction($params) {
             
             try{

             } catch (Exception $ex) {
                   $this->addError($ex->getMessage());
             }
             return array(
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
