{nocache}
<ul class="breadcrumb">
  <li><a href="{$view->url(['module' => 'admin'],null,true)}">Admin Home</a> <span class="divider">&gt;</span></li>
  <li><a href="{$view->url(['module' => 'admin', 'controller' => 'navigation'],null,true)}">Navigation</a> <span class="divider">&gt;</span></li>
  <li class="active">{if isset($item.id)}Edit{else}Add{/if}</li>
</ul>
<h1>{if isset($item.id)}Editing '{$item.name}'{else}Add New Item{/if}</h1>
<form method="post" class="form-horizontal">
<table>
<tr><td>Name:</td><td><input type="text" name="name" value="{$item.name|default:''}" /></td></tr>
<tr><td>Parent:</td><td>{html_options options=$poptions selected=$item.parent|default:0 name="parent"}</td></tr>
<tr><td>URL:</td><td><input type="text" name="link" value="{$item.link|default:''}" /></td></tr>
<tr><td colspan="2"><input type="submit" name="save" value="Save" class="btn" /> <input type="submit" name="cancel" value="Cancel" class="btn" /></td></tr>
</table>
{if isset($item.id)}<input type="hidden" name="id" value="{$item.id}">{/if}
</form>
{/nocache}
