<?php
class Blog_posts extends Trongate {

    private $default_limit = 20;
    private $per_page_options = array(10, 20, 50, 100);
    private $admin_template = 'tiny_bootstrap'; // admin, tiny_bootstrap
    private $date_format_en     = 'm/d/Y, H:i';
    private $date_format_show   = 'F d Y \a\t H:i';
    private $date_format_manage = 'M/d/Y, H:i';
    private $allowed_html  = '<a><p><span><img><h1><h2><h3><h4><h5><h6><br><div><hr><b><i><strong><em><pre><code><ul><li><ol>';
    private $picture_fallback = 'blog_posts_module/img/fallback_img.jpg'; // '' for no pic

    /**
     * Display a webpage with a form for creating or updating a record.
     * 
     * @return void
     */
    public function create(): void {
        $token = $this->_make_sure_allowed();
        $update_id = (int) segment(3);
        $submit = post('submit');

        if (($submit === '') && ($update_id > 0)) {
            $data = $this->get_data_from_db($update_id);
        } else {
            $data = $this->get_data_from_post();
        }

        $data['blog_sources_options'] = $this->_get_blog_sources_options($data['blog_sources_id'] ?? 0);
        $data['blog_categories_options'] = $this->_get_blog_categories_options($data['blog_categories_id'] ?? 0);

        if ($update_id > 0) {
            $data['headline'] = 'Update Blog Post Record';
            $data['cancel_url'] = BASE_URL.'blog_posts/show/'.$update_id;
            $data['date_published'] = date($this->date_format_en, strtotime($data['date_published']));
        } else {
            $data['headline'] = 'Create New Blog Post Record';
            $data['cancel_url'] = BASE_URL.'blog_posts/manage';
            $data['date_published'] = date($this->date_format_en);
        }

        // published default 0
        $data['published'] = ($data['published'] == 1) ? 1 : 0;

        // user ID
        $data['user_id'] = $this->_get_current_admin_id($token);

        // load text editor
        $asset_url = BASE_URL.'blog_posts'.MODULE_ASSETS_TRIGGER.'/js';
        // CKEditor: $additional_includes_top[] = $asset_url.'/ckeditor5/ckeditor5.css';
        //$additional_includes_top[] = '<script src="https://cdn.tiny.cloud/1/3obgaeusymvc4yx9iwug17oo54irousw3e7y50k2g3ggswuk/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>';
        $additional_includes_top[] = $asset_url.'/tinymce/tinymce.min.js';
        #$additional_includes_top[] = '<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>';

        $data['additional_includes_top'] = $additional_includes_top;

        // post id for ckeditor image upload
        $_SESSION['current_post_id'] = $update_id;

        $data['date_format'] = $this->date_format_en;
        $data['form_location'] = BASE_URL.'blog_posts/submit/'.$update_id;
        $data['view_file'] = 'create';
        $this->template($this->admin_template, $data);
    }

    /**
     * Display a webpage to manage records.
     *
     * @return void
     */
    public function manage(): void {
        $token = $this->_make_sure_allowed();
        $user_id = $this->_get_current_admin_id($token);

        if (segment(4) !== '') {
            $data['headline'] = 'Search Results';
            $searchphrase = trim($_GET['searchphrase']);
            $params = [
                'title' => "%$searchphrase%",
                'subtitle' => "%$searchphrase%",
                'youtube' => "%$searchphrase%"
            ];
            $sql = 'select * from blog_posts
            WHERE title LIKE :title
            OR subtitle LIKE :subtitle
            OR youtube LIKE :youtube
            ORDER BY date_published desc, id desc';
            $all_rows = $this->model->query_bind($sql, $params, 'object');
        } else {
            $data['headline'] = 'Manage Blog Posts';
            $all_rows = $this->model->get('date_published desc, id desc');
        }

        $pagination_data = [
            'total_rows' => count($all_rows),
            'page_num_segment' => 3,
            'limit' => $this->get_limit(),
            'pagination_root' => 'blog_posts/manage',
            'record_name_plural' => 'blog_posts',
            'include_showing_statement' => true
        ];
        $data['pagination_data'] = $pagination_data;

        $rows = $this->reduce_rows($all_rows);
        $data['selected_per_page'] = $this->get_selected_per_page();
        $data['per_page_options'] = $this->per_page_options;

        $rows = $this->_add_admin_names_multiple($rows);
        $rows = $this->_add_short_texts($rows, 'text', 'text_short', 5);
        $rows = $this->_add_word_count($rows);
        $rows = $this->_add_category_titles($rows);

        //$rows = $this->_add_source_authors($rows);

        $rows = $this->_add_source_details($rows);
        $rows = $this->_add_picture_and_thumb_urls($rows);
        $rows = $this->_add_picture_counts_multiple($rows);
        $data['rows'] = $rows;

        // adding data
        # $data['thumb_width'] = $this->_init_picture_settings()['thumbnail_max_width'];
        $data['date_format'] = $this->date_format_manage;
        $data['token']       = $token;
        $data['user_id']     = $user_id;

        // load up the template
        $data['view_module'] = 'blog_posts';
        $data['view_file'] = 'manage';
        $this->template($this->admin_template, $data);
    }

    /* ALTERNATIVE - with Admins

        if (segment(4) !== '') {
            $data['headline'] = 'Search Results';
            $searchphrase = trim($_GET['searchphrase']);
            $params = [
                'title' => "%$searchphrase%",
                'subtitle' => "%$searchphrase%",
                'youtube' => "%$searchphrase%"
            ];
            $sql = 'SELECT bp.*, a.username AS created_by_name, a2.username AS updated_by_name
                    FROM blog_posts bp
                    LEFT JOIN trongate_admin a ON bp.created_by = a.id
                    LEFT JOIN trongate_admin a2 ON bp.updated_by = a2.id
                    WHERE bp.title LIKE :title
                    OR bp.subtitle LIKE :subtitle
                    OR bp.youtube LIKE :youtube
                    ORDER BY bp.date_published DESC, bp.id DESC';
            $all_rows = $this->model->query_bind($sql, $params, 'object');
        } else {
            $data['headline'] = 'Manage Blog Posts';
            $sql = 'SELECT bp.*, a.username AS created_by_name, a2.username AS updated_by_name
                    FROM blog_posts bp
                    LEFT JOIN trongate_admin a ON bp.created_by = a.id
                    LEFT JOIN trongate_admin a2 ON bp.updated_by = a2.id
                    ORDER BY bp.date_published DESC, bp.id DESC';
            $all_rows = $this->model->query($sql, 'object');
        }

        SOURCES:
        $sql = 'SELECT bp.*, 
        bs.source_name, bs.author, bs.website, bs.link
        FROM blog_posts bp
        LEFT JOIN blog_sources bs ON bp.blog_sources_id = bs.id
        ...';
    */

    /**
     * Display a webpage showing information for an individual record.
     *
     * @return void
     */
    public function show(): void {
        // verify user permissions
        $token = $this->_make_sure_allowed();

        // get the ID for the record to display
        $update_id = (int) segment(3);

        // Redirect if update_id is invalid
        if ($update_id <= 0 || !is_numeric($update_id)) {
            redirect('blog_posts/manage');
        }

        // fetch record data from the Posts table
        $data = $this->get_data_from_db($update_id);
        $data['token'] = $token;
        
        // redirect to manage if no data
        if ($data === false) {
            redirect('blog_posts/manage');

        } else {
            // add names on created_by + updated_by
            $data = $this->add_admin_names_single($data);

            // short post text and get word count
            $data['text_short'] = $this->wordLimit($this->strip_secure_htmltags($data['text']), 28, ' [...]');
            $data['text_count'] = $this->_count_actual_words($data['text']);

            // initialize settings and generate picture folders, if required
            $picture_settings = $this->_init_picture_settings();
            $this->_make_sure_got_destination_folders($update_id, $picture_settings);

            // attempt to get the picture from table column
            $column_name = $picture_settings['target_column_name'];
            if ($data[$column_name] !== '') {
                //we have a picture - display picture preview
                $data['draw_picture_uploader'] = false;
                $data = $this->add_picture_and_thumb_url($data, $picture_settings);
                # $data['picture_folder'] = $this->get_picture_locations($picture_settings, 'destination')['path'];
                $data['picture_folder'] = $this->get_picture_upload_directory($picture_settings);
            } else {
                // no picture - draw upload form
                $data['draw_picture_uploader'] = true;
            }

            // set date format
            $data['date_format'] = $this->date_format_show;

            // source and category objs
            $data['source'] = $this->_get_relation_obj($data['blog_sources_id'], 'blog_sources');
            $data['category'] = $this->_get_relation_obj($data['blog_categories_id'], 'blog_categories');

            // count gallery pictures
            $data['picture_count'] = $this->_count_post_pictures($update_id);

            // WYSIWYG EDITOR Images for the panel
            $data['editor_images'] = $this->_get_editor_images($update_id);

            // assign data and load the template
            $data['filezone_settings'] = $this->_init_filezone_settings();

            // load trongate scripts
            # $additional_includes_top[] = '<link href="'.BASE_URL.'public/css/admin.css" rel="stylesheet" />';
            # $data['additional_includes_top'] = $additional_includes_top;
            # $additional_includes_btm[] = '<script src="'.BASE_URL.'public/js/admin.js"></script>';
            # $data['additional_includes_btm'] = $additional_includes_btm;
            
            
            $data['update_id'] = $update_id;
            $data['headline'] = 'Blog Post Information';
            $data['view_file'] = 'show_' . $this->admin_template;
            $this->template($this->admin_template, $data);
        }
    }

// ---------------------------------------------------------------------------------

    /** 
     * Handle submitted record data.
     *
     * @return void
     */
    public function submit(): void {
        $token = $this->_make_sure_allowed();
        $user_id = $this->_get_current_admin_id($token);

        $submit = post('submit', true);

        if ($submit !== 'Submit') redirect('blog_posts/manage');

        $this->validation->set_rules('title', 'Title', 'required|min_length[2]|max_length[255]');
        $this->validation->set_rules('subtitle', 'Subtitle', 'min_length[2]|max_length[255]');
        $this->validation->set_rules('text', 'Text', 'min_length[2]');
        $this->validation->set_rules('youtube', 'YouTube', 'min_length[6]|max_length[12]');
        $this->validation->set_rules('date_published', 'Date Published', 'required|valid_datetimepicker_us');
        $this->validation->set_rules('published', 'Published', 'max_length[1]|numeric|integer');

        $result = $this->validation->run();
        if ($result === true) {
            $update_id = (int) segment(3);
            $data = $this->get_data_from_post();

            // make url_string
            $title = $this->clear_sonderzeichen($data['title']);
            $title = $this->strip_secure_htmltags($title);
            $data['url_string'] = strtolower(url_title($title));

            // trim and allow HTML-Tags
            $data['text'] = $this->make_secure_htmltags($data['text']);
            $data['title'] = $this->make_secure_htmltags($data['title']);
            $data['subtitle'] = $data['subtitle'] != '' ? $this->make_secure_htmltags($data['subtitle']): '';

            // prep the publish date
            $data['date_published'] = str_replace(',', '', $data['date_published']); // date with semicolon/at
            $data['date_published'] = date('Y-m-d H:i:s', strtotime($data['date_published']));
            
            if ($update_id > 0) {
                // update an existing record
                $data['date_updated'] = date('Y-m-d H:i:s', time());
                $data['updated_by'] = $user_id;
                $this->model->update($update_id, $data, 'blog_posts');
                $flash_msg = 'The record was successfully updated';
            } else {
                // insert the new record
                $data['date_created'] = date('Y-m-d H:i:s', time());
                $data['created_by'] = $user_id;
                $update_id = $this->model->insert($data, 'blog_posts');
                $flash_msg = 'The record was successfully created';
            }

            // cleanup unused text images from db
            $this->_cleanup_unused_editor_images($update_id, $data['text']);

            set_flashdata($flash_msg);
            redirect('blog_posts/show/'.$update_id);

        } else {
            // form submission error
            $this->create();
        }
    }

    /**
     * Handles the submitted request to delete a blog post, including its related comments and images.
     *
     * Deletes comments from trongate_comments, 
     * the single picture and thumbnail via existing logic,
     * gallery images (files and DB entries) via Blog_filezone module, 
     * and Editor images from the upload directory.
     *
     * @return void
     */
    public function submit_delete(): void {
        $this->_make_sure_allowed();

        $submit = post('submit');
        $update_id = (int) segment(3);

        if ($submit !== 'Yes - Delete Now' || $update_id <= 0) {
            set_flashdata('Invalid delete request.');
            redirect('blog_posts/manage');
        }

        // Check if blog post exists
        $result = $this->model->get_where($update_id, 'blog_posts');
        if ($result === false) {
            set_flashdata('Blog post not found.');
            redirect('blog_posts/manage');
        }

        // Delete comments associated with this blog post
        $sql = 'DELETE FROM trongate_comments WHERE target_table = :module AND update_id = :update_id';
        $params = ['module' => 'blog_posts', 'update_id' => $update_id];
        $this->model->query_bind($sql, $params);

        // Delete single picture and thumbnail (existing logic)
        $single_picture_deleted = $this->_delete_picture_and_thumb_dir($update_id);

        // Delete gallery images and their DB entries via Blog_filezone
        $this->module('blog_filezone');
        $gallery_images_deleted = $this->blog_filezone->_delete_filezone_pictures($update_id);

        // Delete CKEditor image
        $ckeditor_images_deleted = $this->_delete_editor_images($update_id);

        // Delete the blog post record
        if (!$this->model->delete($update_id, 'blog_posts')) {
            set_flashdata('Failed to delete blog post.');
            redirect('blog_posts/manage');
        }

        // Set flashdata based on success/failure of image deletions
        if (!$single_picture_deleted || !$gallery_images_deleted) {
            set_flashdata('Blog post deleted, but no related images were removed.');
        } else {
            set_flashdata('The blog post and all related images were successfully deleted.');
        }

        redirect('blog_posts/manage');
    }

    /**
     * Deletes the picture and thumbnail directories for a given ID.
     *
     * @param int $update_id The ID whose directories should be deleted.
     * @return bool Returns true if all deletions succeed, false otherwise.
     */
    protected function _delete_picture_and_thumb_dir(int $update_id): bool {
        $picture_settings = $this->_init_picture_settings();
        $success = true;

        $directories = [
            'picture_dir' => $this->get_picture_locations($picture_settings, 'destination')['path'] . "/$update_id",
            'thumb_dir' => $this->get_picture_locations($picture_settings, 'thumbnail_dir')['path'] . "/$update_id",
        ];

        foreach ($directories as $type => $path) {
            if (is_dir($path)) {
                if (!$this->_rrmdir($path)) {
                    error_log("Failed to delete $type at $path");
                    $success = false;
                }
            }
        }

        return $success;
    }

    /**
     * Recursively remove a directory and its contents.
     *
     * @param string $dir Path to the directory.
     * @return bool True on success, false on failure.
     */
    protected function _rrmdir(string $dir): bool {
        if (!is_dir($dir)) {
            return true; // Nichts zu löschen
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = "$dir/$file";
            is_dir($path) ? $this->_rrmdir($path) : unlink($path);
        }
        return rmdir($dir);
    }

// ---------------------------------------------------------------------------------

    /**
     * Set the number of items per page.
     *
     * @param int $selected_index Selected index for items per page.
     *
     * @return void
     */
    public function set_per_page(int $selected_index): void {
        $this->_make_sure_allowed();

        if (!is_numeric($selected_index)) {
            $selected_index = $this->per_page_options[1];
        }

        $_SESSION['selected_per_page'] = $selected_index;
        redirect('blog_posts/manage');
    }

    /**
     * Get the selected number of items per page.
     *
     * @return int Selected items per page.
     */
    private function get_selected_per_page(): int {
        $selected_per_page = (isset($_SESSION['selected_per_page'])) ? $_SESSION['selected_per_page'] : 1;
        return $selected_per_page;
    }

    /**
     * Reduce fetched table rows based on offset and limit.
     *
     * @param array $all_rows All rows to be reduced.
     *
     * @return array Reduced rows.
     */
    private function reduce_rows(array $all_rows): array {
        $rows = [];
        $start_index = $this->get_offset();
        $limit = $this->get_limit();
        $end_index = $start_index + $limit;

        $count = -1;
        foreach ($all_rows as $row) {
            $count++;
            if (($count>=$start_index) && ($count<$end_index)) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    /**
     * Get the limit for pagination.
     *
     * @return int Limit for pagination.
     */
    private function get_limit(): int {
        if (isset($_SESSION['selected_per_page'])) {
            $limit = $this->per_page_options[$_SESSION['selected_per_page']];
        } else {
            $limit = $this->default_limit;
        }

        return $limit;
    }

    /**
     * Get the offset for pagination.
     *
     * @return int Offset for pagination.
     */
    private function get_offset(): int {
        $page_num = (int) segment(3);

        if ($page_num>1) {
            $offset = ($page_num-1)*$this->get_limit();
        } else {
            $offset = 0;
        }

        return $offset;
    }

// ---------------------------------------------------------------------------------

    /**
     * Get data from the database for a specific update_id.
     *
     * @param int $update_id The ID of the record to retrieve.
     *
     * @return array|null An array of data or null if the record doesn't exist.
     */
    private function get_data_from_db(int $update_id): ?array {
        $record_obj = $this->model->get_where($update_id, 'blog_posts');

        if ($record_obj === false) {
            $this->template('error_404');
            die();
        } else {
            $data = (array) $record_obj;
            return $data;        
        }
    }

    /**
     * Get data from the POST request.
     *
     * @return array Data from the POST request.
     */
    private function get_data_from_post(): array {
        $data['title'] = post('title');
        $data['subtitle'] = post('subtitle');
        $data['text'] = post('text');
        $data['youtube'] = post('youtube', true);
        $data['date_published'] = post('date_published', true);
        $data['published'] = post('published', true);        
        $data['blog_sources_id'] = post('blog_sources_id');
        $data['blog_categories_id'] = post('blog_categories_id');
        return $data;
    }

// ---------------------------------------------------------------------------------

        /**
     * Initialize picture upload settings for blog posts with fallbacks.
     *
     * @return array Configuration for picture uploads.
     */
    function _init_picture_settings(): array {
        // Define default settings
        $default_settings = [
            'max_file_size' => 13 * 1024, // KB
            'file_types' => ['gif', 'jpg', 'jpeg', 'png', 'jfif', 'webp'],
            'max_width' => 1200, // Main Picture
            'max_height' => 2400,
            'thumbnail_max_width' => 120, // Thumbnail
            'thumbnail_max_height' => 120,
            'destination' => 'blog_posts_pics',
            'thumbnail_dir' => 'blog_posts_pics_thumbnails',
            'target_column_name' => 'picture',
            'upload_to_module' => false,
            'make_rand_name' => false
        ];

        // Load settings from JSON file
        $file_path = APPPATH . 'public/blog_settings/picture_settings.json';
        $picture_settings = $default_settings; // Start with defaults

        if (file_exists($file_path)) {
            $loaded_settings = json_decode(file_get_contents($file_path), true);
            if (is_array($loaded_settings)) {
                // Merge loaded settings with defaults to ensure all keys exist
                $picture_settings = array_merge($default_settings, $loaded_settings);

                // Basic validation to ensure correct types and values
                $picture_settings['max_file_size'] = (int) ($picture_settings['max_file_size'] ?? $default_settings['max_file_size']);
                $picture_settings['file_types'] = is_array($picture_settings['file_types']) ? $picture_settings['file_types'] : $default_settings['file_types'];
                $picture_settings['max_width'] = (int) ($picture_settings['max_width'] ?? $default_settings['max_width']);
                $picture_settings['max_height'] = (int) ($picture_settings['max_height'] ?? $default_settings['max_height']);
                $picture_settings['thumbnail_max_width'] = (int) ($picture_settings['thumbnail_max_width'] ?? $default_settings['thumbnail_max_width']);
                $picture_settings['thumbnail_max_height'] = (int) ($picture_settings['thumbnail_max_height'] ?? $default_settings['thumbnail_max_height']);
                $picture_settings['destination'] = is_string($picture_settings['destination']) ? $picture_settings['destination'] : $default_settings['destination'];
                $picture_settings['thumbnail_dir'] = is_string($picture_settings['thumbnail_dir']) ? $picture_settings['thumbnail_dir'] : $default_settings['thumbnail_dir'];
                $picture_settings['target_column_name'] = is_string($picture_settings['target_column_name']) ? $picture_settings['target_column_name'] : $default_settings['target_column_name'];
                $picture_settings['upload_to_module'] = (bool) ($picture_settings['upload_to_module'] ?? $default_settings['upload_to_module']);
                $picture_settings['make_rand_name'] = (bool) ($picture_settings['make_rand_name'] ?? $default_settings['make_rand_name']);
            }
        }

        return $picture_settings;
    }

    function _make_sure_got_destination_folders($update_id, $picture_settings) {

        $destination = $picture_settings['destination'];

        if ($picture_settings['upload_to_module'] == true) {
            $target_dir = APPPATH.'modules/'.segment(1).'/assets/'.$destination.'/'.$update_id;
        } else {
            $target_dir = APPPATH.'public/'.$destination.'/'.$update_id;
        }

        if (!file_exists($target_dir)) {
            //generate the image folder
            mkdir($target_dir, 0777, true);
        }

        //attempt to create thumbnail directory
        if (strlen($picture_settings['thumbnail_dir'])>0) {
            $ditch = $destination.'/'.$update_id;
            $replace = $picture_settings['thumbnail_dir'].'/'.$update_id;
            $thumbnail_dir = str_replace($ditch, $replace, $target_dir);
            if (!file_exists($thumbnail_dir)) {
                //generate the image folder
                mkdir($thumbnail_dir, 0777, true);
            }
        }
    }

    /**
     * Handle picture upload for a blog post with validation and resizing.
     *
     * @param int $update_id The ID of the blog post.
     * @return void
     */
    public function submit_upload_picture(int $update_id): void {
        $this->_make_sure_allowed();

        if ($update_id <= 0 || empty($_FILES['picture']['name'])) {
            set_flashdata('No file selected or invalid ID.');
            redirect("blog_posts/show/$update_id");
        }

        $picture_settings = $this->_init_picture_settings();

        // Validierung
        $file = $_FILES['picture'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $file_size_kb = $file['size'] / 1024;

        if (!in_array($file_ext, $picture_settings['file_types'])) {
            set_flashdata('Invalid file type. Allowed: ' . implode(', ', $picture_settings['file_types']));
            redirect("blog_posts/show/$update_id");
        }

        if ($file_size_kb > $picture_settings['max_file_size']) {
            $file_size_mb = $file_size_kb / 1024;
            $max_file_size_mb = $picture_settings['max_file_size'] / 1024;
            set_flashdata("File too large. Max: " . number_format($max_file_size_mb, 2) . " MB, got: " . number_format($file_size_mb, 2) . " MB");
            redirect("blog_posts/show/$update_id");
        }

        // Verzeichnisse
        $picture_path = $this->get_picture_locations($picture_settings, 'destination')['path'] . "/$update_id";
        $thumb_path = $this->get_picture_locations($picture_settings, 'thumbnail_dir')['path'] . "/$update_id";
        foreach ([$picture_path, $thumb_path] as $dir) {
            if (!is_dir($dir)) mkdir($dir, 0755, true) or die("Failed to create directory: $dir");
        }

        // Temporärer Upload
        $source_path = $file['tmp_name'];
        $filename = $picture_settings['make_rand_name'] ? uniqid() . '.' . $file_ext : basename($file['name']);
        $target_path = "$picture_path/$filename";

        // Orientierung korrigieren
        $exif = @exif_read_data($source_path);
        $corrected_path = $this->correctImageOrientation($source_path, $exif);

        // Zielpfad festlegen
        if ($corrected_path && file_exists($corrected_path)) {
            if (!rename($corrected_path, $target_path)) {
                // set_flashdata("Error: Failed to move corrected file from $corrected_path to $target_path");
                set_flashdata('Error: Failed to process corrected image.');
                redirect("blog_posts/show/$update_id");
            }
            // set_flashdata("Debug: Corrected file moved from $corrected_path to $target_path");
        } else {
            if (!move_uploaded_file($source_path, $target_path)) {
                // set_flashdata("Error: Failed to move original file from $source_path to $target_path");
                set_flashdata('Error: Failed to upload image.');
                redirect("blog_posts/show/$update_id");
            }
            // set_flashdata("Debug: Original file moved from $source_path to $target_path");
        }

        // Bilddimensionen prüfen
        if (!file_exists($target_path)) {
            set_flashdata('Error: Image file not found after processing.');
            redirect("blog_posts/show/$update_id");
        }
        [$width, $height] = getimagesize($target_path);

        // Skalierung Hauptbild
        if ($width > $picture_settings['max_width'] || $height > $picture_settings['max_height']) {
            set_flashdata("Image resized from {$width}x{$height} to max {$picture_settings['max_width']}x{$picture_settings['max_height']}");
            $this->resize_image($target_path, $picture_settings['max_width'], $picture_settings['max_height']);
        }

        // Thumbnail
        if ($picture_settings['thumbnail_max_width'] > 0 && $picture_settings['thumbnail_max_height'] > 0 && $picture_settings['thumbnail_dir'] !== '') {
            $thumb_file = "$thumb_path/$filename";
            if (!copy($target_path, $thumb_file)) {
                set_flashdata('Failed to create thumbnail.');
                redirect("blog_posts/show/$update_id");
            }
            if ($width > $picture_settings['thumbnail_max_width'] || $height > $picture_settings['thumbnail_max_height']) {
                $this->resize_image($thumb_file, $picture_settings['thumbnail_max_width'], $picture_settings['thumbnail_max_height']);
            }
        }

        // DB Update
        $data[$picture_settings['target_column_name']] = $filename;
        $this->model->update($update_id, $data);

        set_flashdata('Picture uploaded successfully.');
        redirect("blog_posts/show/$update_id");
    }

    private function correctImageOrientation($sourcePath, $exif): ?string {
        if (empty($exif['Orientation'])) return null;

        $imageInfo = getimagesize($sourcePath);
        $mime = $imageInfo['mime'];

        switch ($mime) {
            case 'image/jpeg': $image = imagecreatefromjpeg($sourcePath); break;
            case 'image/png': $image = imagecreatefrompng($sourcePath); break;
            case 'image/gif': $image = imagecreatefromgif($sourcePath); break;
            case 'image/webp': $image = imagecreatefromwebp($sourcePath); break;
            default: return null;
        }

        switch ($exif['Orientation']) {
            case 3: $image = imagerotate($image, 180, 0); break;
            case 6: $image = imagerotate($image, -90, 0); break;
            case 8: $image = imagerotate($image, 90, 0); break;
            case 2: imageflip($image, IMG_FLIP_HORIZONTAL); break;
            case 4: imageflip($image, IMG_FLIP_VERTICAL); break;
            case 5: imageflip($image, IMG_FLIP_HORIZONTAL); $image = imagerotate($image, -90, 0); break;
            case 7: imageflip($image, IMG_FLIP_HORIZONTAL); $image = imagerotate($image, 90, 0); break;
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'rotated_');
        switch ($mime) {
            case 'image/jpeg': imagejpeg($image, $tempFile, 85); break;
            case 'image/png': imagepng($image, $tempFile, 8); break;
            case 'image/gif': imagegif($image, $tempFile); break;
            case 'image/webp': imagewebp($image, $tempFile, 85); break;
        }
        imagedestroy($image);
        return $tempFile;
    }

    /**
     * Resize an image while maintaining aspect ratio.
     *
     * @param string $file_path Path to the image.
     * @param int $max_width Maximum width.
     * @param int $max_height Maximum height.
     * @return void
     */
    protected function resize_image(string $file_path, int $max_width, int $max_height): void {
        [$width, $height] = getimagesize($file_path);
        if ($width <= $max_width && $height <= $max_height) {
            return;
        }

        $src = imagecreatefromstring(file_get_contents($file_path));
        if ($src === false) {
            throw new Exception("Failed to load image: $file_path");
        }

        $ratio = $width / $height;
        $new_width = $width > $max_width ? $max_width : $width;
        $new_height = $new_width / $ratio;
        if ($new_height > $max_height) {
            $new_height = $max_height;
            $new_width = $new_height * $ratio;
        }

        $dst = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

        $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        switch ($ext) {
            case 'jpg': case 'jpeg': imagejpeg($dst, $file_path, 85); break;
            case 'png': imagepng($dst, $file_path, 8); break;
            case 'gif': imagegif($dst, $file_path); break;
            case 'webp': imagewebp($dst, $file_path, 85); break;
            case 'jfif': imagejpeg($dst, $file_path, 85); break;
            default: throw new Exception("Unsupported image type: $ext");
        }
        imagedestroy($src);
        imagedestroy($dst);
    }

    /**
     * Delete a picture and its thumbnail for a blog post.
     *
     * @param int|string $update_id The ID of the blog post.
     * @return void
     */
    public function ditch_picture($update_id): void {
        $this->_make_sure_allowed();
        // Sicherheitschecks
        if (!is_numeric($update_id) || $update_id <= 0) {
            set_flashdata('Invalid ID provided.');
            redirect("blog_posts/show/$update_id");
        }

        $result = $this->model->get_where($update_id, 'blog_posts');
        if ($result === false) {
            set_flashdata('Blog post not found.');
            redirect("blog_posts/show/$update_id");
        }

        $picture_settings = $this->_init_picture_settings();
        $target_column_name = $picture_settings['target_column_name'];
        $picture_name = $result->$target_column_name;

        // Nichts zu löschen, wenn kein Bild da ist
        if (empty($picture_name)) {
            set_flashdata('No picture to delete.');
            redirect("blog_posts/show/$update_id");
        }

        // Pfade mit get_picture_locations()
        $picture_dir = $this->get_picture_locations($picture_settings, 'destination')['path'] . "/$update_id";
        $picture_path = "$picture_dir/$picture_name";

        // Hauptbild löschen
        if (file_exists($picture_path)) {
            if (!unlink($picture_path)) {
                set_flashdata('Failed to delete the picture.');
                redirect("blog_posts/show/$update_id");
            }
        } else {
            set_flashdata('Picture file not found.');
        }

        // Thumbnail löschen (falls vorhanden)
        if (!empty($picture_settings['thumbnail_dir'])) {
            $thumb_dir = $this->get_picture_locations($picture_settings, 'thumbnail_dir')['path'] . "/$update_id";
            $thumbnail_path = "$thumb_dir/$picture_name";
            if (file_exists($thumbnail_path)) {
                if (!unlink($thumbnail_path)) {
                    set_flashdata('Picture deleted, but failed to delete thumbnail.');
                    redirect("blog_posts/show/$update_id");
                }
            }
        }

        // DB Update
        $data[$target_column_name] = '';
        $this->model->update($update_id, $data, 'blog_posts');

        set_flashdata('Picture and thumbnail successfully deleted.');
        redirect("blog_posts/show/$update_id");
    }

    /**
     * Returns picture and thumbnail URLs for a single record.
     * 
     * @param array $data Single record data.
     * @param array|null $picture_settings Optional settings for picture and thumbnail URLs.
     * 
     * @return array|null Modified record with `picture_url` and `thumb_url` properties, or null if invalid.
     */
    function add_picture_and_thumb_url(array $data, array $picture_settings = null): ?array {
        // Initialize picture settings if not provided
        $picture_settings = $picture_settings ?? $this->_init_picture_settings();

        // Extract settings
        $picture_col = $picture_settings['target_column_name'];
        $picture_dir = $picture_settings['destination'];
        $thumb_dir = $picture_settings['thumbnail_dir'];

        // Determine upload directory: 'module/assets' or 'public/'
        $upload_dir = $picture_settings['upload_to_module'] ?? false 
            ? segment(1) . MODULE_ASSETS_TRIGGER . "/"
            : 'public/';

        // Ensure the required fields exist
        if (empty($data['id']) || empty($data[$picture_col])) {
            return null; // Return null if data is invalid
        }

        // Construct URLs
        $data['picture_url'] = BASE_URL . $upload_dir . $picture_dir . "/" . $data['id'] . "/" . $data[$picture_col];
        $data['thumb_url'] = !empty($thumb_dir) ? BASE_URL . $upload_dir . $thumb_dir . "/" . $data['id'] . "/" . $data[$picture_col] : '';

        return $data;
    }

    /**
     * Adds `picture_url` and `thumb_url` to each row in $rows.
     *
     * @param array $rows Array of database rows to process.
     * @param array|null $picture_settings Optional settings for picture URLs.
     *
     * @return array Modified rows with `picture_url` and `thumb_url` properties.
     */
    function _add_picture_and_thumb_urls(array $rows, array $picture_settings = null): array {
        $picture_settings = $picture_settings ?? $this->_init_picture_settings();
        $picture_col = $picture_settings['target_column_name'];
        $picture_dir = $picture_settings['destination'];
        $thumb_dir = $picture_settings['thumbnail_dir'];

        $upload_dir = $picture_settings['upload_to_module'] ?? false
            ? segment(1) . MODULE_ASSETS_TRIGGER . "/"
            : "public/";

        if (empty($picture_col)) {
            return $rows;
        }

        foreach ($rows as $key => $value) {
            if (!empty($value->$picture_col)) {
                $rows[$key]->picture_url = BASE_URL . $upload_dir . $picture_dir . "/" . $value->id . "/" . $value->$picture_col;
                if (!empty($thumb_dir)) {
                    $rows[$key]->thumb_url = BASE_URL . $upload_dir . $thumb_dir . "/" . $value->id . "/" . $value->$picture_col;
                }
            }
        }

        return $rows;
    }

    /**
     * Returns picture and thumbnail paths and URLs based on settings.
     *
     * @param array|null $picture_settings Optional settings for picture locations.
     * @param string $type Type of location ('destination' or 'thumbnail_dir').
     *
     * @return array Array with 'path' and 'url' for picture or thumbnail.
     */
    function get_picture_locations(array $picture_settings = null, string $type = 'destination'): array {
        $picture_settings = $picture_settings ?? $this->_init_picture_settings();
        $destination = $picture_settings[$type] ?? $picture_settings['destination'];
        $upload_to_module = $picture_settings['upload_to_module'] ?? false;

        $data = [
            'path' => '',
            'url'  => ''
        ];

        if ($upload_to_module === true) {
            $data['path'] = APPPATH . 'modules/' . segment(1) . '/assets/' . $destination;
            $data['url'] = segment(1) . MODULE_ASSETS_TRIGGER . '/' . $destination;
        } else {
            $data['path'] = APPPATH . 'public/' . $destination;
            $data['url'] = 'public/' . $destination;
        }

        foreach ($data as &$dir) {
            $dir = preg_replace('#/+#', '/', $dir);
        }
        unset($dir);

        return $data;
    }

    /**
     * Retrieves the upload directory for the main picture based on picture settings.
     *
     * @param array $pictur_settings Settings.
     * @return string The upload directory for the main pictures.
     */
    private function get_picture_upload_directory(array $picture_settings = null): string {
        $picture_settings = $picture_settings ?? $this->_init_picture_settings();
        $destination = $picture_settings['destination'];
        $upload_to_module = $picture_settings['upload_to_module'] ?? false;
        if ($upload_to_module === true) {
            $dir = 'modules/' . segment(1) . '/assets/' . $destination;
        } else {
            $dir = 'public/' . $destination;
        }
        return $dir;
    }   

// ---------------------------------------------------------------------------------

    /**
     * Initialize gallery pictures upload settings for blog_filezone.
     *
     * @return array Configuration for picture uploads.
     */
    function _init_filezone_settings() {
        $data['targetModule'] = 'blog_posts';
        $data['destination'] = 'blog_posts_pictures';
        $data['destination_thumb'] = 'blog_posts_pictures_thumb';
        $data['destination_small'] = 'blog_posts_pictures_small';
        $data['max_file_size'] = 12*1024*1024;
        $data['max_width'] = 1200;         // Hauptbild Breite
        $data['max_height'] = 800;         // Hauptbild Höhe (neu hinzugefügt)
        $data['max_width_thumb'] = 420;    // Thumbnail Breite
        $data['max_height_thumb'] = 280;   // Thumbnail Höhe (optional, für Konsistenz)
        $data['max_width_small'] = 50;     // Small Breite
        $data['max_height_small'] = 50;    // Small Höhe
        $data['add_rand_string'] = false;
        $data['upload_to_module'] = false;
        return $data;
    }

// ---------------------------------------------------------------------------------

    /**
     * one2many relation sources
     * 
     */
    function _get_blog_sources_options($selected_key) {
        $this->module('module_relations');
        $options = $this->module_relations->_fetch_options($selected_key, 'blog_posts', 'blog_sources');
        return $options;
    }

    function _get_blog_categories_options($selected_key) {
        $this->module('module_relations');
        $options = $this->module_relations->_fetch_options($selected_key, 'blog_posts', 'blog_categories');
        return $options;
    }

    /**
     * Get the full source obj
     * source name, author, link
     * 
     */
    function _get_source_obj(int $blog_sources_id) {
        $source_obj = $this->model->get_one_where('id', $blog_sources_id, 'blog_sources');
        if ($source_obj == true){
            $source = $source_obj;
        } else {
            $source = "";
        }        
        return $source;
    }

    /**
     * Get the full category obj
     * title, url_string
     * 
     */
    function _get_category_obj(int $blog_categories_id) {
        $category_obj = $this->model->get_one_where('id', $blog_categories_id, 'blog_categories');
        if ($category_obj == true){
            $category = $category_obj;
        } else {
            $category = "";
        }        
        return $category;
    }

    // - - - GENERIC

    /**
     * Fetch options for a one-to-many relation.
     *
     * @param int $selected_key The currently selected ID.
     * @param string $target_table The target table (e.g., 'blog_posts').
     * @param string $relation_table The relation table (e.g., 'blog_sources').
     * @return array Options for the relation.
     */
    protected function _get_relation_options(int $selected_key, string $target_table, string $relation_table): array {
        $this->module('module_relations');
        return $this->module_relations->_fetch_options($selected_key, $target_table, $relation_table);
    }

    /**
     * Fetch a full object from a relation table.
     *
     * @param int $id The ID of the object.
     * @param string $table The relation table (e.g., 'blog_sources').
     * @return object|null The object or null if not found.
     */
    protected function _get_relation_obj(int $id, string $table): ?object {
        $obj = $this->model->get_one_where('id', $id, $table);
        return $obj ?: null;
    }

// ---------------------------------------------------------------------------------

    /**
     * Ensures that the current user is allowed to access the protected resource.
     * Feel free to change to suit your own individual use case.
     *
     * @return string|false The security token if the user is authorized, or false otherwise.
     */
    private function _make_sure_allowed(): string|false {
        //by default, $admin_template users (i.e., users with user_level_id === 1) are allowed
        $this->module('trongate_security');
        $token = $this->trongate_security->_make_sure_allowed();
        return $token;
    }

    /**
     * Get admin_id sending a trongate_token
     * 
     * @return int|false trongate_user_id or false
     */
    private function _get_current_admin_id($tg_token): int|false {
        $this->module('trongate_tokens');
        $tguser_id = $this->trongate_tokens->_get_user_id($tg_token);
        return $tguser_id;
    }

// ---------------------------------------------------------------------------------

    /**
     * Replace special German characters with ASCII equivalents for URL-safe strings.
     * Used in submit() for url_string generation.
     */
    function clear_sonderzeichen(string $german): string {
        $search = array('Ä', 'Ö', 'Ü', 'ä', 'ö', 'ü', 'ß', '´');
        $replace = array('Ae', 'Oe', 'Ue', 'ae', 'oe', 'ue', 'ss', '');
        return str_replace($search, $replace, $german);
    }

    /**
     * Remove HTML tags and clean text for URL-safe strings.
     * Used in submit() to prepare title for url_string.
     */
    function strip_secure_htmltags(?string $str = null) {
        // if $str is null, make it an empty string
        $str = $str ?? '';
        // decode back into html
        $str = htmlspecialchars_decode($str, ENT_QUOTES);
        // strip all HTML
        $str = strip_tags($str);
        // Strip all HTML tags again
        $str = preg_replace('~<[^>]+>~', ' ', $str);
        // replace &nbsp; with actual spaces
        $str = str_replace('&nbsp;', ' ', $str);
        // replace multiple spaces with single space
        $str = preg_replace('~\s+~', ' ', $str);
        // strip any spaces at start and end of string
        $str = trim($str);
        return $str;
    }

    /** Quote html in text, save TO DATABASE
     *  Returns secure db-safe text
     * 
     */
    function make_secure_htmltags(string $user_input): ?string {
        $text_str = trim($user_input);
        if (strlen($text_str) == 0) return null;

        $allowed = $this->allowed_html;
        $text_str = strip_tags($text_str, $allowed);
        $text_str = preg_replace('~\x{00a0}~siu', ' ', $text_str);
        $text_str = htmlspecialchars($text_str, ENT_QUOTES, 'UTF-8');
        return $text_str;
    }

// ---------------------------------------------------------------------------------

    /**
     * Replace created_by and updated_by IDs with admin usernames in a row set.
     * Used for frontview; consider SQL join for manage().
     *
     * @param array $rows Rows with created_by/updated_by IDs.
     * @return array Rows with usernames instead of IDs.
     */
    function _add_admin_names_multiple(array $rows): array {
        $created_by_values = array_values(array_unique(array_filter(array_column($rows, 'created_by'))));
        if (empty($created_by_values)) {
            return $rows;
        }

        $placeholders = implode(',', array_fill(0, count($created_by_values), '?'));
        $sql = "SELECT trongate_user_id, username FROM trongate_administrators WHERE trongate_user_id IN ($placeholders)";
        $user_rows = $this->model->query_bind($sql, $created_by_values, 'object');

        $all_authors = [];
        foreach ($user_rows as $user_row) {
            $all_authors[$user_row->trongate_user_id] = $user_row->username;
        }

        foreach ($rows as &$row) {
            $row->created_by = $all_authors[$row->created_by] ?? 'Unknown';
            if (isset($row->updated_by)) {
                $row->updated_by = $all_authors[$row->updated_by] ?? 'Unknown';
            }
        }
        return $rows;
    }

    /**
     * Map source authors on rows
     */
    function _add_source_authors(array $rows): array {
        $source_ids = array_values(array_unique(array_filter(array_column($rows, 'blog_sources_id'))));
        if (empty($source_ids)) {
            return $rows;
        }
        $placeholders = implode(',', array_fill(0, count($source_ids), '?'));
        $sql = "SELECT id, author FROM blog_sources WHERE id IN ($placeholders)";
        $source_rows = $this->model->query_bind($sql, $source_ids, 'object');
        $all_sources = array_column($source_rows, 'author', 'id');
        foreach ($rows as &$row) {
            $row->source_author = $all_sources[$row->blog_sources_id] ?? 'No Source';
        }
        unset($row);
        return $rows;
    }

    /**
     * Maps source details (author, website, link) onto blog post rows.
     *
     * This method fetches source details from the blog_sources table based on the blog_sources_id
     * in each blog post row. It adds the properties source_author, source_website, and source_link
     * to each row. If no source is found or the author is empty, source_author defaults to 'none'.
     * If website or link are not available, they default to an empty string.
     *
     * @param array $rows Array of blog post rows, each containing a blog_sources_id
     * @return array Updated rows with added source details (source_author, source_website, source_link)
     */
    function _add_source_details(array $rows): array {
        // Set default values for all rows
        foreach ($rows as &$row) {
            $row->source_author = 'none';
            $row->source_website = '';
            $row->source_link = '';
        }
        unset($row);

        // Fetch sources if any blog_sources_id exists
        $source_ids = array_values(array_unique(array_filter(array_column($rows, 'blog_sources_id'))));
        if (empty($source_ids)) {
            return $rows; // All rows already have default values set
        }

        $placeholders = implode(',', array_fill(0, count($source_ids), '?'));
        $sql = "SELECT id, author, website, link 
                FROM blog_sources 
                WHERE id IN ($placeholders)";
        $source_rows = $this->model->query_bind($sql, $source_ids, 'object');

        $all_sources = [];
        foreach ($source_rows as $source_row) {
            $all_sources[$source_row->id] = [
                'author' => $source_row->author,
                'website' => $source_row->website,
                'link' => $source_row->link
            ];
        }

        foreach ($rows as &$row) {
            if (isset($all_sources[$row->blog_sources_id])) {
                $author = $all_sources[$row->blog_sources_id]['author'];
                $row->source_author = !empty($author) ? $author : 'none'; // Consistent default to 'none'
                $row->source_website = $all_sources[$row->blog_sources_id]['website'] ?? '';
                $row->source_link = $all_sources[$row->blog_sources_id]['link'] ?? '';
            }
            // If no source is found, default values remain
        }
        unset($row);

        return $rows;
    }

    /**
     * Replace created_by and updated_by IDs with admin usernames in a single data array.
     * Used in show(); clears updated_by if no update occurred.
     *
     * @param array $data Data with created_by/updated_by IDs.
     * @return array Data with usernames.
     */
    function add_admin_names_single(array $data): array {
        $admins = $this->_get_admins_array();
        $data['created_by'] = $admins[$data['created_by']] ?? 'Unknown';
        $data['updated_by'] = (isset($data['date_updated']) && $data['date_created'] <= $data['date_updated'])
            ? $admins[$data['updated_by']] ?? 'Unknown'
            : '';
        return $data;
    }

    /**
     * Fetch all admin usernames mapped to their trongate_user_id.
     * Used for replacing created_by and updated_by IDs.
     *
     * @return array|null Map of admin IDs to usernames, or null if none found.
     */
    function _get_admins_array(): ?array {
        $rows = $this->model->get('trongate_user_id', 'trongate_administrators');

        foreach ($rows as $row) {
            $admins[$row->id] = $row->username;
        }

        return $admins;
    }

    /** not in use right now   // - - - - - - - - - eine fx mit token machen ?
     * Funktion unter Verwendung von Platzhaltern
     * :user_id als Platzhalter in der SQL-Abfrage,
     * Werte als assoziatives Array mit der Variable $params.
     * 
     */
    function _get_user_name(int $user_id): string {
        $result = $this->model->get_one_where("trongate_user_id", $user_id, "trongate_administrators");
        $user_name = ($user_id=='') ? 'unknown' : $result->username;
        return $user_name;
    }


    // wordLimit Funktion
    function wordLimit($str, $limit=20, $end_char=' [...]') {
        // leave empty
        if (trim($str) == '') {
            return $str;
        }
        
        $str = strip_tags($str);
        $str = html_entity_decode($str, ENT_COMPAT, 'UTF-8');

        // Entferne überflüssige Leerzeichen, Tabs und Zeilenumbrüche
        $find = array("/\r|\n/u", "/\t/u", "/\s\s+/u");
        $replace = array(" ", " ", " ");
        $str = preg_replace($find, $replace, $str);

        // Finde die ersten $limit Wörter
        preg_match('/\s*(?:\S*\s*){'.(int)$limit.'}/u', $str, $matches);
        // Kein Endzeichen hinzufügen, wenn Text kürzer ist
        if (strlen($matches[0]) == strlen($str)) {
            $end_char = '';
        }
        return rtrim($matches[0]).$end_char;
    }

// ---------------------------------------------------------------------------------

    /**
     * Adds category titles to blog post rows based on blog_categories_id.
     *
     * This method fetches category titles from the blog_categories table using the blog_categories_id
     * in each blog post row. It adds the property category_title to each row. If no category is found
     * or blog_categories_id is empty, category_title defaults to 'none'.
     *
     * @param array $rows Array of blog post rows, each containing a blog_categories_id
     * @return array Updated rows with added category_title property
     */
    function _add_category_titles(array $rows): array {
        // Set a standard for all rows
        foreach ($rows as &$row) {
            $row->category_title = 'none'; // Default value
        }
        unset($row);

        // Get categories if they exist
        $category_ids = array_values(array_unique(array_filter(array_column($rows, 'blog_categories_id'), fn($id) => !empty($id))));
        if (empty($category_ids)) {
            return $rows;
        }

        $placeholders = implode(',', array_fill(0, count($category_ids), '?'));
        $sql = "SELECT id, title FROM blog_categories WHERE id IN ($placeholders)";
        $category_rows = $this->model->query_bind($sql, $category_ids, 'object');

        $all_categories = array_column($category_rows, 'title', 'id');
        foreach ($rows as &$row) {
            if (isset($all_categories[$row->blog_categories_id])) {
                $row->category_title = $all_categories[$row->blog_categories_id];
            }
            // If no category is set, the default 'none' remains
        }
        unset($row);

        return $rows;
    }


        /**
     * Add shortened text to row objects.
     *
     * @param array $rows Array of row objects.
     * @param string $textKey Key for the original text (default: 'text').
     * @param string $shortTextKey Key for the shortened text (default: 'text_short').
     * @param int $limit Character limit for shortening (default: 20).
     * @param string $end_char Ending string for truncated text (default: ' [...]').
     * @return array Modified array of row objects.
     */
    function _add_short_texts(array $rows, string $textKey = 'text', string $shortTextKey = 'text_short', int $limit = 20, string $end_char = ' [...]'): array {
        foreach ($rows as &$row) {
            // Initialize short text property for all rows
            $row->$shortTextKey = '';
            
            if (isset($row->$textKey) && !empty($row->$textKey)) {
                $clean_text = $this->strip_secure_htmltags($row->$textKey);
                $row->$textKey = $clean_text;
                $row->$shortTextKey = $this->wordLimit($clean_text, $limit, $end_char);
            }
        }
        unset($row);
        return $rows;
    }

    /**
     * Add word count to row objects.
     *
     * @param array $rows Array of row objects.
     * @param string $textKey Key for the text to count (default: 'text').
     * @return array Modified array of row objects.
     */
    function _add_word_count(array $rows, string $textKey = 'text'): array {
        foreach ($rows as &$row) {
            // Initialize word count property for all rows
            $row->word_count = 0;

            if (isset($row->$textKey) && !empty($row->$textKey)) {
                $row->word_count = $this->_count_actual_words($row->$textKey);
            }
        }
        unset($row);
        return $rows;
    }

    /**
     * Counts actual words in a text, keeping only whitelisted characters and counting words.
     *
     * Whitelists letters (A-Z, a-z, Umlaute, ß, accents), numbers (0-9), apostrophes, and hyphens.
     * Removes everything else, then counts words using a regular expression.
     *
     * @param string|null $text The text to count words in.
     * @return int The number of actual words.
     */
    function _count_actual_words(?string $text = null): int {
        // If $text is null, make it an empty string
        $text = $text ?? '';
        
        // 1. Remove HTML tags and decode entities
        $clean_text = $this->strip_secure_htmltags($text);

        // 2. Whitelist: Keep only letters (including Umlaute, accents), numbers, apostrophes, and hyphens
        $clean_text = preg_replace('/[^\p{L}\p{N}\'-]/u', ' ', $clean_text);

        // 3. Replace multiple spaces with a single space and trim
        $clean_text = preg_replace('/\s+/', ' ', trim($clean_text));

        // 4. Regular expression for words
        $pattern = '/\b[\p{L}\p{N}]+(?:[\'-][\p{L}\p{N}]+)*\b/u';

        // 5. Count the words
        preg_match_all($pattern, $clean_text, $matches);

        return count($matches[0]);
    }

// ---------------------------------------------------------------------------------

    /**
     * Count gallery pictures of a specific blog post
     * 
     */
    function _count_post_pictures_simple($target_module_id){
        $column = 'target_module_id';
        $value = $target_module_id;
        $operator = '=';
        $target_tbl = 'blog_pictures';

        $num_pictures = $this->model->count_where($column, $value, $operator, $target_tbl);

        return $num_pictures;
    }

    /**
     * Counts the number of pictures associated with a specific blog post.
     *
     * @param int $target_module_id The ID of the blog post to count pictures for
     * @param string $target_module The target module name (defaults to 'blog_posts')
     * @return int The number of pictures found
     */
    function _count_post_pictures(int $target_module_id, string $target_module = 'blog_posts'): int {
        // Sicherstellen, dass $target_module_id gültig ist
        if (!is_numeric($target_module_id) || $target_module_id <= 0) {
            return 0;
        }

        // SQL-Abfrage zum Zählen der Einträge
        $sql = '
            SELECT COUNT(*) as picture_count
            FROM blog_pictures
            WHERE target_module = :target_module
            AND target_module_id = :target_module_id
        ';

        // Parameter für die Abfrage
        $params = [
            'target_module' => $target_module,
            'target_module_id' => $target_module_id
        ];

        // Abfrage ausführen
        $result = $this->model->query_bind($sql, $params, 'object');

        // Ergebnis prüfen und zurückgeben
        if ($result === false || empty($result)) {
            return 0;
        }

        return (int) $result[0]->picture_count;
    }

    /**
     * Adds the picture count to multiple blog post rows.
     *
     * @param array $rows Array of blog post objects with an 'id' property
     * @return array The modified rows with picture_count added
     */
    function _add_picture_counts_multiple(array $rows): array {
        // Extrahiere alle eindeutigen Post-IDs
        $post_ids = array_values(array_unique(array_filter(array_column($rows, 'id'))));
        if (empty($post_ids)) {
            return $rows;
        }

        // Erstelle Platzhalter für die SQL-Abfrage
        $placeholders = implode(',', array_fill(0, count($post_ids), '?'));

        // SQL-Abfrage, um die Bildanzahl pro Post zu zählen
        $sql = "
            SELECT target_module_id, COUNT(*) as picture_count
            FROM blog_pictures
            WHERE target_module = 'blog_posts'
            AND target_module_id IN ($placeholders)
            GROUP BY target_module_id
        ";

        // Führe die Abfrage aus
        $picture_rows = $this->model->query_bind($sql, $post_ids, 'object');

        // Erstelle ein Lookup-Array für die Bildanzahl
        $picture_counts = [];
        foreach ($picture_rows as $picture_row) {
            $picture_counts[$picture_row->target_module_id] = (int) $picture_row->picture_count;
        }

        // Füge die Bildanzahl jedem Datensatz hinzu
        foreach ($rows as &$row) {
            $row->picture_count = $picture_counts[$row->id] ?? 0;
        }

        return $rows;
    }

// ---------------------------------------------------------------------------------

    /**
     * Fetch the first published post
     */
    function get_first_post() {
        $sql = "SELECT * FROM blog_posts 
                WHERE published = 1 
                ORDER BY date_published DESC 
                LIMIT 1";

        $query = $this->db->query($sql);
        
        if ($query->num_rows() > 0) {
            return $query->row_array(); // returns only one and first Post as Array
        } else {
            return null; // No Post found
        }
    }

// --- WYSIWYG EDITOR - Picture Handling -------------------------------------------

    /**
     * Initialize picture upload settings for texterea editor.
     *
     * @return array Configuration for picture uploads.
     */
    function _init_editor_picture_settings(): array {
        return [
            'max_width' => 1200,
            'max_height' => 1200,
            'destination' => 'public/uploads/blog_images/',
        ];
    }

    /**
     * Handles image uploads from TinyMCE and stores them in a public folder.
     *
     * Expects a file upload via POST and returns a JSON response with the image URL.
     * Restricted to logged-in users with Trongate security. Resizes and corrects orientation.
     *
     * @return void Outputs JSON and exits
     */
    public function upload_tinymce_image(): void {
        $this->_make_sure_allowed();

        file_put_contents('upload_log.txt', 'Upload-Try: ' . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

        if (empty($_FILES) || !isset($_FILES['file'])) {
            http_response_code(400);
            echo json_encode(['error' => ['message' => 'No file uploaded']]);
            die();
        }

        $settings = $this->_init_editor_picture_settings();

        $upload_dir = APPPATH . $settings['destination'];
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file = $_FILES['file'];
        // Post-ID aus Session (falls gesetzt) oder Fallback auf 0
        $update_id = $_SESSION['current_post_id'] ?? 0;
        $filename = $update_id . '_' . uniqid() . '_' . basename($file['name']);
        $target_path = $upload_dir . $filename;

        try {
            // Dateityp prüfen
            $allowed_extensions = ['gif', 'jpg', 'jpeg', 'png'];
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($extension, $allowed_extensions)) {
                throw new Exception('Invalid file extension. Allowed: gif, jpg, jpeg, png');
            }

            // Bildvalidierung
            if (!getimagesize($file['tmp_name'])) {
                throw new Exception('Invalid image file');
            }

            // Orientierung korrigieren (falls Methode vorhanden)
            $exif = @exif_read_data($file['tmp_name']);
            $corrected_path = $this->correctImageOrientation($file['tmp_name'], $exif);

            if ($corrected_path && file_exists($corrected_path)) {
                if (!rename($corrected_path, $target_path)) {
                    throw new Exception('Failed to move corrected file');
                }
            } else {
                if (!move_uploaded_file($file['tmp_name'], $target_path)) {
                    throw new Exception('Failed to move uploaded file');
                }
            }

            // Bildgröße anpassen
            [$width, $height] = getimagesize($target_path);
            if ($width > $settings['max_width'] || $height > $settings['max_height']) {
                $this->resize_image($target_path, $settings['max_width'], $settings['max_height']);
            }

            header('Content-Type: application/json');
            $image_url = 'public/uploads/blog_images/' . $filename;
            echo json_encode(['location' => $image_url]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => ['message' => $e->getMessage()]]);
            die();
        }
    }

    /**
     * Deletes pictures in post content uploaded via Editor prefixed with the post's ID.
     *
     * Scans the Editor upload directory for files starting with the given update_id,
     * removes them, and returns a success flag. Used to clean up related images when deleting a blog post.
     *
     * @param int $update_id The ID of the blog post whose Editor images should be deleted.
     * @return bool True if all files were deleted successfully, false if any deletion failed.
     */
    private function _delete_editor_images(int $update_id): bool {
        $settings = $this->_init_editor_picture_settings();
        $upload_dir = APPPATH . $settings['destination'];
        $success = true;
        $pattern = $upload_dir . $update_id . '_*';
        foreach (glob($pattern) as $file) {
            if (is_file($file) && !unlink($file)) {
                $success = false;
            }
        }
        return $success;
    }

    /**
     * Returns an array of pictures related to a blog post from CKEditor uploads
     * 
     * @param int $update_id The blog post ID
     * @return array|null Array of image file info or null if none found
     */
    function _get_editor_images(int $update_id): ?array {
        $settings = $this->_init_editor_picture_settings();
        $upload_dir = APPPATH . $settings['destination']; // z. B. "C:/xampp/htdocs/tinyblog/public/uploads/blog_images/"
        
        $images = [];
        $pattern = $upload_dir . $update_id . '_*'; // z. B. "public/uploads/blog_images/5_*"
        
        foreach (glob($pattern) as $file) {
            if (is_file($file)) {
                $filename = basename($file);
                $images[] = [
                    'path' => $file,
                    'url' => BASE_URL . $settings['destination'] . $filename, // z. B. "http://localhost/tinyblog/public/uploads/blog_images/5_abc.jpg"
                    'filename' => $filename
                ];
            }
        }
        
        return empty($images) ? null : $images;
    }

    /**
     * Delete a specific CKEditor image and remove its reference from the blog post text
     * 
     * @param int $update_id Blog post ID
     */
    function delete_editor_image($update_id): void {
        $this->_make_sure_allowed();

        // POST-Daten abrufen
        $filename = isset($_POST['filename']) ? $_POST['filename'] : '';
        if (!is_numeric($update_id) || empty($filename)) {
            set_flashdata('Invalid request.');
            redirect('blog_posts/show/' . $update_id);
        }
        
        // Bild-URL für die Suche in text
        $settings = $this->_init_editor_picture_settings();
        $image_url = $settings['destination'] . $filename; // no BASE_URL
        
        // Text aus der Datenbank holen
        $sql = "SELECT text FROM blog_posts WHERE id = :id";
        $params = ['id' => $update_id];
        $result = $this->model->query_bind($sql, $params, 'object');
        
        if (empty($result)) {
            set_flashdata('Blog post not found.');
            redirect('blog_posts/show/' . $update_id);
        }
        
        $text = $result[0]->text;
        
        // Text dekodieren, um mit echtem HTML zu arbeiten
        $decoded_text = htmlspecialchars_decode($text, ENT_QUOTES);
        
        // <img>-Element mit der Bild-URL entfernen
        $pattern = '/<img[^>]+src="' . preg_quote($image_url, '/') . '"[^>]*>/i';
        $new_decoded_text = preg_replace($pattern, '', $decoded_text);
        
        // Text wieder encodieren, wie es make_secure_htmltags tun würde
        $new_text = htmlspecialchars($new_decoded_text, ENT_QUOTES, 'UTF-8');
        
        // Text in der Datenbank aktualisieren, wenn sich etwas geändert hat
        if ($new_text !== $text) {
            $data = ['text' => $new_text];
            $this->model->update($update_id, $data, 'blog_posts');
        }
        
        // Bild-Datei löschen
        $file_path = APPPATH . $settings['destination'] . $filename;
        if (file_exists($file_path) && is_file($file_path) && strpos($filename, $update_id . '_') === 0) {
            unlink($file_path);
            set_flashdata('Image deleted successfully.');
        } else {
            set_flashdata('Image not found or invalid.');
        }
        
        redirect('blog_posts/show/' . $update_id);
    }

    /**
     * Cleans up unused editor images by comparing the upload folder with the submitted post text.
     *
     * Scans the upload directory for files starting with the given update_id,
     * checks if they are referenced in the submitted text, and deletes those that are not.
     *
     * @param int $update_id The ID of the blog post.
     * @param ?string $submitted_text The submitted text from the POST data, or null if not provided.
     * @return void
     */
    private function _cleanup_unused_editor_images(int $update_id, ?string $submitted_text = null): void {
        // If $submitted_text is null, handle it like an empty string
        $submitted_text = $submitted_text ?? '';

        // Upload-Settings
        $settings = $this->_init_editor_picture_settings();
        $upload_dir = APPPATH . $settings['destination']; // e.g. "C:/xampp/htdocs/tinyblog/public/uploads/blog_images/"
        $pattern = $upload_dir . $update_id . '_*'; // e.g. "C:/xampp/htdocs/tinyblog/public/uploads/blog_images/5_*"

        // Find all pics in folder, starting with update_id
        $files = glob($pattern);
        if (empty($files)) {
            return; // No pictures to clean up
        }

        // decode text to work with real html
        $decoded_text = htmlspecialchars_decode($submitted_text, ENT_QUOTES);

        // check all pics in folder
        foreach ($files as $file) {
            if (!is_file($file)) {
                continue;
            }

            $filename = basename($file); // e.g. "5_abc.jpg"
            $image_url = $settings['destination'] . $filename; // e.g. "public/uploads/blog_images/5_abc.jpg"

            // check occurance in text (in an <img>-tag)
            $pattern = '/<img[^>]+src="' . preg_quote($image_url, '/') . '"[^>]*>/i';
            if (!preg_match($pattern, $decoded_text)) {
                // pic isn't used in text, delete it
                unlink($file);
            }
        }
    }

    /** not in use
     * Cleans up unused editor images by comparing the upload folder with the post text saved in DB.
     *
     * Scans the upload directory for files starting with the given update_id,
     * checks if they are referenced in the post text, and deletes those that are not.
     *
     * @param int $update_id The ID of the blog post.
     * @return void
     */
    private function _cleanup_unused_editor_images_in_db(int $update_id): void {
        // get upload settings
        $settings = $this->_init_editor_picture_settings();
        $upload_dir = APPPATH . $settings['destination'];
        $pattern = $upload_dir . $update_id . '_*';

        // find all pictures in folder starting with update_id
        $files = glob($pattern);
        if (empty($files)) {
            return; // nothing found
        }

        // get text from db
        $sql = "SELECT text FROM blog_posts WHERE id = :id";
        $params = ['id' => $update_id];
        $result = $this->model->query_bind($sql, $params, 'object');
        if (empty($result)) {
            return; // nothing found
        }
        $text = $result[0]->text;

        // decode text, t work with html
        $decoded_text = htmlspecialchars_decode($text, ENT_QUOTES);

        // check all pictures in folder
        foreach ($files as $file) {
            if (!is_file($file)) {
                continue;
            }

            $filename = basename($file);
            $image_url = $settings['destination'] . $filename;

            // look for picture filename in text (in an <img>-tag)
            $pattern = '/<img[^>]+src="' . preg_quote($image_url, '/') . '"[^>]*>/i';
            if (!preg_match($pattern, $decoded_text)) {
                // delete if it is not used
                unlink($file);
            }
        }
    }

    /**
     * Handles image uploads from CKEditor and stores them in a public folder.
     *
     * Expects a file upload via POST and returns a JSON response with the image URL.
     * Restricted to logged-in users with Trongate security. Resizes and corrects orientation.
     *
     * @return void Outputs JSON and exits
     */
    public function upload_ckeditor_image(): void {
        $this->_make_sure_allowed();

        if (empty($_FILES) || !isset($_FILES['upload'])) {
            http_response_code(400);
            echo json_encode(['error' => ['message' => 'No file uploaded']]);
            die();
        }

        $settings = $this->_init_editor_picture_settings();

        $upload_dir = APPPATH . $settings['destination'];
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file = $_FILES['upload'];
        // Post-ID aus Session (falls gesetzt) oder Fallback auf 0
        $update_id = $_SESSION['current_post_id'] ?? 0; // Passe den Schlüssel an deine Session an
        $filename = $update_id . '_' . uniqid() . '_' . basename($file['name']);
        $target_path = $upload_dir . $filename;

        try {
            if (!getimagesize($file['tmp_name'])) {
                throw new Exception('Invalid image file');
            }

            // Orientierung korrigieren
            $exif = @exif_read_data($file['tmp_name']);
            $corrected_path = $this->correctImageOrientation($file['tmp_name'], $exif);

            if ($corrected_path && file_exists($corrected_path)) {
                if (!rename($corrected_path, $target_path)) {
                    throw new Exception('Failed to move corrected file');
                }
            } else {
                if (!move_uploaded_file($file['tmp_name'], $target_path)) {
                    throw new Exception('Failed to move uploaded file');
                }
            }

            // Resize image
            [$width, $height] = getimagesize($target_path);
            if ($width > $settings['max_width'] || $height > $settings['max_height']) {
                $this->resize_image($target_path, $settings['max_width'], $settings['max_height']);
            }

            $image_url = BASE_URL . 'public/uploads/blog_images/' . $filename;
            echo json_encode(['url' => $image_url]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => ['message' => $e->getMessage()]]);
            die();
        }
    }

// --- DASHBOARD -------------------------------------------------------------------

    /** 
     * Get posts by date for the calendar
     * 
     */
    function get_posts_by_date($date) {
        $this->_make_sure_allowed();
        /*try {
            $this->_make_sure_allowed();
        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized access']);
            die();
        }*/

        // format date
        $date = date('Y-m-d', strtotime($date));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Invalid date format']);
            die();
        }

        // db query
        $sql = "
            SELECT title, date_published
            FROM blog_posts
            WHERE published = 1
            AND DATE(date_published) = ?
        ";
        $rows = $this->model->query_bind($sql, [$date], 'object');

        $posts = [];
        if ($rows) {
            foreach ($rows as $row) {
                $posts[] = [
                    'title' => $row->title,
                    'date_published' => $row->date_published
                ];
            }
        }

        header('Content-Type: application/json');
        echo json_encode($posts);
    }

    /**
     * Returns HTML for a widget showing the 4 latest posts with titles, dates, and images.
     *
     * This method queries the posts table for the latest entries based on their publication date,
     * prepares the data including titles, URLs, publication dates, and images, and renders it
     * into a view template. If a post has an associated picture, it constructs the full URL using
     * the picture directory and post ID; otherwise, it falls back to a default image.
     *
     * @return string HTML string for the widget or empty string if no posts are found.
     */
    public function widget_recent_posts() {
        $sql = '
            SELECT 
                id,
                title, 
                url_string,
                date_published,
                picture
            FROM 
                blog_posts 
            ORDER BY 
                date_published DESC 
            LIMIT 10
        ';
        $rows = $this->model->query($sql, 'object');

        if (empty($rows)) {
            return '';
        }
        
        $pic_dir = $this->get_picture_locations(NULL, 'thumbnail_dir')['url'];
        $pic_fallback = $this->picture_fallback;

        $data = [];
        foreach ($rows as $post) {
            $row_data['id']          = $post->id;
            $row_data['title']          = $post->title;
            $row_data['url_string']     = $post->url_string;
            $row_data['date_published'] = date('Y-m-d h:i', strtotime($post->date_published));
            $row_data['picture']        = $post->picture 
                ? BASE_URL . $pic_dir . '/' . $post->id . '/' . $post->picture 
                : $pic_fallback;
            
            $data['rows'][] = (object) $row_data;
        }

        // Total count of blog posts
        $data['num_blog_posts'] = $this->_get_total_count('blog_posts');

        return $this->view('_widget_recent_posts', $data, true) ?: '';
    }

    /**
     * Retrieves the total count of rows in a specified database table.
     *
     * This private method executes a SQL COUNT query on the given table and returns
     * the result as an integer. It assumes the table exists and the model supports
     * the query method with an 'object' return type.
     *
     * @param string $table The name of the database table to count rows from.
     * @return int The total number of rows, or 0 if the table is empty or query fails.
     * @throws \RuntimeException If the database query fails or the table is invalid.
     */
    private function _get_total_count(string $table): int {
        $sql = "SELECT COUNT(*) as total FROM $table";
        $result = $this->model->query($sql, 'object');
        return $result ? (int) $result[0]->total : 0;
    }
}