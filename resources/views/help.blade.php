
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

<div class="card">
    
<div class="card-header">
    <h3>The Fine Print ...</h3>
</div>

<div class="card-block xs-center">


  <div id="accordion" role="tablist" aria-multiselectable="true">

    <div class="panel panel-default">
      <div class="panel-heading" role="tab" id="headingOne">
        <h4 class="panel-title">
          <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
            Introduction
          </a>
        </h4>
      </div>
      <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
        <p>c-SPOT was designed to help your church organize their Sunday and Midweek services as well as other events.</p>
        <p>As an online tool, it provides event information to every person involved and allows them 
           to add or modify information accordingly.</p>
        <p>However, the ability to contribute to the plan details is based on distinct <span class="text-warning">roles</span> given to each user, so that only authorized people
           can make modifications or even see certain details.</p>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading" role="tab" id="headingTwo">
        <h4 class="panel-title">
          <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
            Sign-Up and Authorization
          </a>
        </h4>
      </div>
      <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
        <p>By default, c-SPOT is designed to allow for 'self-registration'. That means, once you've signed up, either by email or a service provider (see below), you can immediately look into most of the data. However, only very basic access rights are given to a self-registered user, so
              any further rights must be assigned by a user with an "Administrator" role.</p>
        <p>To sign up, just provide your name, <span class="text-success">email address</span> and create a new password. An email containing a verification link will be sent to that email address. Be sure to click on that link - once you received the email - in order to 'release' your account!</p>
        <p>Alternatively, and without the need for an extra password, you can allow your existing registration with one of the "big" <strong>service providers</strong> (like Google, Facebook, Twitter etc.) to be used for this verification process. Just click on the symbol of the service provider you want to use. You will be forwarded <strong>once</strong> to a special page of your service provider for confirmation.</p>
        <p>If you agree to use this <span  class="text-warning">"provider verification"</span>, c-SPOT will be "authorized" to access your basic account 
                information on those accounts (usually name and email address). From then on, <strong>no further login is required anymore</strong> as long as you are logged in to those 
                providers in the same browser program.</p>
        <p>So with that, no need to remember another password!</p>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading" role="tab" id="headingThree">
        <h4 class="panel-title">
          <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
            Data Access und User Roles
          </a>
        </h4>
      </div>
        <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
            <p>Verified users can see and/or modify all or various parts of the information depending on their <span class="text-warning">roles</span> 
                they have been given by an administrator.</p>
            <p>Based on those roles, users can be assigned as 'leader' or 'teacher' of a plan and as such are able to modify, add and 
                delete plan items on those respective plans.</p>
            <p>Copyrighted material like lyrics can only be viewed by users with the role of 'leader' or higher since they are assumed to be
                part of the local church and therefore covered by the church's MRL license from SongSelect. (http://uk.ccli.com/songselect/) </p>
            <p>(User details and all information is stored in a (mySQL) database called 'cspot' in various tables. The major data tables are for 
                <span class="font-italic">users, songs, service plans</span> and <span class="font-italic">service plan items</span>. 
                Auxilliary information is stored in tables for user roles, service plan types and standard items for service plans.)</p>
        </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading" role="tab" id="headingFour">
        <h4 class="panel-title">
          <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
            About this Tool
          </a>
        </h4>
      </div>
      <div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
            <p>This is a 'hobby project' of Matthias Kuhs, intially designed to replace a paper- or email-based system
                    used to organize with the running of our church services to provide the relevant information for musicians 
                    and leaders involved in the service.</p>
            <p>As an open source project, everyone can download the sources and modify the code according to their specific needs.</p>
            <p>The project is currently hosted on Github under 
                <a target="_new" href="https://github.com/matthiku/cSpot">https://github.com/matthiku/cSpot</a>
                from where people can 'clone' (download) it to their own web server.
            </p>
            <p><strong>Installation instructions</strong> can be found in the readme file hosted on Github.</p>
            <p><strong>Note: </strong>After the installation of this tool, the <span  class="text-danger">first</span> user to register will be be getting non-revokable 
                    Administrator rights! (In technical terms, this is the user with id number 1. Of course, like everything else, this can be manipulated in the 'users' 
                    table of the database.)</p>
      </div>
    </div>


  </div><!-- accordion -->

</div><!-- card-block -->

</div><!-- card -->
