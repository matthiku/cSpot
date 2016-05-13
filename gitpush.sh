#!/bin/sh


echo
echo 'This script runs "git add .", "git commit ...." and "git push", then calls the pull script on the server'

# you also need to have a working a SSH connection to your server 
# and a simple script called gitpull.sh with 2 lines:
#    cd <path to your laravel project root folder>
#    git pull

echo
echo 
read -p 'Enter the description of this Commit: ' "DESC"
if [ -z "$DESC" ]; then
    exit
fi

echo
echo ----
git status
echo ----
echo
echo Uploading all changes to GitHub with this description:
echo '====> "' $DESC '" <===='
read -p 'Continue? (Y/n)'
if [ "$REPLY" = "n" ]; then
    echo 'Aborting...'
    exit
fi


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
ssh root@eec.ie ./gitpull.sh plan               # adapt this according to your host name
echo ----



echo
echo DONE!
echo
