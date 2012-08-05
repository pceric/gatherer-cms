{nocache}
<ul class="breadcrumb">
  <li><a href="{$view->url(['module' => 'admin'],null,true)}">Admin Home</a> <span class="divider">&gt;</span></li>
  <li><a href="{$view->url(['module' => 'banner', 'controller' => 'admin', 'action' => 'clients'],null,true)}">Client Management</a> <span class="divider">&gt;</span></li>
  <li><a href="{$view->url(['module' => 'banner', 'controller' => 'admin', 'action' => 'banners', 'cid' => $cid],null,true)}">Banner Management</a> <span class="divider">&gt;</span></li>
  <li class="active">{if isset($data.id)}Edit{else}Add{/if}</li>
</ul>
<div class="page-header">
  <h1>{if !empty($data.id)}Edit Banner{else}Add New Banner{/if}</h1>
</div>
<form method="post" class="form-horizontal">
  <fieldset>
    <div class="control-group">
      <label class="control-label">Size:</label>
      <div class="controls">
        {html_options name=size values=$sizes output=$sizes selected=$data.size|default:'468x60'}
      </div>
    </div>
  </fieldset>
  <fieldset>
    <div class="control-group">
      <label class="control-label">Type:</label>
      <div class="controls">
        <select name="bantype" id="banner-type">
          <option value="img">IMG + URL</option>
          <option value="custom"{if !empty($data.code)} selected="selected"{/if}>Custom Code</option>
        </select>
      </div>
    </div>
  </fieldset>
  <fieldset id="banner-fs-img">
    <div class="control-group">
      <label class="control-label">Image:</label>
      <div class="controls">
        <input type="text" name="image" id="image" value="{$data.image|default:''}" class="input-xlarge" />
      </div>
    </div>
    <div class="control-group">
      <label class="control-label">URL:</label>
      <div class="controls">
        <input type="text" name="url" id="url" value="{$data.url|default:''}" class="input-xlarge" />
      </div>
    </div>
    </fieldset>
    <fieldset id="banner-fs-custom">
    <div class="control-group">
      <textarea rows="5" cols="50" name="code" class="input-xxlarge">{$data.code|default:''}</textarea>
    </div>
    </fieldset>
    <fieldset>
    <div class="control-group">
      <label class="checkbox">Active
        {html_toggle name="active" checked=$data.active labels=false}
      </label>
    </div>
    <div class="control-group">
      <input type="submit" name="save" value="Save" class="btn" /> <input type="submit" name="cancel" value="Cancel" class="btn" />
      <input type="hidden" name="cid" value="{$cid}" />
      {if isset($data.id)}<input type="hidden" name="bid" value="{$data.id}" />{/if}
    </div>
  </fieldset>
</form>
{/nocache}
