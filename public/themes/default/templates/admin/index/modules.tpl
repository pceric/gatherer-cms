<ul class="breadcrumb">
  <li><a href="{$view->url(['module' => 'admin'],null,true)}">Admin Home</a> <span class="divider">&gt;</span></li>
  <li class="active">Module Management</li>
</ul>
<h1>Module Management</h1>
<form method="post">
  <fieldset>
{foreach from=$modules item=v nocache}
  {if is_dir("./modules/$v") && preg_match('/^([A-Z]|[a-z]|_)/',$v)}
    <div class="control-group">
      <label class="checkbox">
        <input type="checkbox" /> {$v}
      </label>
    </div>
  {/if}
{/foreach}
  </fieldset>
</form>
