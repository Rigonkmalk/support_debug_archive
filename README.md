# Support debug archive tool
Download a zipped archive with the platform's information and logs to identify issues easier.

## How to install ?

1. Download the installation script : 

```
curl -O https://raw.githubusercontent.com/rigonkmalk/support_debug_archive/refs/heads/master/install_debug_archive_generation.sh
```
2. Enable the downloaded script to be executed :

```
chmod u+x install_debug_archive_generation.sh
```
3. Execute the script :

```
./install_debug_archive_generation.sh
```
4. Enjoy, go to " Administration  >  Parameters  >  Debug " :

<img alt="image" src="https://github.com/ykacherCentreon/support_debug_archive/assets/85548802/ba40fe1c-b8b1-4b93-9e5e-8106e5ad8c7e">

## How to update ?

- Follow the same steps as for the installation

## Logs

In case there is any issue with the tool, check the log files below for clues :


```
/var/log/centreon/get_platform_log_and_info.log
/var/log/php-fpm/centreon-error.log
```

RHEL : 


```
grep -Rni "timeout" /var/log/httpd
```
Debian : 

```
grep -Rni "timeout" /var/log/apache2/
```

## Known Issues and solutions
  
- Hanging on "Generating, please wait 😁" ?
1. Time the audit execution : 
```
time /usr/share/centreon/www/include/Administration/parameters/debug/gorgone_audit.pl
```
NOTE : The more pollers you have, the longer the audit will take.
- Change the ```ProxyTimeout``` setting of apache in the configuration file to be greater than the "real" value returned by the previous command :
  - Path for RHEL   : ```/etc/httpd/conf.d/10-centreon.conf```
    - Restart apache
      - ```systemctl restart httpd```
  - Path for Debian : ```/etc/apache2/sites-available/centreon.conf```
    - Restart apache
      - ```systemctl restart apache2```
 
2. Refresh the page and try again, this can happen right after installation.
3. If you still encounter the issue, please check if you have a timeout related to /usr/share/centreon/www/include/Administration/parameters/debug/audit.php :
    - RHEL : ```grep -Rni "/parameters/debug/" /var/log/httpd/```
    - Debian : ```grep -Rni "/parameters/debug/" /var/log/apache2/```
4. You can kill the hanging process :
     - ```ps -ef | grep gorgone_audit.pl | grep -v grep | awk '{print $2}' && (kill -TERM $!; sleep 1; kill -9 $!)```
5. Refresh the page and try again.
6. Still stuck ? 
   1. You can adapt the timeout value for the audit generation at line 35 of /usr/share/centreon/www/include/Administration/parameters/debug/functions.php :  
   ```$audit_timeout       = "60"; // if you have a lot of poller you may ajust this value```
   2. Or bypass the audit generation :
     - Comment the line 23 in /usr/share/centreon/www/include/Administration/parameters/debug/audit.php :
     ```php   
      23 ...
      24 //$conf_and_log_files_to_archive [] = generateAudit();
      25 ...
      ```
     - Kill the hanged process (refer to step 3).
