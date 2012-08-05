<ul class="breadcrumb">
  <li><a href="{$view->url(['module' => 'admin'],null,true)}">Admin Home</a> <span class="divider">&gt;</span></li>
  <li><a href="{$view->url(['module' => 'banner', 'controller' => 'admin', 'action' => 'clients'],null,true)}">Client Management</a> <span class="divider">&gt;</span></li>
  <li class="active">{'Banner Management'|translate}</li>
</ul>
<div class="page-header">
  <h1>{'Banner Management'|translate}</h1>
  <h3>{$data.name}</h3>
</div>
<p>
  <a class="btn btn-success" href="{$view->url(['action' => 'add', 'type' => 'banner', 'cid' => $data.id])}"><i class="icon-flag icon-white"></i> New Banner</a>
  <a class="btn btn-success" href="{$view->url(['action' => 'edit', 'type' => 'client', 'cid' => $data.id])}"><i class="icon-user icon-white"></i> Edit Client</a>
</p>
<p>Click on a title to edit an item.</p>
<table class="table table-striped">
  <thead>
    <tr>
      <th>Banner ID</th>
      <th>Dimensions</th>
      <th>Impressions/Clicks</th>
      <th>Ratio</th>
      <td>&nbsp;</td>
    </tr>
  </thead>
  <tbody>
{foreach from=$banners item=banner nocache}
  <tr><td><a href="{$view->url(['action' => 'edit', 'type' => 'banner', 'cid' => $data.id, 'bid' => $banner.id])}">{$banner.id}</a></td><td>{$banner.size}</td><td>{$banner.impressions}/{$banner.clicks}</td><td>{if $banner.clicks > 0}{($banner.clicks/$banner.impressions*100)|string_format:"%.2f"}{else}0{/if}%</td><td><a href="{$view->url(['action' => 'delete', 'type' => 'banner', 'bid' => $banner.id])}" class="mooTips" onclick="return confirm('Really delete?');" title="{'Delete'|translate}"><i class="icon-trash"></i></a></tr>
{foreachelse}
  <tr><td colspan="4">No banners found.</td></tr>
{/foreach}
  </tbody>
</table>
