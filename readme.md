## The Church Service Planning Online Tool.


Create your own service planning database. Deploy for your church and allow other users to participate.

As an open source project, you can download the sources ('clone') and run the tool from your own web site.


### Introduction

c-SPOT was designed to help churches organize their Sunday and Midweek services as well as other events.

As an online tool, it provides event information to every person involved and allows them to add or modify information accordingly.

However, the ability to contribute to the plan details is based on distinct roles given to each user, so that only authorized people can make modifications or even see certain details.

### Authorization

By default, c-SPOT is designed to allow for 'self-registration'. Very basic rights are given to a self-registered user. Any further rights must be assigned by a user with an "Administrator" role.

Users just need an email address to register with c-SPOT (which will be verified by a link sent to that email address) or they can allow their existing registration with one of the "big" service providers (like Google, Facebook, Twitter etc.) to be used for this verification.
If a user chooses to allow provider verification, they need to "authorize" c-SPOT once to access their basic account information on those accounts. From then on, no further login is required anymore as long as they are logged in to those providers in the same browser program.
Note: After the installation of this tool, the first user to register will be be getting non-revokable Administrator rights! (In technical terms, this is the user with id number 1. Of course, like everything else, this can be manipulated in the 'users' table of the database.)

### Data Access und User Roles

User details and all information is stored in a (mySQL) database called 'cspot' in various tables. The major data tables are for users, songs, service plans and service plan items. Auxilliary information is stored in tables for user roles, service plan types and standard items for service plans.
Users can see and/or modify all or various parts of the information depending on their roles they have been given by an administrator. Based on those roles, users can be assigned as 'leader' or 'teacher' of a plan and as such are able to modify, add and delete plan items on those respective plans.

### TODO:

1) Send email to Admin when a new user is registered
2) [DONE] Add CCLI Song number column into Songs table
3) Add License type column into Songs table (CCLI, PublicDomain or unknown)
4) On songs detail page, add a link to CCLI song usage reporting tool
5) Only users with CCLI licence number can access non-PD songs 
6) Add button to search for song on Youtube
