{nocache}
{if !isset($gcms.param.page)}{$gcms.param.page=1}{/if}
<div id="article">
  {section name=sloop loop=$page start=$gcms.param.page-1 max=1}
  <h2 class="title" id="i{$data.id}">{$ptitle[$smarty.section.sloop.index]}</h2>
  <p><small>{$data.pubdate|date_format:"%c"} by {$gcms.config.siteauthor}<br />
  {if !empty($data.moddate)}Modified {$data.moddate|date_format:"%c"}{/if}</small></p>
  <div id="article-body">
    {$page[sloop]}
  </div>
  {/section}
  {if $pagecount > 1}
  <table width="100%" border="0">
    <tr>
      <td width="33%" align="right">{if $gcms.param.page > 1}
        <a href="{$view->url(['page' => $gcms.param.page-1])}">&lt;&lt; Prev Page</a>
        {/if}</td>
      <td width="33%" align="center">
        <select name="page_select" onchange="window.open('{$view->url(['module' => 'article', 'id' => $gcms.param.id],null,true)}/page/' + this.options[this.selectedIndex].value,'_top')">
          {section name=psloop loop=$pagecount}
          <option value="{$smarty.section.psloop.iteration}"{if $gcms.param.page == $smarty.section.psloop.iteration} selected="selected"{/if}>Page {$smarty.section.psloop.iteration} - {$ptitle[$smarty.section.psloop.index]}</option>
          {/section}
        </select>
      </td>
      <td width="33%">{if $gcms.param.page < $pagecount}
        <a href="{$view->url(['page' => $gcms.param.page+1])}">Next Page &gt;&gt;</a>
        {/if}
      </td>
    </tr>
  </table>
  {/if}
  <table width="100%" border="0">
  <tr>
    <td width="90%"><div id="addthis">{include file='addthis.tpl'}</div></td><td><a href="javascript:void(0)" class="myTips" title="{"Print"|translate}" onclick="window.open('{$view->url(['action' => 'print', 'id' => $gcms.param.id])}', 'print', 'status=0,menubar=1,scrollbars=1,width=800,height=600')"><i class="icon-print"></i></a></td>
  </tr>
  </table>
  <div class="meta">
    <p class="links">{'Tags'|translate}: {$data.tags|tagify}{if $data.comments != 0} | {include file="disqus_count.tpl" disqus_identifier="article_{$data.id}"}{/if}{if $gcms.isAdmin == true} | <a href="{$view->url(['module' => 'article', 'controller' => 'admin', 'action' => 'edit', 'id' => $data.id],null,true)}">{'Edit'|translate}</a>{/if}</p>
  </div>
</div>
{if $data.comments != 0}{include file="disqus.tpl" disqus_identifier="article_{$data.id}"}{/if}
{/nocache}
