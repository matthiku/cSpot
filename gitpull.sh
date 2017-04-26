#!/bin/bash

if [ -z "$1" ]; then
	echo Running migrations and optionally installing Composer packages
        echo usage: $0 directory
        exit
fi

echo ' '
echo "restarting the queue workers"
supervisorctl restart all



if [ "$1" = "plan" ]; then

	echo 'Updating plan.eec.ie'
	cd /var/www/eec.ie/html/church/plan/
    pwd

    if ! [ -z "$2" ]; then
        echo '-------------------------------------------------------------------------------'
        echo "But first, we are installing the required package $2 via Composer:"
        composer require "$2"
        echo '-------------------------------------------------------------------------------'
    fi


	php artisan migrate





	echo
	echo '------------------------------------------------------- Updating c-spot.cu.cc'
	cd /var/www/c-spot.cu.cc/html/cSpot/
    pwd

    if ! [ -z "$2" ]; then
        echo '-------------------------------------------------------------------------------'
        echo "But first, we are installing the required package $2 via Composer:"
        composer require "$2"
        echo '-------------------------------------------------------------------------------'
    fi


    php artisan migrate

	echo

	exit

fi


echo $1 not yet implemented
