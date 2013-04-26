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
include_messages('leaderboard');
init_session();


$type = (isset($_GET['type']) && preg_match('/^\w+$/' , $_GET['type']))?$_GET['type']:'points';

$date = (isset($_GET['date']) && ctype_lower($_GET['date']))?$_GET['date']:'submitted';

if (!empty($_GET['whenYear'])) {
	if (!empty($_GET['whenMonth'])) {
		$_GET['when'] = sprintf("%04d-%02d",$_GET['whenYear'],$_GET['whenMonth']);
	} else {
		$_GET['when'] = sprintf("%04d",$_GET['whenYear']);
	}
}

$ri = (isset($_GET['ri']) && is_numeric($_GET['ri']))?intval($_GET['ri']):0;

$when = (isset($_GET['when']) && preg_match('/^\d{4}(-\d{2}|)(-\d{2}|)$/',$_GET['when']))?$_GET['when']:'';

$limit = (isset($_GET['limit']) && is_numeric($_GET['limit']))?min(250,intval($_GET['limit'])):150;

$myriad = (isset($_GET['myriad']) && ctype_upper($_GET['myriad']))?$_GET['myriad']:'';


$minimum = (isset($_GET['minimum']) && is_numeric($_GET['minimum']))?intval($_GET['minimum']):25;
$maximum = (isset($_GET['maximum']) && is_numeric($_GET['maximum']))?intval($_GET['maximum']):0;



$u = (isset($_GET['u']) && is_numeric($_GET['u']))?intval($_GET['u']):0;

if (isset($_GET['me']) && $USER->registered) {
	$u = $USER->user_id;
}

$smarty = new GeographPage;

if (isset($_GET['inner'])) {
	$template='statistics_leaderboard_inner.tpl';
} else {
	$template='statistics_leaderboard.tpl';
}
$cacheid=$minimum.'-'.$maximum.$type.$date.$when.$limit.'.'.$ri.'.'.$u.$myriad;

if ($smarty->caching) {
	$smarty->caching = 2; // lifetime is per cache
	$smarty->cache_lifetime = 3600*3; //3hour cache
}

if (!$smarty->is_cached($template, $cacheid))
{
	require_once('geograph/gridimage.class.php');
	require_once('geograph/gridsquare.class.php');
	require_once('geograph/imagelist.class.php');

	$filtered = ($when || $ri || $myriad);
	
	$db=NewADOConnection($GLOBALS['DSN']);
	if (!$db) die('Database connection failed');  
	$sql_table = "gridimage_search i";
	$sql_where = "1";
	$sql_orderby = '';
	$sql_column = "count(*)";
	$sql_having_having = '';
	if ($maximum) {
		$minimax = sprintf($MESSAGES['leaderboard']['minimax'], $minimum, $maximum);
		$sql_minimax = "between $minimum and $maximum";
	} else {
		$minimax = sprintf($MESSAGES['leaderboard']['minimum'], $minimum);
		$sql_minimax = "> $minimum";
	}

	$sql_qtable_filtered = array (
		'squares' => array(
			'column' => "count(distinct grid_reference)",
		),
		'geosquares' => array(
			'column' => "count(distinct grid_reference)",
			'where' => "i.moderation_status='geograph'",
		),
		'geographs' => array(
			'where' => "i.moderation_status='geograph'",
		),
		'additional' => array(
			'where' => "i.moderation_status='geograph' and ftf = 0",
		),
		'supps' => array(
			'where' => "i.moderation_status='accepted'",
		),
		'images' => array(
			'column' => "sum(i.ftf=1 and i.moderation_status='geograph') as points, count(*)",
			'where' => "1",
			'orderby' => ",points desc",
		),
		'test_points' => array(
			'column' => "sum((i.moderation_status = 'geograph') + ftf + 1)",
		),
		'reverse_points' => array(
			'column' => "count(*) as images, count(*)/(sum(ftf=1)+1)",
			'having_having' => "having count(*) > $minimum",
			'isfloat' => true,
		),
		'depth' => array(
			'column' => "count(*)/count(distinct grid_reference)",
			'having_having' => "having count(*) $sql_minimax",
			'isfloat' => true,
		),
		'depth2' => array(
			'column' => "round(pow(count(*),2)/count(distinct grid_reference))",
			'having_having' => "having count(*) > $minimum",
		),
		'myriads' => array(
			'column' => "count(distinct substring(grid_reference,1,length(grid_reference) - 4))",
		),
		'antispread' => array(
			'column' => "count(*)/count(distinct concat(substring(grid_reference,1,length(grid_reference)-3),substring(grid_reference,length(grid_reference)-1,1)) )",
			'isfloat' => true,
		),
		'spread' => array(
			'column' => "count(distinct concat(substring(grid_reference,1,length(grid_reference)-3),substring(grid_reference,length(grid_reference)-1,1)) )/count(*)",
			'having_having' => "having count(*) > $minimum",
			'isfloat' => true,
		),
		'hectads' => array(
			'column' => "count(distinct concat(substring(grid_reference,1,length(grid_reference)-3),substring(grid_reference,length(grid_reference)-1,1)) )",
		),
		'days' => array(
			'column' => "count(distinct imagetaken)",
		),
		'classes' => array(
			'column' => "count(distinct imageclass)",
		),
		'clen' => array(
			'column' => "avg(length(comment))",
			'having_having' => "having count(*) > $minimum",
			'isfloat' => true,
		),
		'tlen' => array(
			'column' => "avg(length(title))",
			'having_having' => "having count(*) > $minimum",
			'isfloat' => true,
		),
		'category_depth' => array(
			'column' => "count(*)/count(distinct imageclass)",
			'isfloat' => true,
		),
		'centi' => array(
		//NOT USED AS REQUIRES A NEW INDEX ON gridimage!
			'table' => "gridimage i ",
			'column' => "COUNT(DISTINCT nateastings div 100, natnorthings div 100)",
			'where' => "i.moderation_status='geograph' and nateastings div 1000 > 0",
		),
		'points' => array(
			'where' => "i.ftf=1 and i.moderation_status='geograph'",
		),
	);
	$sql_qtable_unfiltered = array (
		'squares' => array(
			'table' => "user_stat i",
			'column' => "squares",
		),
		'geosquares' => array(
			'table' => "user_stat i",
			'column' => "geosquares",
		),
		'geographs' => array(
			'table' => "user_stat i",
			'column' => "geographs",
		),
		'additional' => array(
			'where' => "i.moderation_status='geograph' and ftf = 0",
		),
		'supps' => array(
			'table' => "user_stat i",
			'column' => "images-geographs",
		),
		'images' => array(
			'table' => "user_stat i",
			'column' => "points, images",
			'orderby' => ",points desc",
		),
		'test_points' => array(
			'table' => "user_stat i",
			'column' => "images, images/(points+1)",
			'isfloat' => true,
		),
		'reverse_points' => array(
			'table' => "user_stat i",
			'column' => "images, images/(points+1)",
			'having_having' => "having images > $minimum",
			'isfloat' => true,
		),
		'depth' => array(
			'table' => "user_stat i",
			'column' => "images, depth",
			'having_having' => "having images $sql_minimax",
			'isfloat' => true,
		),
		'depth2' => array(
			'column' => "round(pow(images,2)/squares)",
			'having_having' => "having images > $minimum",
			'isfloat' => true,
		),
		'myriads' => array(
			'table' => "user_stat i",
			'column' => "myriads",
		),
		'antispread' => array(
			'table' => "user_stat i",
			'column' => "images/hectads",
			'isfloat' => true,
		),
		'spread' => array(
			'table' => "user_stat i",
			'column' => "hectads/images",
			'having_having' => "having count(*) > $minimum",
			'isfloat' => true,
		),
		'hectads' => array(
			'table' => "user_stat i",
			'column' => "hectads",
		),
		'days' => array(
			'table' => "user_stat i",
			'column' => "days",
		),
		'classes' => array(
			'column' => "count(distinct imageclass)",
		),
		'clen' => array(
			'column' => "avg(length(comment))",
			'having_having' => "having count(*) > $minimum",
			'isfloat' => true,
		),
		'tlen' => array(
			'column' => "avg(length(title))",
			'having_having' => "having count(*) > $minimum",
			'isfloat' => true,
		),
		'category_depth' => array(
			'column' => "count(*)/count(distinct imageclass)",
			'isfloat' => true,
		),
		'centi' => array(
		//NOT USED AS REQUIRES A NEW INDEX ON gridimage!
			'table' => "gridimage i ",
			'column' => "COUNT(DISTINCT nateastings div 100, natnorthings div 100)",
			'where' => "i.moderation_status='geograph' and nateastings div 1000 > 0",
		),
		'content' => array(
			'table' => "user_stat i",
			'column' => "content",
			'where' => "content > 0",
		),
		'points' => array(
			'table' => "user_stat i",
			'column' => "depth,points",
		),
	);

	if ($filtered) {
		$sql_qtable =& $sql_qtable_filtered;
	} else {
		$sql_qtable =& $sql_qtable_unfiltered;
	}

	if (!isset($sql_qtable[$type])) {
		$type = 'points';
	}

	$isfloat = false;
	if (isset($sql_qtable[$type]['isfloat'])) $isfloat = $sql_qtable[$type]['isfloat'];

	if (isset($sql_qtable[$type]['column'])) $sql_column = $sql_qtable[$type]['column'];
	if (isset($sql_qtable[$type]['having_having'])) $sql_having_having = $sql_qtable[$type]['having_having'];
	if (isset($sql_qtable[$type]['where'])) $sql_where = $sql_qtable[$type]['where'];
	if (isset($sql_qtable[$type]['table'])) $sql_table = $sql_qtable[$type]['table'];
	if (isset($sql_qtable[$type]['orderby'])) $sql_orderby = $sql_qtable[$type]['orderby'];

	$heading = $MESSAGES['leaderboard']['headings'][$type];
	$desc = str_replace(array('@minimum', '@minimax'), array($minimum, $minimax), $MESSAGES['leaderboard']['descriptions'][$type]);

	if ($when) {
		if ($date == 'both') {
			$sql_where .= " and imagetaken LIKE '$when%' and submitted LIKE '$when%'";
			if ($CONF['lang'] == 'de')
				$desc .= ", <b>f�r Bilder mit Aufnahme- und Einreichdatum ".getFormattedDate($when)."</b>";
			else
				$desc .= ", <b>for images taken and submitted during ".getFormattedDate($when)."</b>";
		} else {
			$column = ($date == 'taken')?'imagetaken':'submitted';
			$sql_where .= " and $column LIKE '$when%'";
			if ($CONF['lang'] == 'de') {
				$title = ($date == 'taken')?'Aufnahmedatum':'Einreichdatum'; 
				$desc .= ", <b>f�r Bilder mit $title ".getFormattedDate($when)."</b>";
			} else {
				$title = ($date == 'taken')?'taken':'submitted'; 
				$desc .= ", <b>for images $title during ".getFormattedDate($when)."</b>";
			}
		}
	}
	if ($myriad) {
		$sql_where .= " and grid_reference LIKE '{$myriad}____'";
		$desc .= sprintf($MESSAGES['leaderboard']['in_myriad'], $myriad);
	}
	if ($ri) {
		$sql_where .= " and reference_index = $ri";
		$desc .= sprintf($MESSAGES['leaderboard']['in_grid'], $CONF['references_all'][$ri]);
	}
	
	$smarty->assign('heading', $heading);
	$smarty->assign('desc', $desc);
	$smarty->assign('type', $type);
	$smarty->assign('isfloat', $isfloat);

	if ($sql_table != 'user_stat i') {
		$sql_column = "max(gridimage_id) as last,$sql_column";
	}
	$limit2 = intval($limit * 1.6);
	$topusers=$db->GetAll("select 
	i.user_id,u.realname, $sql_column as imgcount
	from $sql_table inner join user u using (user_id)
	where $sql_where
	group by user_id 
	$sql_having_having
	order by imgcount desc $sql_orderby,last asc limit $limit2"); 
	$lastimgcount = 0;
	$toriserank = 0;
	$points = 0;
	$images = 0;
	foreach($topusers as $idx=>$entry)
	{
		$i=$idx+1;
			
		if ($lastimgcount == $entry['imgcount']) {
			if ($u && $u == $entry['user_id']) {
				$topusers[$idx]['ordinal'] = smarty_function_ordinal($i);
			} elseif ($i > $limit) {
				unset($topusers[$idx]);
			} else {
				$topusers[$idx]['ordinal'] = '&nbsp;&nbsp;&nbsp;&quot;';
			}
		} else {
			$toriserank = ($lastimgcount - $entry['imgcount']);
			if ($u && $u == $entry['user_id']) {
                                $topusers[$idx]['ordinal'] = smarty_function_ordinal($i);
                        } elseif ($i > $limit) {
				unset($topusers[$idx]);
			} else {
				$topusers[$idx]['ordinal'] = smarty_function_ordinal($i);
				$points += $entry['points'];
				if ($points && empty($entry['points'])) $topusers[$user_id]['points'] = '';
				$images += $entry['images'];
				if ($images && empty($entry['images'])) $topusers[$user_id]['images'] = '';
			}
			$lastimgcount = $entry['imgcount'];
			$lastrank = $i;

		}
	}
	
	$smarty->assign_by_ref('topusers', $topusers);
	$smarty->assign('points', $points);
	$smarty->assign('images', $images);


	$smarty->assign('types', array('points','geosquares','images','depth'));
	$smarty->assign('typenames', $MESSAGES['leaderboard']['type_names']);
	
	
	$extra = array();
	$extralink = '';
	
	foreach (array('when','date','ri','myriad') as $key) {
		if (isset($_GET[$key])) {
			$extra[$key] = $_GET[$key];
			$extralink .= "&amp;$key={$_GET[$key]}";
		}
	}
	$smarty->assign_by_ref('extra',$extra);	
	$smarty->assign_by_ref('extralink',$extralink);	
	$smarty->assign_by_ref('limit',$limit);	
	
	//lets find some recent photos
	new RecentImageList($smarty);
}

$smarty->display($template, $cacheid);

	
?>
