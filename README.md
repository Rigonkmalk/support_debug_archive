# Support debug archive tool
Download a zipped archive with the platform's information and logs to identify issues easier.

## How to install ?

1. Intall git :
  - RHEL : ```yum install git``` 
  - Debian : ```apt update && apt install git```
2. Clone this repository on to your centreon central server :
  - ```git clone https://github.com/ykacherCentreon/support_debug_archive.git ~/support_debug_archive```  <br />
  In case you are not able to do so from your central server (e.g., no internet access), transfer the downloaded files via sftp or a drive and proceed with the steps below.
3. Backup the content of /usr/share/centreon/www/include/Administration/parameters/debug : 
  - ```sudo -u centreon /bin/cp -r /usr/share/centreon/www/include/Administration/parameters/debug{,.origin}```
4. Copy all the files from this repo in 
/usr/share/centreon/www/include/Administration/parameters/debug :
  - ```/bin/cp ~/support_debug_archive/* /usr/share/centreon/www/include/Administration/parameters/debug && chown -R centreon: /usr/share/centreon/www/include/Administration/parameters/debug/*```
5. Change the ```ProxyTimeout``` setting of apache to ```1000``` in the configuration file :
  - Path for RHEL   : ```/etc/httpd/conf.d/10-centreon.conf```
    - Restart apache
      - ```systemctl restart httpd```
  - Path for Debian : ```/etc/apache2/sites-available/centreon.conf```
    - Restart apache
      - ```systemctl restart apache2```
6. Add the sudoers command below :
  ```
  cat <<EOF > /etc/sudoers.d/support_debug_archive
User_Alias      HTTP_USERS=apache,www-data
Defaults:HTTP_USERS !requiretty

HTTP_USERS   ALL = (ALL) NOPASSWD: /bin/tar -czvf *

EOF
  ``` 
7. Enjoy, go to " Administration  >  Parameters  >  Debug " :

<img alt="image" src="https://github.com/ykacherCentreon/support_debug_archive/assets/85548802/ba40fe1c-b8b1-4b93-9e5e-8106e5ad8c7e">

## How to update ?

Remove the ``~/support_debug_archive`` directory created upon install and execute the same steps as for the intallation except for 1,3,5; you can skip those :
 - ``rm -rf ~/support_debug_archive``

## Log

In case there is any issue with the tool, check the log files below for clues :
- ```/var/log/centreon/get_platform_log_and_info.log```
- ```/var/log/php-fpm/centreon-error.log```
- RHEL : ```grep -Rni "timeout" /var/log/httpd```
- Debian : ```grep -Rni "timeout" /var/log/apache2/```

## Known Issues and solutions

NOTE : The more poller you have, the longer the audit will take.  
 - You can refer to the upper step 5 to increase the timeout value or the step 5 below to exclude the audit of your pollers and speed up the archive generation. 
  
- Hanging on "Generating, please wait 😁" ?
1. Refresh the page and try again, this can happens right after installation.
2. If you still encounter the issue, please check if you have a timeout related to /usr/share/centreon/www/include/Administration/parameters/debug/audit.php :<br />
    - RHEL : ```grep -Rni "/parameters/debug/" /var/log/httpd/```<br />
    - Debian : ```grep -Rni "/parameters/debug/" /var/log/apache2/```
3. You can kill the hanging process :
     - ```ps -ef | grep gorgone_audit.pl | grep -v grep | awk '{print $2}' && (kill -TERM $!; sleep 1; kill -9 $!)```
4. Refresh the page and try again.
5. Still stuck ? 
   1. You can adapt the timeout value for the audit generation at line 34 of /usr/share/centreon/www/include/Administration/parameters/debug/functions.php :  
   ```$audit_timeout       = "60"; // if you have a lot of poller you may ajust this value```
   2. Or bypass the audit generation :
     - Comment the line 24 in /usr/share/centreon/www/include/Administration/parameters/debug/audit.php :
     ```php   
      23 ...
      24 //$conf_and_log_files_to_archive [] = generateAudit();
      25 ...
      ```
     - Kill the hanged process (refer to step 3).