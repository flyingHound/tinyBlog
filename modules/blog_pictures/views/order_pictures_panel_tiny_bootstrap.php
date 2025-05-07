<div class="container my-4">
    <h1 class="h3">
        <?= $headline ?>
        <small class="text-muted d-none d-sm-inline">
            ( <?= ucfirst($target_module) ?> ID: <?= $target_module_id ?> )
        </small>
    </h1>

    <p class="text-secondary fs-6">
        Take the picture to the desired position. Don’t forget to <u>SAVE</u> when you are finished.
        If you want to delete a picture, double-click on it.
    </p>

    <?= flashdata() ?>
    <?= validation_errors() ?>
    <div id="errorMsg" class="text-danger"></div>

    <div class="card shadow-sm">
        <div class="card-header">
            <strong>Options</strong> <span style="float: right;">count: <?= $rows_count ?></span>
        </div>

        <div class="card-body d-flex flex-wrap gap-2">
            <?= anchor($cancel_url, 'Go Back', ['class' => 'btn btn-secondary']) ?>
            <button id="submit-order-btn" class="btn btn-primary" style="min-width: 12em;">Save Order</button>
            <?= anchor($upload_url, 'Upload More Pictures', ['class' => 'btn btn-success ms-auto']) ?>
        </div>

        <div class="card-body">
            <?php if (count($rows) == 0): ?>
                <div id="gallery-pics" class="d-grid" style="grid-template-columns: repeat(1, 1fr);">
                    <p class="text-center text-muted">There are no pictures for this record yet.</p>
                </div>
            <?php else: ?>
                <div id="gallery-pics" class="d-grid gap-3" style="grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));">
                    <?php
                    $i = 1;
                    foreach ($rows as $row):
                        $picture_path = $target_directory.$row->picture;
                    ?>
                    <div class="sort border rounded p-2 text-center" id="<?= $row->id ?>" ondblclick="openPicPreview('preview-pic-modal', '<?= $picture_path ?>')">
                        <img src="<?= $picture_path ?>" alt="<?= $row->picture ?>" class="img-fluid mb-2">
                        <div class="small"><b>#<?= $i ?></b></div>
                    </div>
                    <?php $i++; endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="card-footer text-end">
            <button id="submit-order-footer-btn" class="btn btn-primary">Save Order</button>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="preview-pic-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-image me-2"></i> Picture</h5>
                    <button type="button" class="btn-close" onclick="closeModal()" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" id="preview-pic"></div>
                <div class="modal-footer justify-content-between">
                    <?= form_button('close', 'Cancel', ['class' => 'btn btn-secondary', 'onclick' => 'closeModal()']) ?>
                    <?= form_button('rotate_pic', 'Rotate This Picture', ['class' => 'btn btn-warning', 'id' => 'rotate-pic-btn', 'onclick' => 'rotatePreviewPic()']) ?>
                    <?= form_button('delete_pic', 'Delete This Picture', ['class' => 'btn btn-danger', 'id' => 'ditch-pic-btn', 'onclick' => 'ditchGalleryPic()']) ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="module">

// --- Globale Konstanten ---
const baseUrl = '<?= BASE_URL ?>';
const token = '<?= $token ?>';
const segment1 = '<?= $target_module ?>';
const updateId = '<?= $target_module_id ?>';
const uploadUrl = '<?= $upload_url ?>';
const deleteUrl = '<?= $delete_url ?>';

document.addEventListener('DOMContentLoaded', () => {
    const galleryPics = document.getElementById('gallery-pics');

    if (galleryPics) {
        new Sortable(galleryPics, {
            animation: 150,
            onEnd: ordenOk
        });
    }

    const submitOrderBtn = document.getElementById('submit-order-btn');
    if (submitOrderBtn) {
        submitOrderBtn.addEventListener('click', submitOrder);
    }

    const submitOrderFooterBtn = document.getElementById('submit-order-footer-btn');
    if (submitOrderFooterBtn) {
        submitOrderFooterBtn.addEventListener('click', submitOrder);
    }
});

// --- Funktionen ---

function ordenOk() {
    const errorMsg = document.getElementById('errorMsg');
    if (errorMsg) {
        errorMsg.textContent = 'Saving Ordered Pictures';
    }
}

function submitOrder() {
    const nodes = document.querySelectorAll('.sort');
    
    nodes.forEach((node, index) => {
        const recordId = node.id;
        const pos = index + 1;

        const params = {
            id: recordId,
            priority: pos
        };

        const orderUrl = `${baseUrl}api/update/blog_pictures/${recordId}`;

        fetch(orderUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'trongateToken': token
            },
            body: JSON.stringify(params)
        })
        .then(response => {
            if (!response.ok) throw new Error('Failed to save order');
        })
        .catch(error => console.error(error));
    });

    console.log('Ordered the pictures');
    location.reload();
}

function openPicPreview(modalId, picPath) {
    openModal(modalId);

    const targetEl = document.getElementById('preview-pic');
    targetEl.innerHTML = '';

    const imgPreview = document.createElement('img');
    imgPreview.src = picPath;
    targetEl.appendChild(imgPreview);

    const ditchPicBtn = document.getElementById('ditch-pic-btn');
    if (ditchPicBtn) {
        const iconCode = '<i class="fa fa-trash"></i>';
        ditchPicBtn.innerHTML = iconCode + ditchPicBtn.textContent.trim();
    }
}

function ditchPreviewPic() {
    const previewPic = document.querySelector('#preview-pic img');
    if (!previewPic) return;

    const picPath = previewPic.src;
    const removePicUrl = `${baseUrl}blog_filezone/upload/${segment1}/${updateId}`;

    fetch(removePicUrl, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'trongateToken': token
        },
        body: JSON.stringify({ picture_path: picPath })
    })
    .then(response => {
        if (!response.ok) throw new Error('Failed to delete preview pic');
        location.reload();
    })
    .catch(error => {
        console.error(error);
        alert('Network error while deleting preview pic');
    });

    closeModal();
}

function ditchGalleryPic() {
    const previewPic = document.querySelector('#preview-pic img');
    if (!previewPic) return;

    const picPath = previewPic.src;
    const removePicUrl = `${baseUrl}blog_filezone/upload/${segment1}/${updateId}`;

    fetch(removePicUrl, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'trongateToken': token
        },
        body: JSON.stringify({ picture_path: picPath })
    })
    .then(response => {
        if (!response.ok) throw new Error('Failed to delete gallery pic');
        location.reload();
    })
    .catch(error => {
        console.error(error);
        alert('Network error while deleting gallery pic');
    });
}

function rotatePreviewPic() {
    const previewPic = document.querySelector('#preview-pic img');
    if (!previewPic) return;

    const picPath = previewPic.src;
    const rotatePicUrl = `${baseUrl}blog_filezone/rotate`;

    fetch(rotatePicUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'trongateToken': token
        },
        body: JSON.stringify({ picture_path: picPath })
    })
    .then(response => response.json())
    .then(data => {
        const targetEl = document.getElementById('preview-pic');
        targetEl.innerHTML = '';

        const imgPreview = document.createElement('img');
        imgPreview.src = picPath + '?t=' + new Date().getTime(); // Cache umgehen
        targetEl.appendChild(imgPreview);

        alert('Picture rotated successfully');
    })
    .catch(error => {
        console.error(error);
        alert('Failed to rotate picture');
    });
}

function closeModal() {
    const modalContainer = document.getElementById('modal-container');
    const overlay = document.getElementById('overlay');

    if (modalContainer) {
        modalContainer.style.display = 'none';
    }
    if (overlay) {
        overlay.remove();
    }
}

// --- Hilfsfunktionen ---

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
        modal.style.opacity = 1;
        modal.style.zIndex = 9999;
    }
}

</script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>