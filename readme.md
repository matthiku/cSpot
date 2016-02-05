## The Church Service Planning Online Tool.

Create your own service planning database. Deploy for your church and allow other users to participate.

As an open source project, you can download ('clone') the sources and run the tool from your own web site.

**Developers**, please feel free to contribute and make pull requests! **Testers**, please send your bug reports and enhancement suggestions!


### Introduction
c-SPOT was designed to help churches organize their Sunday and Midweek services as well as other events.

As a mobile-friendly online tool, it provides event information to every person involved and allows them to add or modify information accordingly.

However, the ability to contribute to the plan items is based on distinct roles given to each user, so that only authorized people can make modifications or even see certain details.

### Authorization
By default, c-SPOT is designed to allow for 'self-registration'. Very basic rights are given to a self-registered user. Any further rights must be assigned by a user with an "Administrator" role.

Users just need an email address to register with c-SPOT (which will be verified by a link sent to that email address) or they can allow their existing registration with one of the "big" service providers (like Google, Facebook, Twitter etc.) to be used for this verification.

If a user chooses to allow provider verification, they need to "authorize" c-SPOT once to access their basic account information on those accounts. From then on, no further login is required anymore as long as they are logged in to those providers in the same browser program.

Note: After the installation of this tool, the first user to register will be be getting non-revokable Administrator rights! (In technical terms, this is the user with id number 1. Of course, like everything else, this can be manipulated in the 'users' table of the database.)

### Data Access und User Roles
User details and all information is stored in a (mySQL) database called 'cspot' in various tables. The major data tables are for users, songs, service plans and service plan items. Auxilliary information is stored in tables for user roles, service plan types and standard items for service plans.

Users can see and/or modify all or various parts of the information depending on their roles they have been given by an administrator. Based on those roles, users can be assigned as 'leader' or 'teacher' of a plan and as such are able to modify, add and delete plan items on those respective plans.


## Technical Blah blah
### Code
#### Backend
This PHP project is based on the [Laravel 5.2 framework](https://laravel.com/) with the [Socialite](https://github.com/laravel/socialite) and [LaravelCollective](https://laravelcollective.com) extensions. 
#### Frontend
Currently, the focus is on the backend development so there is no AJAX and only little Javascript involved.

The design is intended to be fully responsive with a mobile-first approach and uses the [Bootstrap framework](http://v4-alpha.getbootstrap.com/) in version 4. (Which is also beta but hopefully will be fully released before this project is out of beta!)

#### Prerequisites
For the requirements, check [Laravel's website](https://laravel.com/docs/5.2#installation). Mainly, you need to have console access to your web server and need to have [Composer](http://getcomposer.org) and [Git](http://git-scm.com/download) installed.

Composer handles the various dependencies for the Laravel framework. Git is being used to clone the project from the Git hub and also to keep it updated afterwards. Insofar Git is optional and not needed to actually run the project.

##### Database
Out of the box, c-SPOT uses a mySQL database to save all the data. However, Laravel provides for various other database tools, so you can actually modify this requirement.


### Installation

#### Mandatory Steps
1. In the root of your web server's http or html directory (depending on Apache or Nginx), run the command `git clone https://github.com/matthiku/cSpot.git` to download c-SPOT and install it into the c-spot folder. (The folder can be renamed to your liking)
2. Then run `composer install` to install all the dependencies
3. In the root folder of the project, copy the file **.env.example** to **.env** to customize it for your environment. Mainly, configure your database name, db user name and db password for c-SPOT and enter the connectivity details for your mail server to be able to send confirmation emails to users.

#### Optional Steps
In order for the 'social logins' to work, you need to register your c-SPOT clone with some 'service providers' like Google or Faceook and enter the details also into the .env file.


### Bugs/Enhancements:
- Design flaws on mobile devices: 

- [x] buttons on welcome page and their tooltips
- [ ] list of songs from song search on the new item page is awkward
- [x] obscure 'submit' button when adding a new service plan
- [x] too many main menu items, reduce them to one main menu and change the misleading term 'Admin'

- Other issues

- [ ] ...

### TODO:
- [ ] Send email to Admin when a new user is registered
- [x] Add CCLI Song number column into Songs table
- [ ] Add License type column into Songs table (CCLI, PublicDomain or unknown)
- [x] On songs detail page, add a link to CCLI song usage reporting tool
- [ ] Only users with CCLI licence number can access non-PD songs 
- [x] Add button to search for song on Youtube

### Future Enhancements (c-SPOT 2.0)

- Program the frontend as a Single Page App using AngularJS and the backend as a RESTful API
- Enable user to programmatically select Bible references
- Add feature to send lyrics and Bible verses to a projector
