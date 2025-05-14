  <style>
      .notifications a {
          text-decoration: none;
          color: inherit;
      }

      .notifications a:hover .notification-item {
          background-color: #f5f5f5;
          /* Tambahkan efek hover */
      }

      .notifications .notification-list {
          max-height: 300px;
          overflow-y: auto;
      }
  </style>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

      <div class="d-flex align-items-center justify-content-between">
        <a href="/dashboard" class="logo d-flex align-items-center">
            <span class="d-none d-lg-block">Cinta Sehat 24</span>
        </a>
        
      </div><!-- End Logo -->

      <nav class="header-nav ms-auto">
          <ul class="d-flex align-items-center">


              <li class="nav-item dropdown">
                  <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
                      <i class="bi bi-bell"></i>
                      <span class="badge bg-primary badge-number">
                          {{ auth()->user()->unreadNotifications->count() }}
                      </span>
                  </a>

                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
                      <li class="dropdown-header">
                          You have <span
                              id="notification-count">{{ auth()->user()->unreadNotifications->count() }}</span> new
                          notifications
                          <a href="{{ route('notifications.markAllRead') }}" id="mark-all-read">
                              <span class="p-2 badge rounded-pill bg-primary ms-2">Mark all as read</span>
                          </a>
                      </li>

                      <div class="notification-list">
                          @foreach (auth()->user()->unreadNotifications as $notification)
                              <a href="#" class="mark-as-read" data-id="{{ $notification->id }}">
                                  <li class="notification-item">
                                      <i class="{{ $notification->data['icon'] }}"></i>
                                      <div>
                                          <h4>Notifikasi</h4>
                                          <p>{{ $notification->data['message'] }}</p>
                                          <p>{{ \Carbon\Carbon::parse($notification->data['created_at'])->diffForHumans() }}
                                          </p>
                                      </div>
                                  </li>
                              </a>

                              <li>
                                  <hr class="dropdown-divider">
                              </li>
                          @endforeach
                      </div>



                      <li class="dropdown-footer">
                          <a href="{{ route('dashboard') }}">Show all notifications</a>
                      </li>
                  </ul>
              </li>

            <li class="nav-item">
    <span id="datetime" class="text-muted small me-3"></span>
</li>

              <li class="nav-item dropdown pe-3">
                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                    <img src="{{ auth()->user()->photo ? asset('storage/' . auth()->user()->photo) : asset('assets/img/profile-img.jpg') }}"
                         alt="Profile" class="rounded-circle" width="32" height="32" style="object-fit: cover;">
                    <span class="d-none d-md-block dropdown-toggle ps-2">{{ auth()->user()->name }}</span>
                </a>
            
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                    <li class="dropdown-header">
                        <h6>{{ auth()->user()->name }}</h6>
                        <span>{{ auth()->user()->role ?? 'User' }}</span>
                    </li>
                    <li><hr class="dropdown-divider"></li>
            
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('profile') }}">
                            <i class="bi bi-person"></i>
                            <span>My Profile</span>
                        </a>                      
                    </li>
                    <li><hr class="dropdown-divider"></li>
            
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.edit') }}">
                            <i class="bi bi-gear"></i>
                            <span>Account Settings</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.password.edit') }}">
                            <i class="bi bi-unlock"></i>
                            <span>Change Password</span>
                        </a>
                    </li>
        
                </ul>
            </li>
            

          </ul>
      </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <script>
      document.addEventListener("DOMContentLoaded", function() {
          function updateNotificationCount() {
              fetch("{{ url('/notifications/count') }}")
                  .then(response => response.json())
                  .then(data => {
                      let badge = document.querySelector(".badge-number");
                      let countElement = document.getElementById("notification-count");
                      if (data.count > 0) {
                          countElement.textContent = data.count;
                          badge.textContent = data.count;
                      } else {
                          countElement.textContent = 0;
                          badge.textContent = 0;
                      }
                  })
                  .catch(error => console.error("Error fetching notifications:", error));
          }

          document.querySelectorAll(".mark-as-read").forEach(item => {
              item.addEventListener("click", function(event) {
                  event.preventDefault();
                  let notificationId = this.getAttribute("data-id");

                  fetch(`/notifications/${notificationId}/read`, {
                      method: "POST",
                      headers: {
                          "X-CSRF-TOKEN": document.querySelector(
                              'meta[name="csrf-token"]').getAttribute("content"),
                          "Content-Type": "application/json"
                      }
                  }).then(response => {
                      if (response.ok) {
                          this.closest(".mark-as-read").remove();
                          updateNotificationCount();
                      }
                  }).catch(error => console.error("Error:", error));
              });
          });

          setInterval(updateNotificationCount, 5000);
      });

      function updateDateTime() {
        const now = new Date();

        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false,
        };

        const dateTimeString = now.toLocaleString('id-ID', options);
        document.getElementById('datetime').textContent = dateTimeString;
    }

    document.addEventListener("DOMContentLoaded", function () {
        updateDateTime();
        setInterval(updateDateTime, 1000); // update tiap detik
    });
  </script>
