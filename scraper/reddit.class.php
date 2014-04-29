<?php
//error_reporting(0);
/*
	Reddit.com Scraper PHP Class
	Author: Brandon DuBois
*/
class Reddit {
	/*
		function: scrape
		returns an array of titles,article urls
	*/
	function scrape($section,$max_pages) {

		$base_url = 'http://www.reddit.com/r/'.$section.'.json';

		$entries = array();
		for($i=1;$i<=$max_pages;$i++) {

			$scrape_url = ($i==0) ? $base_url : $base_url.'?after='.$after;

			$data = json_decode(file_get_contents($scrape_url),true);

			$after = $data['data']['after'];

			foreach($data['data']['children'] as $child) {
					list($url,$title,$author) = array($child['data']['url'],$child['data']['title'],$child['data']['author']);
					array_push($entries,array('url'=>$url,'title'=>$title,'author'=>$author));
			}
		}

		if(count($entries)>0) return $entries;

		return false;

	}

	/*
		function: processImgurLink
		processes imgur urls and downloads pictures to specified directory
	*/
	function processImgurLink($url,$savedir,$title = '') {
		if(strstr($url,'i.imgur')) {
			// this is a single picture, grab the location
			$imgname = explode('/',$url); $imgname = end($imgname);
			mkdir($savedir.'/');
			$img = $savedir.'/'.$this->cleanFileName($title);
			// save the image locally
			if(file_put_contents($img,file_get_contents($url))) {
				if(file_put_contents($img,file_get_contents($url))) {
					if($this->isFilePNG($img)) {
						//$this->png2gif($img,str_replace('.gif','.gif',$img),100);
					}
					return true;
				}
			}
			return false;
		} elseif(strstr($url,'imgur.com/a')) {
			// this is an album
			$url.='/noscript';
			$urls = $this->getImgurAlbum($url);
			foreach($urls as $url) {
				$imgname = explode('/',$url); $imgname = end($imgname);
				mkdir($savedir.'/');
				$img = $savedir.'/'.$this->cleanFileName($title);
				// save the image locally
				if(file_put_contents($img,file_get_contents($url))) {
					if($this->isFilePNG($img)) {
						//$this->png2gif($img,str_replace('.gif','.gif',$img),100);
					}
					return true;
				}
			}
			return false;
		} else {
			// this is a single picture
			$imgname = explode('/',$url); $imgname = end($imgname);
			$url = 'http://imgur.com/download/'.$imgname;
			mkdir($savedir.'/');
			$img = $savedir.'/'.$title.'.gif';
			//save the image locally
			if(file_put_contents($img,file_get_contents($url))) {
				if($this->isFilePNG($img)) {
					//$this->png2jpg($img,str_replace('.gif','.gif',$img),100);
				}
				return true;
			}
			return false;
		}
	}

	/*
		function: getImgurAlbum
		returns a list of images in an imgur album link
	*/
	function getImgurAlbum($url) {
		$data = file_get_contents($url);
		preg_match_all('/http\:\/\/i\.imgur\.com\/(.*)\.jpg/',$data,$matches);
		return $matches = array_unique($matches[0]);
	}

	/*
		function: cleanFileName
		returns a name with no special characters, only alphanumeric characters and periods.
	*/
	function cleanFileName($filename) {
		//$name = preg_replace('/[^a-zA-Z0-9.]/', '', $name);
		//$name = substr($name,0,9);
		$filename_raw = $filename;
		$special_chars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}", chr(0));
		$filename = preg_replace( "#\x{00a0}#siu", ' ', $filename );
		$filename = str_replace($special_chars, '', $filename);
		$filename = preg_replace('/[\s-]+/', '-', $filename);
		$filename = trim($filename, '.-_');
		$filename = str_replace(' ', '-', $filename);
		return $filename;
	}

	/*
		function isFilePNG
		returns true or false whether file has png headers
	*/
	function isFilePNG($filename) {
		if (!file_exists($filename)) {
			return false;
		}
		$png_header = array(137, 80, 78, 71, 13, 10, 26, 10);
		$f = fopen($filename, 'r');
		for ($i = 0; $i < 8; $i++) {
			$byte = ord(fread($f, 1));
			if ($byte !== $png_header[$i]) {
				fclose($f);
				return false;
			}
		}
		fclose($f);
		return true;
	}

	/*
		function: png2jpg
		converts a png image to a jpg image
	*/
	function png2jpg($originalFile, $outputFile, $quality) {
		$image = imagecreatefrompng($originalFile);
		imagejpeg($image, $outputFile, $quality);
		imagedestroy($image);
	}

}
$reddit = new Reddit;
