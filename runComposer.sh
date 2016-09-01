#!/bin/bash

if [ -z "$1" ]; then
	echo "run 'Composer require' to install a new package into your project"
        echo "usage: $0 directory packagename"
        exit
fi

if [ "$1" = "plan" ]; then

	echo '--------------------------------------------------------- Updating plan.eec.ie'
	cd /var/www/eec.ie/html/church/plan/
    pwd

    if ! [ -z "$2" ]; then
        echo '-------------------------------------------------------------------------------'
        echo "installing the required package $2 via Composer:"
        composer require "$2"
        echo '-------------------------------------------------------------------------------'
    fi

	echo
	echo '------------------------------------------------------- Updating c-spot.cu.cc'
	cd /var/www/c-spot.cu.cc/html/cSpot/
    pwd

    if ! [ -z "$2" ]; then
        echo '-------------------------------------------------------------------------------'
        echo "installing the required package $2 via Composer:"
        composer require "$2"
        echo '-------------------------------------------------------------------------------'
    fi

	exit
fi


echo $1 not yet implemented


