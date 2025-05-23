<h1><?= out($headline) ?></h1>
<?php
flashdata();

echo '<p class="d-flex gap-2">';
echo anchor('blog_sources/create', 'Create New Source Record', array(
    "class" => "button btn btn-primary",
    "role" => "button"
));

if(strtolower(ENV) === 'dev') {
    echo anchor('api/explorer/blog_sources', 'API Explorer', array(
        "class" => "button alt btn btn-outline-secondary",
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
                <th colspan="6">
                    <div class="d-flex justify-content-between py-1 flex-wrap gap-2">
                        <div class="d-flex">
                            <?php
                            echo form_open('blog_sources/manage/1/', array(
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
                <th style="width: 40px;">ID</th>
                <th>Author</th>
                <th>Website</th>
                <th>Link</th>
                <th style="width: 20px;" class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i = 1; 
            $attr = array(
                "class" => "button alt btn btn-outline-secondary btn-sm py-0",
                "role" => "button"
            );
            foreach($rows as $row): ?>
            <tr class="align-middle">
                <th scope="row" style="text-align: right;"><?= $i++ ?></th>
                <td style="text-align: right;"><?= $row->id ?></td>
                <td class="text-nowrap"><?= out($row->author) ?></td>
                <td><?= out($row->website) ?></td>
                <td><?= out($row->link) ?></td>
                <td class="text-center"><?= anchor('blog_sources/show/'.$row->id, 'View', $attr) ?></td>        
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php 
    if(count($rows) > 9) {
        unset($pagination_data['include_showing_statement']);
        echo Pagination::display($pagination_data);
    }
endif;
?>