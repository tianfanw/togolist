@if (Auth::guest())
    <li><a class="static-popup-link" data-popup-id="signup" href="/signup">Sign Up</a></li>
    <li><a class="static-popup-link" data-popup-id="login" href="/login">Login</a></li>
@else
    <li class="avatar">
        <a class="static-popup-link" data-popup-id="profile" href="/profile">
            <img src="/image/default_avatar.png"/>
        </a>
    </li>
    <li><a href="/mylist">My List</a></li>
    <li><a href="/list/create">Create A List</a></li>
    <li><a class="static-popup-link" data-popup-id="notification" href="/notification">Notification</a></li>
    <li><a href="/logout" id="logout">Log Out</a></li>
@endif