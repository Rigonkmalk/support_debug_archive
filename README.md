# Support debug archive tool
Download a zipped archive with the platform's information and logs to identify issues easier (⚠ can take more than 5 min to generate ⚠).

## How to install ?
1. Clone this repository on to your centreon central server :
  - ```git clone https://github.com/ykacherCentreon/support_debug_archive.git ~/support_debug_archive```  
  In case you are not able to do so from your central server (e.g., no internet access), transfer the downloaded files via sftp or a drive and proceed with the steps below.
2. Backup the content of /usr/share/centreon/www/include/Administration/parameters/debug : 
  - ```cp -r /usr/share/centreon/www/include/Administration/parameters/debug{,.origin}```
3. Copy all the files from this repo in 
/usr/share/centreon/www/include/Administration/parameters/debug :
  - ```/bin/cp ~/support_debug_archive/* /usr/share/centreon/www/include/Administration/parameters/debug```
4. Change the ```ProxyTimeout``` setting of apache to ```1000``` in the configuration file :
  - Path for RHEL   : ```/etc/httpd/conf.d/10-centreon.conf```
    - Restart apache
      - ```systemctl restart httpd```
  - Path for Debian : ```/etc/apache2/sites-available/centreon.conf```
    - Restart apache
      - ```systemctl restart apache2```
5. Enjoy, go to " Administration  >  Parameters  >  Debug " :

<img alt="image" src="https://github.com/ykacherCentreon/support_debug_archive/assets/85548802/ba40fe1c-b8b1-4b93-9e5e-8106e5ad8c7e">

## Log
In case there is any issue with the tool, check the log files below for clues :
- ```/var/log/centreon/get_platform_log_and_info.log```
- ```/var/log/php-fpm/centreon-error.log```
