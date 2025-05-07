<div class="card widget-enquiries">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <?= anchor('enquiries/manage', '<i class="fa fa-envelope-o fa-lg text-warning me-2"></i>Enquiries', [
                'aria-label' => 'Manage Enquiries',
                'title' => 'Manage Enqiries'
            ]) ?>
            <span class="float-end"><?= $num_unread_enquiries ?> new of <?= $num_enquiries ?></span>
        </h5>
    </div>
    <div class="card-body p-3">
        <table class="table table-responsive table-sm small">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th>Name</th>
                    <th>Email Address</th>
                    <th>Date Sent</th>
                    <th style="width: 20px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 1; 
                if (!empty($rows)):
                    $attr = array(
                        'class' => 'button alt btn btn-sm btn-secondary',
                        'role' => 'button'
                    );
                    foreach ($rows as $row): 
                        $row_icon = ($row->opened === 0)
                            ? '<i class="fa fa-envelope text-warning"></i>'
                            : '<i class="fa fa-envelope-o text-secondary"></i>';
                ?>
                    <tr>
                        <th scope="row"><?= $i++ ?></th>
                        <td><?= $row_icon . ' ' . '<a href="enquiries/show/' . $row->id . '">' . htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8') . '</a>' ?></td>
                        <td><?= htmlspecialchars($row->email_address, ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= date('l jS F Y \a\t H:i', $row->date_created) ?></td>
                        <td title="Click to view full enquiry"><?= anchor('enquiries/show/' . $row->id, 'View', $attr) ?></td>        
                    </tr>
                <?php 
                    endforeach;
                else: ?>
                    <tr>
                        <td colspan="5"><em>No enquiries found.</em></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer p-2">
        <?= anchor('enquiries/manage', '<i class="fa fa-list-ul"></i> Manage Enquiries', [
            'class' => 'btn btn-outline-dark me-2',
            'role' => 'button',
            'aria-label' => 'Manage enquiries'
        ]) ?>
    </div>
</div>
<style>
    .card.widget-enquiries { height: 100%; /*max-height: 350px;*/ }

    .widget-enquiries a { text-decoration: none; }
    .widget-enquiries .card-body { overflow: auto; }

    .widget-enquiries tr td:last-child {
        text-align: right;
    }
</style>