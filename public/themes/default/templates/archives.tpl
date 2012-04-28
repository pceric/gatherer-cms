{$first = true}
<ul>
{foreach $archive as $year => $monthArray}
    {foreach $monthArray as $month => $count}
            <li{if $first == true} class="first"{$first = false}{/if}><a href="{$view->url(['module' => 'archives', 'year' => $year, 'month' => $month],null,true)}">{mktime(0,0,0,$month,1)|date_format:"%B"} ({$count})</a></li>
    {/foreach}
{/foreach}
<li><a href="{$view->url(['module' => 'archives'],null,true)}">{'Older...'|translate}</a></li>
</ul>
