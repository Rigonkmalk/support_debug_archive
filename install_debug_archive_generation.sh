#!/usr/bin/bash

install_git() {

        which git > /dev/null 2>&1 
        is_git_installed=$?
        which dnf > /dev/null 2>&1
        is_rhel=$?

        if [[ $is_git_installed -eq 0 ]];then
                echo -e "Git already installed"
        else
                echo -e "Intalling git..."

                if [[ $is_rhel -eq 0 ]];then
                        dnf install -y git
                else
                        apt update && apt install -y git
                fi
        fi

}

clone_repo() {

        repo="https://github.com/ykacherCentreon/support_debug_archive.git"
        home=$(echo ~)
        destination="$home/support_debug_archive"

        if [[ -d $destination ]]; then
                rm -rf $destination
        fi
        echo -e "Downloading..."
        git clone $repo $destination
}

install_debug_archive_tool() {

        install_dir="/usr/share/centreon/www/include/Administration/parameters/debug"
        copy_cmd="/bin/cp"
        source=$1

        #Backup the original content
        sudo -u centreon $copy_cmd -r $install_dir{,.origin}
        #Install the tool
        echo -e "Installing..."
        $copy_cmd $source/* $install_dir && chown -R centreon: $install_dir/*

}

add_sudoers_file(){
        name="support_debug_archive"
        path="/etc/sudoers.d/$name"
        apache_users="apache,www-data" #separated_by_comma
        cmd='/bin/tar -czvf *'
        content="User_Alias      HTTP_USERS=$apache_users\nDefaults:HTTP_USERS !requiretty\n\nHTTP_USERS   ALL = (ALL) NOPASSWD: $cmd"
        touch $path
        echo -e "Adding sudoers file..."
        echo -e "$content" > $path

}

echo -e "######### Starting installation #########"
install_git
clone_repo
install_debug_archive_tool $destination
rm -rf $destination #clean the cloned repo file after having installed them
add_sudoers_file
echo -e "######### Installation finished #########"