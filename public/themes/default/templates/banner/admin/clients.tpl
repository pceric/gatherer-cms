<ul class="breadcrumb">
  <li><a href="{$view->url(['module' => 'admin'],null,true)}">Admin Home</a> <span class="divider">&gt;</span></li>
  <li class="active">{'Client Management'|translate}</li>
</ul>
<div class="page-header">
  <h1>Client Management</h1>
</div>
{include 'page_alert.tpl'}
<p><a class="btn btn-success" href="{$view->url(['action' => 'add', 'type' => 'client'])}"><i class="icon-user icon-white"></i> New Client</a></p>
<p>Click on a title to edit an item.</p>
<table class="table table-striped">
  <thead>
    <tr>
      <th>Client Name</th>
      <th>Total Banners</th>
      <th>&nbsp;</th>
    </tr>
  </thead>
  <tbody>
{foreach from=$data item=client nocache}
  <tr>
    <td><a href="{$view->url(['action' => 'banners', 'cid' => $client.id])}">{$client.name}</a></td>
    <td>{$client.count}</td>
    <td><a href="{$view->url(['action' => 'delete', 'type' => 'client', 'cid' => $client.id])}" class="mooTips" onclick="return confirm('Really delete?');" title="{'Delete'|translate}"><i class="icon-trash"></i></a></td>
  </tr>
{foreachelse}
  <tr>
    <td>No clients found.  You must add a client before you can create a banner.</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
{/foreach}
  </tbody>
</table>
