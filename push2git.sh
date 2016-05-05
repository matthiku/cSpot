#!/bin/sh


if [ -z "$1" ]; then
    echo
	echo performing git add, commit and push, then call the pull script on the server
    echo
    echo usage: $0 [description]
    echo 
    echo Enter the description of this Commit:
    read DESC 
    if [ -z "$DESC" ]; then
        exit
    fi
else
    DESC=$*
fi

echo
echo Uploading all changes to GitHub with this description: \"$DESC\"


# add all files to the commit
git add .

# execute the commit and add the comment(description) of the commit
git commit -a -m \"$*\"

# push the commit (all files) to GitHub
git push

# call the pull command script on the server via SSH (using PPK)
ssh root@eec.ie ./gitpull.sh plan

echo
echo DONE!
echo