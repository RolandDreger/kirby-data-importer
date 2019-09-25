<?php

require_once __DIR__ . '/lib/functions.php';

Kirby::plugin('rd/data-importer', [
    'options' => [
        /* Default import mode: String / Value: »update« or »skip« */ 
        /* If options field is hidden or value is unset */
        'default_import_mode' => 'skip',

        /* Field separator in CSV file: String */
        /* Image name separator in CSV file is »,« (without space) */
        /* Do NOT use »,« here! */
        'delimiter' => ";"
    ],
    'fields' => [
        'import_data' => [
            'mixins' => ['filepicker'],
            'props' => [
                'button_headline' => function(string $button_headline = null) {
                    if($button_headline == null) {
                        $default_button_headline = ["de"=>"Daten importieren", "en"=>"Data import"];
                        return I18n::translate($default_button_headline);
                    }
                    return I18n::translate($button_headline, $button_headline);
                },
                'button_label' => function(string $button_label = null) {
                    if($button_label == NULL) {
                        $default_button_label = ["de"=>"Datei auswählen...", "en"=>"Select data file ..."];
                        return I18n::translate($default_button_label);
                    }
                    return I18n::translate($button_label, $button_label);
                },
                'button_help' => function(string $button_help = null) {
                    if($button_help == NULL) {
                        return '';
                    }
                    return I18n::translate($button_help, $button_help);
                },
                'options_headline' => function(string $options_headline = null) {
                    if($options_headline == null) {
                        $default_options_headline = ["de"=>"Optionen", "en"=>"Options"];
                        return I18n::translate($default_options_headline);
                    }
                    return I18n::translate($options_headline, $options_headline);
                },
                'options_help' => function(string $options_help = null) {
                    if($options_help == null) {
                        return '';
                    }
                    return I18n::translate($options_help, $options_help);
                },
                'subpage_section' => function(string $subpage_section = '') {
                    return $subpage_section;
                },
                'subpage_template' => function(string $subpage_template = 'default') {
                    return $subpage_template;
                },
                'subpage_status' => function(string $subpage_status = 'draft') {
                    return $subpage_status;
                },
                'title_key_array' => function(array $title_key_array) {
                    return $title_key_array;
                },
                'image_page_slug' => function(string $image_page_slug = '') {
                    return $image_page_slug;
                },
                'image_field_name' => function(string $image_field_name = '') {
                    return $image_field_name;
                },
                'options_disabled' => function(bool $options_disabled = false) {
                    return $options_disabled;
                },
                'default_import_mode' => function(string $default_import_mode = null) {
                    if($default_import_mode == null) {
                        $default_import_mode = option('rd.data-importer.default_import_mode');
                    }
                    if($default_import_mode === 'update' || $default_import_mode === 'skip') {
                        return $default_import_mode;
                    } else {
                        throw new Exception('This option mode is not allowed: ' . $default_import_mode . '. Allowed values: »update« or »skip«');
                    }
                }
            ],
            'computed' => [
                'options_text' => function() {
                    $option_update_text = I18n::translate(["de"=>"Aktualisieren", "en"=>"Update"]);
                    $option_skip_text = I18n::translate(["de"=>"Vorhandene überspringen", "en"=>"Skip existing records"]);
                    $data = '[
                        { "value": "update", "text": "' . $option_update_text . '" },
                        { "value": "skip", "text": "' . $option_skip_text . '" }
                    ]';
                    return json_decode($data);
                }
            ],
            /* Field API */
            'api' => function () {
                return [
                    [
                        'pattern' => '/get/files',
                        'method' => 'GET',
                        'action' => function() {
                            return $this->field()->filepicker([
                                'query' => 'page.files',
                            ]);
                        }
                    ],
                    [
                        'pattern' => '/import/data',
                        'method' => 'POST',
                        'action' => function() {
                            
                            $file_url = get('url');
                            $page_name = get('page');
                            $template = get('template');
                            $status = get('status');
                            $title_key_array = get('title_key_array');
                            $image_page_slug = get('image_page_slug');
                            $image_field_name = get('image_field_name');
                            $update = get('update');
                            
                            /* Get parent page */
                            try {
                                $parent_page = $this->page($page_name);
                            } catch(Exception $error) {
                                throw new Exception("Error: " . $error->getMessage());
                                return ['data' => []];
                            }
                            
                            /* Read file */
                            $delimiter = option('rd.data-importer.delimiter');
                            $data_array = __get_data($file_url, $delimiter);
                            if(empty($data_array)) {
                                throw new Exception("Error: Selected file contains no data.");
                                return ['data' => []];
                            }
                            
                            /* Create subpages */
                            $create_pages_result = __create_pages(
                                $parent_page, 
                                $data_array, 
                                $update, 
                                $template, 
                                $status, 
                                $title_key_array,
                                $image_page_slug,
                                $image_field_name
                            );
                            if($create_pages_result == null) {
                                return ['data' => []];
                            }

                            return ['data' => $data_array];
                        }
                    ],
                ];
            }
        ]
    ]
]);