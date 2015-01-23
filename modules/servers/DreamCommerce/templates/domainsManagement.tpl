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
<link href="{$assetsUrl}/css/bootstrap.css" rel="stylesheet">
<div class="newvm_form" id="mg-wrapper" style="min-height: 400px;">
    <button onclick="window.location.href='{$serviceMainUrl}'" class="btn btn-small"><i class="icon-arrow-left"></i> {$lang.general.back}</button>
    	<p class='clear'>&nbsp;</p>
	
    <h3 class="set_main_header">{$lang.domainsManagement.main_header}</h3> 
    <div id="vm_alerts">
		{if $errors}
                    {foreach from=$errors item="error"}
                         <div class="box-error">{$error}</div> 
                    {/foreach}
		{else}
                    {foreach from=$infos item="info"}
                         <div class="box-success">{$info}</div> 
                    {/foreach}
              {/if}
    </div>
    <br/>
    <table class="table table-bordered">
          <thead>
                <tr>
                      <th>{$lang.domainsManagement.id}</th>
                      <th>{$lang.domainsManagement.domain}</th>
                      <th width="100" style="text-align: center;">{$lang.domainsManagement.actions}</th>
                </tr>
          </thead>
          {foreach from=$licenseDomains key="key" item="domain"}
                <tr>
                      <td>{$key+1}</td>
                      <td>{$domain}</td>
                      <td style="text-align: center;"> <a class="btn btn-small btn-danger so_delete" data-domain="{$domain}"  href="{$servicePageUrl}&act=domainsManagement&delete={$domain}">{$lang.general.delete}</a></td>
                </tr>
          {foreachelse}
                <tr><td colspan="3">{$lang.domainsManagement.empty}</td></tr>
          {/foreach}
    </table>
    <div style="text-align: center;" >
            <button class="btn btn-success" data-toggle="modal" data-target="#dc_modalAddDomain" id="dc_buttonDomainAdd"> {$lang.domainsManagement.add} </button>
    </div>
</div>
    
<div class="modal fade" id="dc_modalAddDomain" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <form method="post">
            <div class="modal-dialog">
                  <div class="modal-content">
                        <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"></span></button>
                              <h4>{$lang.domainsManagement.modal.header}</h4>
                        </div>
                        <div class="modal-body">
                              <table class="table table-bordered table-striped" style="width:100%; margin-top:10px;">
                                    <tr>
                                          <td style="width:25%;"> 
                                                <label for="licenseDomainDomain" class="control-label" style="display:inline;float:none;">{$lang.domainsManagement.modal.domain}: </label>
                                          </td>
                                          <td>
                                                <select  name="licenseDomain[domain]" id="licenseDomainDomain" style="width: 220px; margin-bottom: -1px;">
                                                      {foreach from=$domains item=domain}
                                                            <option value="{$domain}">{$domain}</option>
                                                      {/foreach}   
                                                </select>
                                          </td>
                                    </tr>

                              </table>
                        </div>
                        <div class="modal-footer">
                              <button type="button" class="btn btn-default" data-dismiss="modal">{$lang.general.close}</button>
                              <button type="submit" class="btn btn-primary"  name="act" value="addDomain">{$lang.general.add}</button>
                        </div>
                  </div>
            </div>
      </form>
</div>
{literal}
<script src="{/literal}{$assetsUrl}{literal}/js/bootstrap.min.js"></script>
<script type="text/javascript">
      $(document).ready(function() {
            $(".so_delete").click(function(){
                  return confirm("{/literal}{$lang.domainsManagement.deleteConfirm}{literal}");
           });
      });
</script>
{/literal}