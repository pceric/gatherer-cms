{nocache}
<ul class="breadcrumb">
  <li><a href="{$view->url(['module' => 'admin'],null,true)}">Admin Home</a> <span class="divider">&gt;</span></li>
  <li><a href="{$view->url(['module' => 'banner', 'controller' => 'admin', 'action' => 'clients'],null,true)}">Client Management</a> <span class="divider">&gt;</span></li>
  <li class="active">{if isset($cid)}Edit{else}Add{/if}</li>
</ul>
<div class="page-header">
  <h1>{if !empty($cid)}Edit Client{else}Add New Client{/if}</h1>
</div>
{include 'page_alert.tpl'}
<form method="post" class="form-horizontal">
  <fieldset>
    <div class="control-group{if isset($alert_msg)} error{/if}">
      <label class="control-label">Name:</label>
      <div class="controls">
        <input type="text" name="name" value="{$data.name|default:''}" class="input-xlarge" />
      </div>
    </div>
    <div class="control-group">
      <label class="control-label">Phone:</label>
      <div class="controls">
        <input type="text" name="phone" value="{$data.contact|default:''}" class="input-xlarge" />
      </div>
    </div>
    <div class="control-group">
      <label class="control-label">E-Mail:</label>
      <div class="controls">
        <input type="text" name="email" value="{$data.email|default:''}" class="input-xlarge" />
      </div>
    </div>
    <div class="control-group">
      <label class="control-label">Notes:</label>
      <div class="controls">
        <textarea rows="5" name="notes" class="input-xxlarge">{$data.extrainfo|default:''}</textarea>
      </div>
    </div>
    <div class="control-group">
      <input type="submit" name="save" value="Save" class="btn" /> <input type="submit" name="cancel" value="Cancel" class="btn" />
      <input type="hidden" name="type" value="client" />
      {if !empty($cid)}<input type="hidden" name="client" value="{$cid}" />{/if}
    </div>
  </fieldset>
</form>
{/nocache}
