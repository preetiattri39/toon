<nav class="navbar p-0 fixed-top d-flex flex-row">
    <div class="navbar-brand-wrapper d-flex d-lg-none align-items-center justify-content-center">
    {{-- <a class="navbar-brand brand-logo-mini" href="index.html"><img src="{{asset('assets/images/logo-mini.svg')}}" alt="logo"></a> --}}
    {{-- <a class="sidebar-brand brand-logo text-center" href="{{route('dashboard')}}"><img src="{{asset('assets/icons/logo.png')}}" alt="logo"></a> --}}
        <a class="sidebar-brand brand-logo-mini" href="{{route('dashboard')}}"><img src="{{asset('assets/icons/logo.png')}}" alt="logo"></a>
    </div>
    <div class="navbar-menu-wrapper flex-grow d-flex align-items-stretch">
    <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
        <span class="mdi mdi-menu"></span>
    </button>
    @php
        $user = Auth::guard('admin')->user();

        $notification_count = DB::table('notifications')
                ->where('read_status', false)
                ->where('type', 'customer')
                ->count();

        $notifications = DB::table('notifications')
            ->where('read_status', false)
            ->where('type', 'customer')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();
    @endphp
    <ul class="navbar-nav navbar-nav-right">
       
        <li class="nav-item dropdown border-left a-notify-icon">
            <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown">
                <i class="mdi mdi-bell"></i>
                @if($notification_count > 0)
                    <span class="badge bg-danger">{{ $notification_count }}</span>
                @endif
            </a>

            <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown" id="header-notification-list">
                @if($notifications->isNotEmpty())
                    @foreach($notifications as $notification)

                        @php
                            if(isset($notification->title) && $notification->title=='New Ticket'){
                                $route = route('tickets');
                            }elseif(isset($notification->title) && $notification->notification_type=='order'){
                                $route = route('order-list');
                            }else{
                                $route = route('user-list');
                            }
                        @endphp

                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item preview-item" href="{{$route}}">
                            <div class="preview-thumbnail">
                                <div class="preview-icon bg-dark rounded-circle">
                                <i class="mdi mdi-calendar text-success"></i>
                                </div>
                            </div>
                            <div class="preview-item-content">
                                <p class="preview-subject mb-1">{{$notification->title}}</p>
                                <p class="text-muted ellipsis mb-0"> {{$notification->message}} </p>
                            </div>
                        </a>
                    @endforeach
                    <div class="dropdown-divider"></div>
                    <a href="{{route('notification-list')}}" class="dropdown-item preview-item justify-content-center" id="view-all-notifications-btn">
                        <p class="p-2 mb-0 text-center">See all notifications</p>
                    </a>
                @else
                    <a class="dropdown-item preview-item" id="no-notifications-message">
                        <span class="text-black">No new notifications</span>
                    </a>
                @endif
            </div>
        </li>
        <li class="nav-item dropdown">
        <a class="nav-link" id="profileDropdown" href="#" data-toggle="dropdown">
            <div class="navbar-profile">
            <img class="img-xs rounded-circle" src="{{asset('storage/profile')}}/{{$user->profile_pic}}" alt="user-profile">
            <p class="mb-0 d-none d-sm-block navbar-profile-name">{{$user->name}}</p>
            <i class="mdi mdi-menu-down d-none d-sm-block"></i>
            </div>
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="profileDropdown">
            <h6 class="p-3 mb-0">Profile</h6>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item preview-item" href="{{route('profile')}}">
            <div class="preview-thumbnail">
                <div class="preview-icon bg-dark rounded-circle">
                <i class="mdi mdi-settings text-success"></i>
                </div>
            </div>
            <div class="preview-item-content">
                <p class="preview-subject mb-1">Settings</p>
            </div>
            </a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item preview-item" href="{{route('admin-logout')}}">
                <div class="preview-thumbnail">
                    <div class="preview-icon bg-dark rounded-circle">
                    <i class="mdi mdi-logout text-danger"></i>
                    </div>
                </div>
                <div class="preview-item-content">
                    <p class="preview-subject mb-1">Log out</p>
                </div>
            </a>
        </div>
        </li>
    </ul>
    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
        <span class="mdi mdi-format-line-spacing"></span>
    </button>
    </div>
</nav>