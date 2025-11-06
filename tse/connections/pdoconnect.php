<?php


/*header("ETag: PUB" . time());
		header("Last-Modified: " . gmdate("D, d M Y H:i:s", time()-10) . " GMT");
		header("Expires: " . gmdate("D, d M Y H:i:s", time() + 5) . " GMT");
		header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
		header('Cache-Control: post-check=0, pre-check=0',false);
		header("Pragma: no-cache");
		header("Cache-Control: max-age=1, s-maxage=1, no-cache, must-revalidate");
		session_cache_limiter("nocache");
		*/

if (!isset($_SESSION)) {
	session_start();
}

date_default_timezone_set("Asia/Taipei");

$hd1 = "President Ramon Magsaysay State University";
$hd2 = "Candelaria Campus, Candelaria, Zambales, Philippines";
//$hd3="<i class='bi-telephone-fill'></i> +63 xxx xxxx <i class='bi-mailbox2'></i> administrator@$app_title.com";
$hd3 = "<i class='bi-mailbox2'></i> administrator@tse.com";
$hd4 = "<i class='ri-chrome-fill'></i> ";
$hd5 = "";

$app_title = "TSE";
$app_sub_title = $hd1;
$app_address_contact = $hd2 . ' ' . $hd3;
$app_footer = "Developed by Johnrey Ednave * John Clyde Ebilane * James Bryan Famisan * Jonard Mas. ver. 1.0.0.0";
$app_copyright = date('Y');
$app_header_footer_background = "slategray";
$app_background_scheme = "slategray";
$app_home_image = "../images/front.png";
$app_user_image_default = "../images/logo.png";
$app_login_background = "../images/front.png";
$tagline = "Tabulation System for Events and Competetion for PRMSU Candelaria Campus";
$font_size_print = "12px";
$font_size_print_11 = "11px";
$app_email = "";


error_reporting(E_ALL);

require_once('../lib/phplib/phpdotenv/autoload.php');
//$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv = Dotenv\Dotenv::createImmutable('../../tse_env/');
$dotenv->safeLoad();


class DatabaseConnect
{

	private $host      = null;
	private $user      = null;
	private $pass      = null;
	private $dbname    = null;
	private $port      = null;

	private $dbh;
	private $error;

	private $stmt;

	public function __construct()
	{

		$this->host      = $_ENV['host'];
		$this->user      = $_ENV['user'];
		$this->pass      = $_ENV['pass'];
		$this->dbname    = $_ENV['dbname'];
		$this->port      = $_ENV['port'];

		// Set DSN
		$dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';port=' . $this->port . ';charset=utf8';
		// Set options
		$options = array(
			PDO::ATTR_PERSISTENT    => true,
			PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION
		);
		// Create a new PDO instanace
		try {
			$this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
		}
		// Catch any errors
		catch (PDOException $e) {
			echo 'Cannot connect to your database server.';
		}
	}

	public function query($query)
	{
		$this->stmt = $this->dbh->prepare($query);
	}

	public function execute()
	{
		return $this->stmt->execute();
	}

	public function bind($param, $value, $type = null)
	{
		if (is_null($type)) {
			switch (true) {
				case is_int($value):
					$type = PDO::PARAM_INT;
					break;
				case is_bool($value):
					$type = PDO::PARAM_BOOL;
					break;
				case is_null($value):
					$type = PDO::PARAM_NULL;
					break;

				default:
					$type = PDO::PARAM_STR;
			}
		}
		$this->stmt->bindValue($param, $value, $type);
	}

	public function rowset()
	{
		$this->execute();
		return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function rowsingle()
	{
		$this->execute();
		return $this->stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function rowcount()
	{
		return $this->stmt->rowCount();
	}

	public function lastinsertid()
	{
		return $this->dbh->lastInsertId();
	}


	public function begintransaction()
	{
		return $this->dbh->beginTransaction();
	}

	public function endtransaction()
	{
		return $this->dbh->commit();
	}

	public function canceltransaction()
	{
		return $this->dbh->rollBack();
	}

	public function debugdumpparams()
	{
		return $this->stmt->debugDumpParams();
	}

	public function close()
	{
		try {
			$this->dbh = null;
		} catch (PDOException $e) {
			echo 'Cannot close your database connection.';
		}
	}
}



class php_util
{
	//"set_consumer_details();"
	//"set_inspection_report();"
	//"set_approved_payment();"
	//"set_payment_details();"
	//"set_turn_on_order();"
	//set_meter_details();"



	public function delTree($dir)
	{
		$files = array_diff(scandir($dir), array('.', '..'));
		foreach ($files as $file) {
			(is_dir("$dir/$file") && !is_link($dir)) ? delTree("$dir/$file") : unlink("$dir/$file");
		}
		return rmdir($dir);
	}

	public function encryptMessage($msgToEncrypt)
	{

		$paddedMessage = sodium_pad($msgToEncrypt, $_ENV['blocksize']);
		$encryptedMessage = sodium_crypto_secretbox($paddedMessage, $_ENV['nonce'], $_ENV['key']);
		$encryptedMessage = base64_encode($encryptedMessage);
		return $encryptedMessage;
		#return 'key='.$key.' / nonce='.$nonce.' / blocksize='.$blockSize;
	}

	public function decryptMessage($msgToDecrypt)
	{

		$encryptedMessage = base64_decode($msgToDecrypt);
		$decryptedPaddedMessage = sodium_crypto_secretbox_open($encryptedMessage, $_ENV['nonce'], $_ENV['key']);
		$decryptedMessage = sodium_unpad($decryptedPaddedMessage, $_ENV['blocksize']);
		return $decryptedMessage;
	}


	public function get_active_menu($session_menu_name, $click_menu_name)
	{
		if (!(strcmp($session_menu_name, $click_menu_name))) {
			return 'active';
		}
	}

	public function set_button_group($group, $filename)
	{
		$phu = new php_util();
		$menu_id=$phu->get_menu_id($filename);

		$sql_string = "select r.id from `user_restriction` as `r`  where r.`group`=? and r.menu_id=?";

		$temp_db = new DatabaseConnect();
		$temp_db->query($sql_string);
		$temp_db->bind(1, $group);
		$temp_db->bind(2, $menu_id);
		$temp_rs = $temp_db->rowsingle();
		$found = $temp_db->rowcount();
		$temp_db->close();

		if ($found > 0) {
			return 1;
		} else {
			return 0;
		}
	}


	public function found_group($group, $menu_id)
	{
		$sql_string = "select r.id from `user_restriction` as `r`  where r.`group`=? and r.menu_id=?";

		$temp_db = new DatabaseConnect();
		$temp_db->query($sql_string);
		$temp_db->bind(1, $group);
		$temp_db->bind(2, $menu_id);
		$temp_rs = $temp_db->rowsingle();
		$found = $temp_db->rowcount();
		$temp_db->close();

		if ($found > 0) {
			return 1;
		} else {
			return 0;
		}
	}

	public function add_log_in_out($user_id, $remarks)
	{

		$SQLcrud = "INSERT INTO user_log (user_id, log_time, remarks) VALUES (?,?,?)";
		$temp_db = new DatabaseConnect();
		$temp_db->query($SQLcrud);
		$temp_db->bind(1, $user_id);
		$temp_db->bind(2, date("Y-m-d H:i:s"));
		$temp_db->bind(3, $remarks);
		$temp_db->execute();
		$temp_db->close();
	}

	/*public function add_log_our($user_log_id,$remarks){
		$SQLcrud = "UPDATE user_log SET log_out=?,remarks=? WHERE user_log_id=?";

		$db->query($SQLcrud);
		$db->bind(1,date());
		$db->bind(2,$user_log_id);
		$db->bind(3,$remarks);
		$db->execute();
	}*/

	public function get_menu_id($filename)
	{
		$sql_string = "select u.id,u.name from user_menu u where u.`href` LIKE ?";

		$temp_db = new DatabaseConnect();
		$temp_db->query($sql_string);
		$temp_db->bind(1, "%" . $filename);
		$temp_rs = $temp_db->rowsingle();
		$found = $temp_db->rowcount();
		$temp_db->close();

		if ($found > 0) {
			$_SESSION['title'] = $temp_rs['name'];
			return $temp_rs['id'];
		} else {
			return 0;
		}
	}

	public function find_restriction($group, $menu_id)
	{
		$temp_db = new DatabaseConnect();
		$query_check = "SELECT * FROM user_restriction WHERE `group`=? and menu_id=?";
		$temp_db->query($query_check);
		$temp_db->bind(1, $group);
		$temp_db->bind(2, $menu_id);
		$rscheck = $temp_db->rowsingle();
		$totalRows_rscheck = $temp_db->rowcount();
		$temp_db->close();
		if ($totalRows_rscheck > 0)
			return "checked";
	}
	public function wordlimit($source, $maxlength)
	{
		if (strlen($source) > $maxlength) {
			$stringCut = substr($source, 0, $maxlength);
			$source = substr($stringCut, 0, strrpos($stringCut, ' '));
			if (empty($source)) {
				$source = $stringCut;
			}
		}

		return $source;
		//usage: 
		//$objVar=new php_util();
		//$o=$objVar->wordlimit("any_string_that_you_want_to_limit",20);
		//echo $o;
	}

	public function formula_reader($result_variable, $formula)
	{
		// convert the string $result_variable to a global variable
		global ${$result_variable};
		// adds $ symbol in front of $result_variable and
		// = after $result_variable so if for example $result_variable contains "f"  
		//then it will become $f=
		$v = "\$" . $result_variable . "=";
		// concatenate $f= with $formula if for example $formula is equal to 
		// "($present-$previous)*$kwh;" then it becomes  
		// $f="($present-$previous)*$kwh;"
		eval($v . $formula);
	}
	//usage: 
	//$var1=5;
	//$var2=7;
	//$objVar=new php_util();
	//$objVar->formula_reader("var","$var1+$var2;");
	//echo $var;

	function recursiveRemoveDirectory($directory)
	{
		foreach (glob("{$directory}/*") as $file) {
			if (is_dir($file)) {
				recursiveRemoveDirectory($file);
			} else {
				unlink($file);
			}
		}
		rmdir($directory);
	}


	function numberTowords($num)
	{
		$ones = array(
			0 => "",
			1 => "one",
			2 => "two",
			3 => "three",
			4 => "four",
			5 => "five",
			6 => "six",
			7 => "seven",
			8 => "eight",
			9 => "nine",
			10 => "ten",
			11 => "eleven",
			12 => "twelve",
			13 => "thirteen",
			14 => "fourteen",
			15 => "fifteen",
			16 => "sixteen",
			17 => "seventeen",
			18 => "eighteen",
			19 => "nineteen"
		);
		$tens = array(
			1 => "ten",
			2 => "twenty",
			3 => "thirty",
			4 => "forty",
			5 => "fifty",
			6 => "sixty",
			7 => "seventy",
			8 => "eighty",
			9 => "ninety"
		);
		$hundreds = array(
			"hundred",
			"thousand",
			"million",
			"billion",
			"trillion",
			"quadrillion"
		); //limit t quadrillion 
		$num = number_format($num, 2, ".", ",");
		$num_arr = explode(".", $num);
		$wholenum = $num_arr[0];
		$decnum = $num_arr[1];
		$whole_arr = array_reverse(explode(",", $wholenum));
		krsort($whole_arr);
		$rettxt = "";
		foreach ($whole_arr as $key => $i) {
			if ($i < 20) {
				$rettxt .= $ones[$i];
			} elseif ($i < 100) {
				$rettxt .= $tens[substr($i, 0, 1)];
				$rettxt .= " " . $ones[substr($i, 1, 1)];
			} else {
				$rettxt .= $ones[substr($i, 0, 1)] . " " . $hundreds[0];
				$rettxt .= " " . $tens[substr($i, 1, 1)];
				$rettxt .= " " . $ones[substr($i, 2, 1)];
			}
			if ($key > 0) {
				$rettxt .= " " . $hundreds[$key] . " ";
			}
		}

		if ($decnum > 0) {
			$rettxt .= " and ";
			if ($decnum < 20) {
				$rettxt .= $ones[$decnum];
			} elseif ($decnum < 100) {
				$rettxt .= $tens[substr($decnum, 0, 1)];
				$rettxt .= " " . $ones[substr($decnum, 1, 1)];
			}
		}
		return $rettxt;
	}
}
