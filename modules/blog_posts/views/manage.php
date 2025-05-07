<h1><?= out($headline) ?></h1>

<!-- Flash Messages -->
<?php flashdata(); ?>

<div class="d-flex gap-2 mb-3">
    <?= anchor('blog_posts/create', '<i class="fa fa-pencil-square-o"></i> Create New Blog Post Record', [
        'class' => 'btn btn-primary btn-sm',
        'role' => 'button'
    ]) ?>
    <?php if (strtolower(ENV) === 'dev'): ?>
        <?= anchor('api/explorer/blog_posts', 'API Explorer', [
            'class' => 'btn btn-outline-secondary btn-sm',
            'role' => 'button'
        ]) ?>
    <?php endif; ?>
</div>

<?= Pagination::display($pagination_data) ?>

<?php if (count($rows) > 0): ?>
    <div class="table-responsive">
        <table id="results-tbl" class="table table-bordered table-striped table-hover table-sm align-middle">
            <caption class="small">List of Blog Posts</caption>
            <thead class="table-light">
                <tr>
                    <th colspan="13">
                        <div class="table-header d-flex justify-content-between align-items-center py-1 flex-wrap gap-2">
                            <div class="d-flex align-items-center">
                                <?= form_open('blog_posts/manage/1/', ['method' => 'get', 'class' => 'me-2']) ?>
                                    <div class="input-group input-group-sm">
                                        <?= form_search('searchphrase', '', ['placeholder' => 'Search records...', 'class' => 'form-control']) ?>
                                        <button type="submit" class="btn btn-outline-secondary btn-sm" onclick="initSearch(); return false;">Search</button>
                                    </div>
                                <?= form_close() ?>
                            </div>
                            <div class="d-flex align-items-center text-nowrap small">
                                Records Per Page:
                                <?= form_dropdown('per_page', $per_page_options, $selected_per_page, [
                                    'class' => 'form-select form-select-sm ms-2',
                                    'onchange' => 'setPerPage()'
                                ]) ?>
                            </div>
                        </div>
                    </th>
                </tr>
                <tr class="table-dark sm">
                    <th scope="col" class="text-center">#</th>
                    <th class="text-center">ID</th>
                    <th class="text-center"><i class="fa fa-eye"></i></th>
                    <th>Title & Subtitle</th>
                    <th>Text</th>
                    <th>Picture</th>
                    <th class="text-center">Gallery</th>
                    <th>YouTube</th>
                    <th>Category</th>
                    <th>Source</th>
                    <th>Dates</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                $none_span = '<div class="text-muted fst-italic text-center small">none</div>';
                $attr = [
                    'class' => 'btn btn-outline-secondary btn-sm d-block w-100 py-0 mb-1',
                    'role' => 'button'
                ];
                foreach ($rows as $row):
                    $published = ($row->published == 1) ? 'fa-check-square' : 'fa-times-circle';
                    $published_status = ($row->published == 1) ? 'Published' : 'Not published';
                ?>
                    <tr>
                        <th scope="row" class="text-center"><?= $i++ ?></th>
                        <td class="text-center"><?= $row->id ?></td>
                        <td class="text-center"><i class="fa <?= $published ?>" id="published-icon-<?= $row->id ?>" title="<?= $published_status ?>"></i></td>
                        <td>
                            <div class="small"><?= strip_tags(htmlspecialchars_decode($row->title)) ?></div>
                            <div class="small text-muted"><?= strip_tags(htmlspecialchars_decode($row->subtitle)) ?></div>
                            <div class="d-none"><?= out($row->url_string) ?></div>
                        </td>
                        <td>
                            <div class="d-none d-lg-block small"><?= $row->text_short ?></div>
                            <div class="small"><?= $row->word_count ?> words</div>
                        </td>
                        <td class="text-center">
                            <?= !empty($row->picture)
                                ? '<img src="' . $row->thumb_url . '" alt="picture preview" title="' . out($row->picture) . '" class="img-fluid">'
                                : $none_span
                            ?>
                        </td>
                        <td class="text-center small">
                            <?= $row->picture_count ?> <?= $row->picture_count === 1 ? 'Pic' : 'Pics' ?>
                        </td>
                        <td class="text-center small">
                            <?= $row->youtube !== '' ? '<div class="fs-tiny">'.out($row->youtube).'<div>' : $none_span ?>
                        </td>
                        <td class="text-center small">
                            <?= $row->category_title != 'none' ? out($row->category_title) : $none_span ?>
                        </td>
                        <td class="text-center small">
                            <?= $row->source_author != 'none'
                                ? '<a href="' . htmlspecialchars($row->source_link) . '" target="_blank" title="Website: ' . htmlspecialchars($row->source_website) . '">' . htmlspecialchars($row->source_author) . '</a>'
                                : $none_span
                            ?>
                        </td>
                        <td class="small">
                            <div class="fw-bold" title="Published date">
                                <span class="date-label publish">P:</span>
                                <?= date($date_format, strtotime($row->date_published)) ?>
                            </div>
                            <div title="Created date">
                                <span class="date-label create">C:</span>
                                <?= date($date_format, strtotime($row->date_created)) ?> by <?= out($row->created_by) ?>
                            </div>
                            <div title="Last updated">
                                <span class="date-label update">U:</span>
                                <?php if ($row->date_updated > $row->date_created): ?>
                                    <?= date($date_format, strtotime($row->date_updated)) ?> by <?= out($row->updated_by) ?>
                                <?php else: ?>
                                    <span class="text-muted">never</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="d-flex flex-column gap-1">
                                <?= anchor('blog_posts/show/' . $row->id, 'View', $attr) ?>
                                <?= anchor('blog_posts/create/' . $row->id, 'Edit', $attr) ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Mini Table for Mobile -->
        <div id="results-tbl-mini" class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <?= form_open('blog_posts/manage/1/', ['method' => 'get']) ?>
                            <div class="input-group input-group-sm">
                                <?= form_search('searchphrase', '', ['placeholder' => 'Search records...', 'class' => 'form-control']) ?>
                                <button type="submit" class="btn btn-outline-secondary btn-sm" onclick="initSearch(); return false;">Search</button>
                            </div>
                        <?= form_close() ?>
                    </div>
                    <div class="d-flex align-items-center text-nowrap small">
                        Records Per Page:
                        <?= form_dropdown('per_page', $per_page_options, $selected_per_page, [
                            'class' => 'form-select form-select-sm ms-2',
                            'onchange' => 'setPerPage()'
                        ]) ?>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach ($rows as $row):
                        $published = ($row->published == 1) ? 'fa-check-square' : 'fa-times-circle';
                        $published_status = ($row->published == 1) ? 'Published' : 'Not published';
                    ?>
                        <div class="list-group-item d-flex align-items-center gap-2 py-2">
                            <div class="text-center small"><?= $row->id ?></div>
                            <div><i class="fa <?= $published ?>" id="published-icon-<?= $row->id ?>" title="<?= $published_status ?>"></i></div>
                            <div class="flex-grow-1">
                                <div class="small"><?= htmlspecialchars_decode($row->title) ?></div>
                                <div class="small text-muted"><?= date($date_format, strtotime($row->date_published)) ?></div>
                            </div>
                            <div>
                                <a href="<?= BASE_URL ?>blog_posts/create/<?= $row->id ?>" class="btn btn-outline-secondary btn-sm py-0">Edit</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (count($rows) > 9): ?>
        <?= Pagination::display($pagination_data) ?>
    <?php endif; ?>
<?php else: ?>
    <div class="text-center my-5 py-5">
        There are currently no blog posts in the database.
    </div>
<?php endif; ?>

<script>
    document.querySelectorAll('[id^="published-icon"]').forEach(icon => {
        icon.addEventListener('click', (ev) => togglePublishedStatus(ev.target));
    });

    function togglePublishedStatus(clickedIcon) {
        const newStatus = clickedIcon.classList.contains('fa-times-circle') ? 1 : 0;
        const recordId = parseInt(clickedIcon.id.replace('published-icon-', ''));
        const params = { published: newStatus };

        clickedIcon.style.display = 'none';
        const targetUrl = '<?= BASE_URL ?>api/update/blog_posts/' + recordId;
        fetch(targetUrl, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'trongateToken': '<?= $token ?>'
            },
            body: JSON.stringify(params)
        }).then(response => {
            if (response.ok) {
                togglePublishedIcon(clickedIcon);
            } else {
                clickedIcon.style.display = 'inline-block';
                alert('Failed to update status');
            }
        });
    }

    function togglePublishedIcon(clickedIcon) {
        const isTimes = clickedIcon.classList.contains('fa-times-circle');
        clickedIcon.classList.replace(isTimes ? 'fa-times-circle' : 'fa-check-square', isTimes ? 'fa-check-square' : 'fa-times-circle');
        clickedIcon.title = isTimes ? 'Published' : 'Not published';
        clickedIcon.style.display = 'inline-block';
    }

    /*function setPerPage() {
        const perPage = document.querySelector('select[name="per_page"]').value;
        window.location.href = '<?= BASE_URL ?>blog_posts/manage/1?per_page=' + perPage;
    }

    function initSearch() {
        const searchInput = document.getElementById('searchphrase');
        const encodedString = encodeURIComponent(searchInput.value.trim());
        if (encodedString) {
            window.location.href = '<?= BASE_URL ?>blog_posts/manage/1?searchphrase=' + encodedString;
        }
    }*/
</script>

<style>
    /* Icons */
    .fa-check-square,
    .fa-times-circle {
        font-size: 1.2rem;
        cursor: pointer;
    }

    .fa-check-square {
        color: #42a8a3;
    }

    .fa-times-circle {
        color: #a84247;
    }

    /* Desktop Table */
    #results-tbl thead th,
    #results-tbl tbody td,
    #results-tbl tbody th {
        padding: 0.5rem;
    }

    #results-tbl thead th:nth-child(1),
    #results-tbl tbody th:nth-child(1) {
        width: 1.5rem;
    }

    #results-tbl thead th:nth-child(2),
    #results-tbl tbody td:nth-child(2),
    #results-tbl thead th:nth-child(3),
    #results-tbl tbody td:nth-child(3) {
        width: 1.5rem;
    }

    #results-tbl thead th:nth-child(4),
    #results-tbl tbody td:nth-child(4) {
        min-width: 200px;
    }

    #results-tbl thead th:nth-child(5),
    #results-tbl tbody td:nth-child(5) {
        min-width: 120px;
    }

    #results-tbl thead th:nth-child(6),
    #results-tbl tbody td:nth-child(6) {
        min-width: 50px;
    }

    #results-tbl thead th:nth-child(7),
    #results-tbl tbody td:nth-child(7) {
        width: 50px;
    }

    #results-tbl img {
        max-width: 100%;
    }

    #results-tbl thead th:nth-child(7),
    #results-tbl tbody td:nth-child(7),
    #results-tbl tbody th:nth-child(8),
    #results-tbl tbody td:nth-child(8),
    #results-tbl thead th:nth-child(9),
    #results-tbl tbody td:nth-child(9),
    #results-tbl thead th:nth-child(10),
    #results-tbl tbody td:nth-child(10),
    #results-tbl thead th:nth-child(11),
    #results-tbl tbody td:nth-child(11) {
        white-space: nowrap;
    }

    #results-tbl tbody td div:nth-child(2),
    #results-tbl tbody td div:nth-child(3) {
        font-size: .875em;
    }

    #results-tbl tbody .fs-tiny {
        font-size: .625rem;
    }

    /* Date Cells */
    .date-label {
        font-weight: bold;
        margin-right: 3px;
    }

    .publish { color: #2a7ae2; }
    .create { color: #e67e22; }
    .update { color: #27ae60; }

    .nowrap { white-space: nowrap; }
    .sm { font-size: 0.8em; }

    /* Responsive Design */
    @media (max-width: 1199px) {
        #results-tbl {
            display: none;
        }

        #results-tbl-mini {
            display: block;
        }
    }

    @media (min-width: 1200px) {
        #results-tbl {
            display: table;
        }

        #results-tbl-mini {
            display: none;
        }
    }

    @media (max-width: 1350px) {
        .d-lg-block {
            display: none !important;
        }
        #results-tbl thead th:nth-child(5), #results-tbl tbody td:nth-child(5) {
            min-width: 1px;
            text-align: right;
        }
    }

    #results-tbl-mini .list-group-item div:nth-child(1) {
        min-width: 20px;
    }

/** pagination */
.tg-showing-statement { margin: 0;}
.pagination { display: inline-block; margin-top: 1em; }
.pagination:first-of-type { margin: 0 0 1em 0; }

.pagination a {
  color: black;
  float: left;
  padding: 8px 16px;
  text-decoration: none;
  border: 1px solid #ddd;
}

.pagination a.active {
  background-color: var(--bs-primary);
  color: white;
  border: 1px solid var(--bs-primary);
}

.pagination a:hover:not(.active) { background-color: #ddd; }
.pagination a:first-child { border-top-left-radius: 5px; border-bottom-left-radius: 5px; }
.pagination a:last-child { border-top-right-radius: 5px; border-bottom-right-radius: 5px; }
</style>
<?#= json($data) ?>