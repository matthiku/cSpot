# The **C**hurch **S**ervice **P**lanning **O**nline **T**ool

c-SPOT was designed to help (small) churches plan their Sunday and Midweek services as well as other events.

c-SPOT provides the tools to manage all event information - the order of services, resources, worship song lists for worship leaders, musicians and every other person involved in the service or event and allows all users to add or modify information accordingly using the platform or device of their choice!

It allows the management of song lyrics, music scores, guitar chords, event resources and teams in a user-friendly web application.

- Frontend: Responsive design, runs on *any* device that supports a browser with javascript!
- Create Order of Services with all relevant data (team (leaders, musicians, other functions), resources, songs, free slides, embedded videoclips etc)
- Customize for your church and allow others to participate
- Manage your team (staff) with their roles; also manage the resources needed for a service (rooms, devices etc.)
- Present your songs in a slideshow fashion via a projector 
- The slideshow can be done off-line with all slides downlaoded to the local computer
- Control the slide show from **any device** using a Chromecast device attached to your projector
- Or control the presentation from a small device (e.g. smartphone) that syncs with the main device attached to the projector!
- Provide chords, lyrics and sheetmusic for musicians on all kinds of devices
- Allows extensive collaboration and input from all users with full rights management
- Allows for a _synchronised presentation_ of lyrics, chords or sheet music _accross all designated devices!_
- Exchange email and/or internal messages between users

As a free and open source project, you can download ('clone') the sources, modify (customize) them and run the tool from your own web site hoster.

**Developers**, please feel free to contribute and make pull requests! **Testers**, please send your bug reports and enhancement suggestions!

Check the [Installation instructions](#installation)

### Why c-SPOT?

Many people don’t understand the complexities of making church services happen. If you just turn up, it probably looks pretty straightforward. However, a lot goes on behind the scenes before the event! This tool tries to help with that.

#### History
In our church, this was first done by the leader of the service, when he handed out his paper-based list of songs to the musicians on a Sunday morning. As things progressed, this was no longer viable, as musicians needed more time to practice the songs and the slides for the projection needed to be prepared. So we switched to sending emails around - but quite often, things were changed after the email went out and then not everyone was up-to-date. 

That's when the development of the predecessor of c-SPOT was started. It was my first trial of a project in PHP, mySQL, HTML and Javascript and therefore, while user-friendly, not very developer-friendly... Also, it was never designed to be used on mobile devices. After recently learning a lot about Laravel and Bootstrap, I finally decided to re-write this tool from scratch, with a popular PHP framwork and the mobile-first approach. Due to time constraints, however, it has not a "[single page application (SPA)](https://en.wikipedia.org/wiki/Single-page_application)" design!

#### New Design
Out came an online tool, designed for mobile devices and desktop devices, fully responsive to all sizes of screens with the ability to still access and/or modify all the relevant data. Tables are adaptive so that more and more columns with less important information are hidden or their content displayed in a more compact way the smaller a device gets.

### Users
c-SPOT provides event information and worship song lists to worship leaders, musicians and every other person involved in the service and allows them to add or modify information accordingly.

However, the ability to contribute to the plan items is based on distinct roles given to each user, so that only authorized people can make modifications or even see certain details.

### Authorization
By default, c-SPOT is designed to allow for 'self-registration'. Very basic rights are given to a self-registered user. Any further rights must be assigned by a user with an "Administrator" role (see below).

Users just need an email address to register with c-SPOT (which will be verified by a link sent to that email address) or they can allow their existing registration with one of the "big" service providers (like Google, Facebook, Twitter etc.) to be used for this verification.

If a user chooses to allow provider verification, they need to "authorize" c-SPOT once to access their basic account information on those accounts. From then on, no further login is required anymore as long as they are logged in to those providers in the same browser program.

**Note**: After the installation of this tool, the first user to register will be be getting non-revokable Administrator rights! (In technical terms, this is the user with id number '1'. Of course, like everything else, this can be manipulated in the 'users' table of the database.)

### Data Access und User Roles
User details and all information is stored in a (mySQL) database in various tables. The major data tables are for users, songs, service plans and service plan items. Auxilliary information is stored in tables for user roles, service plan types and standard items for service plans.

Users can see and/or modify all or various parts of the information depending on their roles they have been given by an administrator. Based on those roles, users can be assigned as 'leader' or 'teacher' of a plan and as such are able to modify, add and delete plan items on those respective plans.

### Roles and Rights
Currently, the following user rights are assigned to roles:

| Role  | Rights  |
| ----- | ------- |
| retired | used only to show historical plans with former teachers or leaders |
| user | Can **see** (read) plans, items and user names and their roles. Can add notes to plans. |
| leader | same as user, can edit items on plans to which they are assigned |
| teacher | same as leader |
| author | same as leader plus can create new plans |
| editor | same as author plus can modify all plans and items, can modify default items, an edits songs |
| administrator | same as editor plus management of all users and their roles |

## Support of mobile devices
Although this is a web-based application, with the frontend running in a browser, a user can run it like an app using Chrome's feater "Add to homescreen",
which is available on all major mobile platforms. Once opened in the Chrome browser app on your mobile device, select this function in 
Chrome's menu and a new icon will appear on your homescreen:
![homescreen](https://raw.githubusercontent.com/matthiku/cSpot/master/public/images/homescreenIcon.png)


## Technical Blah blah
### Code
##### Backend
This PHP project is based on the [Laravel 5.2 framework](https://laravel.com/) with the [Socialite](https://github.com/laravel/socialite) and [LaravelCollective](https://laravelcollective.com) extensions. 
##### Frontend
The design is intended to be fully responsive with a mobile-first approach and uses the [Bootstrap framework](http://v4-alpha.getbootstrap.com/) in version 4. (Which is still in alpha but hopefully will be fully released before this project is out of beta!)

#### Prerequisites
For the requirements, check [Laravel's website](https://laravel.com/docs/5.2#installation). Mainly, you need to have console access to your web server and need to have [Composer](http://getcomposer.org) and [Git](http://git-scm.com/download) installed.

Composer handles the various dependencies for the Laravel framework. Git is being used to clone the project from the Git hub and also to keep it updated afterwards. Insofar Git is optional and not needed to actually run the project.

##### Database
Out of the box, c-SPOT uses a mySQL database to save all the data. However, Laravel provides for various other database tools, so you can actually modify this requirement.


### Installation

#### Mandatory Steps
1. In the root of your web server's http or html directory (depending on Apache or Nginx), run the command `git clone https://github.com/matthiku/cSpot.git` to download c-SPOT and install it into the folder 'cspot'. (The folder can be renamed to your liking)
2. Run `composer install` to install all the dependencies
3. In the root folder of the project, copy the file **.env.example** to **.env** and customize it for your environment. Mainly, configure your database name, db user name and db password for c-SPOT and enter the connectivity details for your mail server in order to be able to send confirmation emails to users.
4. Create a new (empty) database on your mySQL server with the aforementioned parameters (db name, user name and password etc.).
5. Run `php artisan migrate` to initialize your c-SPOT database.
6. Modify the predefined list of user roles according to your needs
7. Add more users and assign roles to them

#### Optional Steps
1. In order for the **social logins** to work, you need to register your own c-SPOT app with some 'service providers' like Google or Faceook and enter the relevant details also into your private .env file.
2. Add your songs to the songs list
3. Modify the list of pre-defined service plan types according to your needs
4. Add 'default items' for each service plan type
5. Start creating new plans and add items accordingly

### Future Enhancements (c-SPOT 2.0)

- Pre-populate the songs database with popular **public domain** lyrics
- Program the frontend as a Single Page App using AngularJS and the backend as a RESTful API
- Enable user to programmatically select Bible references (done)
- Add feature to send lyrics and Bible verses to a projector (done)
