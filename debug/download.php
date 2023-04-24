<?php

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

$my_audit_file=$_GET["audit_file"];

download_audit($my_audit_file);

die;
?>
