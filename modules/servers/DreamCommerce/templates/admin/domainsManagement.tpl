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
 <table  width="100%" border="0" cellspacing="1" cellpadding="3" class="datatable">
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
  <div style="margin:2px;" >
            <button class="btn btn-default" id="dc_buttonDomainAdd" type="button"> {$lang.domainsManagement.add} </button>
    </div>
<div id="so_dialogAddDomain" style="display:none;" title="{$lang.domainsManagement.modal.header}"> 
       <div id="so_dialogAlertError" style="color:red;  margin-left:5px;">   
       </div>
       <div id="so_dialogAlertSucces" style="color:green;  margin-left:5px;">   
       </div>
       <table cellspacing="2" cellpadding="3" border="0" style="margin-top:5px;">
             <tbody>
                   <tr>
                         <td  width="35%">{$lang.domainsManagement.dialog.domain}:</td>
                         <td>
                              <input type="text" id="dcInputDomain" value="" style="width:250px;">
                         </td>
                   </tr>
             </tbody>
       </table>
        <div style="text-align: center; margin-top:10px;">         
              <img src="../images/loading.gif" class="dialogLoader" style="display:none;" />      
        </div>
 </div>
<div id="so_dialogDeleteDomain" style="display:none;" title="{$lang.domainsManagement.modal.deleteDomain}"> 
       <div class="dialogError" style="color:red;  margin-left:5px;">   
       </div>
       <div class="dialogSucces" style="color:green;  margin-left:5px;">   
       </div>
       {$lang.domainsManagement.deleteConfirm}
        <div style="text-align: center; margin-top:10px;">         
              <img src="../images/loading.gif" class="dialogLoader" style="display:none;" />      
        </div>
 </div>      
{literal}
<script type="text/javascript">
      $(document).ready(function() {
            $(".so_delete").click(function(){
                  $("#so_dialogDeleteDomain .dialogError").html("").hide();
                  $("#so_dialogDeleteDomain .dialogSucces").html("").hide();
                  $("#so_dialogDeleteDomain" ).dialog({   
                            "buttons": [ 
                                { 
                                    text: "Cancel", 
                                    click: function() { 
                                        jQuery( this ).dialog( "close" ); 
                                    } 
                                },
                                {
                                    text: "Save", 
                                    click: function(){
                                        $("#so_dialogDeleteDomain .dialogLoader").show();
                                        $.post("{/literal}{$servicePageUrl}{literal}&DreamCommerce_ajax=1", {"action":"addDomain", domain: $("#dcInputDomain").val() }, 
                                            function(res){
                                                   $("#so_dialogDeleteDomain .dialogLoader").hide();
                                                    if (res.result == '1'){
                                                          $("#so_dialogDeleteDomain .dialogSucces").show().html(res.msg).fadeOut(6000);
                                                          location.reload(); 
                                                    }else{
                                                           $("#so_dialogDeleteDomain .dialogError").show().html(res.msg).fadeOut(6000);
                                                    }
                                      }, "json");
                                    }
                                } 
                           ]
                  }).dialog("open");
                  return false;
           });
           
           $("#dc_buttonDomainAdd").click(function(){
                 $("#so_dialogAlertError").html("").hide();
                 $("#so_dialogAlertSucces").html("").hide();
                 $("#so_dialogLoader").hide();
                 $("#dcInputDomain").val("");
                 $( "#so_dialogAddDomain" ).dialog({   
                             width: 350,
                             height: 180,
                            "buttons": [ 
                                { 
                                    text: "Cancel", 
                                    click: function() { 
                                        jQuery( this ).dialog( "close" ); 
                                    } 
                                },
                                {
                                    text: "Save", 
                                    click: function(){
                                        $("#so_dialogLoader").show();
                                        $.post("{/literal}{$servicePageUrl}{literal}&DreamCommerce_ajax=1", {"action":"addDomain", domain: $("#dcInputDomain").val() }, 
                                            function(res){
                                                   $("#so_dialogLoader").hide();
                                                    if (res.result == '1'){
                                                          $("#so_dialogAlertSucces").show().html(res.msg).fadeOut(6000);
                                                          location.reload(); 
                                                    }else{
                                                           $("#so_dialogAlertError").show().html(res.msg).fadeOut(6000);
                                                    }
                                      }, "json");
                                    }
                                } 
                           ]
                  }).dialog("open");
                 return false;
           });
      });
</script>
{/literal}