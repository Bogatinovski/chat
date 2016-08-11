<?php

function generateImgUrlFromName($img, $company_id, $folder){
   $path = "";
    if($img==DEFAULT_IMG_NAME)
        $path = DEFAULT_IMG_PATH;
    else $path = PROFILE_URL."{$company_id}/images/{$folder}/{$img}";
    return $path;
}
function generateUserImgUrlFromName($img, $unique_id, $folder){
   $path = "";
     if($img==DEFAULT_IMG_NAME)
        $path = DEFAULT_IMG_PATH;
    else{
         $path = USERS_URL."{$unique_id}/images/{$folder}/{$img}"; 
    } 
    return $path;
}
function extractImgNameFromUrl($url){
    $array = explode("/", $url);
    $size = count($array);
    $last_part = $array[$size-1];
    return $last_part;
}

function strip_zeros_from_date( $marked_string="" ) {
  // first remove the marked zeros
  $no_zeros = str_replace('*0', '', $marked_string);
  // then remove any remaining marks
  $cleaned_string = str_replace('*', '', $no_zeros);
  return $cleaned_string;
}

function redirect_to( $location = NULL) {
  if ($location != NULL) {
    header("Location: {$location}");
    exit;
  }
}

function log_action($action, $message="") {
	$logfile = SITE_ROOT.DS.'logs'.DS.'log.txt';
	$new = file_exists($logfile) ? false : true;
  if($handle = fopen($logfile, 'a')) { // append
    $timestamp = strftime("%Y-%m-%d %H:%M:%S", time());
		$content = "{$timestamp} | {$action}: {$message}\n";
    fwrite($handle, $content);
    fclose($handle);
    if($new) { chmod($logfile, 0755); }
  } else {
    echo "Could not open log file for writing.";
  }
}

function datetime_to_text($datetime="") {
  $unixdatetime = strtotime($datetime);
  return strftime("%B %d, %Y at %I:%M %p", $unixdatetime);
}

function array_push_assoc($array, $key, $value){
     $array[$key] = $value;
     return $array;
    }

function exitWithError($header, $exitMessage)
{
  header($header);
  exit($exitMessage);
}

function addVisit($company_id)
{
    global $db;
    $sql = "UPDATE `statistics` SET `total_visits` = `total_visits`+1 WHERE `company_id`=?";
      
    $stmt = $db->prepare($sql);
    $stmt->bind_param('i', $company_id);
    $stmt->execute();
}

function getProfileDir($company_id)
{
    return $company_id;
}

function checkProfileExistance($company_id){
  global $db;
  $sql = "SELECT `company_id` FROM `companies` WHERE `company_id`= ? LIMIT 1";
  
    $stmt = $db->prepare($sql);
    $stmt->bind_param('i', $company_id);
    $stmt->execute();
    $stmt->bind_result($c);

   if(!$stmt->fetch())
   {
      $stmt->close();
      return false;
   }
        
  
  if(!$stmt->affected_rows)
    return false;
  return true;
}

function checkUserExistance($user){
     global $db;
     $sql = "SELECT `unique_id` FROM `accounts` WHERE `unique_id`= ? LIMIT 1";
  
    $stmt = $db->prepare($sql);
    $stmt->bind_param('i', $user);
    $stmt->execute();
    $stmt->bind_result($c);

   if(!$stmt->fetch())
   {
      $stmt->close();
      return false;
   }
  
  if(!$c)
    return false;
  return true;
}

  function require_admin_login($company_id)
  {
      global $db;
      global $session;
    
      if(!$session->is_logged_in())
      {
          redirect_to(URL . "public/register/register.php");
          return false;
      }

     $sql = "SELECT `unique_id` FROM `manage` WHERE `company_id`=? AND `unique_id`=? LIMIT 1";
      
      $stmt = $db->prepare($sql);
      $stmt->bind_param('ii', $company_id, $session->unique_id);
      $stmt->execute();
      $stmt->bind_result($id);
      $stmt->fetch();

      if(!$id || $id != $session->unique_id)
      {
          $stmt->close();
          return false;
      }

      $stmt->close();
      return true;
  }
?>