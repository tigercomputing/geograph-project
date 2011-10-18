
<div class="map" style="margin-left:20px;border:2px solid black; height:{$overview_height}px;width:{$overview_width}px">

<div class="inner" style="position:relative;top:0px;left:0px;width:{$overview_width}px;height:{$overview_height}px;">

{foreach from=$overview key=y item=maprow}
	<div>
	{foreach from=$maprow key=x item=mapcell}
	<a href="/mapbrowse.php?o={$overview_token}&amp;i={$x}&amp;j={$y}&amp;center=1"><img
	alt="Klickbare Karte" ismap="ismap" title="zum Vergrößern anklicken" src="{$mapcell->getImageUrl()}" width="{$mapcell->image_w}" height="{$mapcell->image_h}"/></a>
	{/foreach}
	</div>
{/foreach}
{if $marker}
	{if $marker->width > 3}
		<div style="position:absolute;top:{$marker->top+1}px;left:{$marker->left+1}px;width:{$marker->width}px;height:{$marker->height}px; border:1px solid white; font-size:1px;"></div>
		<div style="position:absolute;top:{$marker->top}px;left:{$marker->left}px;width:{$marker->width}px;height:{$marker->height}px; border:1px solid black; font-size:1px;"></div>
	{else}
		<div style="position:absolute;top:{$marker->top-8}px;left:{$marker->left-8}px;">{if $map_token}<a href="/mapbrowse.php?t={$map_token}{if $image}&amp;gridref_from={$image->grid_reference}{/if}">{/if}<img src="http://{$static_host}/img/crosshairs.gif" alt="+" width="16" height="16"/>{if $map_token}</a>{/if}</div>
	{/if}
{/if}
</div>
</div>
