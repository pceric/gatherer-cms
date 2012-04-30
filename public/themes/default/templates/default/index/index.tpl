{include file="disqus_count.tpl"}
{* Print out our sticky posts first if we have any *}
{if !empty($sticky)}
		<div class="sticky">
{section name=sloop loop=$sticky nocache}
			<h2 class="title">{$sticky[sloop].title}</h2>
			<div class="entry">
		        <small><p>{$sticky[sloop].pubdate|date_format:"%c"}{if !empty($sticky[sloop].moddate)} (Modified {$sticky[sloop].moddate|date_format:"%x"}){/if} by {$gcms.config.siteauthor}:</small></p>
				<p id="i{$sticky[sloop].id}">{$sticky[sloop].content}</p>
			</div>
			<div class="meta">
				<p class="links">Tags: {$sticky[sloop].tags|tagify}{if !empty($sticky[sloop].comments)} | <a href="{$view->url(['module' => 'news', 'type' => 'news', 'id' => $sticky[sloop].id],null,true)}#disqus_thread" data-disqus-identifier="news_{$sticky[sloop].id}">{'Comments'|translate}</a>{/if} | <a href="{$view->url(['module' => 'news', 'type' => 'news', 'id' => $sticky[sloop].id],null,true)}">Permalink</a>{if $gcms.isAdmin == true} | <a href="{$view->url(['module' => 'news', 'controller' => 'admin', 'action' => 'edit', 'type' => 'news', 'id' => $sticky[sloop].id])}">{'Edit'|translate}</a>{/if}</p>
			</div>
{/section}
		</div>
{/if}
{* Now our regular posts *}
{section name=floop loop=$feed nocache}
		{if $feed[floop].type == 'reader'}<div class="gfeed">{else}<div class="news">{/if}
			<h2 class="title">{$feed[floop].title|truncate:50:"..."}</h2>
			<div class="entry">
		        <p><small>{$feed[floop].pubdate|date_format:"%c"}{if !empty($feed[floop].moddate)} (Modified {$feed[floop].moddate|date_format:"%x"}){/if}{if !empty($feed[floop].source)} from {$feed[floop].source}{else} by {$gcms.config.siteauthor}{/if}:</small></p>
                {if $feed[floop].type == 'reader'}<blockquote>{/if}
					<p>{$feed[floop].content}</p>
                {if $feed[floop].type == 'reader'}</blockquote>
				{if !empty($feed[floop].annotation) || $gcms.isAdmin}<p class="well annotation" id="i{$feed[floop].id}">{$feed[floop].annotation}</p>{/if}
                {/if}
			</div>
			<div class="meta">
				<p class="links">Tags: {$feed[floop].tags|tagify}{if !empty($feed[floop].comments)} | <a href="{$view->url(['module' => 'news', 'type' => $feed[floop].type, 'id' => $feed[floop].id],null,true)}#disqus_thread" data-disqus-identifier="news_{$feed[floop].id}">{'Comments'|translate}</a>{/if} | <a href="{$view->url(['module' => 'news', 'type' => $feed[floop].type, 'id' => $feed[floop].id],null,true)}">Permalink</a>{if $gcms.isAdmin == true && $feed[floop].type != 'reader'} | <a href="{$view->url(['module' => 'news', 'controller' => 'admin', 'action' => 'edit', 'type' => 'news', 'id' => $feed[floop].id])}">Edit</a>{/if}</p>
			</div>
		</div>
{/section}
<div id="archives-link-row">
    <a href="{$view->url(['module' => 'archives'],null,true)}">Archives...</a>
</div>

