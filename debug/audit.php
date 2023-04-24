<?php

function generateAudit($audit_scrpit_path, $audit_result_folder) {

	$epoch=time();
	$audit_result_path = "$audit_result_folder/$epoch-gorgoneaudit.md";
	$audit_options = "--markdown=$audit_result_path";
		
	audit_log("Generating audit ...");
	$output = shell_exec("$audit_scrpit_path $audit_options");
	audit_log("Audit output : $output");
	
	if (file_exists($audit_result_path)) {
		audit_log("Audit file generated successfully $audit_result_path");
	}
	else {
        	audit_log("Audit file generation failed !");
	}	

	return $audit_result_path;
}


function getAuditScript($url, $file_path) {
	
	audit_log("Downloading audit script from $url");
	if (file_put_contents($file_path, file_get_contents($url))){
		audit_log("File downloaded successfully");
        	audit_log("Audit script donwloaded in $file_path");
  
		shell_exec("chmod +x $file_path");
        	audit_log("Execution right added to $file_path");
    	}
    	else
    	{
        	audit_log("File downloading failed.");
    	}
}

function audit_log(string $message){

	$epoch=time();
	$log_message = "[$epoch] $message\n";
	$log_file = "/var/log/php-fpm/get_platform_log_and_info.log";

	file_put_contents($log_file, $log_message, FILE_APPEND | LOCK_EX);

}

function download_audit($audit_file){

	//Check the file exists or not
	if(file_exists($audit_file)) {
		
		//Define header information
		header( "Content-Description: File Transfer" );
		header('Content-Type: text/markdown');
		header('Expires: 0');
      		header('Cache-Control: must-revalidate');
      		header('Pragma: public');
		header( "Content-Length: " . filesize($audit_file) );
		header('Content-Disposition: attachment; filename="'.basename($audit_file).'"');
		ob_clean();
		flush();
		readfile($audit_file);
	}
	else{
		audit_log("File downloading failed. File '$audit_file' does not exist");
	}
}

$directory_audit_script = "/usr/share/centreon/bin/";
$audit_script_name = "gorgone_audit.pl";
$audit_scrpit_path = $directory_audit_script.$audit_script_name;
$audit_script_get_url = "https://raw.githubusercontent.com/centreon/centreon-gorgone/develop/contrib/gorgone_audit.pl";
$log_file = "/var/log/php-fpm/get_platform_log_and_info.log";
$output_path = "/usr/share/centreon/bin/gorgone_audit.pl";
$result_path = "/var/lib/centreon/audits";
$end_screen = "end_screen.html";

if (!file_exists($audit_scrpit_path)) {	
	audit_log("Audit Script not found !");
	getAuditScript($audit_script_get_url, $output_path);
}
else {
	audit_log("Audit Script found !");
}

$audit_file = generateAudit($audit_scrpit_path, $result_path);
include('ending_screen.html');
$_POST["audit_file"] = $audit_file;

?>

<script>
window.location.replace('download.php?audit_file=<?=$_POST["audit_file"]?>');
</script>
