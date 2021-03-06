<?php
/**
 * $Project: GeoGraph $
 * $Id$
 * 
 * GeoGraph geographic photo archive project
 * This file copyright (C) 2005 Paul Dixon (paul@elphin.com)
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

require_once('geograph/global.inc.php');
init_session();

$USER->mustHavePerm("admin");

$smarty = new GeographPage;
$template='admin_server.tpl';
$cacheid='';


if (!$smarty->is_cached($template, $cacheid))
{
	//todo: these functions are *nix specific?

	$uptime=`uptime`;

	$smarty->assign_by_ref('uptime', $uptime);

	

	$photodir=`du -h --summarize {$_SERVER['DOCUMENT_ROOT']}/photos/`;
	$smarty->assign_by_ref('photodir', $photodir);

	$mapbasedir=`du -h --summarize {$_SERVER['DOCUMENT_ROOT']}/maps/base/`;
	$smarty->assign_by_ref('mapbasedir', $mapbasedir);

	$mapdetaildir=`du -h --summarize {$_SERVER['DOCUMENT_ROOT']}/maps/detail/`;
	$smarty->assign_by_ref('mapbasedir', $mapdetaildir);


	$cachedir=`du -h --summarize {$_SERVER['DOCUMENT_ROOT']}/templates/basic/cache`;
	$smarty->assign_by_ref('cachedir', $cachedir);

}

$smarty->display($template);

	
?>
