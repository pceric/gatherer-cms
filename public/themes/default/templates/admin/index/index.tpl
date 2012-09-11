{nocache}
{if isset($msg)}
<div class="alert alert-{$alert}">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
{if $msg == 'pass'}
  Password changed successfully.
{else}
  Settings saved successfully.
{/if}
</div>
{/if}
<div class="page-header">
  <h1>{'Administration'|translate}</h1>
</div>
<div>
  <h4 class="accordion-toggle" data-toggle="collapse">Site Config</h4>
  <ul class="unstyled-simple">
    <li><a href="{$view->url(['module' => 'admin', 'action' => 'config'],null,true)}">{'Configuration'|translate}</a></li>
    <li><a href="{$view->url(['module' => 'admin', 'controller' => 'navigation', 'action' => 'index'],null,true)}">{'Navigation'|translate}</a></li>
    <li><a href="{$view->url(['module' => 'admin', 'action' => 'chgpass'],null,true)}">{'Change Password'|translate}</a></li>
    <li><a href="{$view->url(['module' => 'admin', 'action' => 'logout'],null,true)}">{'Logout'|translate}</a></li>
  </ul>
{foreach $menu_stack_array as $module}
  <h4 class="accordion-toggle" data-toggle="collapse">{array_shift($module)}</h4>
  <ul class="unstyled-simple">
  {foreach $module as $k=>$v}
    <li><a href="{$k}">{$v}</a></li>
  {/foreach}
  </ul>
{/foreach}
</div>
{/nocache}
