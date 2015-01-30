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
<div>
    <h2 class="set_main_header">{$lang.index.main_header}</h2> 
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
       {if $accountID}
        <div>
            <table width="90%" class="table table-striped">
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
                <tr><td>{$lang.index.username}:</td><td>{$username}</td></tr>
                <tr><td>{$lang.index.password}:</td><td>{$password}</td></tr>
            </table>
        </div>  
        <div id="rbuttons">
                <form method="post" target="blank" action="https://{$domain}/admin/dashboard"> 
                {if $domainsManagement}
				<button class="btn" type="button" onclick="window.location.href ='{$servicePageUrl}&act=domainsManagement';">
					<img class="manage_img" src="{$assetsUrl}/img/rebuild.png"/>{$lang.index.domainsManagement}</button>
                {/if}
           
                      <input type="hidden" name="login" value="{$username}" />
                      <input type="hidden" name="password" value="{$password}" />
                      <input type="hidden" name="redirect" value="/admin/dashboard">
                      <button class="btn" type="submit"><img class="manage_img" src="{$assetsUrl}/img/password.png"/>{$lang.index.login}</button>
                </form>
        </div>
        {/if}
</div>

<script type="text/javascript">
	{literal}
	
	var OSUI = {
		'clearMessages': function(){
			$("#vm_alerts").html('');
		},
              'disableMessages': function(){
                   if($("#vm_alerts .box-success").size()){
                         $("#vm_alerts .box-success").delay(8200).fadeOut(300);
                   }
                   if($("#vm_alerts .box-error").size()){
                         $("#vm_alerts .box-error").delay(8200).fadeOut(300);
                   }
		},
		'addMessage': function(success, msg){
			var cl = success ? "box-success" : "box-error";
			$("#vm_alerts").show().append('<div class="'+cl+'">'+msg+'</div>').delay(8200).fadeOut(300);
		},
		'addLoading': function(){
			$("#vm_alerts").show().html('<div style="margin:10px 0;"><img src="images/loadingsml.gif" /> {/literal}{$lang.general.pleasewait}{literal}</div>');
		}
              
	};
	
	jQuery(document).ready(function(){
		OSUI.disableMessages();
	});
	{/literal}
</script>
