<?php

function __get_data(string $file_url, string $delimiter = ';') {
    
    if($file_url === '') {
        return [];
    }

    $lines = file($file_url);
    if(empty($lines)) {
        return ['Error reading data file.'];
    }

    $search_bom_array = array("\xEF\xBB\xBF","\xFE\xFF","\xFF\xFE");
    $lines[0] = str_replace($search_bom_array, '', $lines[0]);

    $data_obj_array = array_map(function($d) use ($delimiter) {
        return str_getcsv($d, $delimiter);
    }, $lines);

    array_walk($data_obj_array, function(&$a) use ($data_obj_array) {
       $a = array_combine($data_obj_array[0], $a);
    });

    array_shift($data_obj_array);

    return $data_obj_array;
} /* END function __get_data */

function __create_pages(
    Kirby\Cms\Page $parent_page, 
    array   $data_array, 
    bool    $update_pages, 
    string  $template = 'default', 
    string  $default_status = 'draft', 
    array   $title_key_array = [], 
    string  $image_page_slug = '', 
    string  $image_field_name = ''
) {
    
    $log_array = [];

    if($parent_page->exists() == false) {
        throw new Exception('Error: The parent page does not exist.');
        return null;
    }
    if(empty($data_array)) {
        throw new Exception('Error: No data available for creating pages.');
        return null;
    }
    if(empty($title_key_array)) {
        throw new Exception('Error: No field names defined for creating page titles.');
        return null;
    }

    /* Get image page */
    $image_page = site()->findPageOrDraft($image_page_slug);

    /* Create/Update subpages */
    foreach($data_array as $data_item_array) {
        
        $title = "";
        $image_array = [];
        $page_content_array = [];
        
        /* Page title */
        foreach($title_key_array as $title_key) {
            if(array_key_exists($title_key, $data_item_array)) {
                if($title !== '') {
                    $title .= ' ';
                }
                $title .= $data_item_array[$title_key];
            }
        }
        
        if($title === '') {
            $log_array[] = "Error: Page title is empty.";
            continue;
        }
        
        $page_content_array['title'] = $title;

        foreach($data_item_array as $key => $value) {
            if($key === $image_field_name && $template !== 'default') { 
                $image_name_array = str_getcsv($value, ',');
                if($image_page != null && $image_page->exists() == true) {
                    if($images = $image_page->images()->find($image_name_array)) {
                        $image_array = $images->toArray();
                        $page_content_array[$key] = $image_array;
                    }
                }
            } else {
                $page_content_array[$key] = $value;
            }
        }

        $target_page_slug = str::slug($title);
        $target_page = $parent_page->children()->findByURI($target_page_slug) ?? $parent_page->drafts()->findByURI($target_page_slug);
        
        /* Check: Page exists? */
        if($target_page) {
            /* Update Page */
            if($update_pages) {
                try {
                    $target_page->update($page_content_array);
                } catch(Exception $error) {
                    $log_array[] = "Error: " . $error->getMessage();        
                }
            }
        } else {
            /* Create Page */
            try {
                $target_page = $parent_page->createChild([
                    'slug' => $target_page_slug,
                    'template' => $template,
                    'content' => $page_content_array
                ]);
                /* changeStatus error with default template */
                if($template !== 'default') {
                    $target_page->changeStatus($default_status);
                }
            } catch(Exception $error) {
                $log_array[] = "Error: " . $error->getMessage();
            }  
        }
	}
		
    /* Log */
    if(!empty($log_array)) {
        $message_string = implode("; ", $log_array);
        throw new Exception($message_string);
        return null;
    }

    return true;
} /* END __create_pages  */
