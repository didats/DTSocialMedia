<?php
	/*
	
	DTSocialMedia v.1.0
	The requirements are to get the items from Various Social Media such as 
	Twitter, Facebook, Flickr, Instagram, Youtube, Pinterest, and RSS Feed
	
	Created by Didats Triadi on March 25, 2013
	URL: http://didats.net
	Twitter: @didats
	Linkedin: http://kw.linkedin.com/in/didats/
	
	*/
	
	class DTSocialMedia {
		
		private $num;
		private $type, $user, $url_to_grab;
		
		// let the user cache the content by giving them
		public $content_data;
		
		// create url for grabbing the data
		private $grabber = array(
			'feed' => "http://ajax.googleapis.com/ajax/services/feed/load?v=1.0&num=|num|&q=|user|",
			'facebook' => "https://www.facebook.com/feeds/page.php?id=|user|&format=rss20",
			'youtube' => "https://gdata.youtube.com/feeds/api/users/|user|/uploads?alt=json&max-results=|num|",
			'twitter' => "https://api.twitter.com/1/statuses/user_timeline.json?include_entities=true&include_rts=true&screen_name=|user|&count=|num|",
			'flickr' => "http://api.flickr.com/services/feeds/photos_public.gne?id=|user|",
			'pinterest' => "http://ajax.googleapis.com/ajax/services/feed/load?v=1.0&num=|num|&q=http://pinterest.com/|user|/feed.rss",
			'instagram' => "https://api.instagram.com/v1/users/|user|/media/recent?access_token=15969682.ea0ab38.440546b46f334ee58deafa52dec661d5&client_id=ea0ab38daabd4d9594b8a4219e80b41a"
			
		);
		
		// constructor
		function DTSocialMedia() {
			// nothing to do here
		}
		
		private function get_content($postdata = "") {
			$curl = curl_init();
			
			$arr_curl = array(
									CURLOPT_RETURNTRANSFER => 1,
									CURLOPT_URL => $this->url,
									CURLOPT_SSL_VERIFYPEER => 0,
									CURLOPT_USERAGENT => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:19.0) Gecko/20100101 Firefox/19.0"
									
								);
			
			if(strlen($postdata) > 0) {
				array_push($arr_curl, array(CURLOPT_POST => 1, CURLOPT_POSTFIELDS => $postdata));
			}
			
			curl_setopt_array($curl, $arr_curl);
			$content = curl_exec($curl);
			curl_close($curl);
			
			return $content;
		}
		
		private function xml2array(&$string) {
		    $parser = xml_parser_create();
		    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		    xml_parse_into_struct($parser, $string, $vals, $index);
		    xml_parser_free($parser);
		
		    $mnary=array();
		    $ary=&$mnary;
		    foreach ($vals as $r) {
		        $t=$r['tag'];
		        if ($r['type']=='open') {
		            if (isset($ary[$t])) {
		                if (isset($ary[$t][0])) $ary[$t][]=array(); else $ary[$t]=array($ary[$t], array());
		                $cv=&$ary[$t][count($ary[$t])-1];
		            } else $cv=&$ary[$t];
		            if (isset($r['attributes'])) {foreach ($r['attributes'] as $k=>$v) $cv['_a'][$k]=$v;}
		            $cv['_c']=array();
		            $cv['_c']['_p']=&$ary;
		            $ary=&$cv['_c'];
		
		        } elseif ($r['type']=='complete') {
		            if (isset($ary[$t])) { // same as open
		                if (isset($ary[$t][0])) $ary[$t][]=array(); else $ary[$t]=array($ary[$t], array());
		                $cv=&$ary[$t][count($ary[$t])-1];
		            } else $cv=&$ary[$t];
		            if (isset($r['attributes'])) {foreach ($r['attributes'] as $k=>$v) $cv['_a'][$k]=$v;}
		            $cv['_v']=(isset($r['value']) ? $r['value'] : '');
		
		        } elseif ($r['type']=='close') {
		            $ary=&$ary['_p'];
		        }
		    }    
		    
		    $this->_del_p($mnary);
		    return $mnary;
		}
		
		// _Internal: Remove recursion in result array
		private function _del_p(&$ary) {
		    foreach ($ary as $k=>$v) {
		        if ($k==='_p') unset($ary[$k]);
		        elseif (is_array($ary[$k])) $this->_del_p($ary[$k]);
		    }
		}
		
		// Array to XML
		private function ary2xml($cary, $d=0, $forcetag='') {
		    $res=array();
		    foreach ($cary as $tag=>$r) {
		        if (isset($r[0])) {
		            $res[]=$this->ary2xml($r, $d, $tag);
		        } else {
		            if ($forcetag) $tag=$forcetag;
		            $sp=str_repeat("\t", $d);
		            $res[]="$sp<$tag";
		            if (isset($r['_a'])) {foreach ($r['_a'] as $at=>$av) $res[]=" $at=\"$av\"";}
		            $res[]=">".((isset($r['_c'])) ? "\n" : '');
		            if (isset($r['_c'])) $res[]=$this->ary2xml($r['_c'], $d+1);
		            elseif (isset($r['_v'])) $res[]=$r['_v'];
		            $res[]=(isset($r['_c']) ? $sp : '')."</$tag>\n";
		        }
		        
		    }
		    return implode('', $res);
		}
		
		// Insert element into array
		private function ins2ary(&$ary, $element, $pos) {
		    $ar1=array_slice($ary, 0, $pos); $ar1[]=$element;
		    $ary=array_merge($ar1, array_slice($ary, $pos));
		}
		
		public function load_data() {
			// replace the |user| and |num| with the variable that user sent.
			$real_url = preg_replace(array('/\|num\|/', '/\|user\|/'),	array($this->num, $this->user), $this->url);
			$this->url = $real_url;
			
			// create an array to fetch all the data
			$arr_result = array();
			
			// feed, and pinterest are using RSS feed.
			// so we can use google apis to make the return data exactly the same
			if($this->type == "feed" || $this->type == "pinterest") {
				$content = json_decode($this->get_content());
				
				$entries = $content->responseData->feed->entries;
				for($i=0;$i<count($entries);$i++) {
					
					$arr_new = array('title' => $entries[$i]->title, 'date' => strtotime($entries[$i]->publishedDate), 'link' => $entries[$i]->link);
					
					preg_match('/src="([^"]+)"/', $entries[$i]->content, $match);
					if(isset($match[1])) $arr_new['image'] = $match[1];
					
					array_push($arr_result, $arr_new);
				}
			}
			// facebook
			elseif($this->type == "facebook") {
				$content = $this->get_content();
				$arr_data = $this->xml2array($content);
				
				$entries = $arr_data['rss']['_c']['channel']['_c']['item'];
				for($i = 0; $i<count($entries); $i++) {
					$item = $entries[$i]['_c'];
					
					$title = $item['title']['_v'];
					$link = $item['link']['_v'];
					$date = strtotime($item['pubDate']['_v']);
					
					$desc = $item['description']['_v'];
					preg_match('/src="([^"]+)"/', $desc, $match);
					
					$arr_new = array('title' => $title, 'date' => $date, 'link' => $link);
					
					if(isset($match[1])) $arr_new['image'] = $match[1];
					//$image = $item['link'][2]['_a']['href'];
					
					array_push($arr_result, $arr_new);
				}
			}
			// youtube 
			elseif($this->type == "youtube") {
				$content = json_decode($this->get_content());
				$items = $content->feed->entry;
				
				for($i=0;$i<count($items);$i++) {
					
					$url_item = $items[$i]->link[0]->href;
					
					preg_match("/v=([^&]+)/", $url_item, $match);
					
					$url_id = $match[1];
					
					array_push($arr_result, array('title' => $items[$i]->title->{'$t'}, 'date' => strtotime($items[$i]->updated->{'$t'}), 'image' => 'http://img.youtube.com/vi/'.$url_id.'/0.jpg', 'link' => 'https://www.youtube.com/watch?v='.$url_id));
				}
			}
			// twitter
			elseif($this->type == "twitter") {
				$content = json_decode($this->get_content());
				
				
				for($i=0;$i<count($content);$i++) {
					array_push($arr_result, array('title' => $content[$i]->text, 'date' => strtotime($content[$i]->created_at), 'link' => "https://twitter.com/".$this->user."/status/".$content[$i]->id));
				}
			}
			// flickr
			elseif($this->type == "flickr") {
				$content = $this->get_content();
				$arr_data = $this->xml2array($content);
				
				$entries = $arr_data['feed']['_c']['entry'];
				for($i=0;$i<count($entries);$i++) {
					$item = $entries[$i]['_c'];
					$title = $item['title']['_v'];
					$link = $item['link'][0]['_a']['href'];
					$date = $item['published']['_v'];
					$image = $item['link'][1]['_a']['href'];
					
					array_push($arr_result, array('title' => $title, 'date' => strtotime($date), 'link' => $link, 'image' => $image));
				}
			}
			// instagram
			elseif($this->type == "instagram") {
				$content = json_decode($this->get_content());
				
				for($i=0;$i<count($content->data);$i++) {
					if(!empty($content->data[$i]->caption)) {
						$image = $content->data[$i]->images->standard_resolution->url;
						$title = $content->data[$i]->caption->text;
						$date = $content->data[$i]->created_time;
						$link = $content->data[$i]->link;
						
						array_push($arr_result, array('title' => $title, 'date' => strtotime($date), 'link' => $link, 'image' => $image));
					}
				}
			}
			
			$this->content_data = json_encode($arr_result);
			
			return $arr_result;
		}
		
		public function get_data($type, $user, $num = 10) {
			$this->url = $this->grabber[$type];
			$this->type = $type;
			$this->user = $user;
			$this->num = $num;
			
/* 			echo $this->url." / ".$this->type." / ".$this->user; */
			
			return $this->load_data();
		}
	}
	