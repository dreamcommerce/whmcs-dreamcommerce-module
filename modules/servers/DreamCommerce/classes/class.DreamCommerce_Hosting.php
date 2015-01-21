<?php

/* * ********************************************************************
 * Customization Services by ModulesGarden.com
 * Copyright (c) ModulesGarden, INBS Group Brand, All Rights Reserved 
 * (2014-03-20, 15:54:49)
 * 
 *
 *  CREATED BY MODULESGARDEN       ->        http://modulesgarden.com
 *  CONTACT                        ->       contact@modulesgarden.com
 *
 *
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
class DreamCommerce_Hosting extends MG_Abstract_Hosting {
	
	function getMonthlyCycle() {
             
            switch ($this->hosting_details['billingcycle']) {
                  case "Free Account" : return 0;
                        break;
                  case "One Time" : return 0;
                        break;
                  case "Monthly" : return 1;
                        break;
                  case "Quarterly" : return 3;
                        break;
                  case "Semi-Annually" : return 6;
                        break;
                  case "Annually" : return 12;
                        break;
                  case "Biennially" : return 24;
                        break;
                  case "Triennially" : return 36;
                        break;
            }
            return 0;
      }

}
