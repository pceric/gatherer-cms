{nocache}
{$current_page=floor(array_search($year, $years)/5)}
<h1>Archives</h1>
<div id="archives">
<div class="pagination pagination-centered">
  <ul>
  {section p $years start=$current_page*5 max=5}
  {if $smarty.section.p.first && array_search($year, $years) >= 5}<li><a href="{$view->url(['year' => $years[p.index_prev], 'month' => null])}">&laquo; </a></li>{/if}
  <li{if $years[p] == $year} class="active"{/if}><a href="{$view->url(['year' => $years[p], 'month' => null])}">{$years[p]}</a></li>
  {if $smarty.section.p.last && $years[p] != end($years)}<li><a href="{{$view->url(['year' => $years[p.index_next], 'month' => null])}}">&raquo;</a></li>{/if}
  {/section}
  </ul>
</div>
<h2>{$year}</h2>
{section name=aloop loop=$data}
{if !isset($cmonth) || $cmonth != $data[aloop].month}<a id="a-{$data[aloop].month}"></a><h3>{$data[aloop].month_name}</h3>{assign var='cmonth' value=$data[aloop].month}{/if}
<ul>
<li class="{$data[aloop].type}"><a href="{$view->url(['module' => 'news', 'id' => $data[aloop].id, 'type' => $data[aloop].type],NULL,true)}">{$data[aloop].title}</a></li>
</ul>
{sectionelse}
Sorry, no results for the year of {$year}.
{/section}
</div>
{if isset($gcms.param.month)}
<script type="text/javascript">
    new Fx.Scroll(window).toElement('a-{$gcms.param.month}');
</script>
{/if}
{/nocache}
