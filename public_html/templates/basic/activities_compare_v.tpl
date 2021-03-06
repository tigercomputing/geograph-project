{include file="_std_begin.tpl"}
 {literal}<style type="text/css">
#compare_table td {
	background-color:white;
}
#cell11,#cell12 ,#cell13 {
	vertical-align:bottom;
}
#cell21,#cell22 ,#cell23 {
	vertical-align:top;
}
.picinfo {
	width:250px;
}
</style>
<script type="text/javascript">
function reveal() {
	for(n=2;n<=3;n=n+1) {
		document.getElementById('cell1'+n).style.display='';
		document.getElementById('cell2'+n).style.display='';
	}
}
</script>
{/literal}

<h2><a href="/activities/">Activities</a> - Compare-a-pair</h2>
<form action="{$script_name}?v" method="post">
{dynamic}
{if $image1->gridimage_id && $image2->gridimage_id}

<div style="float:right; position:relative; background-color:yellow; padding:10px;"><b><a href="{$script_name}?v&amp;t={$token}">Link to this Pair</a></b></div>

<p>What's similar, or different and how long separates the photos, in the two shots below of the same location, discuss! <small>(Click the photo to swap to the <i>other</i>)</small></p>

<table id="compare_table" cellspacing=0 cellpadding=0>
	<tbody>
		<tr>
		
			<td>
				<!-- Creative Commons Licence -->
				<div class="ccmessage"><a rel="license" href="http://creativecommons.org/licenses/by-sa/2.0/"><img 
				alt="Creative Commons Licence [Some Rights Reserved]" src="http://creativecommons.org/images/public/somerights20.gif" /></a> &nbsp; &copy; Copyright <a title="View profile" href="{$image1->profile_link}">{$image1->realname|escape:'html'}</a> and  
				licensed for reuse under this <a rel="license" href="http://creativecommons.org/licenses/by-sa/2.0/" class="nowrap">Creative Commons Licence</a>.</div>
				<!-- /Creative Commons Licence -->
				<hr/>
				<div class="{if $image1->isLandscape()}photolandscape{else}photoportrait{/if}">
				  <div class="img-shadow" id="mainphoto1"><a href="#2" name="1">{$image1->getFull()}</a></div>
				</div>
			</td>
			<td id="cell12" style="display:none;" width="250px">
				<dl class="picinfo">
					<dt>Grid Square</dt>
					 <dd><a title="Grid Reference {$image1->grid_reference}" href="/gridref/{$image1->grid_reference}">{$image1->grid_reference}</a></dd>

					{if $image1->imagetaken}
					<dt>Date Taken</dt>
					 <dd>{$image1->getFormattedTakenDate()}</dd>
					{/if}
					<dt>Submitted</dt>
						<dd>{$image1->submitted|date_format:"%A, %e %B, %Y"}</dd>

					<dt>Category</dt>

					<dd>{if $image1->imageclass}
						{$image1->imageclass}
					{else}
						<i>n/a</i>
					{/if}</dd>

					<dt>Subject Location</dt>
					<dd style="font-family:verdana, arial, sans serif; font-size:0.8em">
					{if $image1->grid_square->reference_index eq 1}OSGB36{else}Irish{/if}: <img src="http://{$static_host}/img/geotag_16.png" width="10" height="10" align="absmiddle" alt="geotagged!"/> <a href="/location.php?gridref={$image1->getSubjectGridref(true)}">{$image1->subject_gridref}</a> [{$image1->subject_gridref_precision}m precision]<br/>
					WGS84: <span class="geo"><abbr class="latitude" title="{$lat1|string_format:"%.5f"}">{$latdm1}</abbr> <abbr class="longitude" 
					title="{$long1|string_format:"%.5f"}">{$longdm1}</abbr></span>
					</dd>

					{if $image1->getPhotographerGridref(true)}
					<dt>Photographer Location</dt>

					<dd style="font-family:verdana, arial, sans serif; font-size:0.8em">
					{if $image1->grid_square->reference_index eq 1}OSGB36{else}Irish{/if}: <img src="http://{$static_host}/img/geotag_16.png" width="10" height="10" align="absmiddle" alt="geotagged!"/> <a href="/location.php?gridref={$image1->photographer_gridref}">{$image1->photographer_gridref}</a></dd>
					{/if}

					{if $view_direction1 && $image1->view_direction != -1}
					<dt>View Direction</dt>

					<dd style="font-family:verdana, arial, sans serif; font-size:0.8em">
					{$view_direction1} (about {$image1->view_direction} degrees)</dd>
					{/if}
				</dl>
			</td>
			<td id="cell13" style="display:none">
				<h3 class="caption">{$image1->title|escape:'html'}</h3>

				{if $image1->comment}
					<div class="caption">{$image1->comment|escape:'html'|nl2br|geographlinks}</div>
				{/if}
				
				<hr/>
				
				{if $rastermap1->enabled}
					<div class="rastermap" style="width:{$rastermap1->width}px;position:relative; float:right;">
					{$rastermap1->getImageTag($image1->subject_gridref)}
					<span style="color:gray"><small>{$rastermap1->getFootNote()}</small></span>
					</div>
				
					{$rastermap1->getScriptTag()}
				{/if}
			</td>			
			
			
		</tr>
		<tr id="row0">
			<td align="right" style="background-color:#dddddd">
				<input type="button" value="Reveal" onclick="reveal()" style="font-size:1.3em; color:green; font-weight:bold" id="rbutton"/> 
				<input type="hidden" name="pair_id" value="{$pair_id}"/> 
				{if $user->registered}
					<input type="submit" name="invalid" value="This isn't a valid pair" onclick="next(true)" style="color:red" />
				{/if}
				<input type="submit" value="Next" onclick="next(false)" style="color:green" /> 
			</td>
		</tr>
		<tr>	
			<td>
				<div class="{if $image2->isLandscape()}photolandscape{else}photoportrait{/if}">
				  <div class="img-shadow" id="mainphoto2"><a href="#1" name="2">{$image2->getFull()}</a></div>
				</div>
				<hr/>
				<!-- Creative Commons Licence -->
				<div class="ccmessage"><a rel="license" href="http://creativecommons.org/licenses/by-sa/2.0/"><img 
				alt="Creative Commons Licence [Some Rights Reserved]" src="http://creativecommons.org/images/public/somerights20.gif" /></a> &nbsp; &copy; Copyright <a title="View profile" href="{$image2->profile_link}">{$image2->realname|escape:'html'}</a> and  
				licensed for reuse under this <a rel="license" href="http://creativecommons.org/licenses/by-sa/2.0/" class="nowrap">Creative Commons Licence</a>.</div>
				<!-- /Creative Commons Licence -->
			</td>
			<td id="cell22" style="display:none;" width="250px">
				<dl class="picinfo">
					<dt>Grid Square</dt>
					 <dd><a title="Grid Reference {$image2->grid_reference}" href="/gridref/{$image2->grid_reference}">{$image2->grid_reference}</a></dd>

					{if $image2->imagetaken}
					<dt>Date Taken</dt>
					 <dd>{$image2->getFormattedTakenDate()}</dd>
					{/if}
					<dt>Submitted</dt>
						<dd>{$image2->submitted|date_format:"%A, %e %B, %Y"}</dd>

					<dt>Category</dt>

					<dd>{if $image2->imageclass}
						{$image2->imageclass}
					{else}
						<i>n/a</i>
					{/if}</dd>

					<dt>Subject Location</dt>
					<dd style="font-family:verdana, arial, sans serif; font-size:0.8em">
					{if $image2->grid_square->reference_index eq 1}OSGB36{else}Irish{/if}: <img src="http://{$static_host}/img/geotag_16.png" width="10" height="10" align="absmiddle" alt="geotagged!"/> <a href="/location.php?gridref={$image2->getSubjectGridref(true)}">{$image2->subject_gridref}</a> [{$image2->subject_gridref_precision}m precision]<br/>
					WGS84: <span class="geo"><abbr class="latitude" title="{$lat1|string_format:"%.5f"}">{$latdm1}</abbr> <abbr class="longitude" 
					title="{$long1|string_format:"%.5f"}">{$longdm1}</abbr></span>
					</dd>

					{if $image2->getPhotographerGridref(true)}
					<dt>Photographer Location</dt>

					<dd style="font-family:verdana, arial, sans serif; font-size:0.8em">
					{if $image2->grid_square->reference_index eq 1}OSGB36{else}Irish{/if}: <img src="http://{$static_host}/img/geotag_16.png" width="10" height="10" align="absmiddle" alt="geotagged!"/> <a href="/location.php?gridref={$image2->photographer_gridref}">{$image2->photographer_gridref}</a></dd>
					{/if}

					{if $view_direction1 && $image2->view_direction != -1}
					<dt>View Direction</dt>

					<dd style="font-family:verdana, arial, sans serif; font-size:0.8em">
					{$view_direction1} (about {$image2->view_direction} degrees)</dd>
					{/if}
				</dl>
			</td>		
			<td id="cell23" style="display:none">
				<h3 class="caption">{$image2->title|escape:'html'}</h3>
				
				{if $image2->comment}
					<div class="caption">{$image2->comment|escape:'html'|nl2br|geographlinks}</div>
				{/if}
				
				<hr/>
				
				{if $rastermap2->enabled}
					<div class="rastermap" style="width:{$rastermap2->width}px;position:relative">
					{$rastermap2->getImageTag($image2->subject_gridref)}
					<span style="color:gray"><small>{$rastermap2->getFootNote()}</small></span>
					</div>
				
					{$rastermap2->getScriptTag()}
				{/if}
			</td>		
		
		</tr>
	</tbody>
</table>
{if $rastermap1->enabled}
	{$rastermap1->getFooterTag()}
{/if}
{if $rastermap2->enabled}
	{$rastermap2->getFooterTag()}
{/if}
{else}
	<p>No Pairs left, please try again later</p>
	
	<p>Or you can <a href="{$script_name}?v&amp;again">start again</a>
{/if}
{/dynamic}
</form>
 {literal}
  <script type="text/javascript">

  function redrawMainImage2() {
	el = document.getElementById('mainphoto1');
	el.style.display = 'none';
	el.style.display = '';
	el = document.getElementById('mainphoto2');
	el.style.display = 'none';
	el.style.display = '';
  }
  AttachEvent(window,'load',redrawMainImage2,false);

  </script>
  {/literal}


{include file="_std_end.tpl"}