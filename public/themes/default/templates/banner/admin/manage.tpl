<h1>Banners &gt; {$client}</h1>
<table border="0" width="100%">
  <tr><td colspan="3"><a href="index.php?mod=gcmsBanner&amp;client={$smarty.get.client}&amp;action=new">Create New Banner</a></td></tr>
  <tr><td><strong>Banner ID</strong></td><td><strong>Dimensions</strong></td><td><strong>Impressions/Clicks</strong></td><td><strong>Ratio</strong></td><td>&nbsp;</td></tr>
{foreach from=$data item=banner}
  <tr><td><a href="index.php?mod=gcmsBanner&amp;action=edit&amp;client={$banner.client}&amp;bid={$banner.id}">{$banner.id}</a></td><td>{$banner.size}</td><td>{$banner.impressions}/{$banner.clicks}</td><td>{if $banner.clicks > 0}{$banner.clicks/$banner.impressions*100|string_format:"%.2f"}{else}0{/if}%</td><td><a href="index.php?mod=gcmsBanner&amp;action=delete&amp;client={$banner.client}&amp;bid={$banner.id}" onclick="return confirm('Really delete?');"><img src="../../images/trash.gif" alt="Delete" /></a></tr>
{/foreach}
</table>