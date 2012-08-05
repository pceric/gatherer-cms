<ul class="breadcrumb">
  <li><a href="{$view->url(['module' => 'admin'],null,true)}">Admin Home</a> <span class="divider">&gt;</span></li>
  <li class="active">{'News Management'|translate}</li>
</ul>
<div class="page-header">
  <h1>{'News Management'|translate}</h1>
</div>
<p><a class="btn btn-success" href="{$view->url(['action' => 'add'])}"><i class="icon-pencil icon-white"></i> New Item</a></p>
<p>Click on a title to edit an item.</p>
<table class="table table-striped">
  <thead>
    <tr>
      <th>Title</th>
      <th>Date</th>
      <th>&nbsp;</th>
    </tr>
  </thead>
  <tbody>
{foreach from=$data item=v nocache}
    <tr><td><a href="{$view->url(['action' => 'edit', 'id' => $v.id])}">{$v.title}</a></td>
    <td>{$v.pubdate|date_format:'%x %X'}</td>
    <td><a href="{$view->url(['action' => 'delete', 'id' => $v.id])}" class="mooTips" onclick="return confirm('{'Really delete?'|translate}');" title="{'Delete'|translate}"><i class="icon-trash"></i></a></td></tr>
{/foreach}
  </tbody>
</table>
