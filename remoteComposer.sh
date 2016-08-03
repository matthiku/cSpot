#!/bin/sh


# of course you need to copy the corresponding 'runComposer.sh' to the home folder of root
# and you need to change the user and server name:
HOSTNAME="root@eec.ie"

echo ----
echo
echo 'This script runs "composer require" with the package names you provide on the server ' $HOSTNAME
echo

read -p 'Enter the full package name (e.g."ruelps/kapuels": ' "PACKAGE"

if [ -z "$PACKAGE" ]; then
    echo 'Aborting...'
    exit
fi




# call the pull command script on the server via SSH (using PPK)
echo
echo ----
echo "Calling COMPOSER on the server"
echo ----
ssh $HOSTNAME ./runComposer.sh plan $PACKAGE
echo ----



echo
echo DONE!
echo
