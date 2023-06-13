# Support debug archive tool
Download a zipped archive with the platform's informations and logs to identify issues easier

## How to install ?
1. Clone this repository on to your centreon central server :
- ```git clone https://github.com/ykacherCentreon/support_debug_archive.git ~/support_debug_archive```
2. Backup the content of /usr/share/centreon/www/include/Administration/parameters/debug : 
- ```cp -r /usr/share/centreon/www/include/Administration/parameters/debug{,.origin}```
3. Copy all the files from this repo in 
/usr/share/centreon/www/include/Administration/parameters/debug :
- ```/bin/cp ~/support_debug_archive/* /usr/share/centreon/www/include/Administration/parameters/debug```
4. Install the zip command line tool : 
- ```apt install zip``` for debian based distros 
- ```yum install zip``` for RHEL based distros
5. Enjoy in Administration  >  Parameters  >  Debug :

<img alt="image" src="https://github.com/ykacherCentreon/support_debug_archive/assets/85548802/ba40fe1c-b8b1-4b93-9e5e-8106e5ad8c7e">

## Log
Incase there is any issue with the tool, check the log files below for clues :
- ```/var/log/centreon/get_platform_log_and_info.log```
- ```/var/log/php-fpm/centreon-error.log```
