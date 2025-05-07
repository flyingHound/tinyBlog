<div class="content__header">
    <h1 class="page-headline"><?= $headline ?></h1>
    <h2 class="page-subheadline h5"><?= $subheadline ?></h2>
    <p><?= $infoheadline ?></p>
</div>

<div class="container-fluid py-4">

    <?php /* 1. Row */?>
    <div class="row py-3">

        <div class="col-md-5">

            <!-- Blog Posts Info Card -->
            <div class="card" id="info-left-column">
                <div class="card-body">
                    <!-- Blog Posts -->
                    <h3 class="h5 mb-3"><i class="fa fa-newspaper-o fa-lg text-primary me-3"></i>Blog Posts</h3>
                    <div class="row mb-4">
                        <div class="col-6">
                            <div class="h5 display-6 fw-normal">
                                <?= $num_published_posts ?> <span class="fs-6 text-sm align-top">published</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="h5 display-6 fw-normal">
                                <?= $num_posts ?> <span class="fs-6 text-sm align-top">total</span>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Today Tips -->
                    <h5 class="mb-3"><i class="fa fa-lightbulb fa-lg text-warning me-3"></i>Today Tips</h5>
                    <p class="mb-0">Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt.</p>
                </div>

                <div class="card-footer text-center">
                    <?= anchor('blog_posts/create', '<i class="fa fa-pencil-square-o"></i> Create New Blog Post Record', [
                        'class' => 'btn btn-outline-dark me-2',
                        'role' => 'button'
                    ]) ?>
                </div>
            </div>

        </div>

        <div class="col-md-7">

            <!-- Recent Blog Posts -->
            <?= Modules::run("blog_posts/widget_recent_posts") ?>

        </div>

        <script>
            function syncColumnHeights() {
                const left = document.getElementById("info-left-column");
                const right = document.getElementById("info-right-column");

                if (left && right) {
                    // reset height, to shrink if needed
                    right.style.height = "auto";
                    right.style.height = left.offsetHeight + "px";
                }
            }

            document.addEventListener("DOMContentLoaded", syncColumnHeights);
            window.addEventListener("load", syncColumnHeights);
            window.addEventListener("resize", syncColumnHeights);
        </script>

    </div>

    <div class="row py-3">

        <div class="col-md-6">

            <!-- Calendar Section -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fa fa-calendar"></i> Publish Calendar</h5>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="calendarRangeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Last 30 Days
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="calendarRangeDropdown">
                            <li><a class="dropdown-item" href="#" data-days="7">Last 7 Days</a></li>
                            <li><a class="dropdown-item" href="#" data-days="30">Last 30 Days</a></li>
                            <li><a class="dropdown-item" href="#" data-days="90">Last 90 Days</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div id="calendar" tabindex="0"></div>
                </div>

                <div class="calendar-footer d-flex justify-content-end p-3 border-top">
                    <a href="<?= BASE_URL ?>blog_posts/manage" class="btn btn-sm btn-outline-dark me-2" role="button" aria-label="Manage existing blog posts"><i class="fa fa-list-ul"></i> Manage Posts</a>
                    <a href="<?= BASE_URL ?>blog_posts/create" class="btn btn-sm btn-outline-dark" role="button" aria-label="Write a new blog post"><i class="fa fa-file"></i> Write New Post</a>
                </div>
            </div>

        </div>

        <div class="col-md-6">

            <!-- Enquiries -->
            <?= Modules::run("enquiries/widget_enquiries") ?>

        </div>
    </div>

    <div class="row py-3">
        <div class="col-md-4">
            
            <!-- Categories -->
            <div class="card categories-widget">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-tags fa-lg text-info me-3"></i>
                        <h5 class="card-title mb-0"><a href="blog_categories/manage">Categories</a></h5>
                    </div>
                    <h2 class="display-4 mb-0"><?= $num_blog_categories ?></h2>
                </div>
                <div class="card-body p-2">

                    <table class="table table-sm table-hover small table-statistics">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Title</th>
                                <th>Posts</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($categories as $category): ?>
                            <tr>
                                <th scope="row"><?= $i++ ?></th>
                                <td><?= $category->title ?></td>
                                <td><?= $category->post_count ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
        <div class="col-md-4 mb-0">
            
            <!-- Karte 3: Tags -->
            <div class="card tags-widget">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-tag fa-lg text-warning me-3"></i>
                        <h5 class="card-title mb-0"><a href="blog_tags/manage">Tags</a></h5>
                    </div>
                    <h2 class="display-4 mb-0"><?= $num_blog_tags ?></h2>
                </div>
                <div class="card-body p-2">

                    <table class="table table-sm table-hover small table-statistics">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Posts</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($tags as $tag): ?>
                            <tr>
                                <th scope="row"><?= $i ?></th>
                                <td><?= $tag->name ?></td>
                                <td><?= $tag->post_count ?></td>
                            </tr>
                            <?php $i++; endforeach; ?>
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
        <div class="col-md-4 mb-0">
            
            <!-- Karte 4: Sources -->
            <div class="card sources-widget">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-book fa-lg text-danger me-3"></i>
                        <h5 class="card-title mb-0"><a href="blog_sources/manage">Sources</a></h5>
                    </div>
                    <h2 class="display-4 mb-0"><?= $num_blog_sources ?></h2>
                </div>
                <div class="card-body p-2">

                    <table class="table table-sm table-hover small table-statistics">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Author</th>
                                <th>Posts</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($sources as $source): ?>
                            <tr>
                                <th scope="row"><?= $i ?></th>
                                <td><?= $source->author ?></td>
                                <td><?= $source->post_count ?></td>
                            </tr>
                            <?php $i++; endforeach; ?>
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
    </div>

    <style>
        .table-statistics th,
        .table-statistics td {
            padding: 0.25rem 0.5rem; /* smaller than table-sm */
            font-size: 0.875rem; /* 14px */
        }

        .table-statistics thead th {
            font-size: 0.8125rem; /* 13px */
        }
    </style>

    

    <!-- more statistics (Pages, Admins and Comments) -->
    <div class="row">

        <!-- card: Pages -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-file fa-lg text-primary me-3"></i>
                            <h5 class="card-title mb-0">Pages</h5>
                        </div>
                        <h2 class="display-4 mb-0"><?= $num_trongate_pages ?></h2>
                    </div>
                    <ul class="list-unstyled mt-2">
                        <?php foreach ($pages as $page): ?>
                            <li class="text-muted small"><?= $page->url_string ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- card: Admins -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-users fa-lg text-info me-3"></i>
                            <h5 class="card-title mb-0">Admins</h5>
                        </div>
                        <h2 class="display-4 mb-0"><?= $num_admins ?></h2>
                    </div>
                    <ul class="list-unstyled mt-2">
                        <?php foreach ($admins as $admin): ?>
                            <li class="text-muted small"><?= $admin->username ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-start border-info border-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-users fa-lg text-info me-2"></i>
                            <h5 class="card-title mb-0 text-primary fw-bold">Admins</h5>
                        </div>
                        <span class="badge bg-info text-white rounded-pill"><?= $num_admins ?></span>
                    </div>
                    <ul class="list-unstyled mt-2">
                        <?php foreach ($admins as $admin): ?>
                            <li class="d-flex justify-content-between align-items-center py-1">
                                <span class="text-muted small"><?= $admin->username ?></span>
                                <span class="badge bg-secondary text-white small"><?= $admin->post_count ?> Posts</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- card: Comments -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-comments fa-lg text-warning me-3"></i>
                            <h5 class="card-title mb-0">Comments</h5>
                        </div>
                        <h2 class="display-4 mb-0"><?= $num_comments ?></h2>
                    </div>
                    <ul class="list-unstyled mt-2">
                        <?php if (empty($comments)): ?>
                            <li class="text-muted small">No comments available.</li>
                        <?php else: ?>
                            <?php foreach ($comments as $comment): ?>
                                <li class="text-muted small">
                                    <?= substr($comment->comment, 0, 30) . (strlen($comment->comment) > 30 ? '...' : '') ?>
                                    <span class="text-muted">(<span class="text-primary"><?= $comment->post_count ?></span> posts)</span>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Kommentare-Sektion -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Recent Comments</h5>
        </div>
        <div class="card-body">
            <?php if (empty($comments)): ?>
                <p class="text-muted small">No comments available.</p>
            <?php else: ?>
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Comment</th>
                            <th>Post Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($comments as $comment): ?>
                            <tr>
                                <td><?= substr($comment->comment, 0, 30) . (strlen($comment->comment) > 30 ? '...' : '') ?></td>
                                <td><span class="text-primary"><?= $comment->post_count ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div><a href="<?= BASE_URL ?>trongate_comments/manage" class="btn btn-sm btn-outline-dark" role="button" aria-label="Manage trongate comments">Manage Comments</a></div>
    </div>

    <?php /* under construction ...
    <!-- more statistics -->
    <div class="row py-3">

        <!-- Karte 5: Pictures -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-image fa-lg text-secondary me-3"></i>
                            <h5 class="card-title mb-0">Pictures</h5>
                        </div>
                        <h2 class="display-4 mb-0"><?= $num_blog_pictures ?></h2>
                    </div>
                    <ul class="list-unstyled mt-2">
                        <?php foreach ($pictures as $picture): ?>
                            <li class="text-muted small">
                                <?= $picture->picture ?> 
                                <span class="text-muted">(<span class="text-primary"><?= $picture->post_count ?></span> posts)</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>*/ ?>

    <!-- Modal for Posts -->
    <div class="modal fade" id="postModal" tabindex="-1" aria-labelledby="postModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="postModalLabel">Posts for <span id="modalDate"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    <!-- Posts will be added here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</div>

<style>

    
    .text-primary {
        color: #007bff !important;
    }
    .text-success {
        color: #28a745 !important;
    }
    .text-info {
        color: #17a2b8 !important;
    }
    .text-warning {
        color: #ffc107 !important;
    }
    .text-danger {
        color: #dc3545 !important;
    }
    .text-secondary {
        color: #6c757d !important;
    }
    .modal {
        display: none;
    }
    .modal.show {
        display: block !important;
    }
    /* Schriftgrößen */
    main h5, .modal h5 {
        font-size: .9375rem; /* 15px */
    }
    main h2.display-4 {
        font-size: 2rem; /* 32px, proportional */
    }
    .display-4 {
        font-size: 2rem;
        font-weight: 700;
    }
    main p, main li, main small, main button, .modal p, .modal li, .modal button {
        font-size: .8125rem; /* 13px */
    }
    main i, .modal i {
        font-size: .8125rem; /* Icons an Textgröße anpassen */
    }

    #calendar {
        min-height: 400px;
    }
    /* Navigationsleiste unter dem Kalender */
    .calendar-footer {
        background-color: #f8f9fa; /* Leichter grauer Hintergrund */
        border-top: 1px solid #dee2e6; /* Trennlinie */
        transition: background-color 0.2s ease; /* Sanfte Übergänge */
    }
    .calendar-footer .btn, .card-footer .btn {
        font-size: 0.8125rem; /* Passt zur Schriftgröße des Dashboards */
        padding: 0.375rem 0.75rem; /* Kleinere Buttons für ein kompaktes Design */
        transition: all 0.2s ease; /* Sanfte Übergänge für Hover-Effekte */
    }
    .fc-event-main-frame { cursor: pointer; }

    /* cards */
        .card {}
    .card:hover {
        /*transform: translateY(-5px);*/
    }
    .card-body {
        padding: 1.5rem;
    }

    /* expand heights on widget cards*/
    .card.categories-widget,
    .card.sources-widget,
    .card.tags-widget { 
        min-height: 100%; 
    }
</style>

<script>
    const token = '<?= $token ?>';

    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            events: <?= json_encode($calendar_events, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
            eventClick: function(info) {
                // Datum für die Abfrage (YYYY-MM-DD)
                var date = info.event.start.getFullYear() + '-' +
                           String(info.event.start.getMonth() + 1).padStart(2, '0') + '-' +
                           String(info.event.start.getDate()).padStart(2, '0');
                
                // 2. Date format: more general (DD MMM YYYY)
                var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                var formattedDate = String(info.event.start.getDate()).padStart(2, '0') + ' ' +
                                    months[info.event.start.getMonth()] + ' ' +
                                    info.event.start.getFullYear();

                document.getElementById('modalDate').textContent = formattedDate;
                loadPostsForDate(date);

                // Modal initialisieren
                var modalElement = document.getElementById('postModal');
                // Entferne vorherige Modal-Instanz, falls vorhanden
                var existingModal = bootstrap.Modal.getInstance(modalElement);
                if (existingModal) {
                    existingModal.dispose();
                }
                var modal = new bootstrap.Modal(modalElement, {
                    backdrop: true,
                    keyboard: true
                });
                modal.show();

                // Fokus verlagern, bevor das Modal geschlossen wird
                modalElement.addEventListener('hide.bs.modal', function () {
                    // Fokus vom "Close"-Button entfernen
                    var closeButton = modalElement.querySelector('.btn-secondary');
                    if (document.activeElement === closeButton) {
                        closeButton.blur();
                    }
                    // Fokus auf das Kalender-Element setzen
                    calendarEl.focus();
                }, { once: true }); // Event-Listener nur einmal ausführen
            }
        });
        calendar.render();

        // Dropdown Handler
        document.querySelectorAll('#calendarRangeDropdown + .dropdown-menu .dropdown-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                var days = this.getAttribute('data-days');
                document.getElementById('calendarRangeDropdown').textContent = `Last ${days} Days`;
                loadCalendarEvents(days);
            });
        });

        function loadCalendarEvents(days) {
            fetch('<?= BASE_URL ?>blog_dashboard/fetch_calendar_events/' + days, {
                method: 'GET',
                headers: {
                    'trongateToken': token,
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.statusText);
                    }
                    return response.json();
                })
                .then(events => {
                    calendar.getEvents().forEach(event => event.remove());
                    calendar.addEventSource(events);
                })
                .catch(error => {
                    console.error('Error loading calendar events:', error);
                    alert('Failed to load calendar events. Please try again.');
                });
        }

        function loadPostsForDate(date) {
            fetch('<?= BASE_URL ?>blog_posts/get_posts_by_date/' + date, {
                method: 'GET',
                headers: {
                    'trongateToken': token,
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    var modalBody = document.getElementById('modalBody');
                    if (data.length > 0) {
                        modalBody.innerHTML = '<ul>' + data.map(post => {
                            // Zeit direkt aus dem String extrahieren (Format: YYYY-MM-DD HH:mm:ss)
                            var time = post.date_published.split(' ')[1].substring(0, 5);
                            return `<li>${post.title} (${time})</li>`;
                        }).join('') + '</ul>';
                    } else {
                        modalBody.innerHTML = '<p>No posts found for this date.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error loading posts:', error);
                    document.getElementById('modalBody').innerHTML = '<p>Error loading posts. Please try again.</p>';
                });
        }
    });
</script>

<?#= json($data) ?>