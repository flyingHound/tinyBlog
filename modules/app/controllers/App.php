<?php
/**
 * 
 */
class App extends Trongate {
    private $user_level_title   = 'admin';
    private $user_level_id      = 1;
    private $page_template      = 'public';
    private $admin_template     = 'admin'; // admin

    /**
     * Index View
     * 
     */
    function index() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        # Current_url
        $data['current'] = current_url();

        # Module Menu
        $data['mod_nav'] = $this->get_mod_nav();

        # Load the template
        $data['headline']       = 'App Controller';
        $data['view_module']    = 'app';
        $data['view_file']      = 'index_view';
        $this->template($this->admin_template, $data);
    }

    /**
     * Show page_1
     * 
     */
    function page_1() {
        # Module Menu
        $data['mod_nav'] = $this->get_mod_nav();

        # Load the template
        $data['headline']       = 'Page 1';
        $data['view_module']    = 'app';
        $data['view_file']      = 'page_1';
        $this->template($this->admin_template, $data);
    }

    /**
     * Show page_2
     * 
     */
    function page_2() {
        # Module Menu
        $data['mod_nav'] = $this->get_mod_nav();

        # Load the template
        $data['headline']       = 'Page 2';
        $data['view_module']    = 'app';
        $data['view_file']      = 'page_2';
        $this->template($this->admin_template, $data);
    }

    /**
     * Show page_3
     * 
     */
    function page_3() {
        # Module Menu
        $data['mod_nav'] = $this->get_mod_nav();

        # Load the template
        $data['headline']       = 'Page 3';
        $data['view_module']    = 'app';
        $data['view_file']      = 'page_3';
        $this->template($this->admin_template, $data);
    }

    /**
     * Show page_4
     * 
     */
    function page_4() {
        # Module Menu
        $data['mod_nav'] = $this->get_mod_nav();

        # Load the template
        $data['headline']       = 'Page 4';
        $data['view_module']    = 'app';
        $data['view_file']      = 'page_4';
        $this->template($this->admin_template, $data);
    }

    /**
     * Outputs HTML Navigation for this Module
     *
     * @return string HTML string of the navigation menu
     */
    public function get_mod_nav(): string {
        $app_module = 'app';
        $current_url = current_url();
        $base_module_url = BASE_URL . $app_module; // Basis-URL ohne /index

        // Menüpunkte definieren
        $menu_items = [
            ['title' => 'Dashboard',    'url_string' => BASE_URL . $app_module . '/index'],
            ['title' => 'Page 1',       'url_string' => BASE_URL . $app_module . '/page_1'],
            ['title' => 'Page 2',       'url_string' => BASE_URL . $app_module . '/page_2'],
            ['title' => 'Page 3',       'url_string' => BASE_URL . $app_module . '/page_3'],
            ['title' => 'Page 4',       'url_string' => BASE_URL . $app_module . '/page_4'],
            ['title' => 'Font Awesome', 'url_string' => BASE_URL . $app_module . '-fonts']
        ];

        // Daten für den View vorbereiten
        $data = [
            'menu_items' => $menu_items,
            'current_url' => $current_url,
            'base_module_url' => $base_module_url // Für Vergleich ohne /index
        ];

        // Prüfen, ob Menüpunkte vorhanden sind, und View rendern
        if (!empty($data['menu_items'])) {
            $html = $this->view('_html_mod_nav', $data, true);
        } else {
            $html = '';
        }

        return $html;
    }

    /**
     * Show index_view of SubModule fonts
     * 
     */
    function app_fonts() {
        # Load the sub module
        $this->module('app-fonts');
        $this->fonts->index();
    }
}