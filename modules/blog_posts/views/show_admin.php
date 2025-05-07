<h1><?= out($headline) ?> <span class="smaller hide-sm">(Record ID: <?= out($update_id) ?>)</span></h1>
<?= flashdata('<p class="danger">', '</p>') ?>
<div class="card">
    <div class="card-heading">
        Options
    </div>
    <div class="card-body">
        <?php 
        echo anchor('blog_posts/manage', 'View All Blog Posts', array("class" => "button alt"));
        echo anchor('blog_posts/create/'.$update_id, 'Update Details', array("class" => "button"));
        $attr_delete = array( 
            "class" => "danger go-right",
            "id" => "btn-delete-modal",
            "onclick" => "openModal('delete-modal')"
        );
        echo form_button('delete', 'Delete', $attr_delete);
        ?>
    </div>
</div>
<div class="three-col">
    <?php /* Details */ ?>
    <div class="card">
        <div class="card-heading">
            Blog Post Details
        </div>
        <div class="card-body">
            <div class="record-details">
                <div class="row">
                    <div>Title</div>
                    <div><?= htmlspecialchars_decode($title, ENT_QUOTES) ?></div>
                </div>
                <div class="row">
                    <div>Subtitle</div>
                    <div><?= out($subtitle) ?></div>
                </div>
                <div class="row">
                    <div class="full-width">
                        <div><b>Text</b><span class="float-right">[<?= $text_count ?> WORDS]</span></div>
                        <div><?= $text_short ?></div>
                        <div class="text-right"><button class="alt smaller" onclick="openModal('article-preview')">READ ARTICLE</button></div>
                    </div>
                </div>
                <div class="row">
                    <div>Source</div>
                    <div><?= isset($source->author) ? out($source->author) : '' ?></div>
                </div>
                <div class="row">
                    <div>Category</div>
                    <div>
                        <?php if ($category && isset($category->title) && $category->title !== ''): ?>
                            <a href="<?= BASE_URL ?>blog_categories/create/<?= $category->id ?>"><?= $category->title ?></a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row">
                    <div>YouTube ID</div>
                    <div><?= out($youtube) ?> + Pics: <?= $picture_count ?></div>
                </div>
                <div class="row">
                    <div>Created</div>
                    <div>
                        <?= date($date_format,  strtotime($date_created)) ?>
                        <span class="creator"><?= 'by '.$created_by ?></span>
                    </div>
                </div>
                <div class="row">
                    <div>Last Updated</div>
                    <div>
                        <?php if($updated_by != '') { ?>
                        <?= date($date_format, strtotime($date_updated)) ?> 
                        <span class="creator">
                            <?= 'by '.$updated_by ?>
                        </span>
                        <?php } else { echo '<span class="dimmed">never</span>'; } ?>
                    </div>
                </div>
                <?php 
                // published
                $published_answer = ($published == 1 ? 'Yes' : 'No');
                $published_icon_str = ($published == '1') ? 'fa-check-square' : 'fa-times-circle';
                $published_status = $published == '1' ? 'Published' : 'Not Published';
                $published_icon = '<i class="fa '.$published_icon_str.'" id="published-icon-'.$id.'" title="'.$published_status.'"></i>';
                ?>
                <div class="row">
                    <div>
                        <span class="published-label"><?= $published_status ?></span>
                    </div>
                    <div>
                        <span class="published-date">
                            <?= date($date_format,  strtotime($date_published)) ?>
                        </span>
                        <span class="published-icon"> <?= $published_icon ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php /* Single Picture */ ?>
    <div class="card">
        <div class="card-heading">
            Picture
        </div>
        <div class="card-body picture-preview">
            <?php
            if ($draw_picture_uploader == true) {
                echo form_open_upload(segment(1).'/submit_upload_picture/'.$update_id);
                echo validation_errors();
                echo '<p>Please choose a picture from your computer and then press \'Upload\'.</p>';
                echo form_file_select('picture');
                echo form_submit('submit', 'Upload');
                echo form_close();
            } else {
            ?>
                <p class="text-center">
                    <button class="danger" onclick="openModal('delete-picture-modal')"><i class="fa fa-trash"></i> Delete Picture</button>
                </p>
                <p class="text-center">
                    <img max-width="400" height="" src="<?= $picture_url ?>" alt="picture preview">
                    <figcaption><?= $picture ?></figcaption>
                </p>

                <div class="upload-dir">
                    <i class="fa fa-folder-open-o"></i> <small><?= htmlspecialchars($picture_folder) ?></small>
                </div>

                <div class="modal" id="delete-picture-modal" style="display: none;">
                    <div class="modal-heading danger"><i class="fa fa-trash"></i> Delete Picture</div>
                    <div class="modal-body">
                        <?= form_open(segment(1).'/ditch_picture/'.$update_id) ?>
                            <p>Are you sure?</p>
                            <p>You are about to delete the picture.  This cannot be undone. Do you really want to do this?</p>
                            <p>
                                <button type="button" name="close" value="Cancel" class="alt" onclick="closeModal()">Cancel</button>
                                <button type="submit" name="submit" value="Yes - Delete Now" class="danger">Yes - Delete Now</button>
                            </p>
                        <?= form_close() ?>
                    </div>
                </div>
            <?php 
            }
            ?>
        </div>
    </div>

    <?= Modules::run('blog_filezone/_draw_summary_panel', $update_id, $filezone_settings); ?>

    <?php /* Instructions List */ ?>
    <div class="card">
        <div class="card-body">
            <h3>Instructions</h3>

            <ol class="list-counter">
                <li id="mv_create_911_1">Add the pasta to a large pot of boiling salted water over high heat and cook, stirring occasionally, until al dente.</li>
                <li id="mv_create_911_2">Drain, reserving about 1/4 cup of the pasta water.</li>
                <li id="mv_create_911_3">While the pasta is cooking, heat the oil in a large skillet over high heat, then add the pumpkin and sausages.</li>
                <li id="mv_create_911_4">Cook, breaking up the sausage into bite-size pieces, for 8 mins, or until the sausage and pumpkin are beginning to brown.</li>
                <li id="mv_create_911_5">Add the onion, garlic, and sage and cook, stirring often, until the onion softens, about 5 minutes.</li>
                <li id="mv_create_911_6">Stir in the cream and grated cheese, cook for about 3 minutes until thickened, then remove from the heat.</li>
                <li id="mv_create_911_7">Season with salt and pepper, then add to the pasta along with enough pasta water to loosen as needed.</li>
                <li id="mv_create_911_8">Cook briefly, stirring constantly over high heat until piping hot.</li>
                <li id="mv_create_911_9">Serve in individual pasta bowls topped with shavings of Parmesan cheese and cracked black pepper.</li>
            </ol>
        </div>
    </div>

    <?php /* YouTube VIDEO */ ?>
    <div class="card">
        <div class="card-heading">
            YouTube Video <span class="smaller">Id: <?= htmlspecialchars($youtube) ?></span>
        </div>
        <div class="card-body picture-preview">
            <?php if (!empty($youtube)): ?>
                <div class="video-container">
                    <iframe src="https://www.youtube-nocookie.com/embed/<?= htmlspecialchars($youtube) ?>" 
                            title="YouTube video player" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                            referrerpolicy="strict-origin-when-cross-origin" 
                            allowfullscreen></iframe>
                </div>
            <?php else: ?>
                <p class="text-center dimmed">No video</p>
            <?php endif; ?>
        </div>
    </div>

    <?php /* editor Images */ ?>
    <div class="card">
        <div class="card-heading">
            Editor Images
        </div>
        <div class="card-body picture-preview">
            <?php if (is_null($editor_images)): ?>
                <p class="text-center dimmed">No images uploaded yet.</p>
            <?php else: ?>
                <ul class="editor-images-list">
                    <?php foreach ($editor_images as $image):
                        $modal_id = 'delete-editor-image-' . str_replace([' ', '.', '_'], '-', $image['filename']);
                    ?>
                        <li class="image-item">
                            <div class="image-preview">
                                <img src="<?= $image['url'] ?>" alt="<?= htmlspecialchars($image['filename']) ?>">
                            </div>
                            <div class="image-info">
                                <p class="filename"><?= htmlspecialchars($image['filename']) ?></p>
                                <button class="danger smaller delete-btn" onclick="openModal('<?= $modal_id ?>')">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </div>
                            <div class="modal" id="<?= $modal_id ?>" style="display: none;">
                                <div class="modal-heading danger">
                                    <i class="fa fa-trash"></i> Delete Image
                                </div>
                                <div class="modal-body">
                                    <?php
                                    echo form_open(segment(1) . '/delete_editor_image/' . $update_id);
                                    echo form_hidden('filename', $image['filename']);
                                    ?>
                                    <p>Are you sure you want to delete <strong><?= htmlspecialchars($image['filename']) ?></strong>?</p>
                                    <p>This cannot be undone.</p>
                                    <p class="modal-actions">
                                        <?php
                                        echo form_button('close', 'Cancel', ['class' => 'alt', 'onclick' => 'closeModal()']);
                                        echo form_submit('submit', 'Yes - Delete Now', ['class' => 'danger']);
                                        echo form_close();
                                        ?>
                                    </p>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <style>
        /*.editor-images-list {
            list-style: none;
            padding: 0;
        }
        .editor-images-list li {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .editor-images-list img {
            max-width: 100px;
            height: auto;
        }
        .editor-images-list p {
            margin: 0;
            word-break: break-all;
        }*/
        .editor-images-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .image-item {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #eee;
            transition: background-color 0.2s;
        }

        .image-item:last-child {
            border-bottom: none;
        }

        .image-preview {
            flex: 0 0 100px;
            margin-right: 15px;
        }

        .image-preview img {
            max-width: 100px;
            height: auto;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .image-info {
            flex: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .filename {
            margin: 0;
            font-size: 0.9em;
            color: #333;
            word-break: break-all;
        }

        .delete-btn {
            padding: 5px 10px;
            font-size: 0.85em;
            transition: background-color 0.2s;
        }

        .delete-btn:hover {
            background-color: #c0392b;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        /* put some shit in the template.css for christ sake */
        .text-center {
            text-align: center;
        }

        .dimmed {
            opacity: 0.6;
            font-style: italic;
        }

        @media (max-width: 600px) {
            .image-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .image-preview {
                margin-right: 0;
                margin-bottom: 10px;
            }

            .image-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .delete-btn {
                align-self: flex-end;
            }
        }
    </style>
    
    <?= Modules::run('module_relations/_draw_summary_panel', 'blog_tags', $token) ?>

    <?php /* Trongate Comments*/ ?>
    <div class="card">
        <div class="card-heading">
            Comments
        </div>
        <div class="card-body">
            <div class="text-center">
                <p><button class="alt" onclick="openModal('comment-modal')">Add New Comment</button></p>
                <div id="comments-block"><table></table></div>
            </div>
        </div>
    </div>

</div>

<?php /** MODALS */ ?>

<div class="modal" id="article-preview" style="display: none;">
    <div class="modal-heading">Blog Post Preview <span class="float-right close-preview" onclick="closeModal()">&#10005;</span></div>
    <div class="modal-body">
        <div class="text-left">
            <h1><?= htmlspecialchars_decode($title, ENT_QUOTES) ?></h1>
            <h4><?= htmlspecialchars_decode($subtitle, ENT_QUOTES) ?></h4>
            <?= htmlspecialchars_decode($text, ENT_QUOTES) ?>
        </div>
    </div>
</div>

<div class="modal" id="comment-modal" style="display: none;">
    <div class="modal-heading"><i class="fa fa-commenting-o"></i> Add New Comment</div>
    <div class="modal-body">
        <p><textarea placeholder="Enter comment here..."></textarea></p>
        <p><?php
            $attr_close = array( 
                "class" => "alt",
                "onclick" => "closeModal()"
            );
            echo form_button('close', 'Cancel', $attr_close);
            echo form_button('submit', 'Submit Comment', array("onclick" => "submitComment()"));
            ?>
        </p>
    </div>
</div>

<div class="modal" id="delete-modal" style="display: none;">
    <div class="modal-heading danger"><i class="fa fa-trash"></i> Delete Record</div>
    <div class="modal-body">
        <?= form_open('blog_posts/submit_delete/'.$update_id) ?>
        <p>Are you sure?</p>
        <p>You are about to delete a Blog Post record.  This cannot be undone.  Do you really want to do this?</p> 
        <?php 
        echo '<p>'.form_button('close', 'Cancel', $attr_close);
        echo form_submit('submit', 'Yes - Delete Now', array("class" => 'danger')).'</p>';
        echo form_close();
        ?>
    </div>
</div>

<script>
const token = '<?= $token ?>';
const baseUrl = '<?= BASE_URL ?>';
const segment1 = '<?= segment(1) ?>';
const updateId = '<?= $update_id ?>';
const drawComments = true;
</script>
<script>
    // collect array publish-icons + add click fx to each
    const publishIcons = document.querySelectorAll('[id^="published-icon"]');
    //const arrayOne = document.querySelectorAll('#results-tbl > tbody > tr > td.text-center > i.fa');
    //const arrayTwo = document.querySelectorAll('#results-tbl-mini > tbody > tr > td > div:nth-child(1) > div > i.fa');
    //const publishIcons = Array.from(arrayOne).concat(Array.from(arrayTwo));
    for (var i = 0; i < publishIcons.length; i++) {
        publishIcons[i].addEventListener('click', (ev) => {
            togglePublishedStatus(ev.target);
        });
    }
    
    // update icon according to i status
    function togglePublishedStatus(clickedIcon) {
        const newPublishedStatus = (clickedIcon.classList.contains('fa-times-circle')) ? 1 : 0;
        const elId = clickedIcon.id;
        let recordId = elId.replace('published-icon-alt-', '');
        recordId = recordId.replace('published-icon-', '');
        recordId = parseInt(recordId);

        const params = {
            published: newPublishedStatus
        }

        clickedIcon.style.display = 'none';
        const targetUrl = '<?= BASE_URL ?>api/update/blog_posts/' + recordId;
        const http = new XMLHttpRequest();

        http.open('put', targetUrl);
        http.setRequestHeader('Content-type', 'application/json');
        http.setRequestHeader('trongateToken', '<?= $token ?>');
        http.send(JSON.stringify(params));
        http.onload = (ev) => {
            if(http.status == 200) {
                togglePublishedIcon(clickedIcon);
            }
        };
    }
    // update the icon that was clicked - even add an indicating tooltip_title
    function togglePublishedIcon(clickedIcon) {
        //update the icon that was clicked
        const { classList, id } = clickedIcon;
        const relLabel = document.getElementsByClassName('published-label')[0]; // Get the first element with this class
        
        const isTimesCircle = classList.contains('fa-times-circle');
        const newClass = isTimesCircle ? 'fa-check-square' : 'fa-times-circle';
        const oldClass = isTimesCircle ? 'fa-times-circle' : 'fa-check-square';
        const publishedTitle = (newClass === 'fa-check-square') ? 'Published' : 'Not published';

        clickedIcon.classList.remove(oldClass);
        clickedIcon.classList.add(newClass);
        clickedIcon.style.display = 'inline-block';
        clickedIcon.title = publishedTitle;

        // Update the related label text
        if (relLabel) {
            relLabel.innerHTML = `${publishedTitle}`;
        }
    }
</script>

<style>
    .fa-check-square {
        color: #42a8a3;
        cursor: pointer;
    }

    .fa-times-circle {
        color:  #b44646;
        cursor: pointer;
    }
    .record-details i[id^="published-icon"] {
        font-size: 1.5rem;
    }
    div.picture-preview img { 
        max-width: 100%; 
    }
    div.picture-preview figcaption { 
        word-break: break-all; 
    }

    span.dimmed { opacity: .4; }

    figure {
        border: 0px transparent solid;
        padding: 4px;
        margin: auto;
    }

    figcaption {
        background-color: whitesmoke;
        padding: 2px;
        text-align: center;
    }

    #article-preview {
        margin-top: 5% !important;
        height: 90vh;
        max-width: 100%;
        display: flex;
        flex-direction: column;
    }

    #article-preview > .modal-body {
        flex-grow: 1;
        overflow: auto;
    }

    #article-preview p { text-align: left; }
    .close-preview { cursor: pointer; }

    .video-container {
        position: relative;
        width: 100%;
        padding-top: 56.25%; /* 16:9 Aspect Ratio (9 ÷ 16 = 0.5625) */
    }

    .video-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    ol.list-counter {
        margin: 10px 0 10px;
        padding: 0;
    }
    ol.list-counter > li {
        list-style-type: none;
        counter-increment: list-counter;
        position: relative;
        padding-left: 30px;
    }
    ol.list-counter > li:before {
        content: counter(list-counter);
        position: absolute;
        display: block;
        left: 0;
        background-color: #000;
        color: #f7fafc;
        width: 20px;
        height: 20px;
        text-align: center;
        line-height: 20px;
        border-radius: 50%;
        font-size: .8em;
        margin-top: 5px;
        padding-left: 1px;
    }
</style>