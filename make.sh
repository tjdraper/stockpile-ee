#!/usr/bin/env bash

# If the working copy is not clean, we cannot proceed
if [ -n "$(git status --porcelain)" ]; then
	red=`tput setaf 1`;
	reset=`tput sgr0`;
	echo "${red}There are currently uncommitted changes.
Please commit or stash all changes and make sure your working copy is clean.${reset}";
	afplay /System/Library/Sounds/Hero.aiff;
	exit 1;
fi

# Get the software name
name=($(jq -r '.name' system/user/addons/bootstrap/addon.json));

# Get the software version
version=($(jq -r '.version' system/user/addons/bootstrap/addon.json));

# Tell the sure we're making the build directory
echo 'Making build directory...';

# Make build directory
mkdir build;

# Wait just a split second
sleep .2;

# Tell the user we're copying things to the build directory
echo 'Copying files to the build directory...';

# Copy items to the build directory
cp -r system build/system;
cp -r themes build/themes;

# Wait just a split second
sleep .2;

# Tell the user we're creating the distribution file
echo 'Creating distribution zip file..';

# Change directory to the build directory
cd build;

# Wait just a split second
sleep .2;

# Zip it all up
zip -rq ../localStorage/"$name"-"$version".zip system/ themes/ -x "*.DS_Store" "*.gitkeep";

# Tell the user we're deleting the build directory
echo 'Deleting build directory...';

# Change directory back out to repo root
cd ../;

# Remove the build directory
rm -r build/

# Wait just a split second
sleep .2;

# Tell the user everything was successful.
echo "$name-$version.zip has been created.";
