
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

<div class="card card-block text-xs-center">

  <div id="accordion" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default">
      <div class="panel-heading" role="tab" id="headingOne">
        <h4 class="panel-title">
          <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
            Introduction
          </a>
        </h4>
      </div>
      <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
        <p>c-SPOT was designed to help churches organize their Sunday and Midweek services as well as other events.</p>
        <p>As an online tool, it provides event information to every person involved and allows them 
           to add or modify information accordingly.</p>
        <p>However, the ability to contribute to the plan details is based on distinct <span class="text-success">roles</span> given to each user, so that only authorized people
           can make modifications or even see certain details.</p>
      </div>
    </div>
    <div class="panel panel-default">
      <div class="panel-heading" role="tab" id="headingTwo">
        <h4 class="panel-title">
          <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
            Authorization
          </a>
        </h4>
      </div>
      <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
        By default, c-SPOT is designed to allow for 'self-registration'. Very basic rights are given to a self-registered user.
        Any further rights must be assigned by a user with an "Administrator" role.<br><br>
        Users just need an <span class="text-success">email address</span> to register with c-SPOT (which will be verified by a link sent to that email address) 
        or they can allow 
        their existing registration with one of the "big" service providers (like Google, Facebook, Twitter etc.) to be used 
        for this verification.<br>
        If a user chooses to allow <span  class="text-warning">provider verification</span>, they need to "authorize" c-SPOT <strong>once</strong> to access their basic account 
        information on those accounts. From then on, no further login as required anymore as long as they are logged in to those 
        providers in the same browser program.<br>
        <strong>Note: </strong>After the installation of this tool, the <span  class="text-warning">first</span> user to register will be be getting non-revokable 
        Administrator rights! (In technical terms, this is the user with id number 1. Of course this can be manipulated in the 'users' 
        table of the database.)
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
        All data is stored in a single (mySQL) database. The major data tables are for <span class="font-italic">users, songs, 
        service plans</span> and <span class="font-italic">service plan items</span>.
        Auxilliary information is stored in tables for user roles, service plan types and standard items for service plans.<br>
        Users can see and/or modify all or various parts of the information depending on their <span class="text-warning">roles</span> 
        they have been given by an administrator.
        Depending on those roles, users can be assigned as 'leader' or 'teacher' of a plan and as such are able to modify, add and 
        delete plan items on those respective plans.
      </div>
    </div>
  </div>

</div>                
