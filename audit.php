<?php

function generateArchive($files) {

	$epoch=time();
	$archive_destination_path="/tmp/";
	$archive_name="conf_and_logs_$epoch.tar.gz";
	$full_archive_path = $archive_destination_path.$archive_name;
	$zipping_error = shell_exec("tar -czvf $full_archive_path $files");

	if (file_exists($full_archive_path)) {
                audit_log("Zipped archive generated successfully $full_archive_path");
        }
        else {
                audit_log("Zipped archive generation failed ! \ntar -czvf $full_archive_path $files : $zipping_error");
	}
    return $full_archive_path;
}

function generateAudit() {
	
	$audit_scrpit_path   = getAuditScript();
	$audit_result_folder = "/tmp";
	$epoch               = time();
	$audit_result_path   = "$audit_result_folder/$epoch-gorgoneaudit.md";
	$audit_options       = "--markdown=$audit_result_path";
	$timeout_cmd         = "/bin/timeout";
	$audit_timeout       = "1000";
	$audit_command       = "$timeout_cmd $audit_timeout $audit_scrpit_path $audit_options";

	audit_log("Generating audit ...");
	$output = shell_exec($audit_command);
	audit_log("Audit output : $output");
	
	if (file_exists($audit_result_path)) {
		audit_log("Audit file generated successfully $audit_result_path");
	}
	else {
        	audit_log("Audit file generation failed !");
	}	

	return $audit_result_path;
}


function getAuditScript() {

	$url = "https://raw.githubusercontent.com/centreon/centreon-gorgone/develop/contrib/gorgone_audit.pl";
	$directory_audit_script = "/usr/share/centreon/www/include/Administration/parameters/debug/";	
	$audit_script_name = "gorgone_audit.pl";
	$audit_scrpit_path = $directory_audit_script.$audit_script_name;
	
	if (!file_exists($audit_scrpit_path)) {
        	audit_log("Audit Script not found !");
        	audit_log("Downloading audit script from $url");
		
		// Get content from url without setting allow_url_fopen=1
        	$curlSession = curl_init();
        	curl_setopt($curlSession, CURLOPT_URL, $url);
        	curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
        	curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);

        	$audit_script = curl_exec($curlSession);
		curl_close($curlSession);

		if (file_put_contents($audit_scrpit_path, $audit_script)){
                	audit_log("Audit script downloaded successfully");
                	audit_log("Audit script path is : $audit_scrpit_path");
  
                	shell_exec("chmod +x $audit_scrpit_path");
                	audit_log("Execution right added to $audit_scrpit_path");
        	}
        	else {
                	audit_log("Audit script download failed.");
        	}
	}
	else {
	        audit_log("Audit Script found !");
	}
		
     return $audit_scrpit_path;
}

function audit_log(string $message){
	
	$epoch = time();
	$datetimeFormat = 'd-m-Y H:i:s';

	$date = new \DateTime();
	$date->setTimestamp($epoch);
	$formated_date = $date->format($datetimeFormat);
	
	$log_message = "[$formated_date] $message\n";
	$log_file = "/var/log/centreon/get_platform_log_and_info.log";

	file_put_contents($log_file, $log_message, FILE_APPEND | LOCK_EX);
}

$end_screen = "end_screen.html";

// files to archive
$centreon_conf_path = "/etc/centreon*";
$cron_jobs_path     = "/etc/cron.d";
$centreon_logs      = "/var/log/centreon*/*.log";
$messages_logs      = "/var/log/messages";
$syslog_logs        = "/var/log/syslog";
$php_logs           = "/var/log/php-fpm/*.log";
$apache_logs_rhel   = "/var/log/httpd/*log";
$apache_logs_deian  = "/var/log/apache2/*log";

$audit_file = generateAudit();

$files_to_archive = "$centreon_conf_path $cron_jobs_path $centreon_logs $php_logs $messages_logs $audit_file";
$archive=generateArchive($files_to_archive);

include('ending_screen.html');
?>

<script>
	window.location.replace('download.php?audit_file=<?=$archive?>');
</script>