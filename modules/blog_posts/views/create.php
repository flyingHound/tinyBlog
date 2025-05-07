<h1 class="mb-4"><?= $headline ?></h1>
<?php // echo validation_errors(); ?>
<div class="card shadow">
    <div class="card-header bg-primary text-white">
        Blog Post Details
    </div>
    <div class="card-body">
        <?php
        # echo form_open($form_location, ['class' => 'needs-validation', 'novalidate' => true]);
        echo form_open($form_location);
        echo '<div class="mb-3">';
        echo form_label('Title <span class="text-muted"><small>(optional HTML-Tags)</small></span>', array('class' => 'form-label'));
        echo validation_errors('title');
        echo form_input('title', htmlspecialchars_decode($title, ENT_QUOTES), [
            'class' => 'form-control',
            'placeholder' => 'Enter Title',
            /*'required' => true*/
        ]);
        echo '</div>';

        echo '<div class="mb-3">';
        echo form_label('Subtitle <span class="text-muted">(optional)</span>', ['class' => 'form-label']);
        echo validation_errors('subtitle');
        echo form_input('subtitle', htmlspecialchars_decode($subtitle, ENT_QUOTES), [
            'class' => 'form-control',
            'placeholder' => 'Enter Subtitle'
        ]);
        echo '</div>';

        echo '<div class="mb-3">';
        echo form_label('Text <span class="text-muted"><small>(optional HTML-Tags)</small></span>', ['class' => 'form-label']);
        echo validation_errors('text');
        echo '<div id="editor-menu-bar" class="mb-2"></div>';
        echo '<div class="editor-container" id="editor-container">';
        echo form_textarea('text', htmlspecialchars_decode($text, ENT_QUOTES), [
            'class' => 'form-control',
            'placeholder' => 'Enter Text',
            'id' => 'editor',
            'rows' => 10
        ]);
        // echo '<div class="ck-word-count text-end mt-1" id="editor-word-count"></div>';
        echo '</div>';
        echo '</div>';

        echo '<div class="mb-3">';
        echo form_label('YouTube Video ID', ['class' => 'form-label']);
        echo validation_errors('youtube');
        echo form_input('youtube', $youtube, [
            'class' => 'form-control',
            'placeholder' => 'Enter YouTube Video ID'
        ]);
        echo '</div>';

        echo '<div class="mb-3">';
        echo form_label('Associated Blog Source', ['class' => 'form-label']);
        echo validation_errors('blog_sources_id');
        echo form_dropdown('blog_sources_id', $blog_sources_options, $blog_sources_id, ['class' => 'form-select']);
        echo '</div>';

        echo '<div class="mb-3">';
        echo form_label('Associated Blog Category', ['class' => 'form-label']);
        echo validation_errors('blog_categories_id');
        echo form_dropdown('blog_categories_id', $blog_categories_options, $blog_categories_id, ['class' => 'form-select']);
        echo '</div>';

        echo '<div class="mb-3">';
        echo form_label('Date Published', ['class' => 'form-label']);
        echo validation_errors('date_published');
        echo form_input('date_published', $date_published, [
            'class' => 'form-control datetime-picker',
            'autocomplete' => 'off',
            'placeholder' => 'Select Date Published'
        ]);
        echo '</div>';

        echo '<div class="form-check mb-3">';
        echo form_checkbox('published', 1, $published, ['class' => 'form-check-input', 'id' => 'published']);
        // echo validation_errors('published', '<div class="text-danger small">', '</div>');
        echo form_label('Published', ['class' => 'form-check-label']);
        echo '</div>';

        echo '<div class="d-flex gap-3">';
        echo form_submit('submit', 'Submit', ['class' => 'btn btn-success']);
        echo anchor($cancel_url, 'Cancel', ['class' => 'btn btn-secondary']);
        echo '</div>';

        echo form_close();
        ?>
    </div>
</div>

<?php /** CKEditor 
<script type="importmap">
{
    "imports": {
        "ckeditor5": "<?= BASE_URL ?>blog_posts<?= MODULE_ASSETS_TRIGGER ?>/js/ckeditor5/ckeditor5.js",
        "ckeditor5/": "<?= BASE_URL ?>blog_posts<?= MODULE_ASSETS_TRIGGER ?>/js/ckeditor5/"
    }
}
</script>
<script type="module" src="<?= BASE_URL ?>blog_posts<?= MODULE_ASSETS_TRIGGER ?>/js/main_ckeditor5.js"></script>

<style>
    .ck-powered-by {
        display: none;
    }
    .ck-word-count {
        font-size: 85%;
        display: flex;
        justify-content: flex-end;
        gap: .5rem;
    }
    .editor-container {
        border: 1px solid #dee2e6;
        border-radius: .25rem;
        padding: .5rem;
    }
</style>
*/ ?>
<script>
// const BASE_URL = "<?= BASE_URL ?>";
</script>

<?php /** tinyMCE */ ?>
<script>
const image_upload_handler = (blobInfo, progress) => new Promise((resolve, reject) => {
  const xhr = new XMLHttpRequest();
  xhr.withCredentials = false;
  xhr.open('POST', 'blog_posts/upload_tinymce_image');

  xhr.upload.onprogress = (e) => {
    progress(e.loaded / e.total * 100);
  };

  xhr.onload = () => {
    if (xhr.status === 403) {
      reject({ message: 'HTTP Error: ' + xhr.status, remove: true });
      return;
    }

    if (xhr.status < 200 || xhr.status >= 300) {
      reject('HTTP Error: ' + xhr.status);
      return;
    }

    const json = JSON.parse(xhr.responseText);

    if (!json || typeof json.location != 'string') {
      reject('Invalid JSON: ' + xhr.responseText);
      return;
    }

    resolve(json.location);
  };

  xhr.onerror = () => {
    reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
  };

  const formData = new FormData();
  formData.append('file', blobInfo.blob(), blobInfo.filename());

  xhr.send(formData);
});

const image_picker = (cb, value, meta) => {
    const input = document.createElement('input');
    input.setAttribute('type', 'file');
    input.setAttribute('accept', 'image/*');

    input.addEventListener('change', (e) => {
        const file = e.target.files[0];

        const reader = new FileReader();
        reader.addEventListener('load', () => {
            /*
              Note: Now we need to register the blob in TinyMCEs image blob
              registry. In the next release this part hopefully won't be
              necessary, as we are looking to handle it internally.
            */
            const id = 'blobid' + (new Date()).getTime();
            const blobCache =  tinymce.activeEditor.editorUpload.blobCache;
            const base64 = reader.result.split(',')[1];
            const blobInfo = blobCache.create(id, file, base64);
            blobCache.add(blobInfo);

            /* call the callback and populate the Title field with the file name */
            cb(blobInfo.blobUri(), { title: file.name });
        });
        reader.readAsDataURL(file);
    });

    input.click();
};

tinymce.init({
  selector: '#editor',
  license_key: 'gpl',
  content_style: 'body { font-family: Segoe UI, Helvetica, Arial, sans-serif; font-size: 16px; }',
  height: 600,
  placeholder: 'Write your blog text here...',
  toolbar_mode: 'sliding',

  plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help quickbars emoticons',

  toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough subscript superscript | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media table | removeformat code fullscreen preview save pagebreak anchor codesample ltr rtl code',
  menubar: 'file edit view insert format tools table help',
  toolbar_mode: 'wrap', // 'sliding', 'scrolling'

  autosave_ask_before_unload: true,
  autosave_interval: '30s',
  autosave_prefix: '{path}{query}-{id}-',
  autosave_restore_when_empty: false,
  autosave_retention: '2m',

  quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
  contextmenu: 'link image table',
  noneditable_class: 'mceNonEditable',

  font_formats: 'Segoe UI=Segoe UI,Helvetica,Arial,sans-serif; Arial=Arial,Helvetica,sans-serif; Times New Roman=Times New Roman,Times,serif; Courier New=Courier New,Courier,monospace;',
  fontsize_formats: '10px 12px 14px 16px 18px 20px 24px 28px',

  link_default_protocol: 'https://',
  link_default_target: '_blank',

  extended_valid_elements: '*[*]',

  image_toolbar: 'imageoptions alignleft aligncenter alignright | imagecaption imagealt',
  image_caption: true,
  image_title: true,
  image_advtab: true,
  automatic_uploads: true,
  images_upload_handler: image_upload_handler,
  file_picker_types: 'image',
  file_picker_callback: image_picker
});
</script>

<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<!-- Optional: Bootstrap Theme -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
  flatpickr(".datetime-picker", {
    enableTime: true,
    dateFormat: "m/d/Y, H:i",
    altInput: false,
    altFormat: "F j, Y (H:i)",
    time_24hr: true
  });
</script>
<?#= json($data) ?>