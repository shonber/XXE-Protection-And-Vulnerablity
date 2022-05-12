/* The first section is the vulnerable code.
* The LIBXML_NOENT & LIBXML_DTDLOAD are making the code not safe and also are added because they are no longer as a default turned on.
* LIBXML_NOENT => Can execute and run external files.
* LIBXML_DTDLOAD => Can execute and run external DTD files.
*/

<?php 
	$xmlfile = file_get_contents('php://input'); 
	
	$dom = new DOMDocument(); 
	//XXE  LIBXML_NOENT | LIBXML_DTDLOAD
	$dom->loadXML($xmlfile, LIBXML_NOENT | LIBXML_DTDLOAD); 
	
	$creds = simplexml_import_dom($dom); 
	$user = $creds->user; 
	$pass = $creds->pass;
		
	if (strpos($_SERVER['HTTP_REFERER'] , 'page1') !== false)
	{
		echo "Login successfully, Welcome $user";
	}
	else if (strpos($_SERVER['HTTP_REFERER'] , 'page2') !== false)
	{
		echo "Login successfully, Welcome";
	}
	
?>

/* The second section is the fixed one.
* After removing the LIBXML_NOENT & LIBXML_DTDLOAD and adding a logged on cookie, Hackers are no longer able to run external files and DTD files.
*/

<?php 
	if (isset($_COOKIE['loggedIn']) && $_COOKIE['loggedIn'] == "True") {
		die("Already logged in!");
	}

	$xmlfile = file_get_contents('php://input'); 
	
	$dom = new DOMDocument(); 
	$dom->loadXML($xmlfile); 
	
	$creds = simplexml_import_dom($dom); 
	
	$user = $creds->user;
	$pass = $creds->pass;

	if (strpos($_SERVER['HTTP_REFERER'] , 'stage1') !== false)
	{		
		setcookie("loggedIn","True", time()+3600, "/","");
		echo "Login successfully, Welcome " . $user;
	}
	else if (strpos($_SERVER['HTTP_REFERER'] , 'stage2') !== false)
	{
		echo "Login successfully, Welcome";
	}
?>
