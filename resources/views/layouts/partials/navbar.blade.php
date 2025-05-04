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
          <a href="index.html" class="logo d-flex align-items-center">
              <img src="assets/img/logo.png" alt="">
              <span class="d-none d-lg-block">Cinta Sehat 24</span>
          </a>
          <i class="bi bi-list toggle-sidebar-btn"></i>
      </div><!-- End Logo -->

      <nav class="header-nav ms-auto">
          <ul class="d-flex align-items-center">

              <li class="nav-item d-block d-lg-none">
                  <a class="nav-link nav-icon search-bar-toggle " href="#">
                      <i class="bi bi-search"></i>
                  </a>
              </li><!-- End Search Icon-->

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

              <li class="nav-item dropdown">

                  <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
                      <i class="bi bi-chat-left-text"></i>
                      <span class="badge bg-success badge-number">3</span>
                  </a><!-- End Messages Icon -->

                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow messages">
                      <li class="dropdown-header">
                          You have 3 new messages
                          <a href="#"><span class="p-2 badge rounded-pill bg-primary ms-2">View all</span></a>
                      </li>
                      <li>
                          <hr class="dropdown-divider">
                      </li>

                      <li class="message-item">
                          <a href="#">
                              <img src="assets/img/messages-1.jpg" alt="" class="rounded-circle">
                              <div>
                                  <h4>Maria Hudson</h4>
                                  <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                                  <p>4 hrs. ago</p>
                              </div>
                          </a>
                      </li>
                      <li>
                          <hr class="dropdown-divider">
                      </li>

                      <li class="message-item">
                          <a href="#">
                              <img src="assets/img/messages-2.jpg" alt="" class="rounded-circle">
                              <div>
                                  <h4>Anna Nelson</h4>
                                  <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                                  <p>6 hrs. ago</p>
                              </div>
                          </a>
                      </li>
                      <li>
                          <hr class="dropdown-divider">
                      </li>

                      <li class="message-item">
                          <a href="#">
                              <img src="assets/img/messages-3.jpg" alt="" class="rounded-circle">
                              <div>
                                  <h4>David Muldon</h4>
                                  <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                                  <p>8 hrs. ago</p>
                              </div>
                          </a>
                      </li>
                      <li>
                          <hr class="dropdown-divider">
                      </li>

                      <li class="dropdown-footer">
                          <a href="#">Show all messages</a>
                      </li>

                  </ul><!-- End Messages Dropdown Items -->

              </li><!-- End Messages Nav -->

              <li class="nav-item dropdown pe-3">

                  <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#"
                      data-bs-toggle="dropdown">
                      <img src="assets/img/profile-img.jpg" alt="Profile" class="rounded-circle">
                      <span class="d-none d-md-block dropdown-toggle ps-2">K. Anderson</span>
                  </a><!-- End Profile Iamge Icon -->

                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                      <li class="dropdown-header">
                          <h6>Kevin Anderson</h6>
                          <span>Web Designer</span>
                      </li>
                      <li>
                          <hr class="dropdown-divider">
                      </li>

                      <li>
                          <a class="dropdown-item d-flex align-items-center" href="users-profile.html">
                              <i class="bi bi-person"></i>
                              <span>My Profile</span>
                          </a>
                      </li>
                      <li>
                          <hr class="dropdown-divider">
                      </li>

                      <li>
                          <a class="dropdown-item d-flex align-items-center" href="users-profile.html">
                              <i class="bi bi-gear"></i>
                              <span>Account Settings</span>
                          </a>
                      </li>
                      <li>
                          <hr class="dropdown-divider">
                      </li>

                      <li>
                          <a class="dropdown-item d-flex align-items-center" href="pages-faq.html">
                              <i class="bi bi-question-circle"></i>
                              <span>Need Help?</span>
                          </a>
                      </li>
                      <li>
                          <hr class="dropdown-divider">
                      </li>

                      <li>
                          <a class="dropdown-item d-flex align-items-center" href="#">
                              <i class="bi bi-box-arrow-right"></i>
                              <span>Sign Out</span>
                          </a>
                      </li>

                  </ul><!-- End Profile Dropdown Items -->
              </li><!-- End Profile Nav -->

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
  </script>
