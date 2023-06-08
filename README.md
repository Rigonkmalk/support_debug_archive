# Support debug archive tool
Download a zipped archive with the platform's informations and logs to identify issues easier

## How to install ?
1. Clone this repository on to your centreon central server
2. Backup the ```/usr/share/centreon/www/include/Administration/parameters/debug``` folder : ```cp -r /usr/share/centreon/www/include/Administration/parameters/debug{,.origin}```
3. Copy all the files from the ```/debug``` folder of the cloned repository folder in ```/usr/share/centreon/www/include/Administration/parameters/debug```
4. Intall the zip command line tool : ```apt install zip``` or ```yum install zip```
5. Enjoy in Administration  >  Parameters  >  Debug
