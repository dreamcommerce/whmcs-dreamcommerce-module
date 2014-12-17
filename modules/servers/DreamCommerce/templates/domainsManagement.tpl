{**********************************************************************
 * Customization Services by ModulesGarden.com
 * Copyright (c) ModulesGarden, INBS Group Brand, All Rights Reserved 
 * (2014-12-17)
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
<link rel="stylesheet" type="text/css" href="{$assetsUrl}/css/style.css" />
<div class="newvm_form" id="mg-wrapper" style="min-height: 400px;">
    <button onclick="window.location.href='{$serviceMainUrl}'" class="btn btn-small"><i class="icon-arrow-left"></i> {$lang.general.back}</button>
    	<p class='clear'>&nbsp;</p>
	
    <h3 class="set_main_header">{$lang.domainsManagement.main_header}</h3> 
    <div id="vm_alerts"></div>
    <br/>
    <form method="get">
          <input type="hidden" name="action" value="{$smarty.get.action}"/>
          <input type="hidden" name="id" value="{$id}"/>
          <input type="hidden" name="modop" value="{$smarty.get.modop}"/>
          <input type="hidden" name="a" value="{$smarty.get.a}"/>
          <input type="hidden" name="act" value="{$smarty.get.act}"/>
          {$lang.action_logs.from}   <input type="text" name="from" value="" id="actLog_from" style="width:65px;"/>
          <span style="margin-left: 5px;"> {$lang.action_logs.to} </span> <input type="text" name="to" value="" id="actLog_to" style="width:65px; " />
          <input type="submit" class="btn btn-success" name="filter" value="{$lang.action_logs.filter}" style="margin-bottom: 10px; margin-left: 5px;"/>
    </form>
    <table class="table table-bordered">
          <tr>
                <th>{$lang.action_logs.category}</th>
                <th>{$lang.action_logs.title}</th>
                <th>{$lang.action_logs.description}</th>
                <th>{$lang.action_logs.remoteIP}</th>
                <th>{$lang.action_logs.timestamp}</th>
          </tr>
          {foreach from=$logs item="log"}
                <tr>
                      <td>{$log->category}</td>
                      <td>{$log->title}</td>
                      <td>{$log->description}</td>
                      <td>{$log->remoteIP}</td>
                      <td>{$log->getDate()}</td>
                </tr>
          {foreachelse}
                <tr><td colspan="6">{$lang.action_logs.no_log}</td></tr>
          {/foreach}
    </table>
</div>
