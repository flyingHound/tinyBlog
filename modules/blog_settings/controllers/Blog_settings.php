<?php
class Blog_settings extends Trongate {
    private $admin_template = 'admin'; //'tiny_bootstrap';
    private $picture_settings_path = APPPATH . 'modules/blog_settings/assets/settings_files/picture_settings.json';

    /**
     * View Picture Settings
     */
    public function index(): void {
        $token = $this->_make_sure_allowed();

        // Lade die Einstellungen
        $picture_settings = $this->load_settings_from_file();
        
        // Wenn keine Einstellungen geladen wurden, hole sie aus dem Modul
        if (empty($picture_settings)) {
            $this->module('blog_posts');
            $picture_settings = $this->blog_posts->_init_picture_settings();
        }

        $data['picture_settings'] = $picture_settings;
        //$data['token'] = $token; // Für CSRF-Schutz im Formular
        
        // Lade das Template
        $data['headline']  = 'Blog Picture Settings';
        $data['form_location'] = BASE_URL.'blog_settings/save_settings';
        $data['view_file'] = 'picture_settings';
        $this->template($this->admin_template, $data);
    }

    /**
     * Load settings from a JSON file
     *
     * @return array The settings array or empty array if file doesn't exist or is invalid
     */
    private function load_settings_from_file(): array {
        $file_path = $this->picture_settings_path;
        
        if (file_exists($file_path)) {
            $json_content = file_get_contents($file_path);
            $settings = json_decode($json_content, true);
            
            if (json_last_error() === JSON_ERROR_NONE && is_array($settings)) {
                return $settings;
            }
        }
        
        return []; // Rückgabe leerer Array, wenn Datei fehlt oder ungültig
    }

    /**
     * Save settings to a JSON file
     */
    public function save_settings(): void {
        $this->_make_sure_allowed();
        
        // get data from POST-Request 
        $post_data = $_POST;
        
        // Checkboxen als Booleans umwandeln (da sie als "on" oder nicht gesetzt kommen)
        $post_data['upload_to_module'] = isset($post_data['upload_to_module']) ? 1 : 0;
        $post_data['make_rand_name'] = isset($post_data['make_rand_name']) ? 1 : 0;

        // Pfad zur JSON-Datei
        $file_path = $this->picture_settings_path;
        $dir_path = dirname($file_path);

        // Verzeichnis erstellen, falls es nicht existiert
        if (!is_dir($dir_path)) {
            mkdir($dir_path, 0777, true);
        }

        // JSON-Datei schreiben
        $json_content = json_encode($post_data, JSON_PRETTY_PRINT);
        if (file_put_contents($file_path, $json_content) === false) {
            $flash_msg = 'Failed to save settings to file.';
        } else {
            $flash_msg = 'Settings saved successfully!';
        }

        // Zurück zur Settings-Seite
        set_flashdata($flash_msg);
        redirect('blog_settings/index');
    }

    /**
     * Ensures that the current user is allowed to access the protected resource.
     * by default, $admin_template users (i.e., users with user_level_id === 1) are allowed
     *
     * @return string|false The security token if the user is authorized, or false otherwise
     */
    private function _make_sure_allowed(): string|false {
        $this->module('trongate_security');
        return $this->trongate_security->_make_sure_allowed();
    }
}