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
    {if $dedicatedId}
        <div id="serverstats">
            <table width="90%" class="table table-striped">
                <tr><td>{$lang.index.refresh}</td><td><span id="serverstatus" style="display: none;"><img src="images/loadingsml.gif"></span><a href="#" onclick="i3D_Action('details');return false;"><img src="{$assetsUrl}/img/refresh.png" alt="" /></a></td></tr>
                <tr><td>{$lang.index.server}</td><td class="vps_label">{$details->dediserverName}</td></tr>
                <tr><td>{$lang.index.status}</td><td id="vm_status">{if $details->status == "ACTIVE"}<span class="green">{$vm->status}</span>{else}<span class="red">{$details->status}</span>{/if}</td></tr>
                <tr><td>{$lang.index.operating_sys}</td><td class="vps_label">{$details->osName}</td></tr>
                <tr><td>{$lang.index.project_name}</td><td class="vps_label">{if $details->projectName} {$details->projectName} {else} - {/if}</td></tr>
                <tr><td>{$lang.index.server_name}</td><td class="vps_label">{if $details->clientServerName} {$details->clientServerName}{else} - {/if}</td></tr>
                <tr><td>{$lang.index.brand}</td><td class="vps_label">{$details->serverCase}</td></tr>
            </table>
        </div>  

        <div id="rbuttons">
                {if $perm.per_reinstall}
				<button class="btn" onclick="window.location.href ='{$servicePageUrl}&act=reinstall';">
					<img class="manage_img" src="{$assetsUrl}/img/rebuild.png"/>{$lang.index.rebuild}</button>
                {/if}
                {if $perm.per_graphs}
				<button class="btn" onclick="window.location.href ='{$servicePageUrl}&act=graphs';">
					<img class="manage_img" src="{$assetsUrl}/img/graphs.png"/>{$lang.index.graphs}</button>
                {/if}
                {if $perm.per_ip_management}
				<button class="btn" onclick="window.location.href ='{$servicePageUrl}&act=ip_management';">
					<img class="manage_img" src="{$assetsUrl}/img/network2.png"/>{$lang.index.ip_management}</button>
                {/if}
                {if $perm.per_action_logs}
				<button class="btn" onclick="window.location.href ='{$servicePageUrl}&act=action_logs';">
					<img class="manage_img" src="{$assetsUrl}/img/notes.png"/>{$lang.index.action_logs}</button>
                {/if}
        </div>

		{if $perm.caf_backups || $perm.caf_console}
			<h3 class="header_label">{$lang.vm.additionals}</h3>
			<div id='nbuttons'>
				{if $perm.caf_backups}
				<button class="btn"class="btn" onclick="window.location.href='{$servicePageUrl}&act=backups';">
					<img class="manage_img" src="{$assetsUrl}img/backup.png"/> {$lang.vm.backups}</button>
				{/if}
				{if $perm.caf_console}
				<button class="btn" onclick="window.open('{$servicePageUrl}&act=console','{$lang.vm.console},target=_blank','width=950,height=780,resizable=yes');return false;">
					<img class="manage_img" src="{$assetsUrl}img/console.png"/> {$lang.vm.console}</button>
				{/if}
				{if $perm.caf_keypair && $isPrivateKey}
				<button class="btn"class="btn" id="OpenStack_privateKeyDoownload" onclick="{if $delete_keypair}OpenStack_privateKeyDoownload();{else}window.location.href='{$servicePageUrl}&act=keyDownload&keytype=private';{/if}">
					<img class="manage_img" src="{$assetsUrl}img/notes.png"/> {$lang.keypair.download_private}</button>
                            {/if}
                            {if $perm.caf_keypair && $isPublicKey}
				<button class="btn"class="btn" onclick="window.location.href='{$servicePageUrl}&act=keyDownload&keytype=public';">
					<img class="manage_img" src="{$assetsUrl}img/notes.png"/> {$lang.keypair.download_public}</button>
				{/if}
			</div>
		{/if}
              <h3 class="header_label">{$lang.index.mem_header}</h3>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>{$lang.index.mem_slot}</th>
					<th>{$lang.index.mem_brand}</th>
					<th>{$lang.index.mem_model}</th>
                                   <th>{$lang.index.mem_size}</th>
                                   <th>{$lang.index.mem_speed}</th>
                                   <th>{$lang.index.mem_type}</th>
				</tr>
			</thead>
			{foreach from=$memorys item="mem"}
				<tr>
					<td>{$mem->memorySlot}</td>
					<td>{$mem->brand}</td>
					<td>{$mem->model}</td>
                                   <td>{$mem->size}</td>
                                   <td>{$mem->speed}</td>
                                   <td>{$mem->memoryType}</td>
				</tr>
			{foreachelse}
				<tr><td colspan="6">{$lang.index.no_memorys}</td></tr>
			{/foreach}
		</table>
              
		<h3 class="header_label">{$lang.index.hdd_header}</h3>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>{$lang.index.hdd_type}</th>
					<th>{$lang.index.hdd_product}</th>
                                   <th>{$lang.index.hdd_firmware}</th>
                                   <th>{$lang.index.hdd_size}</th>
				</tr>
			</thead>
			{foreach from=$hdds item="hdd"}
				<tr>
					<td>{$hdd->diskType}</td>
					<td>{$hdd->product}</td>
					<td>{$hdd->firmwareVersion}</td>
                                   <td>{$hdd->size}</td>
				</tr>
			{foreachelse}
				<tr><td colspan="4">{$lang.index.no_hdds}</td></tr>
			{/foreach}
		</table>
		
		
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
	
       function so_disableMessages(){

       }
	jQuery(document).ready(function(){
		OSUI.disableMessages();
	});
	{/literal}
</script>
