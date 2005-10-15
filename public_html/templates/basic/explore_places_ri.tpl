{assign var="page_title" value="`$references.$ri` :: Geograph Gazetteer"}
{include file="_std_begin.tpl"}
 
    <h2><a href="/places/">Places</a> &gt; {$references.$ri}</h2>

<ul>
{foreach from=$counts key=adm1 item=line}
<li><a href="/places/{$ri}/{$adm1}/"><b>{$line.name}</b></a> [{$line.places} Places, {$line.images} Images]</li>
{/foreach}
</ul>

{include file="_std_end.tpl"}
