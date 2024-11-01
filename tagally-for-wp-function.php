<?php
error_reporting(0);// Turn off all error reporting


class TagAllyForWPFunction
{
	var $baseurl;
	var $siteurl;
	var $home;
	var $install_directory;
	
	var $tbtags;
	var $tbpost2tag;
	
	var $std_colorschemes;
	
	var $tagsys;
	var $oldtag;

	function TagAllyForWPFunction()
	{
		global $table_prefix, $wpdb;
		
		$this->baseurl = "/wordpress";
		$this->siteurl = get_option('siteurl');
		$this->home = get_option('home');
		$this->install_directory = '/tagally-for-wp';
		$this->tbtags = $table_prefix . "tags";
		$this->tbpost2tag = $table_prefix . "post2tag";
		$this->version = '0.33';
		
		$this->std_colorschemes = array(
			  "iris"=>"#ff4a00,#ff6800,#ff8600,#ffa400,#ffc000,#ffd900,#ffec00,#fffa00,#f6fc02,#e3fc05,"
			 					."#cdfc07,#b5f80a,#9aef0d,#7ee411,#63d915,#48cd1b,#2fc221,#1aba29,#08b532,#00b43e,"
			 					."#00b64c,#00bb5c,#00c26f,#00ca84,#00d399,#00ddb2,#00e8cf,#00eae8,#00ebf9,#00e5ff,"
			 					."#00cefb,#00abeb,#0083d6,#0067c6,#0052b9,#003dad,#002aa2,#001999,#000b92,#040190,"
			 					."#140090,#2b0091,#460096,#62009d,#8400a5,#af00b1,#d800bc,#f600c4,#ff09c8,#FF62DC",
			 	"prairie"=>"#050091,#020091,#213B92,#1C2A84,#294390,#001BC2,#3A55C1,#0022C2,#5558FF,#85FFF2,"
			 						 ."#85FFDD,#85FFEB,#B6F1FF,#3CFFF3,#55EAFF,#55FFF8,#9DF9FF,#55FFE3,#85FFF0,#24FFFA,"
			 						 ."#6DFFF0,#55FFE7,#85FFD8,#85F0FF,#85FFF5,#6DFFED,#6DFFEA,#3CFFCC,#6DD6FF,#3CC8FF,"
			 						 ."#3CFFC4,#24FFFF,#85F7FF,#0CF0FF,#FFEB3C,#04F200,#3CFF6B,#FF3524,#48FF3C,#55FF5B,"
			 						 ."#F200DA,#24FF35,#3CFF63,#00DA15,#FFAC0C,#149100,#00AA28,#0E7900,#076100,#00480D",
        "barbie"=>"#FF6DF0,#FFFF3C,#FF55CC,#FFF73C,#FF85F2",
        "milkcow"=>"#E5E5E5,#000000,#E5E5E5,#E5E5E5,#000000,#E5E5E5,#E5E5E5,#000000,#000000,#E5E5E5"
		);
		
		$this->tagsys = ereg("^[2-9].[3-9]", get_bloginfo('version'));

		$this->oldtag = '';
		
		$maxfont = "28";
		$minfont = "10";
		$maxcolor = array("F0","00","00");
		$mincolor = array("00","00","FF");
		 
		if(!$this->tagsys)
		{
			$sql = <<<SQL
				CREATE TABLE IF NOT EXISTS $this->tbtags (
					tag_ID int(11) NOT NULL auto_increment,
				  tag varchar(255) NOT NULL default '',
				  PRIMARY KEY  (tag_ID)
				)
SQL;
			$wpdb->query($sql);
	
			$sql = <<<SQL
				CREATE TABLE IF NOT EXISTS $this->tbpost2tag (
				  rel_id int(11) NOT NULL auto_increment,
				  tag_id int(11) NOT NULL default '0',
				  post_id int(11) NOT NULL default '0',
				  ip_address varchar(15),
				  PRIMARY KEY  (rel_id)
				)
SQL;
			$wpdb->query($sql);
			
			$sql = "Describe $this->tbtags slug";
			$fields = $wpdb->get_results($sql);
			if(is_null($fields[0]))
			{
				$sql = <<<SQL
					ALTER TABLE $this->tbtags
					  ADD slug varchar(255) NOT NULL default ''
SQL;
				$wpdb->query($sql);
				
				$sql = "SELECT * from $this->tbtags";
				$tags = $wpdb->get_results($sql);
				foreach($tags as $tag)
				{
					$slug = sanitize_title($tag->tag);
					$tagslug_check = $wpdb->get_var("SELECT tag_ID FROM $this->tbtags WHERE slug = '$slug' AND tag_ID != $tag->tag_ID LIMIT 1");
					if($tagslug_check)
					{
						$suffix = 2;
						do {
							$alt_slug = $slug . "-$suffix";
							$tagslug_check = $wpdb->get_var("SELECT tag_ID FROM $this->tbtags WHERE slug = '$alt_slug' AND tag_ID != $tag->tag_ID LIMIT 1");
							$suffix++;
						} while ($tagslug_check);
						$slug = $alt_slug;
					}
					if(!$wpdb->query("UPDATE $this->tbtags SET slug='$slug' WHERE tag_ID=$tag->tag_ID"))
						return;
				}
			}
		}

		add_option("TAFWPShowpopuptooltips","Yes","selecting yes to pop up tooltip when moving mouse cursor onto a tag displayed in tagcloud");
		add_option("TAFWPCloudTitle","Tag Cloud","");
		add_option("TAFWPMaxTagCount","50","");
		add_option("TAFWPMostpopularFont","28","");
		add_option("TAFWPLeastpopularFont","10","");
		
		add_option("TAFWPColorType","2","");
		add_option("TAFWPColorCount","40","");
		add_option("TAFWPStdColorScheme","iris","");
		add_option("TAFWPCustomColorScheme","","");
		add_option("TAFWPPreBGColor","FFFFFF","");
		add_option("TAFWPMostpopularcolor","F00000","");
		add_option("TAFWPLeastpopularcolor","0000FF","");
		
		add_option("TAFWPExportprogress","0","");
		add_option("TAFWPExportId","-1","");
		
		update_option("TAFWPVersion",$this->version);
	}
	
//	function TagAllyForWPInit()
//	{
//		global $wpdb;
//		
//		$maxfont = "28";
//		$minfont = "10";
//		$maxcolor = array("F0","00","00");
//		$mincolor = array("00","00","FF");
//		 
//		if(!$this->tagsys)
//		{
//			$sql = <<<SQL
//				CREATE TABLE IF NOT EXISTS $this->tbtags (
//					tag_ID int(11) NOT NULL auto_increment,
//				  tag varchar(255) NOT NULL default '',
//				  PRIMARY KEY  (tag_ID)
//				)
//SQL;
//			$wpdb->query($sql);
//	
//			$sql = <<<SQL
//				CREATE TABLE IF NOT EXISTS $this->tbpost2tag (
//				  rel_id int(11) NOT NULL auto_increment,
//				  tag_id int(11) NOT NULL default '0',
//				  post_id int(11) NOT NULL default '0',
//				  ip_address varchar(15),
//				  PRIMARY KEY  (rel_id)
//				)
//SQL;
//			$wpdb->query($sql);
//			
//			$sql = "Describe $this->tbtags slug";
//			$fields = $wpdb->get_results($sql);
//			if(is_null($fields[0]))
//			{
//				$sql = <<<SQL
//					ALTER TABLE $this->tbtags
//					  ADD slug varchar(255) NOT NULL default ''
//SQL;
//				$wpdb->query($sql);
//				
//				$sql = "SELECT * from $this->tbtags";
//				$tags = $wpdb->get_results($sql);
//				foreach($tags as $tag)
//				{
//					$slug = sanitize_title($tag->tag);
//					$tagslug_check = $wpdb->get_var("SELECT tag_ID FROM $this->tbtags WHERE slug = '$slug' AND tag_ID != $tag->tag_ID LIMIT 1");
//					if($tagslug_check)
//					{
//						$suffix = 2;
//						do {
//							$alt_slug = $slug . "-$suffix";
//							$tagslug_check = $wpdb->get_var("SELECT tag_ID FROM $this->tbtags WHERE slug = '$alt_slug' AND tag_ID != $tag->tag_ID LIMIT 1");
//							$suffix++;
//						} while ($tagslug_check);
//						$slug = $alt_slug;
//					}
//					if(!$wpdb->query("UPDATE $this->tbtags SET slug='$slug' WHERE tag_ID=$tag->tag_ID"))
//						return;
//				}
//			}
//		}
//
//
//		add_option("TAFWPShowpopuptooltips","Yes","selecting yes to pop up tooltip when moving mouse cursor onto a tag displayed in tagcloud");
//		add_option("TAFWPCloudTitle","Tag Cloud","");
//		add_option("TAFWPMaxTagCount","50","");
//		add_option("TAFWPMostpopularFont","28","");
//		add_option("TAFWPLeastpopularFont","10","");
//		
//		add_option("TAFWPColorType","2","");
//		add_option("TAFWPColorCount","40","");
//		add_option("TAFWPStdColorScheme","coco","");
//		add_option("TAFWPCustomColorScheme","","");
//		
//		add_option("TAFWPMostpopularcolor","#F00000","");
//		add_option("TAFWPLeastpopularcolor","#0000FF","");
//		
//		update_option("TAFWPVersion",$this->version);
//	}
	
	function GetTagsForPost($postid,$limit)
	{
		global $wpdb;

		$sql = "SELECT DISTINCT t.tag, t.slug FROM $this->tbtags t INNER JOIN $this->tbpost2tag p2t ON p2t.tag_id = t.tag_id INNER JOIN $wpdb->posts p ON p2t.post_id = p.ID AND p.ID=$postid ORDER BY t.tag ASC limit $limit";
		$tags = $wpdb->get_results($sql);
		return $tags;
	}

	function GetFrequencyOfTags()
	{
		global $wpdb;
		
		if ($this->tagsys)
		{
			$sql = "select sum(count) from $wpdb->term_taxonomy where taxonomy = 'post_tag'";
		}
		else
		{
			$sql = "select count(*) from $this->tbpost2tag p2t inner join $wpdb->posts p on p2t.post_id = p.ID WHERE post_date_gmt < '" . current_time('mysql', 1)."' and post_type = 'post'";
		}
		return $wpdb->get_var($sql);
	}
	
	function GetPopularTags($limit)
	{
		global $wpdb;

		$now = current_time('mysql', 1);
		$frequency = $this->GetFrequencyOfTags();
		if($frequency && $frequency > 0)
		{
			if ($this->tagsys)
			{
				$sql = <<<SQL
				select t.name as tag, t.term_id as id, t.slug, tt.count as c, (tt.count/$frequency*100) as weight
				from $wpdb->terms t inner join $wpdb->term_taxonomy tt on t.term_id = tt.term_id
				where tt.taxonomy = 'post_tag' and tt.count > 0
				group by t.name
				order by c desc
				limit $limit
SQL;
			}
			else
			{
				$sql = <<<SQL
				select t.tag, t.slug, t.tag_id as id, count(p2t.post_id) as c, (count(p2t.post_id)/$frequency*100) as weight
				from $this->tbtags t inner join $this->tbpost2tag p2t on t.tag_id = p2t.tag_id
								  inner join $wpdb->posts p on p2t.post_id = p.ID
				where post_date_gmt < '$now' and post_type = 'post'
				group by t.tag
				having c > 0
				order by c desc
				limit $limit
SQL;
		 }
		 return $wpdb->get_results($sql);
		}
		else
		{
			return null;
		}
	}
	
	function CreatePreviewCloud()
	{
		$previewtags = array('2007'=>'12px','2008'=>'16px','Adsense'=>'12px','Advertising'=>'12px','Apple'=>'17px','art'=>'17px','blog'=>'32px','TagALLY'=>'19px','blogs'=>'14px','Britney Spears'=>'12px',
												 'Business'=>'13px','China'=>'15px','christmas'=>'13px','Comedy'=>'12px','computer'=>'12px','content'=>'12px','design'=>'19px','download'=>'16px','dreams'=>'13px','dvd'=>'12px',
												 'eBay'=>'12px','education'=>'12px','facebook'=>'14px','family'=>'14px','film'=>'16px','Finance'=>'12px','Firefox'=>'16px','flash'=>'13px','food'=>'16px','free'=>'14px',
												 'friends'=>'12px','fun'=>'12px','Funny'=>'14px','game'=>'15px','games'=>'15px','Google'=>'29px','health'=>'15px','home'=>'14px','humor'=>'13px','india'=>'12px',
												 'internet'=>'23px','iPhone'=>'16px','iPod'=>'14px','japan'=>'12px','Job'=>'14px','John McCain'=>'12px','laptop'=>'12px','Life'=>'12px','Linux'=>'17px','live'=>'12px'
												 );

	 	$sorttags = array('2007','Adsense','Advertising','Britney Spears','Comedy','computer','content','dvd','eBay','education',
 											'Finance','friends','fun','india','japan','John McCain','laptop','Life','live','Business',
 											'christmas','dreams','flash','humor','blogs','facebook','family','free','Funny','home',
 											'iPod','Job','China','game','games','health','2008','download','film','Firefox',
 											'food','iPhone','Apple','art','Linux','TagALLY','design','internet','Google','blog'
 											);
 											
	 	$step = 49;
	 	
	 	$colortype = get_option("TAFWPColorType");
	 	$colorarr = array();
		$colorsum = 0;
		
	 	if($colortype == "0")
		{
			$colorarr = explode(',',get_option("TAFWPCustomColorScheme"));
			$colorsum = count($colorarr);
		}
		else if($colortype == "2")
		{
			$stdcolor = get_option("TAFWPStdColorScheme");
			if(isset($this->std_colorschemes[$stdcolor]))
			{
				$colorarr = explode(',',$this->std_colorschemes[$stdcolor]);
				$colorsum = count($colorarr);
			}
		}

		$i=0;
		foreach($previewtags as $key=>$value)
		{
			if($colortype == "0") //custom colorful tag cloud
			{
				if($colorsum>0)
					$color = '#'.$colorarr[$i%$colorsum];
				else
					$color = '#000042';
			}
			else if($colortype == "1") //progressive color tag cloud
			{
				$curkey = array_keys($sorttags,$key);
				$color = $this->GetTagColor2($step,$curkey[0]);
			}
			else if($colortype == "2") //standard colorful tag cloud
			{
				if($colorsum>0)
					$color = $colorarr[$i%$colorsum];
				else
					$color = '#000042';
			}
			echo "<a id='tagally_ts_$i' style='font-size: $value; color: $color; background-color: transparent;'  href='#'>$key</a>\n";
			$i+=1;
		}
	}

	function GetTagFontSize($maxc,$minc,$weight)
	{
		$mpfont = get_option("TAFWPMostpopularFont");
		$lpfont = get_option("TAFWPLeastpopularFont");
		if($maxc == $minc)
			$fontsize = $mpfont;
		else
			$fontsize = intval(($weight-$minc)/($maxc-$minc)*($mpfont-$lpfont)+$lpfont);
		return $fontsize."px";
	}
	
	function CreateColorTable($colors)
	{
		$colorpercolum = 6;
		$colorarr = explode(',',$colors);
		$sum = count($colorarr);
		if($sum>0)
		{
			echo "<table>";
			$i=$j=0;
			foreach($colorarr as $value)
			{
				if($j%$colorpercolum==0)
				{
					echo "<tr>";
				}
				echo <<< HTML
					<td>
						#<input id="colrPkrshow_$j" class="tga-input "type="text" size="6" maxlength="6" onkeyup="value=value.replace(/[^0-9A-Fa-f]/, &#039;&#039;)" onblur='iupcolor(this , $j)' value="$value"><input id="colrPkrBtn_$j" type="button" class="colourPickerButton" value="" style="background-color:#$value" /></td>
HTML;
			  if($j==$colorpercolum-1)
			  {
			  	echo "</tr>";
			  	$i+=1;
			  }
			  $j+=1;
			}
			// when there are less colors than $colorpercolum in the last line, fill <td>&nbsp;</td>
			if($j%$colorpercolum > 0 && $j>$colorpercolum)
			{
			  	do
			  	{
			  		echo "<td>&nbsp;</td>";
			  		$j+=1;
			  	}while($j%$colorpercolum > 0);
			  	echo "</tr>";
			}
			echo "</table>";
		}
	}
	
	function GetTagColor1($weight)
	{
		$colors = array("#003333","#ff9999","#cc9900","#669966","#336666","#9966cc","#ff33ff","#660066",
            "#990066","#666666","#ff3333","#999933","#00cc00","#003399","#6600cc","#cc00cc",
            "#ff0099","#663333","#999999","#990000","#33cccc","#006600","#3366ff","#333366",
            "#000099","#003300","#999966","#cc6600","#330000","#000033","#663366","#993333",
            "#ff6633","#6666ff","#ff6666","#66cc66","#cc00cc","#333300","#000066","#666633");
     return $colors[$weight%40];
	}
	
	function GetTagColor2($steps,$delta)
	{
		$mpcolor = get_option("TAFWPMostpopularcolor");
		$lpcolor = get_option("TAFWPLeastpopularcolor");
		$minr = hexdec(substr($lpcolor,0,2));
		$ming = hexdec(substr($lpcolor,2,2));
		$minb = hexdec(substr($lpcolor,4,2));
		$maxr = hexdec(substr($mpcolor,0,2));
		$maxg = hexdec(substr($mpcolor,2,2));
		$maxb = hexdec(substr($mpcolor,4,2));
		
		$r = dechex(intval($delta*($maxr-$minr)/$steps+$minr));
		$g = dechex(intval($delta*($maxg-$ming)/$steps+$ming));
		$b = dechex(intval($delta*($maxb-$minb)/$steps+$minb));
		

		if (strlen($r) == 1) $r = "0" . $r;
		if (strlen($g) == 1) $g = "0" . $g;
		if (strlen($b) == 1) $b = "0" . $b;

		return "#$r$g$b";
	}
	
	function GetTagLink($tag)
	{
		global $wp_rewrite;

		if (empty($wp_rewrite->permalink_structure))
		{
			$file = get_option('home') . '/';
			$taglink = $file . '?tag=' . $tag;
		}
	  else
	  {
			$taglink = '/tag/'.$tag;
			$taglink = get_option('home') . user_trailingslashit($taglink, 'category');
		}
		return $taglink;
	}
	
	function SortByAlphabetical($taga, $tagb)
	{
		 return strcmp($taga->tag,$tagb->tag);
	}
	
	function SortByPopularity($taga, $tagb)
	{
		if($taga->c > $tagb->c)
		{
			return 1;
		}
	  if($taga->c == $tagb->c)
	  {
	  	if($taga->id > $tagb->id)
	  		return 1;
	  	if($taga->id == $tagb->id)
	  	  return 0;
	  	if($taga->id < $tagb->id)
	  		return -1;
	  }
	  if($taga->c < $tagb->c)
	  {
	  	return -1;
	  }
	}
	
	function GetPostsForTagTemplates($tags)
	{
	}
	
	function ShowPopularTags($limit)
	{
		global $wpdb;
		
		$poptags = $this->GetPopularTags($limit);
		$showpopup = get_option("TAFWPShowpopuptooltips");
		if($poptags)
		{
			usort($poptags,array("TagAllyForWPFunction","SortByAlphabetical"));
			$taghtml = "<div style='margin:0px;padding:5px;'>";
			
			if($showpopup == "Yes")
			{
				$scriptStr = "var shiStrArr = new Array(";
				$scriptID = "var shiIDArr = new Array(";
			}
			$maxc = $poptags[0]->c;
			$minc = $maxc;
			foreach($poptags as $poptag)
			{
				if($maxc < $poptag->c)  $maxc = $poptag->c;
				if($minc > $poptag->c)  $minc = $poptag->c;
			}

			$colortype = get_option("TAFWPColorType");
			
			$colorarr = array();
			$colorsum = 0;
			
			if($colortype == "0")
			{
				$colorarr = explode(',',get_option("TAFWPCustomColorScheme"));
				$colorsum = count($colorarr);
			}
			else if($colortype == "1")
			{
				$steparr = $poptags;
				usort($steparr,array(&$this,"SortByPopularity"));
			}
			else if($colortype == "2")
			{
				$stdcolor = get_option("TAFWPStdColorScheme");
				if(isset($this->std_colorschemes[$stdcolor]))
				{
					$colorarr = explode(',',$this->std_colorschemes[$stdcolor]);
					$colorsum = count($colorarr);
				}
			}
			
			$i=0;
			foreach($poptags as $poptag)
			{
				$fontsize = $this->GetTagFontSize($maxc,$minc,$poptag->c);
				
				if($colortype == "0") //custom colorful tag cloud
				{
					if($colorsum>0)
						$color = '#'.$colorarr[$i%$colorsum];
					else
						$color = '#000042';
				}
				else if($colortype == "1") //progressive color tag cloud
				{
					$curkey = array_keys($steparr,$poptag);
					$c = count($steparr);
					$color = $this->GetTagColor2(count($steparr)-1,$curkey[0]);
				}
				else if($colortype == "2") //standard colorful tag cloud
				{
					if($colorsum>0)
						$color = $colorarr[$i%$colorsum];
					else
						$color = '#000042';
				}
				
				if ($this->tagsys)
				{
					$taglink = get_tag_link($poptag->id);
				}
				else
				{
					$param = urlencode($poptag->slug);
					$taglink = "$this->siteurl/index.php?tag=$param";
				}
				$taghtml .= " <a id='taly_$i' href=\"$taglink\"";
				if($showpopup == "Yes")
				{
					$taghtml .= " onmouseover=\"try{dropmenu(event);}catch(e){}\" onmouseout=\"try{delayhidemenu();}catch(e){}\"";
				}
				$taghtml .= " style=\"font-size:$fontsize; color:$color ;display:inline;text-decoration:none;background-image:none;background-color:transparent;border:0px;margin:0pt;padding:0px;\">".$poptag->tag."</a>";
				$taghtml .= "&nbsp;&nbsp;&nbsp;";
				
				if($showpopup == "Yes")
				{
					$tag = addslashes($poptag->tag);
					$scriptStr .= "'$tag',";
					$scriptID .= "'-1',";
				}
				
				$i++;
			}
			
			if($showpopup == "Yes")
			{
				$scriptStr = substr($scriptStr,0,-1).");";
				$scriptID = substr($scriptID,0,-1).");";
				$taghtml .= "<script>$scriptStr$scriptID</script>";
			}
			
			$taghtml .= "</div>";
			echo $taghtml;
		}
		else
		{
			echo "<strong style='color:#F00'>No tag</strong>";
		}
	}
	
	function admin_menu_tag_option()
	{
		add_submenu_page('index.php', 'TagALLY', 'TagALLY', 8, "tagally-for-wp-function", array(&$this,'tag_submenu_panel'));
	}
	
	function tag_submenu_panel()
	{
?>
		<div class="subhead">
				<ul class="subsub">
					<li <?php if($_GET['subpage'] == '') {print "class=current";} ?> >
						<a href='?page=tagally-for-wp-function' > <?php print(_e("Options")); ?></a>
					</li>
					<li <?php if($_GET['subpage'] == 'manage') echo "class=current"; ?> >
						<a href='?page=tagally-for-wp-function&subpage=manage' ><?php print(_e("Manage"));?></a>
					</li>
				</ul>
		</div>
<?php

		if($_GET['subpage'] == 'manage')
			$this->tag_manage_panel();
		else
			$this->tag_option_panel();
	}
	
	function tag_manage_panel()
	{
		global $wpdb;
		$ex_pro = get_option('TAFWPExportprogress');
?>
			<div class="wrap"  style="float:left;margin-bottom:10px;width:95%">
				<h2>TagALLY-for-WP Manage</h2>
				<a href="<?php echo "$this->siteurl/wp-content/plugins$this->install_directory/tagally-for-wp-help.html"; ?>" target="_new"><b>Help!</b></a>
				<div class="narrow">
					<p>If you are using wordpress 2.5 or higher, you can use the one-click method to export your posts and related tags. </p>
					<p>We'll only collect those publish-status posts's excerpts and url links grouped by their tags in our <a href="http://www.tagally.com" target="_new">TagALLY</a> website, then make them sharable among wordpress blogs and later even anyone who accesses TagALLY website.</p>
					
					<div style="background:#f4f3fc;border:1px solid #b8b2db;margin-bottom:15px;width:600px;position:relative;padding:1px">
						<div id='mgdbgl' style="background:#f4f3fc url('<?php echo "$this->siteurl/wp-content/plugins$this->install_directory/img/loadingbg.png"; ?>') repeat-x scroll 0 0;width:<?php echo $ex_pro; ?>%;height:23px;">
						</div>
						<div id='mgdtxt' style="color:#343241;height:23px;position:absolute;top:2px;left:45%;"><?php echo $ex_pro; ?>%</div>
						<input type='hidden' id='mgdhdex' value='on' />
					</div>
<?php 
		if ($ex_pro < 100)
		{
			echo "<span id='tga_export'><a class='button rbutton' href='javascript:void(0);' onclick=\"tga_mgdoit('$this->siteurl/wp-content/plugins$this->install_directory/img/loader.gif', '$this->siteurl/wp-admin/admin-ajax.php');\">Export posts and related tags</a></span>";
		}
    else
    {
			echo "<div id=\"message\" class=\"updated fade\"><p><strong>You have succeeded in exporting your posts and related tags.</strong></p></div>";
		}
?>
				</div>
			</div>
<?php
		
	}
	function tag_option_panel()
	{
		if ($_POST["action"] == "updated")
	  {
	  	$success = true;

	  	update_option("TAFWPShowpopuptooltips",$_POST["showpopuptooltips"]);
	  	update_option("TAFWPCloudTitle",$_POST["cloudtitle"]);
	  	update_option("TAFWPMaxTagCount",$_POST["maxtagcount"]);
	  	
	  	update_option("TAFWPColorType",$_POST["colortype"]);
	  	
	  	update_option("TAFWPStdColorScheme",$_POST["stdcolor"]);
	  	
	  	update_option("TAFWPColorCount",$_POST["colorcount"]);
			update_option("TAFWPCustomColorScheme",$_POST["tagally_hdn_scot"]);
			
			update_option("TAFWPPreBGColor",$_POST["prebgcolor"]);

	  	if(get_option("TAFWPColorType") == '1')
		  {
		  	if (!ereg("[0-9A-Fa-f]{6}",$_POST["mpcolor"]))
		  	{
		  		$success = false;
		  		echo "<div id=\"message\" class=\"updated fade\"><p><strong>Most popular color must be hexadecimal colours, and need to have the full six digits.</strong></p></div>";
		  	}
		  	else
		  	{
		  		update_option("TAFWPMostpopularcolor",$_POST["mpcolor"]);
		  	}
		  	if (!ereg("[0-9A-Fa-f]{6}",$_POST["lpcolor"]))
		  	{
			  	$success = false;
		  		echo "<div id=\"message\" class=\"updated fade\"><p><strong>Least popular color must be hexadecimal colours, and need to have the full six digits.</strong></p></div>";
		  	}
		  	else
		  	{
		  		update_option("TAFWPLeastpopularcolor",$_POST["lpcolor"]);
		  	}
		  }
	  	if (!ereg("^[1-9]",$_POST["mpfont"]) && !ereg("^[1-9][0-9]}",$_POST["mpfont"]))
	  	{
	  		$success = false;
	  		echo "<div id=\"message\" class=\"updated fade\"><p><strong>Most popular font size must be single or double-digit, and the first digit can't be 0.</strong></p></div>";
	  	}
	  	else
	  	{
	  		update_option("TAFWPMostpopularFont",$_POST["mpfont"]);
	  	}
	  	if (!ereg("^[1-9]",$_POST["lpfont"]) && !ereg("^[1-9][0-9]}",$_POST["lpfont"]))
	  	{
	  		$success = false;
	  		echo "<div id=\"message\" class=\"updated fade\"><p><strong>Least popular font size must be single or double-digit, and the first digit can't be 0.</strong></p></div>";
	  	}
	  	else
	  	{
	  		update_option("TAFWPLeastpopularFont",$_POST["lpfont"]);
	  	}
	  	if ($success)
	  	{
	  		echo "<div id=\"message\" class=\"updated fade\"><p><strong>Options saved.</strong></p></div>";
	  	}
	  }
	  
		$showpopup = get_option("TAFWPShowpopuptooltips");
		$title = get_option("TAFWPCloudTitle");
		$maxtagcount = get_option("TAFWPMaxTagCount");
		
		$mpfont = get_option("TAFWPMostpopularFont");
		$lpfont = get_option("TAFWPLeastpopularFont");
		
		$colortype = get_option("TAFWPColorType");
		
		$mpcolor = get_option("TAFWPMostpopularcolor");
		$lpcolor = get_option("TAFWPLeastpopularcolor");

		$stdcolor = get_option("TAFWPStdColorScheme");
		
		$colorcount = get_option("TAFWPColorCount");
		$customcolor = get_option("TAFWPCustomColorScheme");
		
		$prebgcolor = get_option("TAFWPPreBGColor");

?>
		<div class="wrap"  style="float:left;margin-bottom:10px;width:95%">
			<h2>TagALLY-for-WP Options</h2>
			<a href="<?php echo "$this->siteurl/wp-content/plugins$this->install_directory/tagally-for-wp-help.html"; ?>" target="_new"><b>Help!</b></a>
			<br/>
			<form method="post">
				<table class="tga-table" border="0" cellpadding="3" cellspacing="0">
					<tr>
						<th valign="top" scope="row" colspan="2">
							<label class="tga-message">The color value must be hexadecimal colors, and need to have the full six digits. The font size must be single or double-digit, and the first digit can not be 0.</label>
						</th>
					</tr>
					<tr>
						<th width="270" align="left" nowrap="nowrap" scope="row"><b>Pop up tooltips in tagcloud:</b></th>
						<td width="710">
							<select name="showpopuptooltips" id="showpopuptooltips">
								<option value="Yes" <?php if($showpopuptooltips == "Yes") echo "selected=\"selected\""; ?>>Yes</option>
								<option value="No" <?php if ($showpopup == "No") echo "selected=\"selected\"";?>>No</option>
							</select>
						</td>
					</tr>
					<tr>
						<th width="270" align="left" scope="row">Most popular Font:</th>
						<td width="710"><input name="mpfont" class="tga-input" size="2" maxlength="2" value="<?php echo $mpfont;?>">px</td>
					</tr>
					<tr>
						<th width="270" align="left" scope="row">Least popular Font:</th>
						<td width="710"><input name="lpfont" class="tga-input" size="2" maxlength="2" value="<?php echo $lpfont;?>">px</td>
					</tr>
					<tr>
						<th width="270" align="left" scope="row">TagCloud Title: <span style="font-size:10px;font-weight:normal">( in 40 characters or less )</span></th>
						<td width="710"><input type='text' name="cloudtitle" class="tga-input" size="40" maxlength="40" value="<?php echo $title;?>"></td>
					</tr>
					<tr>
						<th width="270" align="left" scope="row">Showing how many tags in tagcloud most: <span style="font-size:10px;font-weight:normal">  &nbsp;  ( in 99 tags or less )</span></th>
						<td width="710"><input type='text' name="maxtagcount" class="tga-input" size="2" maxlength="2" onkeyup="value=value.replace(/[^0-9]/, '')" value="<?php echo $maxtagcount;?>"></td>
					</tr>
					<tr>
						<td width="270" valign="top" rowspan="2">
							<p>For example:</p>
							<div class="tagally_tagdiv" id="tga_exmdiv" style="background-color:#<?php echo $prebgcolor;?>;">
<?php
		$this->CreatePreviewCloud();
?>
              </div>
						</td>
						<td  width="710"  valign="top">
					    <lable class="tga_tgctype">TagCloud color type:</label>
					    <!-- custom colorscheme -->
							<p><input name="colortype" id="tga_colortype_0" type="radio" value="0" onclick="hideCofDiv(0);"
<?php
		$display = "none";
		if($colortype == "0")
		{
			echo " checked=\"checked\"";
			$display = "block";
		}
?>
							/><label class="tga-hlab" for="tga_colortype_0">Custom Colorful TagCloud</label></p>
							<p class='tga-wpc'>Custom your color scheme which consists of a series of colors. Firstly setting the amount of colors in 'how many colors:' input field. Then press 'setting' button to setting all these colors detailed. Press 'Preview' button to preview the colorful tagcloud displayed in your color scheme. Finally press 'Update Options' button to save your setting.</p>
							<div id="tagally_vcd_0"  style="display:<?php echo $display;?>">
								<p id="tagally_ctp">
									How many colors : <input type="text" name="colorcount" class="tga-input" value="<?php echo $colorcount;?>" id="tagallytbnum" size="3" maxlength="2" /> 
									<input type="button" value="Setting" class="tga-button" id="tagallytbnumbtn" onclick="tga_createCoTb()" /> 
								</p>
								<div id="tagallyaddcolortable" style="width:400;height:300;">
<?php
	  if(is_string($customcolor) && $customcolor!="")
	  {
			$this->CreateColorTable($customcolor);
	  }
?>
								</div>
								<p>
									<input type="button" class="tga-button" id="tagally_apc_btn" value="Preview" onclick="apply_colortb()" style="float:left;display:none" />
									<input type="button" value="Reset" class="tga-button" id="tagallyrscbtn" onclick="tga_createCoTb()" style="display:none" />
								</p>
								<input type="hidden" id="tagally_hdn_scot" name="tagally_hdn_scot" value="<?php echo $customcolor;?>" />
								<input type="hidden" id="tagally_hdn_selno" name="tagally_hdn_selno" value="<?php echo $colortype;?>" />
							</div>
							<!-- progressive colorsheme -->
							<p><input name="colortype" id="tga_colortype_1" type="radio" value="1" onclick="hideCofDiv(1);"
<?php
		$display = "none";
		if($colortype == "1")
		{
			echo " checked=\"checked\"";
			$display = "block";
		}
?>
							/><label class="tga-hlab" for="tga_colortype_1">Progressive Color TagCloud</label></p>
							<p class='tga-wpc'>Setting 'Most popular color'(most popular tag will be displayed in the color) and 'Least popular color'(least popular tag will be displayed in the color), press 'Preview' button to preview the tagcloud in progressive colors, Finally press 'Update Options' button to save your setting.</p>
							<div id="tagally_vcd_1"  style="display:<?php echo $display;?>">
								<ul id="tagally_color">
								   <li>Most popular color:&nbsp;&nbsp;&nbsp;&nbsp;#<input id="mpcolor" name="mpcolor" class="tga-input" size="6" maxlength="6" onblur="iupcolor(this , 'mpcolorbtn')" onkeyup="value=value.replace(/[^0-9A-Fa-f]/, '')" onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^0-9A-Fa-f]/,''))" value="<?php echo $mpcolor;?>" /><input id='mpcolorbtn' type='button' class='colourPickerButton'  value='' style='background-color:#<?php echo $mpcolor;?>' /></li>
		               <li>Least popular color:&nbsp;&nbsp;&nbsp;&nbsp;#<input id="lpcolor" name="lpcolor" class="tga-input" size="6" maxlength="6" onblur="iupcolor(this , 'lpcolorbtn')" onkeyup="value=value.replace(/[^0-9A-Fa-f]/, '')" onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^0-9A-Fa-f]/,''))" value="<?php echo $lpcolor;?>" /><input id='lpcolorbtn' type='button' class='colourPickerButton' value='' style='background-color:#<?php echo $lpcolor;?>' /></li>
					      </ul>
					      <input type="button" class="tga-button" value="Preview" id="tagallytgdpcbtn" onclick="progColArr()" />
				      </div>
					    <!-- standard colorscheme -->
					    <p><input name="colortype" id="tga_colortype_2" type="radio" value="2" onclick="hideCofDiv(2);"
<?php
		$display = "none";
		if($colortype == "2")
		{
			echo " checked=\"checked\"";
			$display = "block";
		}
?>
							/><label class="tga-hlab" for="tga_colortype_2">Standard Colorful TagCloud</label></p>
							<p class='tga-wpc'>The TagALLY-for-WP plugin supplies six standard color schemes for your convenience. Choose one of them, press 'Preview' button to preview the colorful tagcloud, Finally press 'Update Options' button to save your setting.</p>
					    <div id="tagally_vcd_2"  style="display:<?php echo $display;?>">
								<p id="tagally_ctp_1">
									Select one color scheme : <select name="stdcolor" id="tagally_std_color" onchange="tga_selstac(this.options.selectedIndex)">
<?php
		if($stdcolor == 'iris')
		{
?>
										<option value="iris" selected>iris</option>
										<option value="prairie">prairie</option>
										<option value="barbie">barbie</option>
										<option value="milkcow">milk cow</option>
<?php
		}
		else if($stdcolor == 'prairie')
		{
?>
										<option value="iris">iris</option>
										<option value="prairie" selected>prairie</option>
										<option value="barbie">barbie</option>
										<option value="milkcow">milk cow</option>
<?php
		}
		else if($stdcolor == 'barbie')
		{
?>
										<option value="iris">iris</option>
										<option value="prairie">prairie</option>
										<option value="barbie" selected>barbie</option>
										<option value="milkcow">milk cow</option>
<?php
		}
		else if($stdcolor == 'milkcow')
		{
?>
										<option value="iris">iris</option>
										<option value="prairie">prairie</option>
										<option value="barbie">barbie</option>
										<option value="milkcow" selected>milk cow</option>
<?php
		}
?>
									</select>
									<input type="button" class="tga-button" value="Preview" id="tagally_tbnumbtn1" onclick="tga_selstac(document.getElementById('tagally_std_color').options.selectedIndex)" />
								</p>
							</div>
						</td>
					</tr>
					<tr>
						<td  width="710"  >
							<p>
								<label class="tga-hlab">TagCloud sample background color: </label>&nbsp;&nbsp;&nbsp;&nbsp;#<input id="tga_exbg" name="prebgcolor" class="tga-input" type="text" size="6" maxlength="6" onblur="iupcolor(this , 'tga_exbgbtn')" onkeyup="value=value.replace(/[^0-9A-Fa-f]/, '')" value="<?php echo $prebgcolor;?>" /><input id='tga_exbgbtn' type='button' class='colourPickerButton'  value='' style='background-color:#<?php echo $prebgcolor;?>' />
								<input type="button" class="tga-button" id="tagally_exbg_btn" value="Preview" onclick="tgaexmbg();" />
								<p class='tga-wpc'><strong>Attention : </strong>This background color will not affect real tagcloud displayed in your blog sidebar, just for sample preview.</p>
							</p>
						</td>
					</tr>
					<tr>
						<th valign="top" align="center" scope="row" colspan="2">
							<p class="tga-updt" style="text-align:center"><input type="submit" name="Submit" value="Update Options " /></p>
							<input type="hidden" name="action" value="updated" />
						</th>
					</tr>
				</table>
			</form>
		</div>
<?php
	}

	function the_content_tags($thecontent='')
	{
		global $post;

		$tags = $this->GetTagsForPost($post->ID,10);
		$tagsHTML = "";
		if ($tags)
		{
			$tagsHTML .= "tags:";
	
			foreach ($tags as $tag)
			{
				$para = urlencode($tag->slug);
				$tagsHTML .= " <a href=\"$this->siteurl/index.php?"."tag=$para\">".$tag->tag."</a>";
				$tagsHTML .= "&nbsp;&nbsp;";
			}
		}
		else
		{
			$tagsHTML = "No Tag";
		}
		$thecontent = $thecontent.$tagsHTML;
		return $thecontent;
	}
	
	function edit_post_tags()
	{
		global $post;
		$taglist = "";
		
		if(is_object($post) && $post->ID)
		{
			$tags = $this->GetTagsForPost($post->ID,10);

			if($tags)
			{
			  foreach($tags as $tag)
			  {
				  $taglist .= $tag->tag . ",";
		    }
		  	$taglist = substr($taglist, 0, -1);
		  	$taglist = preg_replace("/\"/","&quot;",$taglist);
	    }
	  }

?>
		<h3 class="dbx-handle">Tags (comma separated list)</h3>
		<div class="dbx-content">
		  <input id="tlmiftaglist" name="taglist" value="<?php echo $taglist; ?>" style="width:98%" onfocus=" if(document.getElementById('hiddenTagList')){ document.getElementById('hiddenTagList').style.display='block'}" /><br />
		</div>
<?php

		$poptags = $this->GetPopularTags(50);
		if($poptags)
		{
			usort($poptags,array("TagAllyForWPFunction","SortByAlphabetical"));
			$taghtml = "<div style=\"margin:5px;padding:5px;border:solid 1px #B3B6B0;display:none;\" id=\"hiddenTagList\">";
			$taghtml .= "<b>existing tags:</b> ";
			
			$maxc = $poptags[0]->c;
			$minc = $maxc;
			foreach($poptags as $poptag)
			{
				if($maxc < $poptag->c)  $maxc = $poptag->c;
				if($minc > $poptag->c)  $minc = $poptag->c;
			}
			$colortype = get_option("TAFWPColorType");
			if($colortype == "2")
			{
					$steparr = $poptags;
					usort($steparr,array("TagAllyForWPFunction","SortByPopularity"));
			}
			$i = 0;
			foreach($poptags as $poptag)
			{
				$fontsize = $this->GetTagFontSize($maxc,$minc,$poptag->c);
				if($colortype == "2")
				{
					$curkey = array_keys($steparr,$poptag);
					$color = $this->GetTagColor2(count($steparr),$curkey[0]+1);
				}
				else
				{
					$color = $this->GetTagColor1($i);
				}
				$slashtag = preg_replace("/\"/","&quot;",$poptag->tag);
				$slashtag = preg_replace("/\\\/","&#92;&#92;",$slashtag);
				$slashtag = preg_replace("/\'/","\'",$slashtag);
				
				$taghtml .= "<a href=\"javascript:insertTag('".$slashtag."')\" style=\"font-size:$fontsize; color:$color\">".$poptag->tag."</a> ";
				$i++;
			}
			$taghtml .= "</div>";
			echo $taghtml;
		}
	}
	
	function save_post_tags($postid)
	{
		global $wpdb;
		if ($this->tagsys)
		{
			$tags = $_POST['tags_input'];
		}
		else
		{
			$tags = $_POST['taglist'];
		}
		$tagarray = explode(',',$tags);
		$taglist = "";
		
		if (!$this->tagsys)
		{
			foreach($tagarray as $tag)
			{
				$tag = trim($tag);
				if($tag == "")
					continue;
				$sql = "select tag_ID from $this->tbtags where tag = '$tag'";
				$tagid = $wpdb->get_var($sql);
				if(is_null($tagid))
				{
					$slug = sanitize_title($tag);
					$slug_check = $wpdb->get_var("SELECT tag_ID FROM $this->tbtags WHERE slug = '$slug' LIMIT 1");
					if (!is_null($slug_check))
					{
						$alt_slug="";
						$suffix = 2;
						do
						{
							$alt_slug = $slug . "-$suffix";
							$slug_check = $wpdb->get_var("SELECT tag_ID FROM $this->tbtags WHERE slug = '$alt_slug' LIMIT 1");
							$suffix++;
						} while (!is_null($slug_check));
						$slug = $alt_slug;
					}
					$sql = "insert into $this->tbtags(tag,slug) values('$tag','$slug')";
					$wpdb->query($sql);
					$tagid = $wpdb->insert_id;
				}
				$sql = "select rel_id from $this->tbpost2tag where tag_id = $tagid and post_id = $postid";
				if(is_null($wpdb->get_var($sql)))
				{
					$sql = "insert into $this->tbpost2tag(post_id,tag_id) values('$postid','$tagid')";
					$wpdb->query($sql);
				}
				$taglist .= $tagid.",";
			}
			if($taglist == "")
			{
				$sql = "delete from $this->tbpost2tag where post_id = $postid";
			}
			else
			{
				$taglist = substr($taglist, 0 ,-1);
				$sql = "delete from $this->tbpost2tag where post_id = $postid and tag_id not in ($taglist)";
			}
			$wpdb->query($sql);
		}
		
		$this->ExportOne($postid,$tags);
	}
	
	function ExportOne($pid,$tags='')
	{
		global $wpdb;

		if($tags=='')
		{
			if ($this->tagsys)
			{
				$sql = <<<SQL
				select t.name as tag from $wpdb->terms t
				inner join $wpdb->term_relationships tr on tr.object_id = $pid
				inner join $wpdb->term_taxonomy tt on tt.term_taxonomy_id = tr.term_taxonomy_id and tt.taxonomy = 'post_tag' and t.term_id = tt.term_id
SQL;
			}
			else
			{
				$sql = "select t.tag as tag from $this->tbtags t inner join $this->tbpost2tag pt on pt.post_id = $pid and t.tag_id = pt.tag_id";
			}
			$ts = $wpdb->get_results($sql);
			foreach($ts as $tag)
			{
				if(strlen($tags))
				{
					$tags .= ','.$tag->tag;
				}
				else
				{
					$tags = $tag->tag;
				}
			}
		}

		$sql = "select * from $wpdb->posts p where p.id = $pid and p.post_status = \"publish\" and p.post_type = \"post\"";
		$posts = $wpdb->get_results($sql);
		if($posts)
		{
			$p = $posts[0];
			$time = date("Y-m-d H:i:s",strtotime($p->post_date));
			$excerpt = $this->cut_str(strip_tags($p->post_content),500);
			$from = get_option('blogname');

			$fp = fsockopen("www.tagally.com", 80, $errno, $errstr, 10);
			if ($fp)
			{
				$post_data = "v=".rawurlencode($this->version);
				$guid = strip_tags($p->guid);
				$post_data .= '&'."guid=".rawurlencode($guid);
				$id = strip_tags($p->ID);
				$post_data .= '&'."realid=".rawurlencode($id);
				$link = strip_tags(get_permalink($p->ID));
				$post_data .= '&'."reallink=".rawurlencode($link);
				$from = strip_tags($from);
				$post_data .= '&'."from=".rawurlencode($from);
				$title = strip_tags($p->post_title);
				$post_data .= '&'."posttitle=".rawurlencode($title);
				$excerpt = strip_tags($excerpt);
				$post_data .= '&'."excerpt=".rawurlencode($excerpt);
				$time = strip_tags($time);
				$post_data .= '&'."posttime=".rawurlencode($time);
				$tags = strip_tags(stripslashes($tags));
				$post_data .= '&'."tags=".rawurlencode($tags);
				$post_length = strlen($post_data);
				$out = "POST http://www.tagally.com/main/import HTTP/1.1\r\n";
				$out .= "Host: www.tagally.com\r\n";
				$out .= "Content-Length:$post_length\r\n";
				$out .= "Content-Type:application/x-www-form-urlencoded; charset=utf-8\r\n";
				$out .= "Connection: Close\r\n\r\n";
				$out .= $post_data;

				$len = fwrite($fp, $out);
				while (!feof($fp))
				{
					fgets($fp, 128);
				}
				return true;
			}
			else
			{
//				trigger_error("connect failed");
				return false;
			}
		}
	}

	function delete_post_tags($postid)
	{
		global $wpdb;
		$sql = "select guid from $wpdb->posts where ID = $postid";
		$guid = $wpdb->get_var($sql);
		if(!is_null($guid))
		{
			$this->UnimportOne($guid);
		}
	}

	function UnimportOne($guid)
	{
		$fp = fsockopen("www.tagally.com", 80, $errno, $errstr, 30);
		if($fp)
		{
			$post_data = "v=".rawurlencode($this->version);
			$post_data .= "&guid=".rawurlencode($guid);
			$post_length = strlen($post_data);
			$out = "POST http://www.tagally.com/main/unimport HTTP/1.1\r\n";
			$out .= "Host: www.tagally.com\r\n";
			$out .= "Content-Length:$post_length\r\n";
			$out .= "Content-Type:application/x-www-form-urlencoded; charset=utf-8\r\n";
			$out .= "Connection: Close\r\n\r\n";
			$out .= $post_data;
			
			$len = fwrite($fp, $out);
			while (!feof($fp))
			{
				fgets($fp, 128);
			}
			return true;
		}
		else
		{
			return false;
		}
	}

	function admin_head_add_javascript()
	{
		if(!$this->tagsys)
		{
			$addtagjs = "$this->siteurl/wp-content/plugins$this->install_directory/js/inserttag.js";
			echo "<script src=\"$addtagjs\" type=\"text/javascript\"></script>";
		}
		if($_GET['page'] == 'tagally-for-wp-function')
		{
			if($_GET['subpage'] == 'manage')
			{
				$tgaajaxjs = "$this->siteurl/wp-content/plugins$this->install_directory/js/ajt.js";
				$sackjs = "$this->siteurl/wp-includes/js/tw-sack.js";
				
				echo "<script type=\"text/javascript\" src=\"$sackjs\"></script>";
				echo "<script type=\"text/javascript\" src=\"$tgaajaxjs\"></script>";
			}
			else
			{
				$cpjs = "$this->siteurl/wp-content/plugins$this->install_directory/js/colorpicker.js";
				$cpcs = "$this->siteurl/wp-content/plugins$this->install_directory/css/colorpicker.css";
				
				echo "<link href=\"$cpcs\" rel=\"stylesheet\" type=\"text/css\" />";
				echo "<script src=\"$cpjs\" type=\"text/javascript\"></script>";
			}
			
			$tgmcs = "$this->siteurl/wp-content/plugins$this->install_directory/css/tgamg.css";
			echo "<link href=\"$tgmcs\" rel=\"stylesheet\" type=\"text/css\" />";
		}
	}
	
	function wp_head_add_javascript()
	{
		$shinejs = "$this->siteurl/wp-content/plugins$this->install_directory/js/uletsshine.js";
		$flcs = "$this->siteurl/wp-content/plugins$this->install_directory/css/fl.css";
		
		echo "<link href=\"$flcs\" rel=\"stylesheet\" type=\"text/css\" />";
		echo "<script src=\"$shinejs\" type=\"text/javascript\"></script>";
	}
	
	function wp_footer_add_dropmenu()
	{
?>
		<div style="clear:both;"></div>
		<div id="tga_dropmenu" class="dpmAp" onmouseover="clearhidemenu()" onmouseout="delayhidemenu()" style="left: 0px; top: 0px; visibility: hidden;">
			<div id="tga_dpmtap" style="background:transparent url(<?php echo "$this->siteurl/wp-content/plugins$this->install_directory/img/ftap.gif"; ?>) no-repeat scroll 0px 0px;width:262px;height:16px"></div>
			<div class="dpms0" id="tga_dpms_0" style="background-color:#FFF;border-bottom:#898C95 1px solid;border-left:#898C95 1px solid;border-right:#898C95 1px solid;font-size:12px;margin:0px;width:260px;z-index:9999">	
  			<form onsubmit="" action="" method="post" target="_blank">
          <div class="dpms1">
          	<span style="border-bottom:#020509 1px solid;color:#000040;font-size:10px;font-weight:bold;text-align:left">Tag&nbsp;:&nbsp;</span>
          	<a id="tga_tNY6IO" target='_blank' href="#" style="color:#E10000;font-weight:700">tag</a><br/>
          	<a id="tga_tNYIMU" href="http://www.tagally.com/main/addfavorite?tags="><img id="addlist" width="124" height="17"  src="<?php echo "$this->siteurl/wp-content/plugins$this->install_directory/img/addto.gif"; ?>" style="padding:2px 0px 0px 0px;margin-top:2px;background:none;border-color:#FFF;border-style:none;border-width:0px;" alt="add this tag to your favorites in TagALLY.com"/></a>
          </div>
          <div class="dpms3">
      	    <div class="dphd">
			    		<ul class="dphd_title" id="tga_dphdtitle">
				        <li><a id="tga_tgda1" class="tgdac_2" href="javascript:void(0)" onmouseover="delaydphdq(0)" onmouseout="cleardelaydphdq();">all blogs</a></li>
				        <li><a id="tga_tgda2" class="tgdac_1" href="javascript:void(0)" onmouseover="delaydphdq(1)" onmouseout="cleardelaydphdq();">this blog</a></li>
					    </ul>
					    <div style='height:2px;width:250px;background-color:#9cf;margin:0px;overflow:hidden'></div>
					    <div>
	            	<span style="border-bottom:#020509 1px solid;color:#000040;font-size:10px;font-weight:bold;text-align:left">Latest&nbsp;:&nbsp;</span>
	            	<span id="tga_lart1" style="display:none;padding-left:3px"><a style="font-size:11px;color:#0033CC;" href="" target="_blank"></a></span>
	              <span id="tga_lart2" style="display:none;padding-left:3px"><a style="font-size:11px;color:#0033CC;" href="" target="_blank"></a></span>
	            </div>
	            <span style="border-bottom:#020509 1px solid;color:#000040;font-size:10px;font-weight:bold;text-align:left">Top Hot&nbsp;:&nbsp;</span>
					    <div class="dphd_con" id="tga_tdpcon1" style="display:none;"></div>
					    <div class="dphd_con" id="tga_tdpcon2" style="display:none;"></div>
			      </div>
          </div>
          <div id="tga_liDv" style="border:0px;margin:3px 115px 10px;"><img style="max-width:none;background:none;border:0px none #FFFFFF;padding:0px;margin:0px" width="33" height="33" src="<?php echo "$this->siteurl/wp-content/plugins$this->install_directory/img/31.gif"; ?>"/></div>
          <div class="dpmsfoot" style="width:248px;background-color:#9cf;color:#333;font-size:9px;margin:10px 1px 1px;padding:5px;text-align:right">
          	<a style="color:#000040;background-color:#9cf" target='_blank' href="http://www.tagally.com">Powered by TagALLY.com</a>
          </div>
        </form>
      </div>
    </div>
<?php
	}
	
	function wp_ajax_export()
	{
		global $wpdb;
		
		$from = get_option('blogname');
		$ex_id = intval(get_option('TAFWPExportId'))+1;
		$ex_pro_old = get_option('TAFWPExportprogress');
		
		if ($this->tagsys)
		{
			$sql = <<<SQL
			select p.id as id,p.post_title as post_title,p.post_content as post_content,p.guid as guid,p.post_date as post_date,t.name as tag
			from $wpdb->posts p
			inner join $wpdb->term_relationships tr on p.id >= $ex_id and p.id = tr.object_id and p.post_status = 'publish' and p.post_type = 'post'
			inner join $wpdb->term_taxonomy tt on tt.term_taxonomy_id = tr.term_taxonomy_id and tt.taxonomy = 'post_tag'
			inner join $wpdb->terms t on t.term_id = tt.term_id
			order by p.id
SQL;
		}
		else
		{
			$sql = "select p.id as id,p.post_title as post_title,p.post_content as post_content,p.guid as guid,p.post_date as post_date,t.tag as tag from $wpdb->posts p inner join $this->tbpost2tag pt on p.id >= $ex_id and pt.post_id = p.id and p.post_status = 'publish' and p.post_type = 'post' inner join $this->tbtags t on t.tag_id = pt.tag_id order by p.id";
		}
	  $posts = $wpdb->get_results($sql);
	  if($posts)
		{
			$postarr = array();
			$tagarr = array();
			foreach($posts as $p)
			{
				$time = date("Y-m-d H:i:s",strtotime($p->post_date));
				$excerpt = $this->cut_str(strip_tags($p->post_content),500);
				if(! isset($postarr["$p->id"]))
				{
					$guid = strip_tags($p->guid);
					$from = strip_tags($from);
					$id = strip_tags($p->id);
					$link = strip_tags(get_permalink($p->id));
					$title = strip_tags($p->post_title);
					$excerpt = strip_tags($excerpt);
					$time = strip_tags($time);
					$postarr["$p->id"] = array("guid"=>$guid,"from"=>$from,"id"=>$id,"link"=>$link,"posttitle"=>$title,"excerpt"=>$excerpt,"posttime"=>$time);
					
				}
				if(! isset($tagarr["$p->id"]))
				{
					$tagarr["$p->id"] = "$p->tag";
				}
				else
				{
					$tagarr["$p->id"] .= ",$p->tag";
				}
			}
			
			$pcount = count($postarr);
			if ($pcount > 20)
			{
				$perc = 20;
			}
			else
			{
				$perc = $pcount;
			}
			$data = "";
			$i = 0;
			$c = 0;
			
			$send_all = 0;
			foreach($postarr as $postid => $post)
			{
				if ($send_all != 0)
				{
					break;
				}
				$i += 1;
				$tags = strip_tags($tagarr[$postid]);
				if(strlen($data))
				{
						$data .= "&guid$i=".rawurlencode($post["guid"]);
						$data .= "&realid$i=".rawurlencode($post["id"]);
						$data .= "&reallink$i=".rawurlencode($post["link"]);
						$data .= "&from$i=".rawurlencode($post["from"]);
						$data .= "&posttitle$i=".rawurlencode($post["posttitle"]);
						$data .= "&excerpt$i=".rawurlencode($post["excerpt"]);
						$data .= "&posttime$i=".rawurlencode($post["posttime"]);
						$data .= "&tags$i=".rawurlencode($tags);
				}
				else
				{
						$data = "&guid$i=".rawurlencode($post["guid"]);
						$data .= "&realid$i=".rawurlencode($post["id"]);
						$data .= "&reallink$i=".rawurlencode($post["link"]);
						$data .= "&from$i=".rawurlencode($post["from"]);
						$data .= "&posttitle$i=".rawurlencode($post["posttitle"]);
						$data .= "&excerpt$i=".rawurlencode($post["excerpt"]);
						$data .= "&posttime$i=".rawurlencode($post["posttime"]);
						$data .= "&tags$i=".rawurlencode($tags);
				}

				if ($i >= $perc || $pcount-$c == $i)
				{
					$fp = fsockopen("www.tagally.com", 80, $errno, $errstr, 10);
					if ($fp)
					{
						$post_data = "v=".rawurlencode($this->version)."&postcount=".rawurlencode($i).$data;
						$post_length = strlen($post_data);
						$out = "POST http://www.tagally.com/main/importall HTTP/1.1\r\n";
						$out .= "Host: www.tagally.com\r\n";
						$out .= "Content-Length:$post_length\r\n";
						$out .= "Content-Type:application/x-www-form-urlencoded; charset=utf-8\r\n";
						$out .= "Connection: Close\r\n\r\n";
						$out .= $post_data;
						
						$len = fwrite($fp, $out);
						while (!feof($fp))
						{
							fgets($fp, 128);
						}
						
						$c += $i;
						$ex_pro = intval(($c*100/$pcount)*(100-$ex_pro_old)/100)+$ex_pro_old;
						update_option("TAFWPExportprogress","$ex_pro");
						update_option("TAFWPExportId","$postid");
						if ($c == $pcount)
						{
							$send_all = 1; //all posts have been sended
							break;
						}
					}
					else
					{
						$send_all = 2; // connect fail
						break;
					}
					$i = 0;
					$data = "";
					
					for($k=0;$k<10000;$k++)
					{
						;;
					}
				}
			}
			
			if ($send_all == 1)
			{
				echo("jQuery('div#mgdbgl').css('width','100%');jQuery('div#mgdtxt').text('100%');jQuery('input#mgdhdex').val('off');");
				echo("jQuery('span#tga_export').html('<div id=\"message\" class=\"updated fade\"><p><strong>Export posts and related tags completed.</strong></p></div>').fadeIn('fast');");
			}
			else if($send_all == 2)
			{
				if($c > 0)
				{
					echo("jQuery('input#mgdhdex').val('off');");
					echo("jQuery('span#tga_export').html('<div id=\"message\" class=\"updated fade\"><p><strong>Intermittent Connection Faults.</strong></p></div>').fadeIn('fast');");
				}
				else
				{
					echo("jQuery('input#mgdhdex').val('off');");
					echo("jQuery('span#tga_export').html('<div id=\"message\" class=\"updated fade\"><p><strong>Can\'t connect to www.tagally.com.</strong></p></div>').fadeIn('fast');");
				}
			}
		}
		else
		{
			echo("jQuery('input#mgdhdex').val('off');");
			echo("jQuery('span#tga_export').html('<div id=\"message\" class=\"updated fade\"><p><strong>No any posts exported.</strong></p></div>').fadeIn('fast');");
		}
	}
	
	function wp_ajax_export_status()
	{
		$ex_prog = get_option("TAFWPExportprogress");
		die("$ex_prog");
	}
	
	function tag_query_vars($vars)
	{
		$vars[] = 'tag';
		return $vars;
	}
		
	function template_redirect_tag()
	{
		if ($tags = get_query_var("tag"))
		{
			$this->GetPostsForTagTemplates($tags);
		}
	}
	
	function posts_join($joinclause)
	{
		if(get_query_var("tag"))
		{
			global $wpdb;
			$joinclause .= "inner join $this->tbpost2tag p2t on p2t.post_id = $wpdb->posts.ID inner join $this->tbtags t on t.tag_ID = p2t.tag_id";
		}
		return $joinclause;
	}
	
	function posts_where($whereclause)
	{
		if($tags = get_query_var("tag"))
		{
			$tags = str_replace(" ",",",$tags);
			$whereclause .= "and t.slug in ('$tags')";
		}
		return $whereclause;
	}
	
	function posts_orderby($orderbyclause)
	{
		if(get_query_var("tag"))
		{
			$orderbyclause = "post_modified desc";
		}
		return $orderbyclause;
	}
	
	function cut_str($msg,$cut_size,$charset="UTF-8",$suffix="...") 
	{
    if($cut_size<=0)
    {
    	return $msg;
    }
    
    $str = ''; 
	  $i = 0;
	  $n = 0;
	  $msg_len = strlen($msg);
	  
    while (($n < $cut_size) && ($i <= $msg_len))
    {
      $tmp_asc = ord(substr($msg,$i,1));
      if($tmp_asc >= 240) // one word is composed of four bytes
      {
        $str .= substr($msg,$i,4);
        $i += 4;
        $n += 1;
      }
      else if($tmp_asc >= 224) // one word is composed of three bytes
      {
      	$str .= substr($msg,$i,3);
        $i += 3;
        $n += 1;
      }
      else if($tmp_asc >= 192) // one word is composed of two bytes
      {
      	$str .= substr($msg,$i,2);
        $i += 2;
        $n += 1;
      }
			else if($tmp_asc>=65 && $tmp_asc <= 90) // one uppercase letter
      {
      	$str .= substr($msg,$i,1);
        $i += 1;
        $n += 1;
      }
      else // lowercase letter or half-angle punctuation
      {
      	$str .= substr($msg,$i,1);
        $i += 1;
        $n += 0.5;
      }
    }
    if( ($i <= $msg_len) && ($tmp_asc >= 97 && $tmp_asc <= 122 || $tmp_asc>=65 && $tmp_asc <= 90) )
    {
    	$j = $i;
    	while($j <= $msg_len)
    	{
    		$tmp_asc = ord(substr($msg,$j,1));
    		if($tmp_asc < 97 || $tmp_asc > 122) //when the first non uppercase letter is met, the english word ends
    		{
    			$str .= substr($msg,$i,$j-$i);
    			break;
    		}
    		else
    		{
    			$j += 1;
    		}
    	}
    }
    
    if ($msg_len>$cut_size)
    {
    	$str .= $suffix;
    }
    
    return $str;
	}
	
}

?>