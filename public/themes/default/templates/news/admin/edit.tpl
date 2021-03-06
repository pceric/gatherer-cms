{nocache}
<ul class="breadcrumb">
  <li><a href="{$view->url(['module' => 'admin'],null,true)}">Admin Home</a> <span class="divider">&gt;</span></li>
  <li><a href="{$view->url(['module' => 'news', 'controller' => 'admin'],null,true)}">News</a> <span class="divider">&gt;</span></li>
  <li class="active">{if isset($row.id)}Edit{else}Add{/if}</li>
</ul>
<div class="page-header">
  <h1>{if isset($row.id)}Edit{else}New{/if} {'News'|translate}</h1>
</div>
<form method="post" class="form-horizontal">
  <fieldset>
    <div class="control-group">
      <label class="control-label">Title:</label>
      <div class="controls">
        <input type="text" name="title" value="{$row.title|escape|default:''}" class="input-xlarge" maxlength="128" />
      </div>
    </div>
    <div class="control-group">
      <textarea rows="20" name="content" id="wysiwyg" class="input-xxlarge">{$row.content|default:''}</textarea>
    </div>
    <div class="control-group">
      <label class="control-label">Tags:</label>
      <div class="controls">
        <input type="text" name="tags" value="{$row.tags|escape|default:''}" class="input-xlarge" maxlength="200" />
      </div>
    </div>
    <div class="control-group">
      <label class="checkbox">Published
        {html_toggle name="published" checked=$row.published|default:0 labels=false}
      </label>
    </div>
    <div class="control-group">
      <label class="checkbox">Enable Comments
        {html_toggle name="comments" checked=$row.comments|default:0 labels=false}
      </label>
    </div>
    <div class="control-group">
      <label class="checkbox">Sticky at top of front page
        {html_toggle name="sticky" checked=$row.sticky|default:0 labels=false}
      </label>
    </div>
    <div class="control-group">
      <label class="control-label">Published Date:</label>
      <div class="controls">
        <input type="text" name="pubdate" id="calendar" value="{$row.pubdate|default:''}" class="input-medium" maxlength="64" placeholder="{$smarty.now|date_format:"%Y-%m-%d %T"}" />
      </div>
    </div>
{* TODO: Filter picker
<div>
{foreach from=$filters item=v}
	{if !is_dir("./filters/$v") && preg_match('/^([A-Z]|[a-z]|_)/',$v)}
		<input type="checkbox" value="{$v}"{if $v eq $config.sitetheme} selected="selected"{/if}/> {$v}
    {/if}
{/foreach}
</div>
*}
    <div class="control-group">
      {if isset($row.id)}<input type="hidden" name="id" value="{$row.id}" />{/if}
      <input type="submit" name="savecontent" value="Save" class="btn" /> <input type="submit" name="cancel" value="Cancel" class="btn" />
    </div>
  </fieldset>
</form>
{/nocache}
