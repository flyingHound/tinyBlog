<?php
class Templates extends Trongate {

    /**
     * Loads the 'tiny_blog' view with provided data.
     *
     * @param mixed $data Data array to be passed to the view.
     * @return void
     */
    function tiny_blog($data): void {
        $data['additional_includes_top'] = $this->_build_additional_includes($data['additional_includes_top'] ?? []);
        $data['additional_includes_btm'] = $this->_build_additional_includes($data['additional_includes_btm'] ?? []);
        $data['partURL']                 = 'partials/tiny_blog/';
        load('tiny_blog', $data);
    }

    /**
     * Loads the 'public' view with provided data.
     *
     * @param mixed $data Data array to be passed to the view.
     * @return void
     */
    function public($data): void {
        load('public', $data);
    }

    /**
     * Loads the 'error_404' view with provided data.
     *
     * @param mixed $data Data array to be passed to the view.
     * @return void
     */
    function error_404($data): void {
        load('error_404', $data);
    }

    /**
     * Loads the 'tiny_bootstrap' view with provided data and additional includes.
     *
     * @param array $data Data array to be passed to the view.
     * @return void
     */
    function tiny_bootstrap(array $data): void {
        // get user data
        $user_data = $this->_fetch_user_data();
        $data['username'] = $user_data['username'];
        $data['user_level_id'] = $user_data['user_level_id'];
        $data['user_id'] = $user_data['user_id'];

        // sidebar navigation
        $this->module('menus');
        $data['sidebar_nav'] = $this->menus->render_sidebar_nav();

        // Zusätzliche Includes hinzufügen
        $data['additional_includes_top'] = $this->_build_additional_includes($data['additional_includes_top'] ?? []);
        $data['additional_includes_btm'] = $this->_build_additional_includes($data['additional_includes_btm'] ?? []);

        // load view
        load('tiny_bootstrap', $data);
    }

    /**
     * Fetches user data based on the provided token.
     *
     * @param string|null $token The authentication token (optional, will be fetched if not provided).
     * @return array An array containing username, user_level_id, and user_id.
     */
    function _fetch_user_data(?string $token = null): array {
        // Benutzerzugriff sicherstellen
        $this->module('trongate_security');
        $token = $token ?? $this->trongate_security->_make_sure_allowed();

        $this->module('trongate_tokens');
        $user_id = $this->trongate_tokens->_get_user_id($token);

        if ($user_id === false) {
            redirect('trongate_administrators/login');
        }

        // Benutzerdaten aus der Datenbank abrufen
        $sql = "
            SELECT ta.username, tu.user_level_id
            FROM trongate_administrators ta
            JOIN trongate_users tu ON ta.trongate_user_id = tu.id
            WHERE ta.trongate_user_id = :user_id
        ";
        $params = ['user_id' => $user_id];
        $user_data = $this->model->query_bind($sql, $params, 'object');

        // Benutzerdaten zusammenstellen
        if (!empty($user_data)) {
            return [
                'username' => $user_data[0]->username,
                'user_level_id' => $user_data[0]->user_level_id,
                'user_id' => $user_id
            ];
        }

        // Fallback, falls keine Daten gefunden wurden
        return [
            'username' => 'Unknown',
            'user_level_id' => 0,
            'user_id' => 0
        ];
    }

    /**
     * Loads the 'admin' view with provided data and additional includes.
     *
     * @param array $data Data array to be passed to the view.
     * @return void
     */
    function admin(array $data): void {
        $data['additional_includes_top'] = $this->_build_additional_includes($data['additional_includes_top'] ?? []);
        $data['additional_includes_btm'] = $this->_build_additional_includes($data['additional_includes_btm'] ?? []);
        load('admin', $data);
    }

    /**
     * Builds CSS include code for the given file.
     *
     * @param string $file File path for CSS include.
     * @return string CSS include code.
     */
    function _build_css_include_code(string $file): string {
        $code = '<link rel="stylesheet" href="' . $file . '">';
        $code = str_replace('""></script>', '"></script>', $code);
        return $code;
    }

    /**
     * Builds JavaScript include code for the given file.
     *
     * @param string $file File path for JavaScript include.
     * @return string JavaScript include code.
     */
    function _build_js_include_code(string $file): string {
        $code = '<script src="' . $file . '"></script>';
        $code = str_replace('""></script>', '"></script>', $code);
        return $code;
    }

    /**
     * Builds HTML code for additional includes based on file types.
     *
     * @param array $files Array of file names.
     * @return string HTML code for additional includes.
     */
    function _build_additional_includes(array|string|null $files): string {
        if (!is_array($files)) {
            return ''; // Return an empty string if $files is not an array
        }

        $html = '';
        $tabs_str = '    '; // Assuming 4 spaces per tab

        foreach ($files as $index => $file) {
            $file_bits = explode('.', $file);
            $filename_extension = end($file_bits);

            if ($index > 0) {
                $html .= $tabs_str; // Add tabs for lines beyond the first
            }

            $html .= match ($filename_extension) {
                'js' => $this->_build_js_include_code($file), // Add JS separately without a newline
                'css' => $this->_build_css_include_code($file) . PHP_EOL, // Add a newline for CSS files
                default => $file . PHP_EOL, // Add a newline for other file types
            };
        }

        return trim($html) . PHP_EOL;
    }
}