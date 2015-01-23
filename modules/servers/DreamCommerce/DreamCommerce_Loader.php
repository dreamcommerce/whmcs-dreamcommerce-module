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
 * Load  classes
 * @author Pawel Kopec <pawelk@modulesgarden.com>
 * @param string $class
 */
function DreamCommerce_Loader($class){
      
    if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.'.$class.'.php')){
                include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.'.$class.'.php';
    }
    else if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'api'.DIRECTORY_SEPARATOR.'class.'.$class.'.php')){
          include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'api'.DIRECTORY_SEPARATOR.'class.'.$class.'.php';
    }
          
}
spl_autoload_register('DreamCommerce_Loader');