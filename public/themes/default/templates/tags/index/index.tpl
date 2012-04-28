{nocache}
<h1>Tags - {$term|default:'[Uncategorized]'}</h1>
<div id="tags">
{if count($data) < 1}<p>Sorry, nothing matching '{$term}'.</p>{/if}
<ul>
{section name=tloop loop=$data}
  <li class="{$data[tloop].type}">
{if $data[tloop].type == 'article'}
  <a href="{$view->url(['module' => 'article', 'id' => $data[tloop].id],NULL,true)}">
{else}
  <a href="{$view->url(['module' => 'news', 'id' => $data[tloop].id, 'type' => $data[tloop].type],NULL,true)}">
{/if}
  {$data[tloop].title}</a></li>
{/section}
</ul>
</div>
{/nocache}
