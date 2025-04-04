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

detect_centreon_version() {
    which dnf > /dev/null 2>&1
    is_rhel=$?

    if [[ $is_rhel -eq 0 ]];then
        version_centreon=$(rpm -qa centreon-web | cut -d'-' -f 3 | cut -d'.' -f 1-2)
    else
        version_centreon=$(apt policy centreon-web |& grep Installed |& awk '{print $2}' | cut -d'.' -f1-2)
    fi
    printf $version_centreon
}

git_raw_content () {
    install_dir="/usr/share/centreon/www/include/Administration/parameters/debug"
    for file in form.ihtml form.php help.php index.html; do
        curl -o $install_dir/${file} https://raw.githubusercontent.com/centreon/centreon/refs/heads/$(detect_centreon_version)/centreon/www/include/Administration/parameters/debug/${file}
        chown centreon: $install_dir/${file}
        chmod 775 $install_dir/${file}
    done
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

restore_original_files() {
    install_dir="/usr/share/centreon/www/include/Administration/parameters/debug"

    # Delete installed content
    printf "Suppress installed content...\n"
    rm -f $install_dir/*

    # Restore the original content
    printf "Restoring original files...\n"
    git_raw_content
}

case "$1" in
    --restore)
        printf "###### Restore files ######\n"
        restore_original_files
        printf "###### End of Restoring files ######\n"
        exit 1
        ;;
    --install)
        echo -e "######### Starting installation #########"
        install_git
        clone_repo
        rm -rf $destination # clean the cloned repo file after having installed them
        add_sudoers_file
        echo -e "######### Installation finished #########"
        ;;
    "")
        echo -e "--restore : Restore files"
        echo -e "--install : Install debug tools"
        exit 0
        ;;
    *)
        echo -e "Unknown option: $1"
        echo -e "--restore : Restore files"
        echo -e "--install : Install debug tools"
        exit 1
        ;;
esac

