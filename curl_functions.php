<?php 
function build_get_string($parameters) {
  $get_string = "";
  foreach ($parameters as $key => $value) {

    $get_string .= "&".urlencode($key)."=".urlencode($value);
  }
  return trim($get_string,"&");
}

function build_multipart_file_upload($file_name)
{
$file = realpath($file_name);
$fp = explode("/",$file_name);
$real_file_name = $fp[count($fp)-1];
// build multipart
$multipart_boundary = "ABCD123ABCD123ABCD123444ABCD1234";
$params  = "--$multipart_boundary\r\n"
    . "Content-Disposition: form-data; name=\"files[]\"; filename=\"$real_file_name\"\r\n"
    . "Content-Type: image/jpeg\r\n"
    . "\r\n"
    . file_get_contents($file) . "\r\n"
    . "--$multipart_boundary--";

$first_newline      = strpos($params, "\r\n");
$request_headers    = array();
$request_headers[]  = 'Content-Length: ' . strlen($params);
$request_headers[]  = 'Content-Type: multipart/form-data; boundary='
    . $multipart_boundary;
return array($request_headers, $params);

}



function multipart_build_query($fields, $boundary){
  $retval = '';
  foreach($fields as $key => $value){
    $retval .= "--$boundary\nContent-Disposition: form-data; name=\"$key\"\n\n$value\n";
  }
  $retval .= "--$boundary--";
  return $retval;
}
/** 
 * Returns array of photos with full path to file if it exists
 * Array is keyed by picture_$i index.
 * Array will contain false if an image does not exist.
 */

function to_camel_case($str, $capitalise_first_char = false) {
  if($capitalise_first_char) {
    $str[0] = strtoupper($str[0]);
  }
  $func = create_function('$c', 'return strtoupper($c[1]);');
  return preg_replace_callback('/_([a-z])/', $func, $str);
}
function from_camel_case($str) {
  $str[0] = strtolower($str[0]);
  $func = create_function('$c', 'return "_" . strtolower($c[1]);');
  return preg_replace_callback('/([A-Z])/', $func, $str);
}

function get_curl_get($url, $getstring, $cookie="cookie.txt" ) 
{
  if (strlen($getstring)>0)
    $get_url = $url."?".$getstring;
  else 
    $get_url = $url;
  $ch = curl_init(); 
  curl_setopt ($ch, CURLOPT_URL, $get_url); 
  curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
  curl_setopt ($ch, CURLOPT_TIMEOUT, 60); 
  curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
  curl_setopt ($ch, CURLOPT_COOKIEJAR, $cookie); 
  curl_setopt ($ch, CURLOPT_REFERER, $url); 
  curl_setopt ($ch, CURLOPT_POST, 0); 

  return $ch;
}
/**
 * Creates a new curl handle to post against a URL
 */
function get_curl_post($url, $postdata, $cookie="cookie.txt" ) 
{
  $ch = curl_init(); 
  curl_setopt ($ch, CURLOPT_URL, $url); 
  curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
  curl_setopt ($ch, CURLOPT_TIMEOUT, 160); 
  curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
  curl_setopt ($ch, CURLOPT_COOKIEJAR, $cookie); 

  curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata); 
  curl_setopt ($ch, CURLOPT_POST, 1); 

  return $ch;
}
function curl_exec_return($ch)
{
  $result = curl_exec ($ch); 
  curl_close($ch);
  return $result;
}
function execute_multi_threaded_curl($curl_multi_handle, $curl_array, $result_callback=null, $final_callback=null)
{

          $running = NULL; 
        do { 
            usleep(5000); 
            curl_multi_exec($curl_multi_handle,$running); 
        } while($running > 0); 
        
        $res = array(); 
        foreach($curl_array as $i => $ch) 
        { 
          $result = curl_multi_getcontent($curl_array[$i]); 
          $url = curl_getinfo($curl_array[$i], CURLINFO_EFFECTIVE_URL);

          if (null !== $result_callback){
            $result = call_user_func($result_callback, $result);
            $res[$i] = $result;
          }
          else 
            $res[$i]["result"] = $result;
        } 
        
        foreach($curl_array as $i => $ch) {
            curl_multi_remove_handle($curl_multi_handle, $curl_array[$i]); 
        } 
        curl_multi_close($curl_multi_handle);
        echo "size of curl_array in execute_multi_threaded_curl is: ".count($curl_array)."\n";
        if (null !== $final_callback){
          $res = call_user_func($final_callback, $res);
        }
        return $res; 
}


function prep_multi_threaded_curl($mh, $nodes, &$curl_array){ 

        $i=count($curl_array);// for zero Index.  can be chained, hooray!
        foreach($nodes as $url) 
        { 
            $curl_array[$i] = curl_init($url); 
            curl_setopt($curl_array[$i], CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($curl_array[$i], CURLOPT_FOLLOWLOCATION, true);
            curl_multi_add_handle($mh, $curl_array[$i]); 
        } 
        echo "prepped $i nodes"."\n";
        return $mh;
        
} 
