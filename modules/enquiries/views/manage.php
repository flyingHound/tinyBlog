<h1><?= out($headline) ?></h1>

<?php flashdata(); ?>

<div class="d-flex gap-2 mb-3">
    <?php /* anchor('enquiries/create', 'Create New Enquiry Record', array(
        "class" => "button btn btn-primary", 
        "role" => "button"
    ));*/ ?>
</div>

<?= Pagination::display($pagination_data) ?>

<?php if (count($rows) > 0) { ?>
    <div class="table-responsive">
        <table id="results-tbl" class="table table-bordered table-striped table-hover table-sm small">
            <caption class="small">List of Enquiries</caption>
            <thead class="table-light">
                <tr>
                    <th colspan="6">
                        <div class="table-header d-flex justify-content-between align-items-center py-1 flex-wrap gap-2">
                            <div class="d-flex">
                                <?php
                                echo form_open('enquiries/manage/1/', array('method' => 'get', 'class' => 'me-2'));
                                echo '<div class="input-group input-group-sm">';
                                echo form_input('searchphrase', '', array('placeholder' => 'Search records...', 'class' => 'form-control'));
                                echo form_submit('submit', 'Search', array('class' => 'alt btn btn-outline-secondary btn-sm'));
                                echo '</div>';
                                echo form_close();
                                ?>
                                    
                            </div>
                            <div class="btn-group btn-group-sm align-items-center small text-nowrap">
                                Records Per Page:
                                <?= form_dropdown('per_page', $per_page_options, $selected_per_page, [
                                    'class' => 'form-select form-select-sm ms-2',
                                    'onchange' => 'setPerPage()'
                                ]) ?>
                            </div>
                        </div>                    
                    </th>
                </tr>
                <tr class="table-dark">
                    <th scope="col" style="text-align: right; width: 1.5rem;">#</th>
                    <th style="width: 40px;">ID</th>
                    <th>Name</th>
                    <th>Email Address</th>
                    <th>Date Sent</th>
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
                foreach($rows as $row) { 
                    $row_icon = ($row->opened == 'no') 
                    ? '<i class="fa fa-envelope"></i> ' 
                    : '<i class="fa fa-envelope-open"></i> ';
                ?>
                <tr class="align-middle">
                    <th scope="row" style="text-align: right;"><?= $i++ ?></th>
                    <td style="text-align: right;"><?= $row->id ?></td>
                    <td><?= $row_icon.' '.$row->name ?></td>
                    <td><?= $row->email_address ?></td>
                    <td><?= date('l jS F Y \a\t H:i',  $row->date_created) ?></td>
                    <td><?= anchor('enquiries/show/'.$row->id, 'View', $attr) ?></td>        
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
<?php 
    if(count($rows)>9) {
        unset($pagination_data['include_showing_statement']);
        echo Pagination::display($pagination_data);
    }
}
?>

<style>
    #results-tbl .fa-envelope {
        color: #d2b207;
    }

    #results-tbl .fa-envelope-open {
        color: #b9b9b9;
    }

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
  background-color: var(--primary);
  color: white;
  border: 1px solid var(--primary);
}

.pagination a:hover:not(.active) { background-color: #ddd; }
.pagination a:first-child { border-top-left-radius: 5px; border-bottom-left-radius: 5px; }
.pagination a:last-child { border-top-right-radius: 5px; border-bottom-right-radius: 5px; }
</style>