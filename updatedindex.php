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
	<form method="POST">
	<fieldset style="width:350px" novalidate>
		<legend>DNS Information</legend>
		IP Address:<br> <input type="text" name="IPAddress" formnovalidate placeholder="EG: 192.168.0.1" required size="50"><br> <!--IP Address field-->
		Hostname:<br> <input type="text" name="Hostname" formnovalidate placeholder="EG: www" required size="50"><br> <!--Address field-->
		Zone:<br>
		<select name="Zone">
		<option value=""> </option>
		<option value="ns1.mydomain">ns1.mydomain</option>
		<option value="ns2.otherdomain">ns2.otherdomain</option>
		</select>
		<br>
		<input type="submit" value="Update Record" name="update">
		<input type="submit" value="Remove Record" name="remove">
		<input type="reset" value="Reset" name="Clear">
	</fieldset>
	</form>
<?PHP
#Code for adding a record to the zone file
#Runs commands once update is clicked
if (isset($_POST["update"])) {
	$TTL = 86400;
	$IPAddress =$_POST["IPAddress"];
	$Hostname =$_POST["Hostname"];
	$TTL =$_POST["TTL"];
	$Zone =$_POST["Zone"];
	$IPOctet = array();
	$IPOctet =explode(".",$IPAddress);
	$reverse =array_reverse($IPOctet);
	$Arpa =implode(".",$reverse);
	#Checks if fields are emptry before writing to file
	if (!empty($IPAddress) && !empty($Hostname)  && !empty($Zone)) {
			#Opens the file for nsupdate inputs, saves inputs to update.txt in /usr/local/www/apache24/data/
			$update =fopen("update.txt", "w")or die("Unable to create/open update.txt, please check directory permissions and configuration");
			exec ("chmod 777 /usr/local/www/apache24/data/update.txt");
			fwrite($update, "server rule94.caia.swin.edu.au.\n");
			fwrite($update, "update add ".$Hostname.".".$Zone." 86400 A ".$IPAddress."\n");
			fwrite($update, "\n");
			#Updates the reverse zone if IPv4 matches 136.186.230.0/24
			if ($IPOctet[0]=='136' && $IPOctet[1]=='186' && $IPOctet[2]=='230') {	
				fwrite($update, "update add ".$Arpa.".in-addr.arpa 86400 PTR ".$Hostname.".".$Zone."\n");
			}
			fwrite($update, "send\n");
			fclose($update);
			echo "<span style='color:#e6000d;'><b>Response: </b></span>";
			#Runs the nsupdate command with input from update.txt
			#Update this command with your own key
			system('/usr/local/bin/nsupdate -d -k /usr/local/etc/namedb/Kddns-key.+157+22976.private update.txt'); 
			echo "<br><br>";
			echo '<span style="color:#e6000d;"><b>Recorded has been submitted, please check for successfull update.</b></span>';
	} else {
		echo "<span style='color:#e6000d;'><b>Error: </b></span>Please ensure that all fields are filled before submission"; }
}
#Code for removing a record from the zone file
#Runs commands once remove is clicked
if (isset($_POST["remove"])) {
	$Hostname =$_POST["Hostname"];
	$Zone =$_POST["Zone"];
	$IPAddress =$_POST["IPAddress"];
	$IPOctet = array();
	$IPOctet =explode(".",$IPAddress);
	$reverse =array_reverse($IPOctet);
	$Arpa =implode(".", $reverse);
	#Checks if fields are empty before writing to file
	if (!empty($Hostname) && !empty($Zone)) {
			#Opens the file for nsupdate inputes, saves the inputs to remove.txt in /usr/local/www/apache24/data
			$remove =fopen("remove.txt", "w")or die("Unable to create/open remove.txt, please check directory/file permissions and configuration");
			exec ("chmod 777 /usr/local/www/apache24/data/remove.txt");
			fwrite($remove, "server rule94.caia.swin.edu.au\n");
			fwrite($remove, "update delete $Hostname.$Zone. A\n");
			fwrite($remove, "\n");
			if ($IPOctet[0]=='136' && $IPOctet[1]=='186' && $IPOctet[2]=='230') {
				fwrite($remove, "update delete ".$Arpa.".in-addr.arpa 86400 PTR ".$Hostname.".".$Zone."\n");
			}
			fwrite($remove, "send\n");
			fclose($remove);
			#Runs the nsupdate command with input from remove.txt
			echo "<span style='color:#e6000d;'><b>Response : </b></span>";
			system("/usr/local/bin/nsupdate -d -k /usr/local/etc/namedb/Kddns-key.+157+22976.private remove.txt");
			echo "<br><br>";
			echo "<span style='color:#e6000d;'><b>Record has been submitted, please check for successfull update.</b></span>";
	} else {
		echo "<span style='color:#e6000d;'><b>Error: </b></span>Please ensure that all fields are filled before submission";}
}
?>
</body>
</html>
