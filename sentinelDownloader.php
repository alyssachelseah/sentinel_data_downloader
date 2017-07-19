#!/usr/bin/php
<!--
	Automated Sentinel Satellite Data Downloader

	University of the Philippines Los Banos

	Authors:
		Aaron Jemuel C. Cruz
		Ma. Alyssa Chelseah N. Blaquera
-->


<?php
	$date=date('m.d.Y');
	$dir=$argv[1];
	$x = $argv[2];
	$y = $argv[3];
	echo $dir."\n\n\n\n\n\n";

	#searches for new satellite data from starting the script to yesterday's data
	exec("wget --no-check-certificate --user=accruz --password=ECgkstm1 --output-document=query_results.xml 'https://scihub.copernicus.eu/dhus/search?q=footprint:\"Intersects($x $y)\" AND (platformname:Sentinel-1) AND ingestiondate=[NOW-7DAY TO NOW]&rows=1'");
	$xml=simplexml_load_file("query_results.xml") or die("Error: Cannot create object");
	
	#accesses the download URL
	$command = "wget --no-check-certificate --user=accruz --password=ECgkstm1 -O ". $dir . $xml->entry->title . " https://scihub.copernicus.eu/dhus/odata/v1/Products\(\'" . $xml->entry->id . "\'" . '\)/\$value';
	if(!(file_exists($dir.$xml->entry->title)))
		exec($command);
	else
		echo("\n\n\t\tAlready downloaded Satellite data!\n\n\n\n");

	#prompts user if data was downloaded from the site.
	$dirSize=GetDirectorySize($dir);
	if($dirSize==0){
		echo "\n\n\t\t====NO NEW SATELLITE DATA FROM SENTINEL-1====\n\n\n\n";
		#deletes the zip file if it is empty
		$command = "rm -rf " . $dir . 'Sentinel-1.zip';
		exec($command);
	}

	#searches for new satellite data from starting the script to yesterday's data
	exec("wget --no-check-certificate --user=accruz --password=ECgkstm1 --output-document=query_results.xml 'https://scihub.copernicus.eu/dhus/search?q=footprint:\"Intersects($x $y)\" AND (platformname:Sentinel-2) AND ingestiondate=[NOW-7DAY TO NOW]&rows=1'");
	$xml=simplexml_load_file("query_results.xml") or die("Error: Cannot create object");

	#accesses the download URL
	$command = "wget --no-check-certificate --user=accruz --password=ECgkstm1 -O " . $dir . $xml->entry->title . " https://scihub.copernicus.eu/dhus/odata/v1/Products\(\'" . $xml->entry->id . "\'" . '\)/\$value';
	if(!(file_exists($dir.$xml->entry->title)))
		exec($command);
	else
		echo("\n\n\t\tAlready downloaded Satellite data!\n\n\n\n");

	#removes query_results.xml
	exec("rm query_results.xml");
	
	#checks if any data was downloaded, if no data was downloaded, prompts the user and then proceeds to delete the directory
	if(GetDirectorySize($dir)==0){
		echo "\n\n\t\t====NO NEW SATELLITE DATA FROM BOTH SATELLITES====\n\n\n\n";
		exec("rm -r -f ".$dir);
	} else if(GetDirectorySize($dir)==$dirSize){
		echo "\n\n\t\t====NO NEW SATELLITE DATA FROM SENTINEL-2====\n\n\n\n";
		#deletes the zip file if it is empty
		$command = "rm -rf " . $dir . 'Sentinel-2.zip';
		exec($command);
	} else
		echo "\n\n\t\t====Successfully downloaded latest Satellite Data\n\n\n\n";

	#checks if the folder is empty
	function GetDirectorySize($path){
	    $bytestotal = 0;
	    $path = realpath($path);
	    if($path!==false && $path!='' && file_exists($path)){
	        foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object){
	            $bytestotal += $object->getSize();
	        }
	    }
	    return $bytestotal;
	}
?>