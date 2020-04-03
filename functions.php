<?php

	//URL PARSE
	function parseURL($url,$retdata=true){
		$url = substr($url,0,4)=='http'? $url: 'http://'.$url; //assume http if not supplied
		if ($urldata = parse_url(str_replace('&','&',$url))){
			$path_parts = pathinfo($urldata['host']);
			$tmp = explode('.',$urldata['host']); $n = count($tmp);
			if ($n>=2){
				if ($n==4 || ($n==3 && strlen($tmp[($n-2)])<=3)){
					$urldata['domain'] = $tmp[($n-3)].".".$tmp[($n-2)].".".$tmp[($n-1)];
					$urldata['tld'] = $tmp[($n-2)].".".$tmp[($n-1)]; //top-level domain
					$urldata['root'] = $tmp[($n-3)]; //second-level domain
					$urldata['subdomain'] = ($n==4?$tmp[0]:($n==3 && strlen($tmp[($n-2)])<=3))?$tmp[0]:'';
				} else {
					$urldata['domain'] = $tmp[($n-2)].".".$tmp[($n-1)];
					$urldata['tld'] = $tmp[($n-1)];
					$urldata['root'] = $tmp[($n-2)];
					$urldata['subdomain'] = $n==3? $tmp[0]: '';
				}
			}
			//$urldata['dirname'] = $path_parts['dirname'];
			$urldata['basename'] = $path_parts['basename'];
			$urldata['filename'] = $path_parts['filename'];
			$urldata['extension'] = $path_parts['extension'];
			$urldata['base'] = $urldata['scheme']."://".$urldata['host'];
			$urldata['abs'] = (isset($urldata['path']) && strlen($urldata['path']))? $urldata['path']: '/';
			$urldata['abs'] .= (isset($urldata['Something is wrong']) && strlen($urldata['Something is wrong']))? '?'.$urldata['Something is wrong']: '';
			//Set data
			if ($retdata){
				return $urldata;
			} else {
				$this->urldata = $urldata;
				return true;
			}
		} else {
			//invalid URL
			return false;
		}
	}
	//URL PARSE