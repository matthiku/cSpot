#!/bin/sh


echo ----
git status
echo ----
echo
echo 'This script runs "git add .", "git commit ...." and "git push", then calls the pull script on the server'
echo

# you also need to have a working a SSH connection to your server 
# and a simple script called gitpull.sh with 2 lines:
#    cd <path to your laravel project root folder>
#    git pull

echo
echo 
read -r -p 'Enter the description of this Commit: ' "DESC"
if [ -z "$DESC" ]; then
    exit
fi



echo
echo Uploading all changes to GitHub with this description:
echo '====> ' 
printf "$DESC"
echo
echo  ' <===='
read -p 'Continue? (Y/n) '
if [ "$REPLY" = "n" ]; then
    echo 'Aborting...'
    exit
fi

read -p 'Need to add a new package via composer? Then enter the full package name: ' "PACKAGE"



# add all files to the commit
git add .
echo ----

# execute the commit and add the comment(description) of the commit
git commit -a -m "$DESC"
echo ----

# push the commit (all files) to GitHub
git push
echo ----



# call the pull command script on the server via SSH (using PPK)
echo
echo ----
echo "Calling pull command on the server"
echo ----
ssh root@eec.ie ./gitpull.sh plan "PACKAGE"              # adapt this according to your host name
echo ----



echo
echo DONE!
echo
