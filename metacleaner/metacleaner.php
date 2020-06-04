<?php
/**
 * @package metacleaner
 * @version 1.0.0
 */
/*
Plugin Name: metacleaner
Plugin URI: http://wordpress.org/plugins/metacleaner
Description: This plugin hooks "wp_handle_upload" and then calls https://github.com/ozzi-/metacleaner to remove metadata.
Author: ozzi-
Version: 1.0.0
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

$metacleanerJARpath = __DIR__."\metacleaner.jar";
add_filter('wp_handle_upload_prefilter', 'metacleaner_filter' );

function metacleaner_filter( $file ){
	global $metacleanerJARpath;
	$fileType="";
	$fileNameEnd = strtolower($file["name"]);
	
    if(endsWith($fileNameEnd,".jpg") || endsWith($fileNameEnd,".jpeg")){
		$fileType="jpg";
	}
	if(endsWith($fileNameEnd,".png")){
		$fileType="png";
	}
	if(endsWith($fileNameEnd,".pdf")){
		$fileType="pdf";
	}
	if(endsWith($fileNameEnd,".odt") || endsWith($fileNameEnd,".ods") || endsWith($fileNameEnd,".odp")){
		$fileType="odt";
	}
	if(endsWith($fileNameEnd,".doc") || endsWith($fileNameEnd,".ppt") || endsWith($fileNameEnd,".xls")){
		$fileType="doc";
	}
	if(endsWith($fileNameEnd,".docx")){
		$fileType="docx";
	}
	if(endsWith($fileNameEnd,".xlsx")){
		$fileType="xlsx";
	}
	if(endsWith($fileNameEnd,".xml")){
		$fileType="xml";
	}
    
	if(strContains($file["tmp_name"],"\"")){
		$file['error'] = 'metacleaner failed to run, file tmp_name contains one or more " characters.';
	}else{
		// only run metacleaner if we are uploading a matching filetype
		if($fileType!==""){
			exec("java -version",$output,$retVal);
			if($retVal!=0){
				$file['error'] = 'metacleaner failed to run, as java cannot be found or started (is JAVA_HOME set?)';
			}else{
				// we need to use -f to force the filetype, as tmp_name will have the file ending .tmp
				exec("java -jar ".$metacleanerJARpath." -i \"".$file["tmp_name"]."\" -o -f ".$fileType, $output,$retVal);
				if($retVal!=0){
					$file['error'] = 'metacleaner failed to run, received error code '.$retVal;
				}
			}
		}
	}
	
    return $file;
}

function strContains($haystack, $needle){
	return (strpos($haystack, $needle) !== false);
}

function endsWith($haystack, $needle){
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }
    return (substr($haystack, -$length) === $needle);
}