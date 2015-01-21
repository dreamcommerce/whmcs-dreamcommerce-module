{**********************************************************************
 * Customization Services by ModulesGarden.com
 * Copyright (c) ModulesGarden, INBS Group Brand, All Rights Reserved 
 * (2015-01-21)
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
 **********************************************************************}

{**
 * @author Paweł Kopeć <pawelk@modulesgarden.com>
 *}
 <table width="600" class="table">
          <tr><td>{$lang.index.host}:</td><td>{$info->host}</td></tr>
          <tr><td>{$lang.index.customer_name}:</td><td>{$info->customer_name}</td></tr>
          <tr><td>{$lang.index.email}:</td><td> {if !$info->email} - {else} {$info->email} {/if}</td></tr>
          <tr><td>{$lang.index.date_start}:</td><td>{$info->date_start}</td></tr>
          <tr><td>{$lang.index.date_end}:</td><td>{$info->date_end}</td></tr>
          <tr><td>{$lang.index.type_header}:</td><td>{if $info->type eq 1} {$lang.index.type.1}{else} {$lang.index.type.0}{/if}</td></tr>
          <tr><td>{$lang.index.limits.products}:</td><td>{$info->limits->products}</td></tr>
          <tr><td>{$lang.index.limits.admins}:</td><td>{$info->limits->admins}</td></tr>
          <tr><td>{$lang.index.limits.disable_facebook}:</td><td>{if $info->limits->disable_facebook eq 1}<span style="color: green">{$lang.general.on}</span>{else}<span style="color: red">{$lang.general.off}</span>{/if}</td></tr>
          <tr><td>{$lang.index.limits.disable_mobile}:</td><td>{if $info->limits->disable_mobile eq 1}<span style="color: green">{$lang.general.on}</span>{else}<span style="color: red">{$lang.general.off}</span>{/if}</td></tr>
 </table>