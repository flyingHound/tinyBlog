<div class="card h-100">
    <div class="card-heading card-header">
        Picture Gallery
    </div>
    <div class="card-body picture-preview">
        <div class="d-flex gap-2 mb-3">
            <?= anchor($uploader_url, '<i class="fa fa-image"></i> Upload Pictures', ['class' => 'button btn btn-primary btn-sm']) ?>
            <?= anchor($order_url, '<i class="fa fa-list-ul"></i> Order', ['class' => 'button alt btn btn-secondary btn-sm']) ?>
        </div>

        <?php if (empty($pictures)): ?>
            <div id="gallery-pics" class="text-center p-4">
                <p class="text-center text-muted fst-italic mb-0">There are currently no gallery pictures for this record.</p>
            </div>
        <?php else: ?>
            <div id="gallery-pics" class="gallery-grid">
                <?php foreach ($pictures as $picture):
                    $el_id = str_replace('.', '-', $picture);
                    $thumb_path = $thumbs_target . '/' . $picture;
                    $full_path = $pictures_target . '/' . $picture;
                ?>
                    <div id="gallery-preview-<?= htmlspecialchars($el_id) ?>" 
                         class="gallery-item" 
                         data-bs-toggle="modal" 
                         data-bs-target="#filezone-preview-modal"
                         data-full-path="<?= htmlspecialchars($full_path) ?>"
                         data-pic-path="<?= htmlspecialchars($full_path) ?>"
                         role="button"
                         aria-label="View picture <?= htmlspecialchars($picture) ?>">
                        <img src="<?= htmlspecialchars($thumb_path) ?>" 
                             alt="Thumbnail of <?= htmlspecialchars($picture) ?>" 
                             class="img-fluid rounded">
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="upload-dir text-muted small p-2 bg-light rounded">
            <i class="fa fa-folder-open"></i> <?= htmlspecialchars($pictures_dir) ?>
        </div>
    </div>
</div>

<div class="modal fade" id="filezone-preview-modal" tabindex="-1" aria-labelledby="filezonePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filezonePreviewModalLabel">
                    <i class="fa fa-image"></i> Picture Preview
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img id="filezone-preview-img" src="" alt="Picture preview" class="img-fluid d-block mx-auto mb-3">
                <div class="d-flex justify-content-end gap-2">
                    <?= form_button('close', 'Cancel', ['class' => 'btn btn-secondary btn-sm', 'data-bs-dismiss' => 'modal']) ?>
                    <?= form_button('delete_pic', 'Delete This Picture', [
                        'class' => 'btn btn-danger btn-sm',
                        'id' => 'filezone-ditch-btn',
                        'onclick' => 'ditchFilezonePic()',
                        'aria-label' => 'Delete this picture'
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>

.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
    padding: 0;
}

.gallery-item {
    cursor: pointer;
    transition: transform 0.2s;
}

.gallery-item:hover {
    transform: scale(1.05);
}

/* for equalized height */
#gallery-pics div img { 
    width: 100%;
    max-height: 150px;
}
</style>

<script>
const targetModule = '<?= $target_module ?>';

function ditchFilezonePic() {
    const picPath = document.getElementById('filezone-ditch-btn').dataset.picPath;
    if (!picPath || !confirm('Really delete this picture?')) return;

    const http = new XMLHttpRequest();
    http.open('DELETE', `${baseUrl}blog_filezone/upload/<?= $target_module ?>/<?= $update_id ?>`);
    http.setRequestHeader('Content-Type', 'application/json');
    http.setRequestHeader('trongateToken', token);
    
    http.onload = () => http.status === 200 ? window.location.reload() : alert('Error: ' + http.responseText);
    http.onerror = () => alert('Network Error');
    http.send(JSON.stringify({ picture_path: picPath }));
}

document.addEventListener('DOMContentLoaded', function () {
    const previewModal = document.getElementById('filezone-preview-modal');
    if (previewModal) {
        previewModal.addEventListener('show.bs.modal', function (event) {
            const galleryItem = event.relatedTarget; // Das Element, das das Modal geöffnet hat
            const fullPath = galleryItem.getAttribute('data-full-path');
            const picPath = galleryItem.getAttribute('data-pic-path');

            // set source of the picture
            const previewImg = document.getElementById('filezone-preview-img');
            if (previewImg) {
                previewImg.src = fullPath;
            }

            // set data-pic-path for delete-button
            const deleteBtn = document.getElementById('filezone-ditch-btn');
            if (deleteBtn) {
                deleteBtn.setAttribute('data-pic-path', picPath);
            }
        });
    }
});


function OLD_ditchFilezonePic() {
    const picPath = document.getElementById('filezone-ditch-btn').dataset.picPath;
    
    if (!picPath) {
        alert('Error: No picture path found.');
        return;
    }

    if (confirm('Really delete this picture?')) {
        fetch('<?= BASE_URL ?>blog_filezone/upload/<?= $target_module ?>/<?= $update_id ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'trongateToken': '<?= $token ?>'
            },
            body: JSON.stringify({ picture_path: picPath, _method: 'DELETE' })
        })
        .then(response => response.json())
        .then(data => {
            if (data === picPath) {
                closeModal();
                const elId = picPath.split('/').pop().replace('.', '-');
                document.getElementById('gallery-preview-' + elId)?.remove();
            } else {
                throw new Error('Delete failed: ' + JSON.stringify(data));
            }
        })
        .catch(error => {
            console.error('ERROR:', error);
            alert('Error deleting picture: ' + error.message);
        });
    }
}

// equalize height of gallery pics
document.addEventListener('DOMContentLoaded', function () {
    const images = document.querySelectorAll('#gallery-pics img');
    let minHeight = Infinity;

    // wait untill pictures are loaded
    Promise.all(Array.from(images).map(img => {
        return img.complete ? Promise.resolve(img) : new Promise(resolve => {
            img.onload = () => resolve(img);
            img.onerror = () => resolve(img);
        });
    })).then(() => {
        // find smallest height among landscape pictures
        images.forEach(img => {
            const naturalRatio = img.naturalWidth / img.naturalHeight;
            if (naturalRatio > 1) { // landscape
                minHeight = Math.min(minHeight, img.clientHeight);
            }
        });

        // give pics a height and crop centered
        images.forEach(img => {
            img.style.height = minHeight + 'px';
            img.style.objectFit = 'cover';
            img.style.objectPosition = 'center';
        });
    });
});
</script>