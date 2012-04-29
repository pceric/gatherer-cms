{nocache}
{if $data.type == 'reader'}<div class="gfeed">{else}<div class="news">{/if}
	<h2 class="title">{$data.title|truncate:50:"..."}</h2>
    <div class="entry">
        <p><small>{$data.pubdate|date_format:"%c"}{if !empty($data.source)} from {$data.source}{else} by {$gcms.config.siteauthor}{/if}<br />
        {if !empty($data.moddate)}Modified {$data.moddate|date_format:"%c"}{/if}</small></p>
        {if $data.type == 'reader'}<blockquote>{/if}
            <p class="content" id="i{$data.id}">{$data.content}</p>
        {if $data.type == 'reader'}</blockquote>
        {if !empty($data.annotation) || $gcms.isAdmin}<p class="well annotation" id="i{$data.id}">{$data.annotation}</p>{/if}
        {/if}
	    {if $data.type != 'reader'}
        <div id="addthis">
		  {include file='addthis.tpl'}
        </div>
	    {/if}
    </div>
	<div class="meta">
		<p class="links">{'Tags'|translate}: {$data.tags|tagify}{if $data.type != 'reader' && $data.comments != 0} | {include file="disqus_count.tpl" disqus_identifier="news_{$data.id}"}{/if}{if $gcms.isAdmin == true && $data.type != 'reader'} | <a href="{$view->url(['module' => 'news', 'controller' => 'admin', 'action' => 'edit', 'type' => 'news', 'id' => {$data.id}])}">{'Edit'|translate}</a>{/if}</p>
	</div>
</div>
{if $data.type != 'reader' && $data.comments != 0}{include file="disqus.tpl" disqus_identifier="news_{$data.id}"}{/if}
{/nocache}
