<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0"><?= out($headline) ?> <small class="fs-6 text-muted d-none d-md-inline">(Record ID: <?= out($update_id) ?>)</small></h1>
</div>

<!-- Flash Messages -->
<?php flashdata(); ?>

<!-- Options Card -->
<div class="card mb-3">
    <div class="card-header">Options</div>
    <div class="card-body d-flex gap-2">
        <?= anchor('blog_posts/manage', 'View All Blog Posts', ['class' => 'btn btn-outline-primary']) ?>
        <?= anchor('blog_posts/create/' . $update_id, 'Update Details', ['class' => 'btn btn-primary']) ?>
        <?php 
        $attr_delete = array( 
            "class" => "btn btn-danger ms-auto",
            "data-bs-toggle" => "modal",
            "data-bs-target" => "#delete-modal"
        );
        echo form_button('delete', 'Delete', $attr_delete); ?>
    </div>
</div>

<!-- Blog Post Content -->
<div class="row">
    <!-- Blog Post Details -->
    <div class="col-12 col-md-6 col-xl-4 mb-3">
        <div class="card h-100">
            <div class="card-header">
                Blog Post Details
            </div>
            <div class="card-body">
                <div class="record-details">
                    <div class="row detail-row">
                        <div class="col-4 label">Title</div>
                        <div class="col-8 value"><?= htmlspecialchars_decode($title, ENT_QUOTES) ?></div>
                    </div>
                    <div class="row detail-row">
                        <div class="col-4 label">Subtitle</div>
                        <div class="col-8 value"><?= out($subtitle) ?></div>
                    </div>
                    <div class="row detail-row">
                        <div class="col-12 label">
                            Text <span class="float-end">[<?= $text_count ?> WORDS]</span>
                        </div>
                        <div class="col-12 value">
                            <div><?= $text_short ?></div>
                            <div class="text-end mt-2">
                                <button class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#article-preview">
                                    <i class="fa fa-file"></i> Preview
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row detail-row">
                        <div class="col-4 label">Source</div>
                        <div class="col-8 value"><?= isset($source->author) ? out($source->author) : '' ?></div>
                    </div>
                    <div class="row detail-row">
                        <div class="col-4 label">Category</div>
                        <div class="col-8 value">
                            <?php if ($category && !empty($category->title)): ?>
                                <a href="<?= BASE_URL ?>blog_categories/create/<?= $category->id ?>"><?= $category->title ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row detail-row">
                        <div class="col-4 label">YouTube ID</div>
                        <div class="col-8 value"><?= out($youtube) ?></div>
                    </div>
                    <div class="row detail-row">
                        <div class="col-4 label">Created</div>
                        <div class="col-8 value">
                            <?= date($date_format, strtotime($date_created)) ?>
                            <span class="creator d-block mt-1 text-muted"><?= 'by ' . $created_by ?></span>
                        </div>
                    </div>
                    <div class="row detail-row">
                        <div class="col-4 label">Updated</div>
                        <div class="col-8 value">
                            <?php if ($updated_by != ''): ?>
                                <?= date($date_format, strtotime($date_updated)) ?> 
                                <span class="creator d-block mt-1 text-muted"><?= 'by ' . $updated_by ?></span>
                            <?php else: ?>
                                <span class="dimmed">never</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php
                    $published_status = $published == 1 ? 'Published' : 'Not Published';
                    $published_icon_class = $published == 1 ? 'fa-check-square' : 'fa-times-circle';
                    ?>
                    <div class="row detail-row">
                        <div class="col-4 label">
                            <span class="published-label"><?= $published_status ?></span>
                        </div>
                        <div class="col-8 value">
                            <span class="published-date"><?= date($date_format, strtotime($date_published)) ?></span>
                            <span class="published-icon float-end">
                                <i class="fa <?= $published_icon_class ?>" id="published-icon-<?= $id ?>" title="<?= $published_status ?>"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Picture -->
    <div class="col-12 col-md-6 col-xl-4 mb-3">
        <div class="card h-100">
            <div class="card-header">Picture</div>
            <div class="card-body picture-preview">
                <?php if ($draw_picture_uploader): ?>
                    <?= form_open_upload(segment(1) . '/submit_upload_picture/' . $update_id) ?>
                        <?= validation_errors() ?>
                        <p class="mb-3">Please choose a picture from your computer and then press 'Upload'.</p>
                        <?= form_file_select('picture', ['class' => 'form-control mb-3', 'id' => 'picture-input']) ?>
                        <?= form_submit('submit', 'Upload', ['class' => 'btn btn-primary', 'id' => 'upload-button', 'style' => 'display: none;']) ?>
                    <?= form_close() ?>
                <?php else: ?>
                    <div class="text-center mb-3">
                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#delete-picture-modal">
                            <i class="fa fa-trash"></i> Delete Picture
                        </button>
                    </div>
                    <img src="<?= $picture_url ?>" alt="picture preview" class="img-fluid rounded mb-3">
                    <figcaption class="text-muted text-center mb-1"><?= $picture ?></figcaption>
                    <div class="upload-dir text-muted small p-2 bg-light rounded">
                        <i class="fa fa-folder-open"></i> <?= htmlspecialchars($picture_folder) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Filezone Summary -->
    <div class="col-12 col-md-6 col-xl-4 mb-3">
        <?= Modules::run('blog_filezone/_draw_summary_panel', $update_id, $filezone_settings) ?>
    </div>

    <!-- YouTube Video -->
    <div class="col-12 col-md-6 col-xl-4 mb-3">
        <div class="card h-100">
            <div class="card-header">
                YouTube Video
                <span class="float-end fs-6 text-muted">ID: <?= htmlspecialchars($youtube) ?></span>
            </div>
            <div class="card-body picture-preview text-center">
                <?php if (!empty($youtube)): ?>
                    <div class="video-container">
                        <iframe data-src="https://www.youtube-nocookie.com/embed/<?= htmlspecialchars($youtube) ?>"
                                title="YouTube video player" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                    </div>
                <?php else: ?>
                    <p class="text-muted fst-italic">No video</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Editor Images -->
    <div class="col-12 col-md-6 col-xl-4 mb-3">
        <div class="card h-100">
            <div class="card-header">Embedded Text Images</div>
            <div class="card-body picture-preview">
                <?php if (is_null($editor_images)): ?>
                    <p class="text-center text-muted fst-italic">No images uploaded yet.</p>
                <?php else: ?>
                    <ul class="list-unstyled editor-images-list mb-0">
                        <?php foreach ($editor_images as $image):
                            $modal_id = 'delete-editor-image-' . str_replace([' ', '.', '_'], '-', $image['filename']);
                        ?>
                            <li class="image-item mb-1">
                                <div class="row align-items-center">
                                    <div class="col-4 col-md-3 image-preview">
                                        <img src="<?= $image['url'] ?>" alt="<?= htmlspecialchars($image['filename']) ?>" class="img-fluid rounded">
                                    </div>
                                    <div class="col-8 col-md-9 image-info">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="filename mb-0"><?= htmlspecialchars($image['filename']) ?></p>
                                            <button class="btn btn-danger btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#<?= $modal_id ?>">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <!-- Delete Editor Image Modal -->
                            <div class="modal fade" id="<?= $modal_id ?>" tabindex="-1" aria-labelledby="<?= $modal_id ?>-label" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title" id="<?= $modal_id ?>-label">
                                                <i class="fa fa-trash"></i> Delete Image
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <?= form_open(segment(1) . '/delete_editor_image/' . $update_id, ['id' => 'form-' . $modal_id]) ?>
                                                <?= form_hidden('filename', $image['filename']) ?>
                                                <p>Are you sure you want to delete <strong><?= htmlspecialchars($image['filename']) ?></strong>?</p>
                                                <p>This cannot be undone.</p>
                                                <div class="text-end">
                                                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                                                    <?= form_submit('submit', 'Yes - Delete Now', ['class' => 'btn btn-danger']) ?>
                                                </div>
                                            <?= form_close() ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Blog Tags Relation Panel -->
    <div class="col-12 col-md-6 col-xl-4 mb-3">
        <?= Modules::run('module_relations/_draw_summary_panel_tbs', 'blog_tags', $token) ?>
    </div>

    <!-- Comments -->
    <div class="col-12 col-md-6 col-xl-4 mb-3">
        <div class="card h-100">
            <div class="card-header">
                Comments
            </div>
            <div class="card-body"> 
                    <p><button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#comment-modal">Add New Comment</button></p>
                <div id="comments-block"><table class="table"></table></div>
            </div>
        </div>
    </div>
</div>

<!-- Article Preview Modal -->
<div class="modal fade" id="article-preview" tabindex="-1" aria-labelledby="articlePreviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="articlePreviewLabel">Blog Post Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h1><?= htmlspecialchars_decode($title, ENT_QUOTES) ?></h1>
                <h4 class="text-muted"><?= htmlspecialchars_decode($subtitle, ENT_QUOTES) ?></h4>
                <?= htmlspecialchars_decode($text, ENT_QUOTES) ?>
            </div>
        </div>
    </div>
</div>

<!-- Delete Picture Modal -->
<div class="modal fade" id="delete-picture-modal" tabindex="-1" aria-labelledby="deletePictureModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deletePictureModalLabel"><i class="fa fa-trash"></i> Delete Picture</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?= form_open(segment(1) . '/ditch_picture/' . $update_id) ?>
                    <p>Are you sure?</p>
                    <p>You are about to delete the picture. This cannot be undone. Do you really want to do this?</p>
                    <div class="text-end">
                        <button type="button" class="btn btn-outline-primary me-2" data-bs-dismiss="modal">Cancel</button>
                        <?= form_submit('submit', 'Yes - Delete Now', ['class' => 'btn btn-danger']) ?>
                    </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<!-- Comment Modal -->
<div class="modal fade" id="comment-modal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-commenting-o"></i> Add New Comment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><textarea placeholder="Enter comment here..." class="form-control"></textarea></p>
                <p class="text-end">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitComment()">Submit Comment</button>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Delete Blog Post Modal -->
<div class="modal fade" id="delete-modal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel"><i class="fa fa-trash"></i> Delete Record</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?= form_open('blog_posts/submit_delete/' . $update_id, ['id' => 'delete-form']) ?>
                    <p>Are you sure?</p>
                    <p>You are about to delete a Blog Post record. This cannot be undone. Do you really want to do this?</p>
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                        <?= form_submit('submit', 'Yes - Delete Now', ['class' => 'btn btn-danger']) ?>
                    </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
const token = '<?= $token ?>';
const baseUrl = '<?= BASE_URL ?>';
const segment1 = '<?= segment(1) ?>';
const updateId = '<?= $update_id ?>';
const drawComments = true;

// Show picture upload button after file was selected
document.addEventListener('DOMContentLoaded', () => {
    const pictureInput = document.getElementById('picture-input');
    const uploadButton = document.getElementById('upload-button');

    if (pictureInput && uploadButton) {
        pictureInput.addEventListener('change', () => {
            uploadButton.style.display = pictureInput.files.length > 0 ? 'inline-block' : 'none';
        });
    }
});

// Publish/Unpublish Toggle
document.querySelectorAll('[id^="published-icon"]').forEach(icon => {
    icon.addEventListener('click', (ev) => {
        const icon = ev.target;
        const newStatus = icon.classList.contains('fa-times-circle') ? 1 : 0;
        const recordId = parseInt(icon.id.replace('published-icon-', ''));

        icon.style.display = 'none';

        fetch(`${baseUrl}api/update/blog_posts/${recordId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'trongateToken': token
            },
            body: JSON.stringify({ published: newStatus })
        })
        .then(response => {
            if (response.ok) {
                const isUnpublished = icon.classList.contains('fa-times-circle');
                icon.classList.replace(
                    isUnpublished ? 'fa-times-circle' : 'fa-check-square',
                    isUnpublished ? 'fa-check-square' : 'fa-times-circle'
                );
                icon.title = isUnpublished ? 'Published' : 'Not Published';
                icon.style.display = 'inline-block';

                const label = document.querySelector('.published-label');
                if (label) label.textContent = isUnpublished ? 'Published' : 'Not Published';
            }
        })
        .catch(error => console.error('Error updating published status:', error));
    });
});

// Lazy Load YouTube Video
document.querySelectorAll('.video-container').forEach(container => {
    new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const iframe = entry.target.querySelector('iframe');
                iframe.src = iframe.dataset.src;
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 }).observe(container);
});
</script>

<!-- CSS -->
<style>
.record-details {
    overflow: hidden;
}

.record-details .detail-row {
    border-bottom: 1px solid var(--bs-border-color);
    padding: 0.5rem 0;
    align-items: center;
}

.record-details .label {
    font-size: 0.825rem;
    font-variant-caps: all-petite-caps;
    font-weight: bold;
    color: #6c757d; 
}

.record-details .value {
    margin-bottom: 0;
}

.record-details .detail-row:last-child {
    border-bottom: none;
}

.picture-preview {
    display: flex;
    flex-direction: column;
}

.picture-preview img,
.video-container iframe,
#article-preview img {
    max-width: 100%;
}

.upload-dir {
    margin-top: auto;
}

.fa-check-square {
    color: #42a8a3;
    cursor: pointer;
}

.fa-times-circle {
    color: #b44646;
    cursor: pointer;
}

.record-details .fa[id^="published-icon"] {
    font-size: 1.5rem;
}

.editor-images-list .filename {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
}
</style>