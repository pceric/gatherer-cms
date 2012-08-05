<ul class="breadcrumb">
  <li><a href="{$view->url(['module' => 'admin'],null,true)}">Admin Home</a> <span class="divider">&gt;</span></li>
  <li class="active">Navigation</li>
</ul>
<div class="page-header">
  <h1>Navigation</h1>
</div>
<table>
  <tbody>
    <tr><td><a class="btn btn-success" href="{$view->url(['action' => 'add'])}"><i class="icon-th-list icon-white"></i> New Item</a></td></tr>
    <tr><td>Drag-and-drop menu items around.</td></tr>
  </tbody>
</table>
<ul id="navList">
{foreach from=$menu item=item nocache}
  {if $item.id == 1}
    <li id="nl1"><a href="{$view->url(['action' => 'edit', 'id' => 1])}">{$item.name}</a>
  {elseif $item.id == 2}
    <li id="nl2">{$item.name|translate}
    <ul id="conList">
    {foreach from=$cmenu item=sitem}
      <li id="cl{$sitem.id}">{$sitem.name} &nbsp;&nbsp;<a href="{$view->url(['action' => 'delete', 'parent' => $sitem.parent, 'item' => $sitem.id])}" class="mooTips" title="{'Delete'|translate}"><i class="icon-trash"></i></a></li>
    {/foreach}
    </ul>
  {else}
    <li id="nl{$item.id}"><a href="{$view->url(['action' => 'edit', 'id' => $item.id])}">{$item.name}</a> &nbsp;&nbsp;<a href="{$view->url(['action' => 'delete', 'item' => $item.id])}" class="mooTips" title="{'Delete'|translate}"><i class="icon-trash"></i></a>
  {/if}
  </li>
{/foreach}
</ul>
