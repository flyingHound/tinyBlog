<h1><?= out($headline) ?></h1>
<?php
flashdata();
echo '<p>'.anchor('menus/create', 'Create New Menu Record', array("class" => "button"));
if(strtolower(ENV) === 'dev') {
    echo anchor('api/explorer/menus', 'API Explorer', array("class" => "button alt"));
}
echo '</p>';
echo Pagination::display($pagination_data);
if (count($rows)>0) { ?>
    <table id="results-tbl">
        <thead>
            <tr>
                <th colspan="8">
                    <div>
                        <div><?php
                        echo form_open('menus/manage/1/', array("method" => "get"));
                        echo form_search('searchphrase', '', array("placeholder" => "Search records..."));
                        echo form_submit('submit', 'Search', array("class" => "alt"));
                        echo form_close();
                        ?></div>
                        <div>Records Per Page: <?php
                        $dropdown_attr['onchange'] = 'setPerPage()';
                        echo form_dropdown('per_page', $per_page_options, $selected_per_page, $dropdown_attr); 
                        ?></div>

                    </div>                    
                </th>
            </tr>
            <tr>
                <th>ID</th>
                <th>Active</th>
                <th>Name</th>
                <th>Description</th>
                <th>Template</th>
                <th>Date Created</th>
                <th>Date Updated</th>
                <th style="width: 20px;">Action</th>            
            </tr>
        </thead>
        <tbody>
            <?php 
            $attr['class'] = 'button alt';
            foreach($rows as $row) { 
                $published = ($row->published == 1) ? 'fa-check-square' : 'fa-times-circle';
                $published_status = ($row->published == 1) ? 'Published' : 'Not published';
            ?>
            <tr>
                <td><?= out($row->id) ?></td>
                <td style="text-align: center;"><i class="fa <?= $published ?>" id="published-icon-<?= $row->id ?>" title="<?= $published_status ?>"></i></td>
                <td><?= out($row->name) ?></td>
                <td><?= out($row->description) ?></td>
                <td><?= out($row->template) ?></td>
                <td><?= date($date_format,  strtotime($row->date_created)) ?></td>
                <td><?php 
                if (strtotime($row->date_updated) != 0) {
                    echo date($date_format,  strtotime($row->date_updated));
                } else { echo 'never';}
                ?></td>
                <td><?= anchor('menus/show/'.$row->id, 'View', $attr) ?></td>        
            </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
<?php 
    if(count($rows)>9) {
        unset($pagination_data['include_showing_statement']);
        echo Pagination::display($pagination_data);
    }
}
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
        const targetUrl = '<?= BASE_URL ?>api/update/menus/' + recordId;
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
    }

    .fa-times-circle {
        color: #a84247;
    }
</style>