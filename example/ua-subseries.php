<?php 


// This script creates a navigation bar of letters and numbers. When the user clicks on a letter, all terms that 
// begin with that letter are presented. When the user clicks on any term in the list, that term is searched in the 6.x 
// CDM system. 


///////////####################
####################################
################################### //https://server16631.contentdm.oclc.org:81/dmwebservices/index.php?q=dmGetCollectionFieldVocabulary/SheetMusic/compoa/0/0/json
//https://server16631.contentdm.oclc.org:81/dmwebservices/index.php?q=dmGetCollectionFieldInfo/SheetMusic/json <--this will tell you the field nicknames !!!!Mui importante !!!
//////////////////new update  https://server16631.contentdm.oclc.org:/dmwebservices/index.php?q=dmGetCollectionFieldInfo/SheetMusic/json

ini_set('display_errors', 1);
error_reporting(E_ALL);


set_time_limit(0);      // run as long as required 

print "Starting......<br>\n";
echo date("D, d M Y H:i:s O");
print "<br>\n";

#exit;

// this array controls what we look for in the controlled vocab for subjects as well as the files we process containing each alphabetical subject...
$alphanumeric = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0');
#print_r($alphanumeric); print "<br>\n";
$server_name = $_SERVER['SERVER_NAME'];

print "server name: $server_name <br>\n";




// Delete all files of the form ua_subseries_msstatecv* before re-creating them again. 
print "CLEANING up old files..... <br> \n";
foreach (glob("ua_subseries_msstatecv*") as $filename) {
   echo "$filename size " . filesize($filename) . "<br>";
   unlink($filename) or print("could not unlink $filename ....does not exist <br>");
}

#exit;

$server_name = $_SERVER['SERVER_NAME'];
# For testing:

$server_name = "server16631.contentdm.oclc.org";

print "server name: $server_name <br>\n";

$full_URL_name = "cdm16631.contentdm.oclc.org/ui/custom/default/collection/coll_uac/resources/custompages/browsesubseries/";

print "Full URL: $full_URL_name <br>\n";

print "STARTING TO GET COLLECTION LIST....<br>\n";
$ch = curl_init(); 
// all collections// 
//$curl_url =  "http://" . $server_name .  ":81/dmwebservices/index.php?q=dmGetCollectionList/json";
//one collection //
//*******//
$curl_url =  "https://" . $server_name .  "/dmwebservices/index.php?q=dmGetCollectionParameters/uac/json";
#print "<br> url: $curl_url <br>";
// set url 
curl_setopt($ch, CURLOPT_URL, "$curl_url"); 
//return the transfer as a string 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
curl_setopt($ch, CURLOPT_HEADER, 0);
// $output contains the output string 
$records = curl_exec($ch); 
$array_records = json_decode($records,true);  
curl_close($ch); 

// get and segregate terms into seperate files
print "STARTING TO GET ALL TERMS in COLLECTION LIST ...<br>\n";
$counter = count($array_records);
print "count array_records: $counter <br>";
foreach ($alphanumeric as $letter) {
  #print "############ LETTER $letter #################################<br>";
  #$tempfile = "C:\\xampp\htdocs\ua_subseries_msstatecv_temp_" . "$letter";
 // this is for cdm hosted // $tempfile = "/cdm/sites/16631/Website/public_html/ui/custom/default/collection/coll_uac/resources/custompages/browsesubseries/" . "ua_subseries_msstatecv_temp_" . "$letter";
 $tempfile = "/var/www/cdm/scriptoutput/" . "ua_subseries_msstatecv_temp_" . "$letter";
  $handle = fopen("$tempfile", "a");
 if (file_exists($tempfile)){
        print "File exists: $tempfile <br>";
    } else {
        print "File does NOT exist: $tempfile .....EXITING.....<br>";
  	    exit;
    }
  for($i = 0; $i < $counter; ++$i) {
       
		$ch = curl_init(); 
//		$curl_url = "http://" . $server_name . ":81/dmwebservices/index.php?q=dmGetCollectionFieldVocabulary/" . $alias . "/subjec/0/0/json";
//change alias to name they are ***Case sensitive//	
/////////////////////#####Change collection allias here #################/////////////////////////////
/////////////////////#####Change the field here too ex. subject or Title or compoa see URL in top comment to fin nick #################/////////////////////////////
$curl_url = "https://" . $server_name . "/dmwebservices/index.php?q=dmGetCollectionFieldVocabulary/uac/subser/0/0/json";
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

    $tempfile = "/var/www/cdm/scriptoutput/" . "ua_subseries_msstatecv_temp_" . "$letter";
  $tempfile_out = "/var/www/cdm/scriptoutput/" . "ua_subseries_msstatecv_temp_" . "$letter" . "_sorted";
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

    $tempfile = "/var/www/cdm/scriptoutput/"  . "ua_subseries_msstatecv_temp_" . "$letter" . "_sorted";
  $tempfile_out = "/var/www/cdm/scriptoutput/"  . "ua_subseries_msstatecv_temp_" . "$letter" . "_dedupped";
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

    $tempfile = "/var/www/cdm/scriptoutput/"  . "ua_subseries_msstatecv_temp_" . "$letter" . "_dedupped";
  $tempfile_out = "/var/www/cdm/scriptoutput/"  . "ua_subseries_msstatecv_" . "$letter" . ".html";
  $handle_out = fopen("$tempfile_out", "a");
  $contents = file($tempfile);  // put contents of $tempfile into array $contents ...
  $count = count($contents);    // ...so that we can count the lines in $contents
  if ($count >= 1 ) {           // only create files if there are subjects! Some alphabets/numbers are empty files.  
    #print "count for $letter: $count <br>";
    $count_text = "<strong>There are $count items beginning with \"$letter\".</strong><br />"; // info to user
    fwrite($handle_out, $count_text);
    foreach ($contents as $term) {

         $original = $term;                         // keep original term to insert in query what user sees on web page 
         $original = rtrim($term, "\n");     

		  ///##################################################################################//////
///////////####		  Limit collection here - change /all to the collection name///////////////////////

		 $link_tmp = "<a href=\"http://cdm16631.contentdm.oclc.org/cdm/search/collection/uac/searchterm/$original/field/subser/mode/exact\">$original</a>";
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
// make the ua_subseries_msstatecv_navbar.html file 
print "STARTING TO MAKE NAVBAR...<br>\n";

$tempfile_out = "/var/www/cdm/scriptoutput/ua_subseries_msstatecv_navbar" . ".html";
$handle_out = fopen("$tempfile_out", "a");
foreach ($alphanumeric as $letter) {

   $tempfile_in = "/var/www/cdm/scriptoutput/ua_subseries_msstatecv_" . "$letter" . ".html";

  $size = filesize($tempfile_in);
  if ($size  > 0 ) {    // there are subjects in a file, some files don't have subjects!
     print "good--file ua_subseries_msstatecv_$letter.html has data <br>";
    if ( $letter === "0" ) {	 
	
	} else {
	
	}

$linenavbar = "<a href=\"http://blogs.library.msstate.edu/cdm/scriptoutput/ua_subseries_msstatecv_" . "$letter" . ".html\">$letter</a>\n";  // For non-msstate interface (generic)
     fwrite($handle_out, $linenavbar);
#     //print "file $filename_for_time written! <br>";
  } 
} 
  fclose($tempfile_out);

cleanup:  
# Remove soreted and depudded temp files:
foreach (glob("ua_subseries_msstatecv_*sorted") as $filename) {
      print "removing $filename <br>";
      unlink($filename) or print("could not unlink $filename ....does not exist <br>");
}  
foreach (glob("ua_subseries_msstatecv_*dedupped") as $filename) {
	  print "removing $filename <br>";
      unlink($filename) or print("could not unlink $filename ....does not exist <br>");
}
foreach (glob("ua_subseries_msstatecv*temp*") as $filename) {
      print "removing $filename <br>";
      unlink($filename) or print("could not unlink $filename ....does not exist <br>");
}
print "Done with everything!! <br>";


  

?>






