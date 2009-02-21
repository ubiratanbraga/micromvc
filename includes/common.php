<?php
/**
 * Common Functions
 *
 * This file contains a growing list of common functions for use in throughout
 * the MicroMVC system.
 *
 * @package		MicroMVC
 * @author		David Pennington
 * @copyright	Copyright (c) 2009 MicroMVC
 * @license		http://www.gnu.org/licenses/gpl-3.0.html
 * @link		http://micromvc.com
 * @version		1.0.0 <2/20/2009>
 ********************************** 80 Columns *********************************
 */


/**
 * Add <pre> tags around objects you want to dump.
 *
 * @param mixed $text
 */
function print_pre($object=null) {
	print '<pre>';
	print_r($object);
	print '</pre>';
}

/**
 * Wrap an error message with the global error class tag
 */
function wrap_error($text=null) {
	return '<div class="error">'. $text. '</div>';
}


/**
 * Cleans text of all bad characters
 * @param string	$text	text to clean
 * @param boolean	$level	Set to TRUE to only enable file safe chars
 * @return void
 */
function sanitize_text(&$text, $level=0){
	if(!$level) {
		//Delete anything that isn't a letter, number, or common symbol - then HTML encode the rest.
		$text = trim(htmlentities(preg_replace("/([^a-z0-9!@#$%^&*()_\-+\]\[{}\s\n<>:\\/\.,\?;'\"]+)/i", '', $text), ENT_QUOTES, 'UTF-8'));
	} else {
		//Make the text file/title/emailname safe
		$text = preg_replace("/([^a-z0-9_\-\.]+)/i", '_', trim($text));
	}
}


/**
 * comma_to_array
 *
 * Create an array containing elements from a comma-separated string
 *
 * @param	String  Text containing values
 * @return	Array
 */
function comma_to_array($tags) {
	/*
	 //Can handle even the most messed-up tag strings like below:
	 $tags = "\n\r". 'tag1, this is tag2, or tag3. but we can\'t tag4, tag5, other '. "\n".
	 'tag6, "plus tag7", #tag8,'. "\n\n". ',,,, ,,,,, ,,,'. "\n". ',, '.
	 "\n\n\n". '< this is another, tag9.,, , ';
	 */

	//Replace anything that isn't a letter, comma, space, or number!
	$tags = preg_replace("/[^a-z0-9, ]/", '', trim(strtolower($tags)));

	//Remove empty "," so that we don't make empty elements
	$tags = preg_replace("/,[^a-z0-9]*,/", ',', $tags);

	//If there is an ending comma.... kill it!
	$tags = rtrim($tags, ',');

	//Turn the string into an array of tags
	$tags = explode(',', $tags);

	//Remove extra spaces from front and back of each element
	foreach($tags as $key => $tag) {
		$tags[$key] = trim($tag);
	}

	return $tags;
}



/**
 * split_text
 *
 * Split text into chunks ($inside contains all text inside
 * $start and $end, and $outside contains all text outside)
 *
 * @param	String  Text to split
 * @param	String  Start break item
 * @param	String  End break item
 * @return	Array
 */
function split_text($text='', $start='<code>', $end='</code>') {
	$tokens = explode($start, $text);
	$outside[] = $tokens[0];

	$num_tokens = count($tokens);
	for ($i = 1; $i < $num_tokens; ++$i) {
		$temp = explode($end, $tokens[$i]);
		$inside[] = $temp[0];
		$outside[] = $temp[1];
	}

	return array($inside, $outside);

}



/**
 * Random Charaters
 *
 * Pass this function the number of chars you want
 * and it will randomly make a string with that
 * many chars. (I removed chars that look alike.)
 *
 * @param	Int		Length of character string
 * @param	Int		Charater set to use
 * @return	Array
 */
function random_charaters($number, $type=0) {
	$ascii[0] = 'ACEFGHJKLMNPRSTUVWXY345679';
	$ascii[1] = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJK.MNOPQRSTUVWXYZ'
	. '!"#$%&\'()*+`-.\\/0123456789:;<=>?@{|}~';
	$chars = null;
	for($i=0; $i<$number; $i++) {
		$chars .= $ascii[$type]{rand(0,strlen($ascii[$type])-1)};
	}
	return $chars;
}


/**
 * timestamp_to_array
 *
 * Create an array containing the pieces of a timestamp.
 * The currnent time is used if $time is empty.
 *
 * @param	Int		10 Digit Hex Timestamp
 * @return	Array
 */
function timestamp_to_array($time=null) {
	$time = $time ? $time : time();
	$output = array();
	$output['year'] = date("Y", $time);
	$output['month'] = date("F", $time);
	$output['day'] = date("j", $time);
	$output['hour'] = date("g", $time);
	$output['minute'] = date("i", $time);
	$output['second'] = date("s", $time);
	$output['ampm'] = date("A", $time);
	$output['gmt'] = date("O", $time);
	return $output;
}



/**
 * array_to_timestamp
 *
 * Turn Date array back into a UNIX timestamp (10 digit number)
 *
 * @param	Array   Array of time elements
 * @return	String
 */

function array_to_timestamp($date=null) {

	//If they entered "pm" then we should add 12 hours to get the computer time.
	//i.e. if it is 1pm then that is 13:00 in computer langauge. (they don't use am/pm)
	if($date['ampm'] == 'pm') {
		$date['hour'] += 12;
	}

	// Feed it all to the mktime function and it will give us a 10 digit UNIX timestamp.
	return mktime($date['hour'], $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']);

}


/**
 * Show a human-readable time difference ("10 seconds")
 *
 * @param int		$from_time
 * @param int		$to_time
 * @return string
 */
function time_difference($from_time=0, $to_time=null) {

	//If not set - use current time
	if(!$to_time) { $to_time= time(); }

	//timestamp difference
	$difference = round(abs($to_time - $from_time));

	//Try seconds first
	if ($difference <= 60) {
		return $difference. ' seconds';
	}

	//Time Types (you can add to this)
	$times = array(
    	'minutes'	=> 60,
    	'hours'		=> 60,
    	'days'		=> 24,
    	'weeks'		=> 7,
    	'years'		=> 52
	);

	//Try each type of time
	foreach($times as $type => $value) {

		//Find number of minutes
		$difference = round($difference / $value);

		if ($difference <= $value) {
			return $difference. ' '. $type;
		}
	}
}



/**
 * Too Long
 *
 * Check to make sure a value is not a long XSS attack
 *
 * @param	string	value to check
 * @param	int		max length it can be
 * @return	boolean
 */
function too_long($value, $length) {

	//Ok, check to see if it is an array, integer, or string -
	//And then make sure it is not to long!
	if ( (is_string($value)) && (strlen($value) <= $length) ) {
		return TRUE;
	} elseif ( (is_numeric($value)) && ($value <= $length) ) {
		return TRUE;
	} elseif ( (is_array($value) && (count($value) <= $length)) ) {
		return TRUE;
	} else {
		/* If it is somthing else (object, Boolean, NULL, etc..), or
		 if it is too long, return FALSE (to warn the script before something bad happens!)
		 */
		return FALSE;
	}
}



/**
 * Valid Email
 * @param	string	email to check
 * @return	boolean
 */
function valid_email($text){
	return ( ! preg_match("/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i", $text)) ? FALSE : TRUE;
}



/**
 * unzip a file to a new location
 */
function unzip($file, $new_file) {

	if(file_exists($file)) {
		$zip = new ZipArchive;
		$zip->open($file);
		$zip->extractTo($new_file);
		$zip->close();
		return true;
	}

	return false;
}



/**
 * Upload Check Errors
 *
 * Checks the given tmpfile for any errors or problems with
 * the upload
 *
 * @access	public
 * @param	string	Name of the File
 * @return	boolean
 */
function upload_check_errors($file_name='') {

	$errors = array(
	UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
	UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
	UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
	UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
	UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
	UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
	UPLOAD_ERR_EXTENSION => 'File upload stopped by extension.',
	);

	//Get the error
	$error = $_FILES[$file_name]['error'];

	//IF the error is something OTHER than "OK"
	if($error !== UPLOAD_ERR_OK) {
		if(isset($errors[$error])) {
			trigger_error($errors[$error], E_USER_WARNING);
		} else {
			trigger_error('Unknown file upload error in file: <b>'
			. clean_value($_FILES[$file_name]['name']). '</b>',
			E_USER_WARNING);
		}
		return false;
	}

	//If the file never made it to the server
	if(!is_uploaded_file($_FILES[$file_name]['tmp_name'])) {
		trigger_error('Possible file upload attack in file: '
		. clean_value($_FILES[$file_name]['name']). '</b>',
		E_USER_WARNING);
		return false;
	}

	return true;

}



/**
 * Upload Files
 *
 * @access	public
 * @param	string	The directory to place the uploaded files
 * @return	boolean
 */
function upload_files($dir) {

	//If the upload directory is useable and there are files to upload
	if(directory_usable($dir) && isset($_FILES)) {

		//Foreach file that has been uploaded
		foreach($_FILES as $name => $file) {

			//If no errors with the file
			if(upload_check_errors($name)) {
				if(!move_uploaded_file($file['tmp_name'], $dir. $file['name'])) {
					trigger_error('Could not move file', E_USER_ERROR);
					return;
				}
			}

		}
		return true;
	}

}



///////////////////////////////////////////////////////////
// A function to list all files within the specified directory
// and it's sub-directories. This is a recursive function that
// has no limit on the number of levels down you can search.
///////////////////////////////////////////////////////////
// What info does this function need?
/////////////////
//
// $data['start_dir']   The directory to start searching from   (Required) ("./" = current dir, "../" = up one level)
// $data['good_ext']    The file extensions to allow.           (Required) (set to 'array('all') to include everything)
// $data['skip_files']  An array of files to skip.              (Required) (empty array if you don't want to skip anything)
// $data['limit']       The limit of dir to search              (Required)
// $data['type']        Return files or Directories?            (Optional) (defaults to BOTH types but can also set to 'dir' or 'file')
// $data['light']       Only return file name and path          (Optional) (defaults to false) (true or false)
//
/////////////////
// Example data
/////////////////
//
// $data['start_dir']      = "../../";
// $data['good_ext']       = array('php', 'html');
// $data['skip_files']     = array('..', '.', 'txt', '.htaccess');
// $data['limit']          = 5;
// $data['type']           = 'file';
// $data['light']          = false;
//
//////////////////////////////////////////////////
function directory($data, $level=1) {

	//If no type was specified - default to showing BOTH
	if(!isset($data['type']) || !$data['type']) { $data['type'] = false; }

	//If light was not specified - defualt to heavy version
	if(!isset($data['light']) || !$data['light']) { $data['light'] = false; }

	//If the directory given actually IS a directory
	if (is_dir($data['start_dir'])) {

		//Then open the directory
		$handle = opendir($data['start_dir']);

		//Initialize array
		$files = array();

		//while their are files in the directory...
		while (($file = readdir($handle)) !== false) {

			//If the file is NOT in the bad file list...
			if (!(array_search($file, $data['skip_files']) > -1)) {

				//Store the full file path in a var
				$path = $data['start_dir']. $file;

				//if it is a dir
				if (filetype($path) == "dir") {

					//add it to our list of dirs
					if(!$data['type'] || $data['type'] == 'dir') {
						//Add the dir to our list
						$files[$path]['file'] = $file;
						$files[$path]['dir'] = substr($path, strlen(SITE_DIR), -strlen($file));

						//If we are only getting the file names/paths
						if(!$data['light']) {
							$files[$path]['ext'] = 'dir';
							$files[$path]['level'] = $level;
							$files[$path]['size'] = 0;//@disk_total_space($path);
						}
					}

					//If the dir is NOT deeper than the limit && 'recursive' is set to true
					if($data['limit'] > $level){

						//Run this function on on the directory to see what is in it (this is where the recursive part starts)
						$files2 = directory(array('start_dir' => $path. '/', 'good_ext' => $data['good_ext'],
                                                  'skip_files' => $data['skip_files'], 'limit' => $data['limit'], 
                                                  'type' => $data['type'], 'light' => $data['light']), $level + 1);

						//then combine the output with the current $files array
						if(is_array($files2)) { $files = array_merge($files, $files2); }
						$files2 = null;
					}

					//Else if it is a file
				} else {

					//get the extension of the file
					$ext = preg_replace('/(.+)\.([a-z0-9]{2,4})/i', '\\2', $file);

					//And if it is in the GOOD file extension list OR if the list is set to allow ALL files
					if( (($data['good_ext'][0] == "all") || (array_search($ext, $data['good_ext']) > -1)) && (!$data['type'] || $data['type'] == 'file') ) {

						//Add the file to our list
						$files[$path]['file'] = $file;
						$files[$path]['dir'] = substr($path, strlen(SITE_DIR), -strlen($file));
						//Get the LAST "." followed by 2-4 letters/numbers
						$files[$path]['ext'] = $ext;

						//If we are only getting the file names/paths
						if(!$data['light']) {
							$files[$path]['level'] = $level;
							$files[$path]['size'] = filesize($path);
						}

					}
				}
			}
		}

		//Close the dir handle
		closedir($handle);

		//If there ARE files to sort
		if($files) {
			//sort by KEYS
			ksort($files);
		}

		//Return the result
		return $files;

	} else {
		trigger_error($data['start_dir']. " is not a valid directory.");
		return array('0' => array('file' => 'not a valid directory'));
	}
}


/**
 * Checks that a directory exists and is writable. If the directory does
 * not exist, the function will try to create it and/or change the
 * CHMOD settings on it.
 *
 * @param string $dir	directory you want to check
 * @param string $chmod	he CHMOD value you want to make it
 * @return unknown
 */
function directory_usable($dir, $chmod='0777') {

	//If it doesn't exist - make it!
	if(!is_dir($dir)) {
		if(!mkdir($dir, $chmod, true)) {
			trigger_error('Could not create the directory: <b>'. $dir. '</b>', E_USER_WARNING);
			return;
		}
	}

	//Make it writable
	if(!is_writable($dir)) {
		if(!chmod($dir, $chmod)) {
			trigger_error('Could not CHMOD 0777 the directory: <b>'. $dir. '</b>', E_USER_WARNING);
			return;
		}
	}

	return true;
}



/**
 * A function to recursively delete files and folders
 * @thanks: dev at grind [[DOT]] lv
 *
 * @param string	$dir	The path of the directory you want deleted
 * @param boolean	$remove	Remove Files (false) or Folder and Files (true)
 * @return boolean
 */
function destroy_directory($dir='', $remove=true) {

	//Try to open the directory handle
	if(!$dh = opendir($dir)) {
		trigger_error('<b>'. $dir. '</b> cannot be opened or does not exist', E_USER_WARNING);
		return;
	}

	//While there are files and directories in this directory
	while (false !== ($obj = readdir($dh))) {

		//Skip the object if it is the linux current (.) or parent (..) directory
		if($obj=='.' || $obj=='..') continue;

		$obj = $dir. $obj;

		//If the object is a directory
		if(is_dir($obj)) {

			//If we could NOT delete this directory
			if(!destroy_directory($obj, $remove)) {
				return;
			}

			//Else it must be a file
		} else {
			unlink($obj) or trigger_error('Could not remove file <b>'. $obj. '</b>', E_USER_WARNING);
		}

	}

	//Close the handle
	closedir($dh);

	if ($remove){
		rmdir($dir) or trigger_error('Could not remove directory <b>'. $dir. '</b>');
	}

	return true;
}


/**
 * Fetch a cache file
 *
 * @param	string	Name of the cache File
 * @param	int		max file life
 * @param	boolean	return data or print it out?
 * @return	void
 */
function fetch_cache($file='', $time=null, $method=true) {

	//If caching is disabled
	if(!CACHING) { return; }

	//If the life of the cache is not given - use default
	if(!$time) { $time = CACHING; }

	//set the file path
	$path = SITE_DIR. 'cache/'. $file. '.php';

	//IF the file exists AND the cach life has not expired
	if(file_exists($path) && ((time() - filemtime($path)) < $time)) {

		if($method) {
			//return string containing the file contents
			return file_get_contents($path);
		} else {
			//Print the file to the screen and return true
			readfile($path);
			return true;
		}
	}
}



/**
 * Create Cache
 *
 * @param	string	Name of the cache File
 * @param	string	String of contents to insert
 * @return	void
 */
function create_cache($file=null, $contents=null) {

	//If cacheing is not enabled - quit function
	if (!CACHING) { return true; }

	//If one isn't set - return
	if(!$file || !$contents) { return; }

	//Set the file path
	$path = SITE_DIR. 'cache/'. $file. '.php';

	// Open for writing and place the file pointer at the beginning
	// of the file and truncate the file (if it doesn't exist try to make it)
	if (!$handle = @fopen($path, 'w')) {
		trigger_error('Cannot open/create cache file ('. $path. ')');
		return;
	}

	// Write $content to our opened file.
	if (fwrite($handle, $contents) === FALSE) {
		trigger_error('Cannot write to cache file ('. $path. ')');
		return;
	}

	//Close the file
	fclose($handle);

	return true;

}


/**
 * Delete a Cache
 * @return	boolean
 */
function delete_cache($file=null) {
	if(!$file) { return; }

	//Delete the file+path
	if(unlink(SITE_DIR. 'cache/'. $file. '.php')) {
		return true;
	}

}

/**
 * Delete ALL Caches
 * @return	boolean
 */
function delete_caches() {
	//Destroy all files in the cache dir
	if(destroy_dir(CACHE_DIR, false)) {
		return true;
	}
}


/**
 * Highlight PHP code using proper CSS class names
 *
 * @param	string $code Code to highlight
 * @return	string
 */
function highlight_php($code='') {

	//If there are opening and closing PHP tags
	if (strpos($code, '<?php') !== false && strpos($code, '?>') !== false) {

		ini_set('highlight.default', 'code_default');
		ini_set('highlight.comment', 'code_comment');
		ini_set('highlight.keyword', 'code_keyword');
		ini_set('highlight.string', 'code_string');
		ini_set('highlight.html', 'code_html');

		$code = highlight_string($code, true);
		$code = str_replace('<span style="color: ', '<span class="', $code);

		return '<div class="codebox">'. $code. '</div>';

		//Else since there are no PHP tags - just place it in a code box
	} else {
		return '<div class="codebox"><code>'
		. htmlentities($code, ENT_QUOTES, 'UTF-8'). '</code></div>';
	}
}

/**
 * Gzip/Compress Output
 * Original function came from wordpress.org
 * @return void
 */
function gzip_compression() {

	//If no encoding was given - then it must not be able to accept gzip pages
	if(!isset($_SERVER['HTTP_ACCEPT_ENCODING'])) { return false; }

	//If zlib is not ALREADY compressing the page - and ob_gzhandler is set
	if (( ini_get('zlib.output_compression') == 'On'
	|| ini_get('zlib.output_compression_level') > 0 )
	|| ini_get('output_handler') == 'ob_gzhandler' ) {
		return false;
	}

	//Else if zlib is loaded start the compression.
	if ( (extension_loaded( 'zlib' ))
	&& (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ) {
		ob_start('ob_gzhandler');
	}

	/*
	 print $_SERVER['HTTP_ACCEPT_ENCODING']. '<br />'.
	 'extension_loaded("zlib") = '. extension_loaded( 'zlib' ). '<br />'.
	 'ini_get("zlib.output_compression") = '. ini_get('zlib.output_compression'). '<br />'.
	 'ini_get("output_handler") = '. ini_get('output_handler'). '<br />';
	 */
}


/**
 * Return a singleton instance of the current controller
 * @return object
 */
function &get_instance(){
	return core::get_instance();
}




/**
 * Creates pagination links for the number of pages given
 *
 * @param array $options
 * @return array
 */
function pagination($options=null) {
	
	/** [Options]
	 * total		Total number of items
	 * per_page		Items to show each page
	 * current_page	The current page that the user is on
	 * url			URI value to place in the links (must include "[[page]]")
	 * 				Example: /home/blog/page/[[page]]/
	 */
	
	//Don't allow page 0 or lower
	if($options['current_page'] < 0) {
		$options['current_page'] = 0;
	}
	
	
	//Initialize
	$data = array(
		'links' => null,
		'next' => null,
		'previous' => null,
		'total' => null,
		'offset' => 0,
	);
	
	//The offset to start from. This is useful if you are running a DB query
	if($options['current_page'] > 1) {
		$data["offset"] = (($options['per_page'] * $options['current_page']) - $options['per_page']);
	}
	
	//The Number of pages based on the total number of items and the number to show each page
	$data['total'] = ceil($options['total'] / $options['per_page']);
	
	//If there is more than one page...
	if($data['total'] > 1) {
	
		//If this is NOT the first page - show a previous link
		if($options['current_page'] > 1) {
			$data['previous'] = str_replace('[[page]]', ($options['current_page'] - 1), $options['url']); 
		}
	
		//If this isn't the last page - add a "next" link
		if($options['current_page'] + 1 < $data['total']) {
			$data["next"] = str_replace('[[page]]', ($options['current_page'] + 1), $options['url']);
		}
	}
	
	//For each page, create the URL
	for($i = 0; $i < $data['total']; $i++) {
		if($options['current_page'] == $i) {
			$data['links'][$i] = '';
		} else {
			//Replace [[page]] with the page number
			$data["links"][$i] = str_replace('[[page]]', $i, $options['url']);
		}
	}
	
	return $data;
}
