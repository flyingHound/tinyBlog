<h1><?= out($headline) ?></h1>
<?php
flashdata();

echo '<p class="d-flex gap-2">';
echo anchor('menu_items/create', 'Create New Menu Item', array(
    "class" => "btn btn-primary",
    "role" => "button"
));

if(strtolower(ENV) === 'dev') {
    echo anchor('api/explorer/menu_items', 'API Explorer', array(
        "class" => "btn btn-outline-secondary",
        "role" => "button"
    ));
}
echo '</p>';

echo Pagination::display($pagination_data);

if (count($rows) > 0): ?>
<div class="table-responsive">
    <table id="results-tbl" class="table table-bordered table-striped table-hover table-sm small">
        <caption class="small">List of Blog Sources</caption>
        <thead class="table-light">
            <tr>
                <th colspan="12">
                    <div class="d-flex justify-content-between py-1 flex-wrap gap-2">
                        <div class="d-flex">
                        	<?php
	                        echo form_open('menu_items/manage/1/', array(
                                "method" => "get",
                                "class" => "me-2"
                            ));
	                        echo '<div class="input-group input-group-sm">';
                            echo form_search('searchphrase', '', array(
                                "placeholder" => "Search records...",
                                "class" => "form-control"
                            ));
                            echo form_submit('submit', 'Search', array(
                                "class" => "alt btn btn-outline-secondary"
                            ));
                            echo '</div>';
                            echo form_close();
                            ?>
	                    </div>
                        <div class="btn-group btn-group-sm align-items-center small text-nowrap">
                            Records Per Page:
                            <?php
                            $dropdown_attr = array(
                                "onchange" => "setPerPage()",
                                "class" => "form-select form-select-sm"
                            );
                            echo form_dropdown('per_page', $per_page_options, $selected_per_page, $dropdown_attr); 
                            ?>
                        </div>
                    </div>                    
                </th>
            </tr>
            <tr class="table-dark">
                <th scope="col" style="text-align: right; width: 1.5rem;">#</th>
                <th>Active</th>
                <th style="width: 40px;">ID</th>
                <th>Title</th>
                <th>URL String</th>
                <th>Menu</th>
                <th>Parent ID</th>
                <th>Sort Order</th>
                <th>Target</th>
                <th>Date Created</th>
                <th>Date Updated</th>
                <th style="width: 20px;">Action</th>            
            </tr>
        </thead>
        <tbody>
            <?php 
            $i = 1;
            $attr = array(
                "class" => "button alt btn btn-outline-secondary btn-sm py-0",
                "role" => "button"
            );
            foreach($rows as $row): 
                $published = ($row->published == 1) ? 'fa-check-square' : 'fa-times-circle';
                $published_status = ($row->published == 1) ? 'Published' : 'Not published';
            ?>
            <tr class="align-middle">
            	<th scope="row" style="text-align: right;"><?= $i++ ?></th>
                <td style="text-align: center;"><i class="fa <?= $published ?>" id="published-icon-<?= $row->id ?>" title="<?= $published_status ?>"></i></td>
                <td style="text-align: right;"><?= $row->id ?></td>
                <td><?= out($row->title) ?></td>
                <td><?= out($row->url_string) ?></td>
                <td><?= out($row->menus_id) ?></td>
                <td><?= out($row->parent_id) ?></td>
                <td><?= out($row->sort_order) ?></td>
                <td><?= out($row->target) ?></td>
                <td><?= date($date_format, strtotime($row->date_created)) ?></td>
                <td><?php 
                if (strtotime($row->date_updated) != 0) {
                    echo date($date_format,  strtotime($row->date_updated));
                } else { echo 'never';}
                ?></td>
                <td><?= anchor('menu_items/show/'.$row->id, 'View', $attr) ?></td>        
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php 
    if(count($rows)>9) {
        unset($pagination_data['include_showing_statement']);
        echo Pagination::display($pagination_data);
    }
endif;
?>
<script>
    document.querySelectorAll('[id^="published-icon"]').forEach(icon => {
        icon.addEventListener('click', (ev) => togglePublishedStatus(ev.target));
    });

    function togglePublishedStatus(clickedIcon) {
        const newStatus = clickedIcon.classList.contains('fa-times-circle') ? 1 : 0;
        const recordId = parseInt(clickedIcon.id.replace('published-icon-', ''));
        const params = { published: newStatus };

        clickedIcon.style.display = 'none';
        const targetUrl = '<?= BASE_URL ?>api/update/menu_items/' + recordId;
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
        /*color: rgba(var(--bs-success-rgb)); */
    }

    .fa-times-circle {
        color: #a84247;
        /*color: rgba(var(--bs-danger-rgb)); */
    }
</style>