
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@if (isset($showTitle))
    @if (env('GOOGLE_CLIENT_ID') || env('FACEBOOK_CLIENT_ID') || env('TWITTER_CLIENT_ID') || env('LINKEDIN_CLIENT_ID') || env('GITHUB_CLIENT_ID'))
        <h4 class="lora">Sign in using your account from one of these service providers:</h4>
    @endif
@endif

@if (env('GOOGLE_CLIENT_ID'))
    <a href="{{ url('social/redirect/google') }}"   class="btn btn-{{ isset($hideLblText) ? 'sm' : 'lg' }} btn-secondary" role="button">
        <i class="fa fa-google"  ></i>
        <span class="{{ isset($hideLblText) ? 'hidden-xs-up' : '' }}">Google</span>
    </a>
@endif

@if (env('FACEBOOK_CLIENT_ID'))
    <a href="{{ url('social/redirect/facebook') }}" class="btn btn-{{ isset($hideLblText) ? 'sm' : 'lg' }} btn-secondary" role="button">
        <i class="fa fa-facebook"></i>
        <span class="{{ isset($hideLblText) ? 'hidden-xs-up' : '' }}">Facebook</span>
    </a>
@endif

@if (env('TWITTER_CLIENT_ID'))
    <a href="{{ url('social/redirect/twitter') }}"  class="btn btn-{{ isset($hideLblText) ? 'sm' : 'lg' }} btn-secondary" role="button">
        <i class="fa fa-twitter" ></i>
        <span class="{{ isset($hideLblText) ? 'hidden-xs-up' : '' }}">Twitter</span>
    </a>
@endif

@if (env('LINKEDIN_CLIENT_ID'))
    <a href="{{ url('social/redirect/linkedin') }}" class="btn btn-{{ isset($hideLblText) ? 'sm' : 'lg' }} btn-secondary" role="button">
        <i class="fa fa-linkedin"></i>
        <span class="{{ isset($hideLblText) ? 'hidden-xs-up' : '' }}">LinkedIn</span>
    </a>
@endif

@if (env('GITHUB_CLIENT_ID'))
    <a href="{{ url('social/redirect/github') }}"   class="btn btn-{{ isset($hideLblText) ? 'sm' : 'lg' }} btn-secondary" role="button">
        <i class="fa fa-github"  ></i>
        <span class="{{ isset($hideLblText) ? 'hidden-xs-up' : '' }}">Github</span>
    </a>
@endif
