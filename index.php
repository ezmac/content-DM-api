<?php 
//a code to make browse by X 
//
// starting with an title
include 'simple-CONTENTdm/lib/content_dm.php';
include 'simple-CONTENTdm/lib/collection.php';
require 'curl_functions.php';
define('CDM_API_SERVER', 'https://server16631.contentdm.oclc.org');
define('CDM_HOST', 'http://digital.library.msstate.edu');
$dir = '../../';
$collections = ContentDM::all_collections();
foreach($collections as $collection){
  echo "<a href=\"{$collection->landingPageUrl()}\">" . $collection->name ."</a><br/>";
  echo "<!-- ";var_dump($collection);echo "-->";
  echo "<ul>";
  foreach ($collection->getFieldInfo() as $field) {
    echo "<li><a href=\"".
      $collection->browseByFieldUrl($field);
    echo "\">".$field['name']."</a>";
    $fieldVocabulary = ($collection->getFieldVocabulary($field['nick']));
    echo "<ul>";
    $fieldVocabulary = array_unique($fieldVocabulary);
    usort($fieldVocabulary,'titleCmp');
    // this is going to be replaced with a usort.  Stupid non alpha characters.

    foreach ($fieldVocabulary as $key => $vocab){
      echo "<li><a href=\"".$collection->searchByUrl($field['nick'],$vocab)."\">$vocab</a></li>";

      //brain dead.. must recharge.
      //still want more coffee.
    }
    echo "</ul>";
    echo "</li>";

  }
  echo "</ul>";
  //now, it's making like 35 more requests for each (1) colleciton.   :-\.
}
/*
 * also, this code makes like 35 requests to another server.  sequentially.  So yes, it loads slowly.
 * sorry.
 *
 *At approximately 12:12pm MSU police was notified of a armed robbery that took place at Campus Trails apartments. Suspects were seen fleeing toward campus. Description of suspects are two black males one with a blue jacket and one with a black jacket with pants with yellow strips. Please call MSUPD immediately if you see anything suspicious.
 just so you know..
 */

function titleCmp($a,$b){
  $i=0;
  do{
    $aFL = $a[$i];
    $i++;
  } while(!ctype_alnum($aFL));
  $i=0;

  do{
    $bFL = $b[$i];
    $i++;
  } while(!ctype_alnum($bFL));
  $aFL = strtolower($aFL);
  $bFL = strtolower($bFL);
  if ($aFL==$bFL) return 0;
  return ($aFL<$bFL)? -1 : 1 ;

}
?>
