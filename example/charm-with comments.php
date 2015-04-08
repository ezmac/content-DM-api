<?php 
// msstate_generate_cv.php for CONTENTdm 6.x

// This script creates a navigation bar of letters and numbers. When the user clicks on a letter, all terms that 
// begin with that letter are presented. When the user clicks on any term in the list, that term is searched in the 6.x 
// CDM system. 


///////////####################
####################################
################################### //http://cdm16631.contentdm.oclc.org:81/dmwebservices/index.php?q=dmGetCollectionFieldVocabulary/SheetMusic/compoa/0/0/json
//http://cdm16631.contentdm.oclc.org:81/dmwebservices/index.php?q=dmGetCollectionFieldInfo/SheetMusic/json <--this will tell you the field nicknames !!!!Mui importante !!!
//////////////////new update  https://server16631.contentdm.oclc.org:/dmwebservices/index.php?q=dmGetCollectionFieldInfo/SheetMusic/json

ini_set('display_errors', 1);
error_reporting(E_ALL);

// used in conjunction with the calling function "callWebsite()" at the foot of index.php
#ignore_user_abort(TRUE);        // run this script in the background
set_time_limit(0);      // run as long as required 

print "Starting......<br>\n";
echo date("D, d M Y H:i:s O");
print "<br>\n";

#exit;

// this array controls what we look for in the controlled vocab for subjects as well as the files we process containing each alphabetical subject...
$alphanumeric = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0');
#print_r($alphanumeric); print "<br>\n";
$server_name = $_SERVER['SERVER_NAME'];
# For testing:
# $server_name = "cdm16631.contentdm.oclc.org";
//$server_name = "server16631.contentdm.oclc.org";
print "server name: $server_name <br>\n";


#goto sort;
#goto deduplicate;
#goto populate;
#goto make_nav;
#goto cleanup;

// Delete all files of the form charm_ocol_msstatecv* before re-creating them again. 
print "CLEANING up old files..... <br> \n";
foreach (glob("charm_ocol_msstatecv*") as $filename) {
   echo "$filename size " . filesize($filename) . "<br>";
   unlink($filename) or print("could not unlink $filename ....does not exist <br>");
}

#exit;

$server_name = $_SERVER['SERVER_NAME'];
# For testing:
//$server_name = "cdm16631.contentdm.oclc.org";
$server_name = "server16631.contentdm.oclc.org";
///////////////$curl_server_name = "server16631.contentdm.oclc.org";
print "server name: $server_name <br>\n";
///////Clay added
$full_URL_name = "cdm16631.contentdm.oclc.org/ui/custom/default/collection/coll_charm/resources/custompages/scriptcharm/";

print "Full URL: $full_URL_name <br>\n";

//////end clay addition
///////////////print "curl server name: $curl_server_name <br>\n";
#exit;
//for ssl
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);     
//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); 
print "STARTING TO GET COLLECTION LIST....<br>\n";
$ch = curl_init(); 
// all collections// 
//$curl_url =  "http://" . $server_name .  ":81/dmwebservices/index.php?q=dmGetCollectionList/json";
//one collection //
//*******//
$curl_url =  "https://" . $server_name .  "/dmwebservices/index.php?q=dmGetCollectionParameters/charm/json";
#print "<br> url: $curl_url <br>";
// set url 
curl_setopt($ch, CURLOPT_URL, "$curl_url"); 
//return the transfer as a string 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
curl_setopt($ch, CURLOPT_HEADER, 0);
// $output contains the output string 
$records = curl_exec($ch); 
$array_records = json_decode($records,true);   # true=associative array, nothing=object
#print "<br>johng: {$array_records[0]['alias']}  <p>";
#print "<br>johng: {$array_records[0]['name']}  <p>";
#var_dump($array_records[0]["alias"]); print "<p>";
#foreach ($array_records as $key1 => $value1) {
#    echo "Key1: $key1 ==> Value1: $value1<br />\n";
#    foreach ($value1 as $key2 => $value2) {
#	    echo ".....................................$key2 ==> $value2<br />\n";
#	} 
#     break;
#}
#var_dump($array_records);    # to see raw data
# close curl resource to free up system resources 
curl_close($ch); 

// get and segregate terms into seperate files
print "STARTING TO GET ALL TERMS in COLLECTION LIST ...<br>\n";
$counter = count($array_records);
print "count array_records: $counter <br>";
foreach ($alphanumeric as $letter) {
  #print "############ LETTER $letter #################################<br>";
  #$tempfile = "C:\\xampp\htdocs\charm_ocol_msstatecv_temp_" . "$letter";
 // this is for cdm hosted // $tempfile = "/cdm/sites/16631/Website/public_html/ui/custom/default/collection/coll_charm/resources/custompages/scriptcharm/" . "charm_ocol_msstatecv_temp_" . "$letter";
 $tempfile = "/var/www/cdm/scriptcharm/" . "charm_ocol_msstatecv_temp_" . "$letter";
  $handle = fopen("$tempfile", "a");
 if (file_exists($tempfile)){
        print "File exists: $tempfile <br>";
    } else {
        print "File does NOT exist: $tempfile .....EXITING.....<br>";
  	    exit;
    }
  for($i = 0; $i < $counter; ++$i) {
       #print "coll_name: {$array_records[$i]['name']} <br>"; 
	    #print "alias: {$array_records[$i]['alias']} <br>"; 
//		$alias = $array_records[$i]['alias'];
//		$alias = substr( $alias, 1);  # strip off the / from the alias!
		$ch = curl_init(); 
//		$curl_url = "http://" . $server_name . ":81/dmwebservices/index.php?q=dmGetCollectionFieldVocabulary/" . $alias . "/subjec/0/0/json";
//change alias to name they are ***Case sensitive//	
/////////////////////#####Change collection allias here #################/////////////////////////////
/////////////////////#####Change the field here too ex. subject or Title or compoa see URL in top comment to fin nick #################/////////////////////////////
$curl_url = "https://" . $server_name . "/dmwebservices/index.php?q=dmGetCollectionFieldVocabulary/charm/origin/0/0/json";
        #print "url: $curl_url <br>";
		#        // set url 
        curl_setopt($ch, CURLOPT_URL, "$curl_url"); 
#        //return the transfer as a string 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_HEADER, 0);
#        // $output contains the output string 
       $output = curl_exec($ch); 
#		//print "$output <br>"; exit;
#		// close curl resource to free up system resources 
        curl_close($ch); 
		$array_subjects = json_decode($output,true);   # true=associative array, nothing=object
       #var_dump($array_subjects);
	    $counter_subjects = count($array_subjects);
		#print "counter_subjects: $counter_subjects <br>";
		#for($n = 0, $size = count($array_subjects); $n < $size; ++$n) {		
		for($n = 0;  $n < $counter_subjects; ++$n) {		
		      #print "<br>#### SUBJECTS ##########################<br>";
			  $pattern = "/^$letter.+$/i";     // the pattern we're looking for: A, then B, then C, then D....
			  #print "subject: {$array_subjects[$n]}<br>";
			  preg_match($pattern, $array_subjects[$n], $matches);
#			  print "pattern: $pattern <br>";
		 	  if (!empty($matches)) {
			    #print "<br> {$array_records[$i]['name']} {$array_records[$i]['alias']} >>>>>>>>> string to write to file $letter : ";
				#print "matches: {$matches[0]} <br>"; 
				$string_to_write = $matches[0];
				$string_to_write .= "\n";    # put newline on end 
			    fwrite($handle, $string_to_write);  
			  }
		#break;
		}	
	 #break;
     }  # end for
  #break;
 } # end foreach
 //print "DONE getting all <b>subjects</b> into files....<br>";
print "DONE getting all <b>Titles</b> into files....<br>";

#exit;

sort:
 // sort the terms
print "<br>STARTING TO SORT TERMS.........\n";
foreach ($alphanumeric as $letter) {
#$tempfile = "C:\\xampp\htdocs\charm_ocol_msstatecv_temp_" . "$letter";
#$tempfile_out = "C:\\xampp\htdocs\charm_ocol_msstatecv_temp_" . "$letter" . "_sorted";
//  $tempfile = "/cdm/sites/16631/Website/public_html/ui/custom/default/collection/coll_charm/resources/custompages/scriptcharm/" . "charm_ocol_msstatecv_temp_" . "$letter";
//  $tempfile_out = "/cdm/sites/16631/Website/public_html/ui/custom/default/collection/coll_charm/resources/custompages/scriptcharm/" . "charm_ocol_msstatecv_temp_" . "$letter" . "_sorted";
    $tempfile = "/var/www/cdm/scriptcharm/" . "charm_ocol_msstatecv_temp_" . "$letter";
  $tempfile_out = "/var/www/cdm/scriptcharm/" . "charm_ocol_msstatecv_temp_" . "$letter" . "_sorted";
  $handle_out = fopen("$tempfile_out", "a");
       $contents = file($tempfile);
       sort($contents);                                // sort the array for alphabetical listing
       $string_to_write = join("", $contents);
       fwrite($handle_out, $string_to_write);
  fclose($handle_out);
} 

#exit;

deduplicate:

// de-duplicate terms in the files
print " STARTING TO DEDUPLICATE TERMS ....\n";
foreach ($alphanumeric as $letter) {
#$tempfile = "C:\\xampp\htdocs\charm_ocol_msstatecv_temp_" . "$letter" . "_sorted";
#$tempfile_out = "C:\\xampp\htdocs\charm_ocol_msstatecv_temp_" . "$letter" . "_dedupped";
//  $tempfile = "/cdm/sites/16631/Website/public_html/ui/custom/default/collection/coll_charm/resources/custompages/scriptcharm/" . "charm_ocol_msstatecv_temp_" . "$letter" . "_sorted";
 // $tempfile_out = "/cdm/sites/16631/Website/public_html/ui/custom/default/collection/coll_charm/resources/custompages/scriptcharm/" . "charm_ocol_msstatecv_temp_" . "$letter" . "_dedupped";
    $tempfile = "/var/www/cdm/scriptcharm/"  . "charm_ocol_msstatecv_temp_" . "$letter" . "_sorted";
  $tempfile_out = "/var/www/cdm/scriptcharm/"  . "charm_ocol_msstatecv_temp_" . "$letter" . "_dedupped";
  $handle_out = fopen("$tempfile_out", "a");
       $contents = file($tempfile);
       $unique = array_unique($contents);              // identify only the unique terms in array of subjects
       $string_to_write = join("", $unique);
       fwrite($handle_out, $string_to_write);
  fclose($handle_out);
} 

#exit;
populate:

print "STARTING TO POPULATE files with clickable links.... \n";
 foreach ($alphanumeric as $letter) {
#$tempfile = "C:\\xampp\htdocs\charm_ocol_msstatecv_temp_" . "$letter" . "_dedupped";
#$tempfile_out = "C:\\xampp\htdocs\charm_ocol_msstatecv_" . "$letter" . ".html";
 // $tempfile = "/cdm/sites/16631/Website/public_html/ui/custom/default/collection/coll_charm/resources/custompages/scriptcharm/" . "charm_ocol_msstatecv_temp_" . "$letter" . "_dedupped";
//  $tempfile_out = "/cdm/sites/16631/Website/public_html/ui/custom/default/collection/coll_charm/resources/custompages/scriptcharm/" . "charm_ocol_msstatecv_" . "$letter" . ".html";
//  "/var/www/cdm/scriptcharm/" 
    $tempfile = "/var/www/cdm/scriptcharm/"  . "charm_ocol_msstatecv_temp_" . "$letter" . "_dedupped";
  $tempfile_out = "/var/www/cdm/scriptcharm/"  . "charm_ocol_msstatecv_" . "$letter" . ".html";
  $handle_out = fopen("$tempfile_out", "a");
  $contents = file($tempfile);  // put contents of $tempfile into array $contents ...
  $count = count($contents);    // ...so that we can count the lines in $contents
  if ($count >= 1 ) {           // only create files if there are subjects! Some alphabets/numbers are empty files.  
    #print "count for $letter: $count <br>";
    $count_text = "<strong>There are $count composers in the library beginning with \"$letter\".</strong><br />"; // info to user
    fwrite($handle_out, $count_text);
    foreach ($contents as $term) {
         #print "------------<br>";
         #print "original term: $term <br>";
         $original = $term;                         // keep original term to insert in query what user sees on web page 
         $original = rtrim($term, "\n");     
         #print "original term after rtrim: $term <br>";		 
         #preg_match('/USE\b.+$/', $term, $match);   // search on the term that follows USE, matches go in array $match 
         #$noUSEterm = substr($match[0], 4);          // now return everything after USE, insert in query that actually runs 
		 #print "noUSEterm: $noUSEterm <br>";
		 #if ($noUSEterm == '') {
                 //print "noUSEterm is empty: $noUSEterm <br>";
                //$pattern = '/[\(]|[\)]/';               // now remove any parans since they throw off searching 
                //$replacement = '';                      // replace with nothing effective erasure
                //$cleanterm = preg_replace($pattern, $replacement, $original);   // the replacement operation
                //print "new term (from original): $cleanterm <br>" ;
		#		$USEthisone = $original;
         #} else {
                 //print "noUSEterm is full: $noUSEterm <br>";
                //$pattern = '/[\(]|[\)]/';               // now remove any parans since they throw off searching 
                //$replacement = '';                      // replace with nothing effective erasure
                //$cleanterm = preg_replace($pattern, $replacement, $noUSEterm);   // the replacement operation
                //print "new term (was USE): $cleanterm <br>" ;
				//$USEthisone = $noUSEterm;
         #}
      #print ">> TERM to USE: $USEthisone <<";
		  #$link_tmp = "<a href=\"http://" . $server_name . "/cdm/search/collection/all/searchterm/$USEthisone/field/subjec/mode/exact\" target=\"_blank\">$original</a>";
		  ///##################################################################################//////
///////////####		  Limit collection here - change /all to the collection name///////////////////////
//		  $link_tmp = "<a href=\"http://$server_name/cdm/search/collection/all/searchterm/$original/field/subjec/mode/exact\" target=\"_blank\">$original</a>";
///////////####		  Limit field here - example---change subjec to title or compoa all///////////////////////		  
		 // $link_tmp = "<a href=\"https://$server_name/cdm/search/collection/charm/searchterm/$original/field/origin/mode/exact\" target=\"_blank\">$original</a>";
		 $link_tmp = "<a href=\"http://cdm16631.contentdm.oclc.org/cdm/search/collection/charm/searchterm/$original/field/origin/mode/exact\" target=\"_blank\">$original</a>";
		  #print "link_tmp: $link_tmp <br>";
		  $link = $link_tmp . "<br />";
          fwrite($handle_out, $link);
   } // end foreach
    $last_link = "<br />";
     fwrite($handle_out, $last_link);
     fclose($handle_out);
  } // end if count
}

#exit;
make_nav:
// make the charm_ocol_msstatecv_navbar.html file 
print "STARTING TO MAKE NAVBAR...<br>\n";
//$tempfile_out = "/cdm/sites/16631/Website/public_html/ui/custom/default/collection/coll_charm/resources/custompages/scriptcharm/charm_ocol_msstatecv_navbar" . ".html";
//"/var/www/cdm/scriptcharm/" 
$tempfile_out = "/var/www/cdm/scriptcharm/charm_ocol_msstatecv_navbar" . ".html";
$handle_out = fopen("$tempfile_out", "a");
foreach ($alphanumeric as $letter) {
#$tempfile = "C:\\xampp\htdocs\charm_ocol_msstatecv_navbar" . ".html";
 // $tempfile_in = "/cdm/sites/16631/Website/public_html/ui/custom/default/collection/coll_charm/resources/custompages/scriptcharm/charm_ocol_msstatecv_" . "$letter" . ".html";
   $tempfile_in = "/var/www/cdm/scriptcharm/charm_ocol_msstatecv_" . "$letter" . ".html";
  #$filesize = "C:\\xampp\htdocs\charm_ocol_msstatecv_" . "$letter" . ".html";
  #$filesize = "/cdm/sites/16631/Website/public_html/ui/custom/default/collection/default/resources/custompages/home/charm_ocol_msstatecv_" . "$letter" . "_.html";
  $size = filesize($tempfile_in);
  if ($size  > 0 ) {    // there are subjects in a file, some files don't have subjects!
     print "good--file charm_ocol_msstatecv_$letter.html has data <br>";
    if ( $letter === "0" ) {	 
	//**delivered//  $linenavbar = "<a href=\"http://" . $server_name . "/index.php?subject=-\">$letter</a>\n";           // For msstate interface
////////clay ** temp	$linenavbar = "<a href=\"http://" . $full_URL_name . "/index.php?subject=-\">$letter</a>\n";           // <--Clay
	} else {
	//**delivered//  $linenavbar = "<a href=\"http://" . $server_name . "/index.php?subject=$letter\">$letter</a>\n";           // For msstate interface
////////clay ** temp	  $linenavbar = "<a href=\"http://" . $full_URL_name . "/index.php?subject=$letter\">$letter</a>\n";           // <--Clay
	}
//$linenavbar = "<a href=\"http://cdm16631.contentdm.oclc.org/ui/custom/default/collection/coll_charm/resources/custompages/scriptcharm/charm_ocol_msstatecv_" . "$letter" . ".html\">$letter</a>\n";  // For non-msstate interface (generic)
$linenavbar = "<a href=\"http://cdm16631.contentdm.oclc.org/ui/custom/default/collection/coll_charm/resources/custompages/scriptcharm/charm_ocol_msstatecv_" . "$letter" . ".html\">$letter</a>\n";  // For non-msstate interface (generic)
     fwrite($handle_out, $linenavbar);
#     //print "file $filename_for_time written! <br>";
  } 
} 
  fclose($tempfile_out);

cleanup:  
# Remove soreted and depudded temp files:
foreach (glob("charm_ocol_msstatecv_*sorted") as $filename) {
      print "removing $filename <br>";
      unlink($filename) or print("could not unlink $filename ....does not exist <br>");
}  
foreach (glob("charm_ocol_msstatecv_*dedupped") as $filename) {
	  print "removing $filename <br>";
      unlink($filename) or print("could not unlink $filename ....does not exist <br>");
}
foreach (glob("charm_ocol_msstatecv*temp*") as $filename) {
      print "removing $filename <br>";
      unlink($filename) or print("could not unlink $filename ....does not exist <br>");
}
print "Done with everything!! <br>";

#debug....list the final output files
#foreach (glob("charm_ocol_msstatecv_*") as $filename) {
#   echo "$filename size " . filesize($filename) . "<br>";
#}
#foreach (glob("charm_ocol_msstatecv__1*") as $filename) {
#   echo "$filename size " . filesize($filename) . "<br>";
#   system("cat $filename 2>&1",$output);
#   echo "$output";
#}
  

?>






