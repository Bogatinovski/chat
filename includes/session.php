<?php
class Session {
	
	private $logged_in=false;
  public $country_id;
  public $company_name;
  public $profile_dir;
	public $email;
  public $current_page;
  public $errors;
  public $default_country;
  public $unique_id;
  public $first;
  public $last;
  public $img;

	function __construct() {
		session_start();
		$this->check_login();
    $this->check_profile();
    $this->check_current_page();
    $this->check_errors();
    $this->errors = array();
    $this->checkDefaultCountry();
	}

  public function resetImg()
  {
      global $db;

      $sql = "SELECT `image` FROM `account` WHERE `unique_id`= ? LIMIT 1";
      $stmt = $db->prepare($sql);
      $stmt->bind_param('i', $this->unique_id);
      $stmt->execute();
      $stmt->bind_result($image);
      $stmt->execute();
      $stmt->fetch();
      $stmt->close();
      $path = USERS_URL . "{$this->unique_id}/images/profile/thumb_{$image}";
      $this->img = $path;
      $_SESSION['img'] = $path;
  }

  private function checkDefaultCountry()
  {
      if(isset($_SESSION['default_country']))
        $this->$default_country = $_SESSION['default_country'];
      else
        $this->setDefaultCountry();
  }

  private function setDefaultCountry()
  {
      $this->default_country = 1;
      $session['default_country'] = $this->default_country;
  }

  public function clearErrors()
  {
       $this->errors = array();
       $_SESSION['errors']=$this->errors;
  }

  public function getUniqueId()
  {
      return $this->unique_id;
  }

  public function addError($error)
  { 
      $this->errors = $_SESSION['errors'];
      array_push($this->errors, $error);
      $_SESSION['errors'] = $this->errors;
  }

  private function check_errors()
  {
      if(isset($_SESSION['errors']))
          $this->errors = $_SESSION['errors'];
      else $this->errors= $_SESSION['errors'] =array();
  }


  public function setCurrentPage($page)
  {
      $this->current_page = $page;
      $_SESSION['current_page'] = $page;
  }

  public function setProfile($country_id, $company_name)
  {
      $this->setCountryId($country_id);
      $this->setCompanyName($company_name);
      $this->setProfileDir();
  }

  private function setProfileDir()
  {
      $this->profile_dir = $this->country_id.'_'.$this->company_name;
      $_SESSION["profile_dir"] = $this->profile_dir;
  }

  public function getProfileDir()
  {
      return $this->profile_dir;
  }

  private function setCountryId($country_id)
  {
      $this->country_id = $country_id;
      $_SESSION['country_id'] = $country_id;
  }

  private function setCompanyName($company_name)
  {
      $this->company_name = $company_name;
      $_SESSION['company_name'] = $company_name;
  }

  public function is_logged_in() {
    return $this->logged_in;
  }

	public function login($email) {
    global $db;

    if($email){     
      
      $sql = "SELECT `unique_id`, `first_name`, `last_name` FROM `users` WHERE `email`= ? LIMIT 1";
      $stmt = $db->prepare($sql);
      $stmt->bind_param('s', $email);
      $stmt->execute();
      $stmt->bind_result($id, $first, $last);
      $stmt->fetch();
      $stmt->close();

      $this->logged_in = true;
      $this->email = $email;
      $_SESSION['email'] = $email;
    //  $path = USERS_URL . "{$id}/images/profile/thumb_{$image}";
      $path="";
      $this->setUniqueId($id);
      $this->setUserInfo($first, $last, $path);
      return true;
    }
    return false;
  }

  private function setUniqueId($id){
      $this->unique_id = $id;
      $_SESSION['unique_id'] = $id;
  }

  private function setUserInfo($first, $last, $img){
    $this->$first = $first;
    $this->last = $last;
    $this->img = $img;
    $_SESSION['first'] = $first;
    $_SESSION['last'] = $last;
    $_SESSION['img'] = $img;
  }
  
  public function logout() {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
      $params["path"], $params["domain"],
      $params["secure"], $params["httponly"]
    );
    session_destroy();
    $this->logged_in = false;
  }

	private function check_login() {
    if(session_id() && isset($_SESSION['unique_id'])) {
      $this->email = $_SESSION['email'];
      $this->unique_id = $_SESSION['unique_id'];
      $this->first = $_SESSION['first'];
      $this->last = $_SESSION['last'];
      $this->img = $_SESSION['img'];
      $this->logged_in = true;
    } else {
      unset($this->email);
      unset($this->unique_id);
      unset($this->first);
      unset($this->last);
      unset($this->img);
      $this->logged_in = false;
    }
  }

  private function check_current_page()
  {
      if(isset($_SESSION['current_page']))
        $this->current_page = $_SESSION['current_page'];
      else
        unset($this->current_page);
  }

  private function check_profile(){
    if(isset($_SESSION['company_name'])) {
      $this->setProfile($_SESSION['country_id'], $_SESSION['company_name']);
    } else {
      unset($this->company_name);
      unset($this->country_id);
      unset($this->profile_dir);
    }
  }

  public function require_login()
  {
   // echo $_SESSION['email'];
      if(!$this->logged_in)
      {
          redirect_to(URL . "public/register/register.php");
      }
  }

  public function getErrors()
  {
    $html = "<ul class='errors'>";
    $this->errors = $_SESSION['errors'];
    foreach($this->errors as $error)
      $html .= "<li>{$error}</li>";
    $html .= "</ul>";
    return $html;
  }
	
}

$session = new Session();
?>