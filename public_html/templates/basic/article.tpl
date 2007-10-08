{assign var="page_title" value="Articles"}

{include file="_std_begin.tpl"}
{literal}<style type="text/css">
ul.explore li {	padding:3px; }
</style>{/literal}

<div style="float:right"><a title="geoRSS Feed for Geograph Articles" href="/article/feed/recent.rss" class="xml-geo">geo RSS</a></div>

<h2>Articles</h2>

{foreach from=$list item=item}
{if $lastcat != $item.category_name}
{if $lastcat}
</ul>
</div>
{cycle values=",<br style='clear:both'/>"}
{/if}
<div style="float:left;width:46%;position:relative; padding:5px;">
<h3>{$item.category_name}</h3>
<ul class="explore">
{/if}
	<li><b>{if $item.approved != 1 || $item.licence == 'none'}<s>{/if}<a title="{$item.extract|default:'View Article'}" href="/article/{$item.url}">{$item.title}</a></b>{if $item.approved != 1 || $item.licence == 'none'}</s> ({if $item.approved == -1}<i>Archived <small>and not available for publication</small></i>{else}Not publicly visible{/if}){/if}<br/>
	<small><small style="color:lightgrey">by <a href="/profile/{$item.user_id}" title="View Geograph Profile for {$item.realname}"  style="color:#6699CC">{$item.realname}</a>
		{if $item.user_id == $user->user_id && !$item.locked_user}
			&nbsp;&nbsp;&nbsp;&nbsp; 
			[<a title="Edit {$item.title}" href="/article/edit.php?page={$item.url}">Edit</a>]
		{/if} 
		
		</small></small>

	</li>
	{assign var="lastcat" value=$item.category_name}
{foreachelse}
	<li><i>There are no articles to display at this time.</i></li>
{/foreach}

</ul>
</div>
<br style="clear:both"/>

<div style="float:right"><a title="geoRSS Feed for Geograph Articles" href="/article/feed/recent.rss" class="xml-geo">geo RSS</a></div>
<br style="clear:both"/>

<div class="interestBox">
{if $user->registered} 
	<a href="/article/edit.php?page=new">Submit a new Article</a> (Registered Users Only)
{else}
	<a href="/login.php">Login</a> to Submit your own article!
{/if}
</div>

{include file="_std_end.tpl"}
