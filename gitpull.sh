#!/bin/bash

if [ -z "$1" ]; then
	echo update a git project using GIT PULL
        echo usage: $0 directory
        exit
fi

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

	git pull
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

	echo '---------------------------------------------- fetch all updates'
	git fetch --all
	echo '---------------------------------------------- ignore all local changes'
	git reset --hard origin/master
	php artisan migrate
	echo
	exit
fi


echo $1 not yet implemented


