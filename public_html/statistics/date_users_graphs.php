<?php
/**
 * $Project: GeoGraph $
 * $Id$
 * 
 * GeoGraph geographic photo archive project
 * This file copyright (C) 2005 Barry Hunter (geo@barryhunter.co.uk)
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




$smarty = new GeographPage;

$smarty->caching = 2; // lifetime is per cache
$smarty->cache_lifetime = 3600*24; //24hr cache

$template='statistics_graph.tpl';

$cacheid='statistics|users';

if (!$smarty->is_cached($template, $cacheid))
{
	require_once('geograph/gridimage.class.php');
	require_once('geograph/gridsquare.class.php');
	require_once('geograph/imagelist.class.php');

	$db=NewADOConnection($GLOBALS['DSN']);
	if (!$db) die('Database connection failed');  

	$column = 'signup_date';  
	
		
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	 
	$graphs = array();	
	
	//================= 
	
	$graph = array();

	$title = "User Signups by Hour";

	$table=$db->GetAll("SELECT 
	HOUR($column) AS `title`, 
	count( * ) AS `value`
	FROM `user` $where_sql
	GROUP BY HOUR($column)" );

	$graph['table'] = $table;

	$graph['title'] = $title;
	$max = 0;
	foreach ($table as $row) {
		$max = max($max,$row['value']);
	}
	$graph['max'] = $max;

	$graphs[] = &$graph;
		


	//================= 
	
	$graph2 = array();
	
	$title = "User Signups by Day of Week";
	
	$table=$db->GetAll("SELECT 
	DAYNAME($column) AS `title`, 
	count( * ) AS `value`
	FROM `user` $where_sql
	GROUP BY WEEKDAY($column)" );

	$graph2['table'] = $table;
	
	$graph2['title'] = $title;
	$max = 0;
	foreach ($table as $row) {
		$max = max($max,$row['value']);
	}
	$graph2['max'] = $max;
	
	$graphs[] = &$graph2;
		
	//=================
	
	$graph3 = array();

	$title = "Average User Signups each Month";

	$table=$db->GetAll("SELECT 
	MONTHNAME($column) AS `title`, 
	count( * ) div count(distinct YEAR($column)) AS `value`
	FROM `user` $where_sql
	GROUP BY MONTH($column)" );

	$graph3['table'] = $table;

	$graph3['title'] = $title;
	$max = 0;
	foreach ($table as $row) {
		$max = max($max,$row['value']);
	}
	$graph3['max'] = $max;

	$graphs[] = &$graph3;

	//=================
	
	$smarty->assign_by_ref('graphs',$graphs);	
	
	$extra = array();
	foreach (array('date') as $key) {
		if (isset($_GET[$key])) {
			$extra[$key] = $_GET[$key];
		}
	}
	$smarty->assign_by_ref('extra',$extra);	
} 

$smarty->display($template, $cacheid);

	
?>
