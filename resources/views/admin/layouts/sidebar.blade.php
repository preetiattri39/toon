<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <div class="sidebar-brand-wrapper d-none  d-lg-flex align-items-center justify-content-center fixed-top">
        <a class="sidebar-brand brand-logo text-center" href="{{route('dashboard')}}"><img src="{{asset('assets/icons/logo.png')}}" alt="logo"></a>
        <a class="sidebar-brand brand-logo-mini" href="{{route('dashboard')}}"><img src="{{asset('assets/icons/logo.png')}}" alt="logo"></a>
    </div>
    @php
        $user = Auth::guard('admin')->user();
    @endphp
    <ul class="nav">
        <li class="nav-item profile">
        <div class="profile-desc">
            <div class="profile-pic">
            <div class="count-indicator">
                <!-- <img class="img-xs rounded-circle " src="{{asset('assets/images/faces/face15.jpg')}}" alt=""> -->
                <!-- <span class="count bg-success"></span> -->
                <img class="img-xs rounded-circle" src="{{asset('storage/profile')}}/{{$user->profile_pic}}" alt="user-profile">
            </div>
       
            <div class="profile-name">
                <h5 class="mb-0 font-weight-normal"> {{$user->name}}</h5>
                <!-- <span>Gold Member</span> -->
            </div>
            </div>
            <a href="#" id="profile-dropdown" data-toggle="dropdown"><i class="mdi mdi-dots-vertical"></i></a>
            <div class="dropdown-menu dropdown-menu-right sidebar-dropdown preview-list" aria-labelledby="profile-dropdown">
            <a href="{{route('profile')}}" class="dropdown-item preview-item">
                <div class="preview-thumbnail">
                <div class="preview-icon bg-dark rounded-circle">
                    <i class="mdi mdi-settings text-primary side-icn"></i>
                </div>
                </div>
                <div class="preview-item-content">
                <p class="preview-subject ellipsis mb-1 text-small">Account settings</p>
                </div>
            </a>
            <div class="dropdown-divider"></div>
            <a href="{{route('change-password')}}" class="dropdown-item preview-item">
                <div class="preview-thumbnail">
                <div class="preview-icon bg-dark rounded-circle">
                    <i class="mdi mdi-onepassword  text-info side-icn"></i>
                </div>
                </div>
                <div class="preview-item-content">
                <p class="preview-subject ellipsis mb-1 text-small">Change Password</p>
                </div>
            </a>
        </div>
        </div>
        </li>
        <li class="nav-item nav-category">
        <span class="nav-link">Navigation</span>
        </li>
        <li class="nav-item menu-items">
        <a class="nav-link" href="{{route('dashboard')}}">
            <span class="menu-icon">
            <i class="mdi mdi-speedometer side-icn"></i>
            </span>
            <span class="menu-title  text-truncate">Dashboard</span>
        </a>
        </li>

        <li class="nav-item menu-items">
            <a class="nav-link" href="{{route('user-list')}}">
                <span class="menu-icon">
                    <i class="fa-solid fa-user side-icn"></i>
                </span>
                <span class="menu-title  text-truncate">Users</span>
                <!-- <i class="menu-arrow"></i> -->
            </a>
            <!-- <div class="collapse" id="ui-user">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="{{route('user-list')}}">List</a></li>
                </ul>
            </div> -->
        </li>

        <li class="nav-item menu-items">
            <a class="nav-link" data-toggle="collapse" href="#ui-category" aria-expanded="false" aria-controls="ui-basic">
                <span class="menu-icon">
                    <i class="fa-solid fa-table-list"></i>
                </span>
                <span class="menu-title  text-truncate">Category Management</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-category">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="{{route('add-category')}}">Add New</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{route('category-list')}}">List</a></li>
                </ul>
            </div>
        </li>

        <li class="nav-item menu-items">
            <a class="nav-link" href="{{route('page-list')}}">
                <span class="menu-icon">
                    <i class="fa-solid fa-list-check"></i>
                </span>
                <span class="menu-title">Content Management</span>
            </a>
        </li>

        <li class="nav-item menu-items">
            <a class="nav-link" data-toggle="collapse" href="#ui-product" aria-expanded="false" aria-controls="ui-basic">
                <span class="menu-icon">
                    <i class="fa-solid fa-people-carry-box"></i>
                </span>
                <span class="menu-title">Product Management</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-product">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="{{route('add-product')}}">Add New</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{route('product-list')}}">List</a></li>
                </ul>
            </div>
        </li>

        <li class="nav-item menu-items">
            <a class="nav-link" href="{{route('order-list')}}">
                <span class="menu-icon">
                    <i class="fa-solid fa-cart-plus"></i>
                </span>
                <span class="menu-title">Orders</span>
            </a>
        </li>

        <li class="nav-item menu-items">
            <a class="nav-link" data-toggle="collapse" href="#ui-shipping" aria-expanded="false" aria-controls="ui-basic">
                <span class="menu-icon">
                    <i class="fa-solid fa-truck-fast"></i>
                </span>
                <span class="menu-title">Shipping Method</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-shipping">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="{{route('add-shipping')}}">Add New</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{route('shipping-list')}}">List</a></li>
                </ul>
            </div>
        </li>

        <li class="nav-item menu-items">
            <a class="nav-link" data-toggle="collapse" href="#ui-vat" aria-expanded="false" aria-controls="ui-basic">
                <span class="menu-icon">
                    <i class="fa-solid fa-truck-fast"></i>
                </span>
                <span class="menu-title">Vat</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-vat">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="{{route('vat.rates.create')}}">Add New</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{route('vat.rates.index')}}">List</a></li>
                </ul>
            </div>
        </li>

        <li class="nav-item menu-items">
            <a class="nav-link" data-toggle="collapse" href="#ui-faq" aria-expanded="false" aria-controls="ui-basic">
                <span class="menu-icon">
                    <i class="fa-solid fa-clipboard-question"></i>
                </span>
                <span class="menu-title">FAQs</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-faq">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="{{route('faq-show')}}">Add New</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{route('faq-list')}}">List</a></li>
                </ul>
            </div>
        </li>

        <li class="nav-item menu-items">
            <a class="nav-link" href="{{route('tickets')}}">
                <span class="menu-icon">
                    <i class="fa-solid fa-headset"></i>
                </span>
                <span class="menu-title">Help And Support</span>
            </a>
        </li>
    </ul>
</nav>
