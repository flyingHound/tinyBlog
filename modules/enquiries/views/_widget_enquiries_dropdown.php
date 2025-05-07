<button class="header__btn btn btn-icon btn-sm me-2 dropdown-toggle" title ="Enquiries" type="button" data-bs-toggle="dropdown" aria-label="Enquiries dropdown" aria-expanded="false">
    <span class="d-block position-relative">
        <?php
            $btn_icon = ($num_unread_enquiries > 0)
                ? '<i class="fa fa-envelope text-warning"></i>'
                : '<i class="fa fa-envelope-o text-secondary"></i>';
        ?>
        <?= $btn_icon ?>
        <?php if ($num_unread_enquiries > 0): ?>
            <span class="badge rounded-pill bg-danger p-1">
                <?= $num_unread_enquiries ?>
                <span class="visually-hidden">unread enquiries</span>
            </span>
        <?php endif; ?>
    </span>
</button>

<div class="dropdown-menu dropdown-menu-end">
    <div class="dropdown-header">
        <h5 class="mb-0"><?= out($headline) ?></h5>
    </div>

    <div class="list-group">
        <?php if (!empty($rows)): ?>
            <?php $i = 1; ?>
            <?php foreach ($rows as $row): ?>
                <a href="<?= BASE_URL ?>enquiries/show/<?= $row->id ?>" class="list-group-item list-group-item-action d-flex align-items-start">
                    <div class="flex-shrink-0 me-3">
                        <?php
                            $row_icon = ($row->opened === 0)
                                ? '<i class="fa fa-envelope text-warning"></i>'
                                : '<i class="fa fa-envelope-o"></i>';
                        ?>
                        <?= $row_icon ?>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <span class="fw-normal mb-0 stretched-link">
                                <?= htmlspecialchars($row->name ?? 'Unnamed Enquiry', ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <?php if ($row->opened === 0): ?>
                                <span class="badge bg-info rounded-pill">NEW</span>
                            <?php endif; ?>
                        </div>
                        <small class="text-body-secondary">
                            <?= date('jS F Y \a\t H:i', $row->date_created) ?>
                        </small>
                        <small class="text-body-secondary">
                            <?= htmlspecialchars($row->email_address ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </small>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="list-group-item text-center">
                <em>No enquiries found.</em>
            </div>
        <?php endif; ?>

        <div class="list-group-item text-center">
            <a href="<?= BASE_URL ?>enquiries/manage" class="smalltext-decoration-none">
                Show all Enquiries
                <i class="fa fa-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</div>

<style>
    /* Dropdown bleibt Bootstrap-Standard mit hellem Hintergrund */
    .dropdown-menu {
        min-width: 250px; 
    }

    /*.list-group-item {
        color: #333;
    }

    .list-group-item:hover {
        background-color: #f8f9fa; 
        color: #0d6efd; 
    } */
</style>