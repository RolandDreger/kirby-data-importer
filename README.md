# Kirby Data Importer (BETA, Version 1.0)
Kirby 3 plugin for data import and generating subpages

# Overview

https://vimeo.com/360116607

This plugin can read CSV files and create individual subpages from the data they contain. 


# 1 Installation

Download and copy this repository to /site/plugins/data-importer

Or you can install it with composer: composer require rd/data-importer


# 2 Usage
## 2.1 Blueprint
### Page with import data field

    title: Import Data Field Page

    columns:
        main:
            width: 2/3
            sections:
                #list of subpages
                card_pages:
                    type: pages
                    headline: Cards
                    empty: No cards yet
                    layout: list
                    status: all
        sidebar:
            width: 1/3
            sections:
                #import data field
                import_data:
                    type: fields
                    fields:
                        import_data:
                            type: import_data
                            button_headline: "Data Import"
                            button_label: "Select data file ..."
                            button_help: ""
                            options_disabled: false
                            options_headline: "Options"
                            options_help: ""
                #list of data files
                data_files:
                    type: files
                    headline: "Data files"
                    layout: list
                    template: data_files

### Subpages (records)
The field names in the blueprint correspond to the column labels in the CSV table.

    columns:
        # Portrait
        image:
            width: 1/4
            sections:
                image:
                    type: fields
                    fields:
                        portrait:
                            type: files
                            label: Portrait
                            layout: cards
                            size: tiny
                            query: site.find('image-files').images
                            max: 1
                            image:
                                cover: true
                                ratio: 1/1
                                multiple: false
        # Data input
        data:
            width: 3/4
            sections:
                content:
                    type: fields
                    fields:
                        first_name:
                            type: text
                            label: First Name
                            width: 1/2
                        last_name:
                            type: text
                            label: Last Name
                            width: 1/2
                        jobtitle:
                            type: text
                            label: Job Title
                            width: 1/1
                        company:
                            type: text
                            label: Company
                            width: 1/1
                        street:
                            type: text
                            label: Street
                            width: 1/1
                        postcode:
                            type: text
                            label: ZIP
                            width: 1/3
                        state:
                            type: text
                            label: State
                            width: 2/3
                        phone:
                            type: tel
                            label: Phone
                            width: 1/2
                        fax:
                            type: tel
                            label: Fax
                            width: 1/2
                        email:
                            type: text
                            label: Email
                            width: 1/1
                            placeholder: ""
                            icon: email
                        website:
                            type: text
                            label: Website
                            before: https://
                            placeholder: ""
                            icon: url  
                            
## 2.2 Options

### Default import mode: String »update« or »skip« 
If options field is hidden or value is unset.

    'default_import_mode' => 'skip' 


### Page section: String
Field name in blueprint.

    'page_section_name' => 'subpages'


### Template for generated subpages: String

    'page_template' => 'import-data-subpage'


### Default status for generated subpages: String »listed«, »unlisted« or »draft« 

    'page_status' => 'listed' 

### Field separator in CSV file: String
Image name separator in CSV file is »,« (without space).
Do NOT use »,« here!

    'delimiter' => ";" 


### Field names for creating subpage title: Array of strings

    'title_key_array' => ['first_name','last_name'], 


### Slug of image page (optional): String

    'image_page_slug' => 'image-files',


### Name of image field (optional): String 

    'image_field_name' => 'portrait'  


# 3 Credits

Based on Kirby CSV Handler (Kirby 2) https://github.com/texnixe/kirby-csv-handler  with some inspirations from here https://github.com/OblikStudio/kirby-link-field and other great plugins. Thanks to all!


# 4 Notice

This plugin is provided »as is«. Use it at your own risk. Please test the plugin carefully before using it in your production environment.

Feedback is welcome.


# 5 License

http://www.opensource.org/licenses/mit-license.php


# 6 Authors

Roland Dreger, www.rolanddreger.net


PayPal.me Link 

https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=roland%2edreger%40a1%2enet&lc=AT&item_name=Roland%20Dreger%20%2f%20Donation%20for%20script%20development%20Kirby-Data-Importer&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
