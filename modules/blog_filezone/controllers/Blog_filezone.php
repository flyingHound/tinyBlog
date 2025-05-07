<?php

/**
 * Trongate Filezone is a multi-file uploader, giving users the ability to drag and drop files.
 * Trongate Filezone comes with built-in authentication via usage of Trongate's token system.
 * 
 * UPGRADE: 
 * Abandoned resized_max_width and -height for max_width and max_height
 * Resizing main, thumb and small picture, xif cell phone data, ordering
 * 
 */
class Blog_filezone extends Trongate {

    /**
     * Renders the summary panel for a given update ID and Filezone settings.
     *
     * @param int $update_id The ID of the update.
     * @param array $filezone_settings Settings related to the Filezone.
     * @return void
     */
    public function _draw_summary_panel(int $update_id, array $filezone_settings): void {
        $this->module('trongate_security');
        $data['token'] = $this->trongate_security->_make_sure_allowed();

        // ensure upload folders exists
        $this->make_sure_got_sub_folder($update_id, $filezone_settings);

        // get directories
        $locations = $this->_get_filezone_locations($filezone_settings);

        // data for the view
        $data['pictures_dir'] = $locations['pictures']['url'];
        # $data['thumbs_dir'] = $locations['thumbs']['url'];
        # $data['small_dir'] = $locations['small']['url'];
        
        $data['pictures_target'] = BASE_URL . $locations['pictures']['url'] . '/' . $update_id;
        $data['thumbs_target'] = BASE_URL . $locations['thumbs']['url'] . '/' . $update_id;
        $data['small_target'] = BASE_URL . $locations['small']['url'] . '/' . $update_id;

        $data['pictures'] = $this->_fetch_pictures($update_id, $filezone_settings);
        $data['uploader_url'] = 'blog_filezone/uploader/' . $filezone_settings['targetModule'] . '/' . $update_id;
        $data['order_url'] = 'blog_pictures/order_pictures/' . $filezone_settings['targetModule'] . '/' . $update_id;
        $data['update_id'] = $update_id;
        $data['target_module'] = $filezone_settings['targetModule'];

        $this->view('multi_summary_panel', $data);
    }

// ---------------------------------------------------------------------------------

    /**
     * Ensure subfolders for all Filezone directories exist.
     */
    private function make_sure_got_sub_folder(int $update_id, array $filezone_settings, ?string $target_module = null): void {
        $locations = $this->_get_filezone_locations($filezone_settings, $target_module);
        
        foreach ($locations as $type => $location) {
            $target_dir = APPPATH . $location['path'] . DIRECTORY_SEPARATOR . $update_id;
            $target_dir = str_replace('\\', DIRECTORY_SEPARATOR, $target_dir);
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
        }
    }
    
    /**
     * Get paths and URLs for all Filezone directories (pictures, thumbs, small).
     * Be aware that params can't be empty without correct url-segment(1)
     *
     * @param array|null $filezone_settings Filezone settings, defaults to _init_filezone_settings().
     * @param string|null $target_module Optional target module override.
     * @return array Associative array with paths and URLs for all three directories.
     * @throws InvalidArgumentException If settings are invalid.
     */
    function _get_filezone_locations(?array $filezone_settings = null, ?string $target_module = null): array {
        $target_module = $target_module ?? segment(1);
        if ($filezone_settings === null) {
            $this->module($target_module); // Lade das Modul
            $filezone_settings = $this->$target_module->_init_filezone_settings();
        }
        
        // validate
        if (empty($filezone_settings)) {
            throw new InvalidArgumentException('Filezone settings cannot be empty');
        }

        $target_module = $target_module ?? $filezone_settings['targetModule'] ?? segment(1);
        $upload_to_module = $filezone_settings['upload_to_module'] ?? false;

        // directories of picture versions
        $types = [
            'pictures' => $filezone_settings['destination'] ?? 'blog_posts_pictures',
            'thumbs' => $filezone_settings['destination_thumb'] ?? 'blog_posts_pictures_thumb',
            'small' => $filezone_settings['destination_small'] ?? 'blog_posts_pictures_small'
        ];

        $locations = [];
        foreach ($types as $type => $destination) {
            $locations[$type] = [
                'path' => '',
                'url' => ''
            ];

            if ($upload_to_module === true) {
                $locations[$type]['path'] = 'modules/' . $target_module . '/assets/' . $destination;
                $locations[$type]['url'] = $target_module . MODULE_ASSETS_TRIGGER . '/' . $destination;
            } else {
                $locations[$type]['path'] = 'public/' . $destination;
                $locations[$type]['url'] = 'public/' . $destination;
            }

            // normalizing paths
            $locations[$type]['path'] = preg_replace('#/+#', '/', $locations[$type]['path']);
            $locations[$type]['url'] = preg_replace('#/+#', '/', $locations[$type]['url']);
        }

        return $locations;
    }

    /**
     * Fetch picture filenames from the Filezone directory for a given update ID.
     *
     * @param int $update_id The ID of the blog post.
     * @param array $filezone_settings Filezone settings.
     * @return array List of picture filenames.
     */
    private function _fetch_pictures(int $update_id, array $filezone_settings): array {
        $data = [];

        // Pfad aus _get_filezone_locations()
        $pictures_path = APPPATH . $this->_get_filezone_locations($filezone_settings)['pictures']['path'] . '/' . $update_id;

        // Pfad standardisieren
        $pictures_path = str_replace('\\', '/', $pictures_path);

        // Prüfen, ob Verzeichnis existiert
        if (is_dir($pictures_path)) {
            $pictures = scandir($pictures_path);

            // Unerwünschte Einträge filtern
            foreach ($pictures as $value) {
                if (!in_array($value, ['.', '..', '.DS_Store'])) {
                    $data[] = $value;
                }
            }
        }

        return $data;
    }

// ---------------------------------------------------------------------------------

    function OUT_get_upload_dir(array $filezone_settings, string $target_module = null): string {
        $target_module = $target_module ?? $filezone_settings['targetModule'];
        return ($filezone_settings['upload_to_module'] ?? false) 
            ? 'modules/'.$target_module . '/assets'//  - - - - - - - - - - >  wie jetzt ???
            : 'public';
    }

    /** NOT IN USE !!
     * Retrieves the directory for pictures based on Filezone settings.
     *
     * @param array $filezone_settings Settings related to the Filezone.
     * @return string The directory for pictures.
     */
    private function get_pictures_directory(array $filezone_settings): string {
        $target_module = $filezone_settings['targetModule'];
        $directory = $target_module . '_pictures';
        return $directory;
    }

// ---------------------------------------------------------------------------------

    /**
     * Renders a page that displays the uploader view.
     *
     * @return void
     */
    public function uploader(): void {
        $this->module('trongate_security');
        $data['token'] = $this->trongate_security->_make_sure_allowed();

        $target_module = segment(3); // Child-Module: settings['targetModule'] übernimmt später
        $update_id = segment(4);

        $this->module($target_module);
        $settings = $this->$target_module->_init_filezone_settings();
        $target_module = $settings['targetModule'] ?? $target_module;

        $locations = $this->_get_filezone_locations($settings);
        $dir = APPPATH . $locations['pictures']['path'] . '/' . $update_id;
        $target_dir = BASE_URL . $locations['pictures']['url'] . '/' . $update_id;

        // Bereits hochgeladene Bilder
        $previously_uploaded_files = [];
        if (is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file) {
                if (!in_array($file, ['.', '..', '.DS_Store'])) {
                    $previously_uploaded_files[] = [
                        'directory' => $target_dir,
                        'filename' => $file,
                        'overlay_id' => $this->_get_overlay_id($file)
                    ];
                }
            }
        }

        $data['previously_uploaded_files'] = $previously_uploaded_files;
        $data['previously_uploaded_files_cnt'] = count($previously_uploaded_files);
        $data['files_cnt'] = count($previously_uploaded_files) + 1;
        $data['update_id'] = $update_id;
        $data['dir'] = $dir;

        $data['target_module_desc'] = ucwords(str_replace('_', ' ', $target_module));
        $data['target_module'] = $target_module;
        
        $data['previous_url'] = BASE_URL . $target_module . '/show/' . $update_id;
        $data['upload_url'] = BASE_URL . 'blog_filezone/upload/' . $target_module . '/' . $update_id;
        $data['order_url'] = BASE_URL . 'blog_pictures/order_pictures/' . $target_module . '/' . $update_id;
        $data['delete_url'] = BASE_URL . 'blog_filezone/ditch';

        $data['additional_includes_top'] = [BASE_URL . 'blog_filezone_module/css/trongate-filezone.css'];
        $data['additional_includes_btm'] = [BASE_URL . 'blog_filezone_module/js/trongate-filezone.js'];

        $data['headline'] = 'Upload Pictures';
        $data['view_file'] = 'uploader_panel';
        $this->template('admin', $data);
    }

    /**
     * Handles upload and deletion requests for Filezone.
     */
    public function upload(): void {
        api_auth();

        $request_type = $_SERVER['REQUEST_METHOD'];
        $target_module = segment(3);
        $update_id = segment(4);

        $this->module($target_module);
        $settings = $this->$target_module->_init_filezone_settings();
        $target_module = $settings['targetModule'] ?? $target_module;

        if ($request_type === 'DELETE') {
            $this->_remove_picture($target_module, $update_id);
        } else {
            $this->_do_upload($target_module, $update_id);
        }
    }

    /**
     * Process the file upload and create multiple versions.
     */
    private function _do_upload(string $target_module, int $update_id): void {
        try {
            if (empty($_FILES)) {
                throw new Exception('No files uploaded.');
            }

            $file = reset($_FILES); // Erste Datei (Dropzone macht meist eine pro Request)
            $this->_make_sure_image($file);
            $file['name'] = $this->_prep_file_name($file['name']);

            list($width, $height) = getimagesize($file['tmp_name']);
            $file_size = $file['size'];

            $this->module($target_module);
            $filezone_settings = $this->$target_module->_init_filezone_settings();
            $locations = $this->_get_filezone_locations($filezone_settings);

            $config = [
                'data' => [
                    'target_module' => $target_module,
                    'update_id' => $update_id,
                    'max_file_size' => $filezone_settings['max_file_size'] ?? 12 * 1024 * 1024,
                    'add_rand_string' => $filezone_settings['add_rand_string'] ?? false,
                    'permissions' => 0755,
                    'source_width' => $width,
                    'source_height' => $height,
                    'source_size' => $file_size,
                ],
                'versions' => [
                    'picture' => [
                        'path' => APPPATH . $locations['pictures']['path'] . '/' . $update_id,
                        'max_width' => $filezone_settings['max_width'] ?? 2500,
                        'max_height' => $filezone_settings['max_height'] ?? 1400,
                        'compression' => 100,
                    ],
                    'thumb' => [
                        'path' => APPPATH . $locations['thumbs']['path'] . '/' . $update_id,
                        'max_width' => $filezone_settings['max_width_thumb'] ?? 420,
                        'max_height' => null, // Nur Breite für Thumbs
                        'compression' => 90,
                    ],
                    'small' => [
                        'path' => APPPATH . $locations['small']['path'] . '/' . $update_id,
                        'max_width' => $filezone_settings['max_width_small'] ?? 50,
                        'max_height' => $filezone_settings['max_height_small'] ?? 50,
                        'compression' => 70,
                    ],
                ],
            ];

            if ($file_size > $config['data']['max_file_size']) {
                throw new Exception('File exceeds max size.');
            }

            $secure_filename = $this->_process_upload($file, $config);
            $response = [
                'success' => true,
                'filename' => $secure_filename,
                'overlay_id' => $this->_get_overlay_id($secure_filename)
            ];
            echo json_encode($response);
            http_response_code(200);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }

    /**
     * Process the upload, resize, and store multiple versions of an image.
     */
    private function _process_upload(array $file, array $config): string {
        $filename = $file['name'];
        $source_path = $file['tmp_name'];

        $exif = @exif_read_data($source_path);
        $corrected_image_path = $this->correctImageOrientation($source_path, $exif);

        if ($corrected_image_path) {
            $image = new Image($corrected_image_path);
            list($width, $height) = getimagesize($corrected_image_path);
        } else {
            $image = new Image($source_path);
            $width = $config['data']['source_width'];
            $height = $config['data']['source_height'];
        }

        $data = [
            'source_path' => $source_path,
            'image' => $image,
            'filename' => $filename,
            'width' => $width,
            'height' => $height,
        ];

        foreach ($config['versions'] as $version_name => $version) {
            if (isset($version['path'])) {
                $this->_ensure_directory_exists($version['path'], $config['data']['permissions']);
                
                $tmp_image = clone $image;
                $data['image'] = $tmp_image;
                $data['path'] = $version['path'];
                $data['max_width'] = $version['max_width'];
                $data['max_height'] = $version['max_height'];
                $data['compression'] = $version['compression'] ?? 100;

                $this->_resize_and_store_image($data);
                $tmp_image->destroy();
            }
        }

        if ($corrected_image_path && file_exists($corrected_image_path)) {
            unlink($corrected_image_path);
        }
        $image->destroy();

        $priority = $this->_fetch_highest_priority($config['data']['target_module'], $config['data']['update_id']);
        $priority = $priority[0]->highest_priority ? $priority[0]->highest_priority + 1 : 1;
        $this->_save_to_database($filename, $priority, $config['data']['target_module'], $config['data']['update_id']);

        return $filename;
    }

// ---------------------------------------------------------------------------------

    /**
     * Ensures that the provided argument is an image.
     *
     * @param array $value The value to be checked if it represents an image.
     * @return void
     */
    private function _make_sure_image(array $value): void {
        $target_str = 'image/';
        $first_six = substr($value['type'], 0, 6);

        if ($first_six !== $target_str) {
            http_response_code(403);
            echo 'Not an image!';
            die();
        }
    }

    /** Must still add option 'add_rand_string' in future
     * 
     * 
     */
    function _prep_file_name($file_name) {
        $bits = explode('.', $file_name);
        $last_bit = '.'.$bits[count($bits)-1];

        //remove last_bit from the file_name
        $file_name = str_replace($last_bit, '', $file_name);
        $safe_file_name = $file_name;
        $safe_file_name = url_title($file_name);

        //get the first 15 chars, enough for timestamp
        $safe_file_name = substr($safe_file_name, 0, 15);
        $safe_file_name.= '_'.make_rand_str(4);
        $safe_file_name.= $last_bit;
        return $safe_file_name;
    }

    private function correctImageOrientation($sourcePath, $exif) {
        if (!empty($exif['Orientation'])) {
            
            // Bildtyp erkennen
            $imageInfo = getimagesize($sourcePath);
            $mime = $imageInfo['mime'];

            // Bild je nach Typ laden
            switch ($mime) {
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($sourcePath);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($sourcePath);
                    break;
                case 'image/gif':
                    $image = imagecreatefromgif($sourcePath);
                    break;
                case 'image/webp':
                    $image = imagecreatefromwebp($sourcePath);
                    break;
                default:
                    return false; // Nicht unterstütztes Format
            }

            switch ($exif['Orientation']) {
                case 3:
                    $image = imagerotate($image, 180, 0);
                    break;
                case 6:
                    $image = imagerotate($image, -90, 0);
                    break;
                case 8:
                    $image = imagerotate($image, 90, 0);
                    break;
                case 2:
                    imageflip($image, IMG_FLIP_HORIZONTAL);
                    break;
                case 4:
                    imageflip($image, IMG_FLIP_VERTICAL);
                    break;
                case 5:
                    imageflip($image, IMG_FLIP_HORIZONTAL);
                    $image = imagerotate($image, -90, 0);
                    break;
                case 7:
                    imageflip($image, IMG_FLIP_HORIZONTAL);
                    $image = imagerotate($image, 90, 0);
                    break;
            }

            // Save the rotated image to a temporary file based on its original format
            $tempFile = tempnam(sys_get_temp_dir(), 'rotated_');
            switch ($mime) {
                case 'image/jpeg':
                    imagejpeg($image, $tempFile);
                    break;
                case 'image/png':
                    imagepng($image, $tempFile);
                    break;
                case 'image/gif':
                    imagegif($image, $tempFile);
                    break;
                case 'image/webp':
                    imagewebp($image, $tempFile);
                    break;
            }
            imagedestroy($image);

            return $tempFile;
        }
        return false; // No rotation needed
    }

    /**
     * Ensures that a directory exists or creates it, if it doesn't.
     *
     * @param string $path The path of the directory.
     * @param int $permissions (optional) Permissions for the directory (Standard: 0755).
     * @throws Exception In case directory can't be created.
     */
    private function _ensure_directory_exists(string $path, int $permissions = 0755): void {
        if (!is_dir($path)) {
            if (!mkdir($path, $permissions, true) && !is_dir($path)) {
                throw new Exception("Failed to create directory: $path");
            }
        }
    }

    /**
     * Resizes an image to fit within specified maximum dimensions while preserving its aspect ratio,
     * then saves it to the given file path. Used for processing uploaded images in different versions
     * (e.g., main picture, thumbnail, small).
     *
     * @param array $data Associative array containing:
     *              - 'image': Trongate Image object to be resized.
     *              - 'path': Directory path where the image will be saved.
     *              - 'filename': Name of the file to save.
     *              - 'width': Original width of the image in pixels.
     *              - 'height': Original height of the image in pixels.
     *              - 'max_width' (optional): Maximum allowed width (default: 2500).
     *              - 'max_height' (optional): Maximum allowed height (default: 1400).
     *              - 'compression' (optional): Compression quality (default: 100).
     * @return void
     */
    function _resize_and_store_image($data): void {
        $image = $data['image'];
        $filename = $data['path'] . '/' . $data['filename'];
        $source_width = $data['width'];
        $source_height = $data['height'];
        $max_width = $data['max_width'] ?? 2500;
        $max_height = $data['max_height'] ?? 1400;
        $compression = $data['compression'] ?? 100;

        if ($source_width <= $max_width && $source_height <= $max_height) {
            $image->save($filename, $compression);
            return;
        }

        $ratio = $source_width / $source_height;

        if ($source_width > $max_width) {
            $image->resize_to_width($max_width);
            $new_width = $max_width;
            $new_height = $new_width / $ratio;
        } else {
            $new_width = $source_width;
            $new_height = $source_height;
        }

        if ($new_height > $max_height) {
            $image->resize_to_height($max_height);
        }

        $image->save($filename, $compression);
    }

    /**
     * Not in use
     * 
     */
    function _crop_resize_and_store_image($data) {
        // data
        $image = $data['image'];
        $filename =  $data['path'] . '/' . $data['filename'];
        $source_width = $data['width'];
        $source_height = $data['height'];
        $max_width  = $data['max_width'];
        $max_height = $data['max_height'];

        $reduce_width = false;
        $reduce_height = false;

        #echo json($data, true);

        if (!isset($data['compression'])) {
            $compression = 100;
        } else {
            $compression = $data['compression'];
        }

        //do we need to resize the picture?
        if ((isset($max_width)) && ($source_width>$max_width)) {
            $reduce_width = true;
        }

        if ((isset($max_height)) && ($source_height>$max_height)) {
            $reduce_height = true;
        }

        //resize rules figured out, let's rock...
        if (($reduce_width == true) && ($reduce_height == false)) {
            $image->resize_to_width($max_width);
            $image->save($filename, $compression);
        }

        if (($reduce_width == false) && ($reduce_height == true)) {
            $image->resize_to_height($max_height);
            $image->save($filename, $compression);
        }

        if (($reduce_width == false) && ($reduce_height == false)) {
            $image->save($filename, $compression);
        }

        if (($reduce_width == true) && ($reduce_height == true)) {
            
            // For square thumbnails, this logic assumes $max_width == $max_height
            if ($max_width == $max_height) {
                // Scale image to fit within the square
                if ($source_width > $source_height) {
                    $image->resize_to_height($max_height);
                } else {
                    $image->resize_to_width($max_width);
                }
                // Now crop it to be square
                $image->crop($max_width, $max_height, 'center');
                $image->save($filename, $compression);
            } else {
                // Non-square thumbnail logic
                
                // Calculate aspect ratios
                $source_aspect_ratio = $source_width / $source_height;
                $target_aspect_ratio = $max_width / $max_height;

                if ($source_aspect_ratio > $target_aspect_ratio) {
                    // Source is wider than target, fit by height
                    $image->resize_to_height($max_height);
                    // Now crop width to fit
                    $image->crop($max_width, $max_height, 'center');
                    $image->save($filename, $compression);
                } else {
                    // Source is taller or equal to target in aspect ratio, fit by width
                    $image->resize_to_width($max_width);
                    // Now crop height to fit
                    $image->crop($max_width, $max_height, 'center');
                    $image->save($filename, $compression);
                }
            }
        }
    }

    function _fetch_highest_priority($target_module, $target_module_id) {
        $params['target_module'] = $target_module;
        $params['target_module_id'] = $target_module_id;

        $sql = 'SELECT MAX(priority) AS highest_priority
                FROM blog_pictures 
                WHERE target_module = :target_module AND target_module_id = :target_module_id';
        $data = $this->model->query_bind($sql, $params, 'object');

        return $data;
    }

    private function _save_to_database($filename, $priority, $target_module, $update_id) {
        // priority: count pictures in db, increment by 1
        $data = [
            'picture' => $filename,
            'priority' => $priority,
            'target_module' => $target_module,
            'target_module_id' => $update_id,
            // 'created_at' => date('Y-m-d H:i:s'), // noch nicht in db table realisiert.
        ];
        $this->model->insert($data, 'blog_pictures');
    }



// ---------------------------------------------------------------------------------

    /**
     * Removes a picture and all thumbnails from Folders and DB
     * Called from the Multi Summary Panel
     * 
     */
    function _remove_picture($target_module, $update_id) {
        $input = json_decode(file_get_contents('php://input'), true);
        $picture_path = $input['picture_path'] ?? '';

        if (!$picture_path) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'No picture path provided']);
            exit;
        }

        // extract name from path
        $picture_name = $this->get_file_name($picture_path);
        
        // check if picture is in db and recklessly delete it
        $picture_obj = $this->model->get_one_where('picture', $picture_name, 'blog_pictures'); 
        if ($picture_obj) {
            $this->model->delete($picture_obj->id, 'blog_pictures');
        }

        // get filezone setting and locations
        $this->module($target_module);
        $settings = $this->$target_module->_init_filezone_settings();
        $locations = $this->_get_filezone_locations($settings);

        // get directory paths
        $directories = [
            'pictures' => $locations['pictures']['path'],
            'thumbs' => $locations['thumbs']['path'],
            'small' => $locations['small']['path'],
        ];

        // delete pictures
        $success = true;
        foreach ($directories as $type => $dir) {
            $file_path = APPPATH . $dir . '/' . $update_id . '/' . $picture_name;
            if (file_exists($file_path)) {
                if (!unlink($file_path)) {
                    $success = false;
                }
            }
        }

        // just tell em how it went
        if ($success) {
            echo json_encode($picture_path); // Frontend erwartet den Pfad als Bestätigung
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to delete some files']);
        }
    }

    /**
     * Returns the file name of a file path
     * 
     */
    function get_file_name($picture_path) {
        $bits = explode('/', $picture_path);
        return end($bits); // Letztes Element direkt holen
    }

    /**
     * Fetches pictures based on the provided update ID and Filezone settings.
     * Outputs the fetched pictures as a JSON response.
     *
     * @return void
     */
    private function fetch(): void {
        $target_module = segment(3);
        $update_id = segment(4);

        if (($target_module === '') || (!is_numeric($update_id))) {
            http_response_code(422);
            echo 'Invalid target module and/or update_id.';
            die();
        }

        //get the settings
        $this->module($target_module);
        $filezone_settings = $this->$target_module->_init_filezone_settings();
        $pictures = $this->fetch_pictures($update_id, $filezone_settings);
        http_response_code(200);
        echo json_encode($pictures);
    }

    /**
     * Handles the deletion of a specific image file based on the posted data.
     * Authorizes the API request, retrieves the necessary data, and removes the designated image file.
     *
     * @return void
     */
    function ditch() {
        api_auth();
        $post = file_get_contents('php://input');
        $posted_data = json_decode($post, true);

        #echo json($posted_data, true);

        // Validate input
        if (!isset($posted_data['elId'], $posted_data['update_id'], $posted_data['target_module'])) {
            http_response_code(400);
            echo 'Invalid input data';
            return;
        }

        $element_id = $posted_data['elId'];
        $update_id = $posted_data['update_id'];
        $target_module = $posted_data['target_module'];

        $this->module($target_module);
        $settings = $this->$target_module->_init_filezone_settings();

        // Convert element_id to filename
        $target_filename = preg_replace('/-(\w+)$/', '.$1', $element_id);

        // Delete from database, no mercy
        $picture_obj = $this->model->get_one_where('picture', $target_filename, 'blog_pictures');
        if ($picture_obj) {
            $this->model->delete($picture_obj->id, 'blog_pictures');
        }

        $locations = $this->_get_filezone_locations($settings);

        // Define directories in an array
        $directories = [
            APPPATH . $locations['pictures']['path'] . '/' . $update_id . '/' . $target_filename,
            APPPATH . $locations['thumbs']['path'] . '/' . $update_id . '/' . $target_filename,
            APPPATH . $locations['small']['path'] . '/' . $update_id . '/' . $target_filename,
        ];

        // Brutally delete everything
        $something_deleted = false;
        foreach ($directories as $file_path) {
            if (file_exists($file_path)) {
                unlink($file_path);
                $something_deleted = true;
            }
        }
        
        // Response: If anything was deleted, it's a success
        if ($something_deleted || $picture_obj) {
            http_response_code(200);
            echo $element_id; // Erfolg, etwas wurde gelöscht
        } else {
            http_response_code(404);
            echo 'Nothing found to delete'.$element_id.' - '.json($directories);
        }
    }

    /**
     * Retrieves the overlay ID that corresponds with the provided filename.
     *
     * @param string $filename The input filename.
     * @return string The extracted overlay ID.
     */
    private function _get_overlay_id(string $filename): string {
        $bits = explode('.', $filename);
        $last_bit = $bits[count($bits) - 1];
        $ditch = '.' . $last_bit;
        $replace = '-' . $last_bit;
        $overlay_id = str_replace($ditch, $replace, $filename);
        return $overlay_id;
    }

    /**
     * Retrieves a specified portion of a string.
     *
     * @param string $str The input string.
     * @param int $target_length The length of the desired string portion.
     * @param bool|null $from_start Determines if the portion is retrieved from the start or the end of the string.
     * @return string The extracted string portion.
     */
    private function OLD_get_str_chunk(string $str, int $target_length, ?bool $from_start = null): string {
        $strlen = strlen($str);
        $start_pos = $strlen - $target_length;

        if (isset($from_start)) {
            $start_pos = 0;
        }

        $str_chunk = substr($str, $start_pos, $target_length);
        return $str_chunk;
    }

    /**
     * Removes a picture based on the provided target module and update ID.
     * Handles the deletion of the specified picture path.
     *
     * @param string $target_module The target module for the picture.
     * @param int $update_id The ID related to the update of the picture.
     * @return void
     */
    private function OLD_remove_picture(string $target_module, int $update_id): void {
        $post = file_get_contents('php://input');
        $decoded = json_decode($post, true);
        $picture_path = file_get_contents("php://input");
        $picture_path = str_replace(BASE_URL, '', $picture_path);

        $this->module($target_module);
        $filezone_settings = $this->$target_module->_init_filezone_settings();

        if ($filezone_settings['upload_to_module'] === true) {
            $target_module = (isset($filezone_settings['targetModule']) ? $filezone_settings['targetModule'] : segment(1));
            $ditch = $target_module . MODULE_ASSETS_TRIGGER . '/';
            $replace = '';
            $picture_path = str_replace($ditch, $replace, $picture_path);
            $picture_path = APPPATH . 'modules/' . $target_module . '/assets/' . $picture_path;
        } else {
            $picture_path = APPPATH . 'public/' . $picture_path;
        }

        $picture_path = str_replace('\\', '/', $picture_path);

        if (file_exists($picture_path)) {
            //delete the picture
            unlink($picture_path);
            $this->fetch();
        } else {
            echo 'file does not exist at ' . $picture_path;
            die();
            http_response_code(422);
            echo $picture_path;
        }
    }

    /**
     * Fetches pictures based on the provided update ID and Filezone settings.
     * Outputs the fetched pictures as a JSON response.
     *
     * @return void
     */
    private function OLD_fetch(): void {
        $target_module = segment(3);
        $update_id = segment(4);

        if (($target_module === '') || (!is_numeric($update_id))) {
            http_response_code(422);
            echo 'Invalid target module and/or update_id.';
            die();
        }

        //get the settings
        $this->module($target_module);
        $filezone_settings = $this->$target_module->_init_filezone_settings();
        $pictures = $this->fetch_pictures($update_id, $filezone_settings);
        http_response_code(200);
        echo json_encode($pictures);
    }

// ---------------------------------------------------------------------------------

    /**
     * Deletes all gallery images and their database entries for a given blog post ID.
     *
     * Removes files from the pictures, thumbnails, and small directories as defined in
     * Blog_filezone settings, and deletes corresponding entries from the blog_pictures table.
     *
     * @param int $update_id The ID of the blog post whose images are to be deleted.
     * @param string|null $target_module Optional module name; defaults to current segment(1).
     * @return bool True if all deletions succeed, false if any part fails.
     */
    function _delete_filezone_pictures(int $update_id, ?string $target_module = null): bool {
        $target_module = $target_module ?? segment(1);
        $this->module($target_module);
        $filezone_settings = $this->$target_module->_init_filezone_settings();
        $locations = $this->_get_filezone_locations($filezone_settings); // Methode ohne blog_filezone->

        // Directories for pictures, thumbnails, and small images
        $directories = [
            'pictures' => APPPATH . $locations['pictures']['path'] . '/' . $update_id,
            'thumbs' => APPPATH . $locations['thumbs']['path'] . '/' . $update_id,
            'small' => APPPATH . $locations['small']['path'] . '/' . $update_id,
        ];

        $success = true;

        // Delete files from each directory
        foreach ($directories as $type => $dir) {
            if (is_dir($dir)) {
                $files = scandir($dir);
                foreach ($files as $file) {
                    if (!in_array($file, ['.', '..', '.DS_Store'])) {
                        $file_path = $dir . '/' . $file;
                        if (file_exists($file_path) && !unlink($file_path)) {
                            $success = false; // Mark failure but continue
                        }
                    }
                }
                // Remove directory if empty
                if (count(scandir($dir)) == 2) { // Only '.' and '..' remain
                    if (!rmdir($dir)) {
                        $success = false;
                    }
                }
            }
        }

        // Delete database entries
        $sql = 'DELETE FROM blog_pictures WHERE target_module = :module AND target_module_id = :update_id';
        $params = ['module' => 'blog_posts', 'update_id' => $update_id];
        if (!$this->model->query_bind($sql, $params)) {
            $success = false;
        }

        return $success;
    }

}