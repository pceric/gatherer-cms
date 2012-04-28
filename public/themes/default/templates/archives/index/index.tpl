<h1>Archives</h1>
<div id="archives">
{section name=aloop loop=$data nocache}
{if !isset($cyear) || $cyear != $data[aloop].year}<h2>{$data[aloop].year}</h2>{assign var='cyear' value=$data[aloop].year}{/if}
{if !isset($cmonth) || $cmonth != $data[aloop].month}<h3>{$data[aloop].month}</h3>{assign var='cmonth' value=$data[aloop].month}{/if}
<ul>
<li class="{$data[aloop].type}"><a href="{$view->url(['module' => 'news', 'id' => $data[aloop].id, 'type' => $data[aloop].type],NULL,true)}">{$data[aloop].title}</a></li>
</ul>
{/section}
</div>
