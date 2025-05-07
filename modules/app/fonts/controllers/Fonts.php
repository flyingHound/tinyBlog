<?php
class Fonts extends Trongate {

	function __construct() {
		parent::__construct();
		$this->parent_module 	= 'app';
		$this->child_module 	= 'fonts';
	}

	/**
     * Index for Fonts - Web Resources
     */
    function index() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

		# get icons grid
		$data['output'] = $this->show_fa_icons();

		# Module Menu from Parent
        $this->module($this->parent_module);
        $data['mod_nav'] = $this->{$this->parent_module}->get_mod_nav();

		# Load the template
		$data['headline'] = 'Fontawesome Icons';
		$data['view_module'] = $this->parent_module . '/' . $this->child_module;
		$data['view_file'] = 'fontawesome_view';
		$this->template('admin', $data);
	}

	/**
	 * Show Font Awesome Icons
	 *
	 * This function retrieves Font Awesome icon classes and 
	 * their corresponding Unicode characters from the CSS file 'font-awesome.css'.
	 * It then generates HTML markup to display each icon along with its name and reference class.
	 *
	 * @return string HTML markup for displaying Font Awesome icons in a grid layout
	 */
	function show_fa_icons(): string {
        $css_file_path = APPPATH . 'public/css/fontawesome.css';
        /* Vielleicht auch so in Zukunft: 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'; */

        if (!file_exists($css_file_path)) {
            return '<p class="warning text-center">Font Awesome CSS file missing at: <br>\''.BASE_URL.'public/css/fontawesome.css\'</p>';
        }

        $css_content = file_get_contents($css_file_path);
        preg_match_all('/\.fa-(.*?):before\s*{\s*content:\s*"\\\(.*?)";\s*}/', $css_content, $matches, PREG_SET_ORDER);

        // Kategorien simulieren (da sie nicht im CSS stehen, rudimentäre Logik basierend auf Namen)
        $icons_by_category = [
            'Communication' => [],
            'Navigation' => [],
            'Media' => [],
            'Objects' => [],
            'Other' => []
        ];

        foreach ($matches as $match) {
            $class_name = $match[1];
            $icon_name = ucwords(str_replace('-', ' ', $class_name));
            $icon_html = '<i class="fa fa-' . $class_name . '"></i>';

            // Rudimentäre Kategorisierung basierend auf Schlüsselwörtern
            if (stripos($class_name, 'phone') !== false || stripos($class_name, 'envelope') !== false || stripos($class_name, 'comment') !== false) {
                $category = 'Communication';
            } elseif (stripos($class_name, 'arrow') !== false || stripos($class_name, 'map') !== false || stripos($class_name, 'compass') !== false) {
                $category = 'Navigation';
            } elseif (stripos($class_name, 'play') !== false || stripos($class_name, 'video') !== false || stripos($class_name, 'music') !== false) {
                $category = 'Media';
            } elseif (stripos($class_name, 'book') !== false || stripos($class_name, 'car') !== false || stripos($class_name, 'clock') !== false) {
                $category = 'Objects';
            } else {
                $category = 'Other';
            }

            $icons_by_category[$category][] = [
                'class_name' => $class_name,
                'icon_name' => $icon_name,
                'icon_html' => $icon_html
            ];
        }

        // HTML generieren
        $html = '<div class="filter-container">';
        $html .= '<input type="text" id="icon-search" placeholder="Search icons..." onkeyup="filterIcons()">';
        $html .= '</div>';

        $html .= '<div class="icon-grid-container">';
        foreach ($icons_by_category as $category => $icons) {
            if (empty($icons)) continue;

            $html .= '<h3 class="category-title">' . $category . ' (' . count($icons) . ')</h3>';
            $html .= '<div class="icon-grid" data-category="' . strtolower($category) . '">';
            foreach ($icons as $icon) {
                $html .= '<div class="icon-container">';
                $html .= '<div class="icon-name"><h6>' . $icon['icon_name'] . '</h6></div>';
                $html .= '<div class="icon neon-glow">' . $icon['icon_html'] . '</div>';
                $html .= '<div class="icon-ref" data-icon-html="' . htmlspecialchars($icon['icon_html']) . '"><small>' . htmlspecialchars($icon['icon_html']) . '</small></div>';
                $html .= '</div>';
            }
            $html .= '</div>';
        }
        $html .= '</div>';

        return $html;
    }

	/**
	 * Desctruct Parent-Child Relation
	 * mandatory for SubModules inside SuperModules
	 * 
	 */
	function __destruct() {
		$this->parent_module = '';
		$this->child_module = '';
	}
}