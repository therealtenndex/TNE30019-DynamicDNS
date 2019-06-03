<html>
	<head>
	<title>DNS Records</title>
	<meta charset="UTF-8">
	<meta name="name" content="Anastasios Petropoulos">
	<meta name="description" content="Assignment">
	<meta name="pagedescription" content="Page for editing DNS records">
</head>
<body>
	<h1>Unix Telecommunication - Add/Remove DNS Record</h1>
	<h2>By Anastasios Petropoulos - 100581495</h2>
	<form method="POST" novalidate>
	<br>
	<fieldset style="width:350px">
	<legend>Zone Location</legend>
		Zone Directory (Check Zone Files):<br><input type="text" name="zonedirectory" formnovalidate placeholder="EG: /usr/local/etc/namedb/working/" required size="50"><br><br>
		<input type="submit" value="Update Directory" name="updatedirectory">
	</fieldset><br>
	<fieldset style="width:350px" novalidate>
		<legend>DNS Information</legend>
		Server: <br><input type="text" name="Server" formnovalidate placeholder="EG: rule94.caia.swin.edu.au" required size="50"><br>
		IP Address:<br> <input type="text" name="IPAddress" formnovalidate placeholder="EG: 192.168.0.1" required size="50"><br> <!--IP Address field-->
		Physical Address :<br> <input type="text" name="Address" formnovalidate placeholder="EG: www.swinburne.edu.au" required size="50"><br> <!--Address field-->
		Zone: <br> <input type="text" name="Zone" formnovalidate placeholder="EG: ns1.mydomain" required size="50"><br> 
		Time To Live (TTL):<br><input type="text" name="TTL" formnovalidate placeholder="EG: 86400" required size="50"><br><br>
		Record Type:<br><input type="radio" name="recordtype" value="A" formnovalidate>A Record<br><input type="radio" name="recordtype" value="CNAME">CNAME Record
		<br><br>	
		<input type="submit" value="Update Record" name="update">
		<input type="submit" value="Remove Record" name="remove">
		<input type="reset" value="Reset" name="Clear">
	</fieldset>
	</form> 
	<br>
<?PHP
#Default path for zone file
$path ="/usr/local/etc/namedb/working/";
#Updates the directory from the text field, default to normal directory if not changed
if (isset($_POST["updatedirectory"])) {
	$path =$_POST["zonedirectory"];
	echo '<span style="color:#e6000d;">Zone Directory has been updated</span>';
}
#Code for adding a record to the zone file
if (isset($_POST["update"])) {
	$Server =$_POST["Server"];
	$IPAddress =$_POST["IPAddress"];
	$Address =$_POST["Address"];
	$TTL =$_POST["TTL"];
	$Record =$_POST["recordtype"];
	$Zone =$_POST["Zone"];
	if (!empty($IPAddress) && !empty($Address) && !empty($TTL) && !empty($Record) && !empty($Zone)) {
		#Opens the file for nsupdate inputs, saves inputs to update.txt in /usr/local/www/apache24/data/
		$update =fopen("update.txt", "w")or die("Unable to create/open update.txt, please check directory permissions and configuration");
		exec ("chmod 777 /usr/local/www/apache24/data/update.txt");
		fwrite($update, "server ".$Server."\n");
		fwrite($update, "zone ".$Zone."\n");
		fwrite($update, "update add ".$Address." ".$TTL." ".$Record." ".$IPAddress."\n");
		fwrite($update, "show\n");
		fwrite($update, "send\n");
		fwrite($update, "quit");
		fclose($update);
		echo "<span style='color:#e6000d;'><b>Response: </b></span>";
		#Runs the nsupdate command with input from update.txt
		#Update this command with your own key
		system('/usr/local/bin/nsupdate -k /usr/local/etc/namedb/Kddns-key.+157+22976.private update.txt'); 
		echo "<br><br>";
		echo '<span style="color:#e6000d;"><b>Recorded has been submitted, please check for successfull update.</b></span>';
	}
}
#Code for removing a record from the zone file
if (isset($_POST["remove"])) {
	$IPAddress =$_POST["IPAddress"];
	$Address =$_POST["Address"];
	$Record =$_POST["recordtype"];
	if (!empty($IPAddress) && !empty($Address) && !empty($Record)) {
		$remove =fopen("remove.txt", "w")or die("Unable to create/open remove.txt, please check directory/file permissions and configuration");
		exec ("chmod 777 /usr/local/www/apache24/data/remove.txt");
		fwrite($remove, "server rule94.caia.swin.edu.au\n");
		fwrite($remove, "zone ns1.mydomain\n");
		#Add line here	fwrite($remove, stuff);
		fwrite($remove, "show\n");
		fwrite($remove, "send\n");
		fwrite($remove, "quit");
		fclose($remove);
		#Runs the nsupdate command with input from remove.txt	
		echo "<span style='color:#e6000d;'><b>Response :</b></span>";
		system("/usr/local/bin/nsupdate -k /usr/local/etc/namedb/Kddns-key.+157+22976.private remove.txt");
		echo "<br><br>";
		echo "<span style='color:#e6000d;'><b>Record has been submitted, please check for successfull update.</b></span>";
	}
}

#Code to view all records
$jnlcheck = "forward-zone-file.jnl";
$swpcheck = ".forward-zone-file.swp";

echo "<h2>Current Zones - Refresh page for updated list</h2>";
$filelist = scandir($path);
unset($filelist[0]);
unset($filelist[1]);
#Retrieves all fileames from the path set in $path
foreach ($filelist as $name) {
	$result = $path . $name;
	#Skips opening .jnl files which display errors on the index page
	if ($name == $jnlcheck)  {
		continue;
	}
	if ($name == $swpcheck) {
		continue;
	}
	echo "<h3>";
	echo "Name: ".$name."<br>";;
	echo "Directory: ".$path;
	echo "</h3>";
	#Loops all filenames and reads the content of each file
	$myfile =fopen($result, "r") or die ("File cannot be opened, please check configuration and permissions");
	while (!feof($myfile)) {
		$line =fgets($myfile);
		echo "<code>";
		echo nl2br($line);
		echo "</code>";
		#Checks for end of file to echo page break for presentation
		if(feof($myfile)) {
			echo "<br>";
		}
	}
}
?>
</body>
</html>
