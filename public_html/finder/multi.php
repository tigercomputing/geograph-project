<?php
/**
 * $Project: GeoGraph $
 * $Id: places.php 5786 2009-09-12 10:18:04Z barry $
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




$smarty = new GeographPage;
$template = 'finder_multi.tpl';

if (!empty($_GET['q'])) {
	$q=trim($_GET['q']);
	
	$fuzzy = !empty($_GET['f']);
	
	$sphinx = new sphinxwrapper($q);

	//gets a cleaned up verion of the query (suitable for filename etc) 
	$cacheid = $sphinx->q.'.'.$fuzzy;

	$sphinx->pageSize = $pgsize = 15;

	
	#$pg = (!empty($_GET['page']))?intval(str_replace('/','',$_GET['page'])):0;
	if (empty($pg) || $pg < 1) {$pg = 1;}
	
	$cacheid .=".".$pg;
	
	if (!$smarty->is_cached($template, $cacheid)) {
		
		$db = GeographDatabaseConnection(true);
		
		$prev_fetch_mode = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		
		$others = array();
		$inners = array();
		##########################################################
		
		$u2 = urlencode($sphinx->qclean);
		
		$others['search'] = array('title'=>'Full Image Search','url'=>"/search.php?q=$u2");
		$others['content'] = array('title'=>'Collections','url'=>"/content/?q=$u2");
		$others['places'] = array('title'=>'Placenames','url'=>"/finder/places.php?q=$u2");
		$others['users'] = array('title'=>'Contributors','url'=>"/finder/contributors.php?q=$u2");
		$others['sqim'] = array('title'=>'Images by Square','url'=>"/finder/sqim.php?q=$u2");
		$others['text'] = array('title'=>'Simple Text Search','url'=>"/fulltext.php?q=$u2");
		if ($CONF['forums']) {
			$others['discuss'] = array('title'=>'Discussions','url'=>"/finder/discussions.php?q=$u2");
		}	
		
		$others['google'] = array('title'=>'Google Search','url'=>"http://www.google.co.uk/search?q=$u2&sitesearch=".$_SERVER['HTTP_HOST']);
		$others['gimages'] = array('title'=>'Google Images','url'=>"http://images.google.com/images?q=$u2+site:".$_SERVER['HTTP_HOST']);
		
		$try_words = true;
		
		if (preg_match("/\b([a-zA-Z]{1,2}) ?(\d{1,5})[ \.]?(\d{1,5})\b/",$sphinx->qclean,$gr)) {
			$square=new GridSquare;
			$grid_ok=$square->setByFullGridRef($sphinx->qclean,true);

			if ($grid_ok) {
				$gr = $square->grid_reference;
				
				$old = $sphinx->qclean;
				
				//todo check for high res - eg centisquare (brwose has a centisquare filter) 
				
				##########################################################
				
				$sphinx->prepareQuery("grid_reference:{$square->grid_reference}");
				$ids = $sphinx->returnIds(1,"_images");
				if (!empty($ids) && count($ids)) {
					
					if (count($ids) > 15) {
						$inners['browse'] = array('title'=>'In '.$gr,'url'=>"/gridref/$gr?inner");
					} else {
						$u2 = urlencode($sphinx->qclean);
	
						$inners['browse'] = array('title'=>'In '.$gr,'url'=>"/finder/search-service.php?q=$u2&amp;inner");
					}
				}
			
				##########################################################
				
				$ids = $sphinx->returnIdsViewpoint($square->getNatEastings(),$square->getNatNorthings(),$square->reference_index,$square->grid_reference);
				if (!empty($ids) && count($ids)) {

					$u2 = urlencode($sphinx->q);

					$inners['taken'] = array('title'=>'Taken From '.$gr,'url'=>"/finder/search-service.php?q=$u2&amp;inner");
				}
			
				##########################################################
				
				$sphinx->prepareQuery("{$square->grid_reference} -grid_reference:{$square->grid_reference}");
				$ids = $sphinx->returnIds(1,"_images");
				if (!empty($ids) && count($ids)) {
					//search-service automatically searches nearby, if first param is a gr, so swap them
					$u2 = urlencode("-grid_reference:{$square->grid_reference} {$square->grid_reference}");
					
					$inners['mentioning'] = array('title'=>'Mentioning '.$gr,'url'=>"/finder/search-service.php?q=$u2&amp;inner");
				}
			
				##########################################################
				
				//search-service automatically searches nearby, but we can exclude the current square
				$u2 = urlencode("{$square->grid_reference} -grid_reference:{$square->grid_reference}"); 
				
				$inners['nearby'] = array('title'=>'Near '.$gr,'url'=>"/finder/search-service.php?q=$u2&amp;inner");
				
				##########################################################
				
				$sphinx->qclean = $old;
				$try_words = false;
			} 
		}
		
		if ($try_words) {
			##########################################################		
			
			//specifically exclude contributor column!
			$old = $sphinx->q;
			if (!preg_match('/@\w+/',$old)) {
				//todo, maybe extend this to myriad etc?
				$sphinx->q = "@(title,comment,imageclass) ".$sphinx->q;
			}
			
			$ids = $sphinx->returnIds($pg,'_images');	
			if (!empty($ids) && count($ids)) {
				if (count($ids) == 1) {
					$inners['text'] = array('title'=>'','url'=>"/frame.php?id=".implode(',',$ids));
				} else {
					$u3 = urlencode($sphinx->q);
					$inners['text'] = array('title'=>'Textual Matches','url'=>"/finder/search-service.php?q=$u3&amp;inner");

				}
				unset($others['text']);
			}
			
			$sphinx->q = $old;
			
			##########################################################		

			$ids = $sphinx->returnIds($pg,'gaz');	
			if (!empty($ids) && count($ids)) {
				if (count($ids) == 1) {
					$where = "id IN(".join(",",$ids).")";
					$row = $db->getRow("select name,gr from placename_index where $where");

					$inners['search-maker'] = array('title'=>'Images in '.$row['name'],'url'=>"/finder/search-maker.php?placename=".implode(',',$ids)."&amp;do=1");

					$inners['places'] = array('title'=>'around '.$row['name'].', '.$row['gr'],'url'=>"/search.php?placename=".implode(',',$ids)."&amp;do=1&displayclass=search");
				} else {
					$inners['places'] = array('title'=>'Places matching '.$sphinx->qclean.' ['.$sphinx->resultCount.']','url'=>"/finder/places.php?q=$u2&amp;inner");

				}
				unset($others['places']);
			}

			##########################################################		

			$ids = $sphinx->returnIds($pg,'user');	
			if (!empty($ids) && count($ids)) {
				if (count($ids) == 1) {
					$where = "user_id IN(".join(",",$ids).")";
					$row = $db->getRow("select realname from user where $where");

					$inners['places'] = array('title'=>'Images by '.$row['realname'],'url'=>"/search.php?user_id=".implode(',',$ids)."&amp;do=1&displayclass=search");
				} else {
					$inners['places'] = array('title'=>'Contributors Matching '.$sphinx->qclean.' ['.$sphinx->resultCount.']','url'=>"/finder/contributors.php?q=$u2&amp;inner");

				}
				unset($others['places']);
			}

			##########################################################		

			$ids = $sphinx->returnIds($pg,'category');	
			if (!empty($ids) && count($ids)) {
				if (count($ids) == 1) {
					$where = "category_id IN(".join(",",$ids).")";
					$row = $db->getRow("select imageclass from category_stat where $where");

					$inners['category'] = array('title'=>'Category ',$row['imageclass'],'url'=>"/search.php?imageclass=".urlencode($row['imageclass'])."&amp;do=1&displayclass=search");
				} else {
					$inners['category'] = array('title'=>'Images in similar categories','url'=>"/finder/search-service.php?q=category:$u2&amp;inner");

				}
			}

			##########################################################
		}
		
		$smarty->assign_by_ref("others",$others);
		$smarty->assign_by_ref("inners",$inners);
	}
	
	$smarty->assign("q",$sphinx->qclean);
	$smarty->assign("fuzzy",$fuzzy);
}

$smarty->display($template,$cacheid);
