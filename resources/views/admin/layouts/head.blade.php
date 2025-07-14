<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{config('app.name')}}</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{asset('assets/vendors/mdi/css/materialdesignicons.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/css/vendor.bundle.base.css')}}">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{asset('assets/vendors/jvectormap/jquery-jvectormap.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/flag-icon-css/css/flag-icon.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/owl-carousel-2/owl.carousel.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/owl-carousel-2/owl.theme.default.min.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/custom.css')}}">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="{{asset('assets/icons/logo.png')}}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    
    <script>
        // Enable Pusher logging - don't include this in production
        Pusher.logToConsole = true;

        if ('{{ auth()->guard('admin')->check() }}') {
            // Initialize Pusher
            var pusher = new Pusher('98a4d638299f9ae02d19', {
                cluster: 'ap2',
                encrypted: true
            });

            // Subscribe to register-customer channel
            var newUserEventChannel = pusher.subscribe('register-customer');
            newUserEventChannel.bind('NewUserEvent', function(data) {
            
                var loggedInUserIsAdmin = {!! auth()->guard('admin')->user() && auth()->guard('admin')->user()->roles->first()->name === 'super-admin' ? 'true' : 'false' !!};
                if (loggedInUserIsAdmin) {
                    handleNotificationEvent(data);
                }

            });

            // Subscribe to sent-ticket channel
            var sentTicketChannel = pusher.subscribe('sent-ticket');
            sentTicketChannel.bind('CustomerTicketSentNotificationEvent', function(data) {
            
                var loggedInUserIsAdmin = {!! auth()->guard('admin')->user() && auth()->guard('admin')->user()->roles->first()->name === 'super-admin' ? 'true' : 'false' !!};
                if (loggedInUserIsAdmin) {
                    handleNotificationEvent(data);
                }

            });

            // Subscribe to new-order channel
            var newOrderChannel = pusher.subscribe('new-order');
            newOrderChannel.bind('NewOrderGenerateNotificationsEvent', function(data) {
            
                var loggedInUserIsAdmin = {!! auth()->guard('admin')->user() && auth()->guard('admin')->user()->roles->first()->name === 'super-admin' ? 'true' : 'false' !!};
                if (loggedInUserIsAdmin) {
                    handleNotificationEvent(data);
                }

            });

            /**
            * Handles the notification event and updates UI
            * @param {Object} notification - The notification data received from Pusher
            */
            function handleNotificationEvent(notification) {
                console.log(notification);
                // Display notification using Toastr
                if (notification.title) {
                    toastr.info(
                        ``,
                        notification.title,
                        {
                            closeButton: true,
                            progressBar: true,
                            timeOut: 0, // Set timeOut to 0 to make it persist until closed
                            extendedTimeOut: 0, // Ensure the notification stays open
                            positionClass: 'toast-top-right',
                            enableHtml: true
                        }
                    );
                } else {
                    console.error('Invalid data received:', notification);
                }

                // Append the notification to the header list
                appendNotificationToHeader(notification);

                // Increment the notification count
                incrementNotificationCount();
            }

            /**
            * Function to increment the notification count badge
            */
            function appendNotificationToHeader(notification) {
                const notificationList = document.getElementById('header-notification-list');

                const noNotificationMessage = document.getElementById('no-notifications-message');
                
                // Remove the "No new notifications" message if it's there
                if (noNotificationMessage) {
                    noNotificationMessage.remove();
                }

                if (notificationList) {

                    var route;
                    if (notification.title) {
                        if (notification.title === 'New Ticket') {
                            route = "{{route('tickets')}}";
                        }else if(notification.title === 'New Order Received'){
                            route = "{{route('order-list')}}";
                        }else {
                            route = "{{route('user-list')}}";
                        }
                    }

                    const newNotification = `
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item preview-item" href="${route}">
                            <div class="preview-thumbnail">
                                <div class="preview-icon bg-dark rounded-circle">
                                <i class="mdi mdi-calendar text-success"></i>
                                </div>
                            </div>
                            <div class="preview-item-content">
                                <p class="preview-subject mb-1">${notification.title}</p>
                                <p class="text-muted ellipsis mb-0">${notification.message}</p>
                            </div>
                        </a>
                    `;
                    notificationList.insertAdjacentHTML('afterbegin', newNotification);
                }

                // Ensure that "View all notifications" button appears if it's not there
                if (!document.querySelector('#header-notification-list #view-all-notifications-btn')) {
                    const viewAllButton = `
                        <div class="dropdown-divider"></div>
                        <a href="{{route('notification-list')}}" class="dropdown-item preview-item justify-content-center" id="view-all-notifications-btn">
                            <p class="p-3 mb-0 text-center">See all notifications</p>
                        </a>
                    `;
                    notificationList.insertAdjacentHTML('beforeend', viewAllButton);
                }
            }

            /**
            * Function to increment the notification count badge
            */
            function incrementNotificationCount() {
                const notificationBadge = document.querySelector('.a-notify-icon .badge');

                if (notificationBadge) {
                    let currentCount = parseInt(notificationBadge.textContent) || 0;
                    notificationBadge.textContent = currentCount + 1;
                } else {
                    // If badge doesn't exist, create and append it
                    const notifyIcon = document.querySelector('.a-notify-icon a');
                    if (notifyIcon) {
                        const badge = document.createElement('span');
                        badge.className = 'badge bg-danger';
                        badge.textContent = '1';
                        notifyIcon.appendChild(badge);
                    }
                }
            }

        }
    </script>
    

</head>