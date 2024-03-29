# Kirby Data Importer (BETA, Version 1.0)

Kirby 3 plugin for data import and generating subpages

**Note:** Unfortunately this plugin no longer works with the current version of Kirby. I hope to find time to update it soon.

## Preview (on Vimeo)

[![Vimeo video to Kirby Plugin Data Importer](https://user-images.githubusercontent.com/19747449/65554134-f98d8c80-df28-11e9-8140-23114fd88a1d.png)](https://vimeo.com/360116607)


This plugin imorts data from a CSV file and creates subpages of it. 


---


## 1. Installation

Download and copy this repository to **/site/plugins/data-importer**

Or you can install it with composer: **composer require rd/data-importer**



## 2. Usage

### 2.1 Insert field

Blueprint of preferred main page

```yaml
fields:
  import_data:
    type: import_data
```

### 2.2 Define field labels

```yaml
    button_headline: "Data Import"
    button_label: "Select data file ..."
    button_help: ""
    options_headline: "Options"
    options_help: ""
```

### 2.3 Set visibility of options field

```yaml
    options_disabled: false
```

### 2.4 Define page section 

For reload page section after generate subpages.

```yaml
    subpage_section: "pages"
```

### 2.5 Define template for generated subpages

Name of php file in /site/templates

```yaml
    subpage_template: "subpage"
```

### 2.6 Set status for generated subpages 

Values: »listed«, »unlisted« or »draft«

```yaml
    subpage_status: "unlisted"
```

### 2.7 Define field names for subpage title 

Array of strings: Column labels in CSV table (see 3.2 Data file)

```yaml
    title_key_array: ["first_name","last_name"]
```

### 2.8 Slug of image page (optional)

The images can be uploaded to a separate page and fetched from there.

```yaml
    image_page_slug: "image-files"
```

### 2.9 Name of image field (optional) 

Field name in blueprint of main page

```yaml
    image_field_name: "portrait"
```



## 3. Examples
### 3.1 Blueprint Main Page
Page with import data field

#### fieldpage.yml

```yaml
title: Field page
preset: page
fields:
  import_data:
    type: import_data
    button_headline: "Data Import"
    button_label: "Select data file ..."
    button_help: ""
    options_disabled: false
    options_headline: "Options"
    options_help: ""
    #Page section with generated subpages (for reload)
    subpage_section: "pages"
    # Template for generated subpages
    subpage_template: "subpage"
    # Status for subpages (Values: »listed«, »unlisted« or »draft«)
    subpage_status: "unlisted"
    # Field names for subpage title (Array of strings: Column labels in CSV table)
    title_key_array: ["first_name","last_name"]
    # Slug of image page (optional)
    image_page_slug: "image-files"
    # Name of image field (optional) 
    image_field_name: "portrait"
```

### 3.2 Data file
The **field names** in the blueprint for subpages correspond to the **column labels** in the CSV table. If there is more than one image, the image file names are separated by commas (without spaces).

#### data_file.csv

<table>
 <tr>
  <td>first_name</td>
  <td>last_name</td>
  <td>jobtitle</td>
  <td>company</td>
  <td>street</td>
  <td>postcode</td>
  <td>state</td>
  <td>phone</td>
  <td>fax</td>
  <td>email</td>
  <td>website</td>
  <td>portrait</td>
  <td></td>
 </tr>
 <tr>
  <td>Michael</td>
  <td>Collins</td>
  <td>Command Module Pilot</td>
  <td>NASA Headquarters</td>
  <td>300 E. Street SW, Suite 5R30</td>
  <td>DC 20546</td>
  <td>Washington</td>
  <td>(202) 358-0001</td>
  <td>(202) 358-4338</td>
  <td>michael.colli ns@nasa.gov</td>
  <td>https://www.nasa.gov</td>
  <td>michael_collins.jpg</td>
 </tr>
 <tr>
  <td>Edwin E.</td>
  <td>Aldrin</td>
  <td>Lunar Module Pilot</td>
  <td>NASA Headquarters</td>
  <td>300 E. Street SW, Suite 5R30</td>
  <td>DC 20546</td>
  <td>Washington</td>
  <td>(202) 358-0001</td>
  <td>(202) 358-4338</td>
  <td>edwin.e.aldrin@nasa.gov</td>
  <td>https://www.nasa.gov</td>
  <td>buzz_aldrin.jpg</td>
 </tr>
 <tr>
  <td>Neil A.</td>
  <td>Armstrong</td>
  <td>Commander</td>
  <td>NASA Headquarters</td>
  <td>300 E. Street SW, Suite 5R30</td>
  <td>DC 20546</td>
  <td>Washington</td>
  <td>(202) 358-0001</td>
  <td>(202) 358-4338</td>
  <td>neil.a.armstrong@nasa.gov</td>
  <td>https://www.nasa.gov</td>
  <td>neil_armstrong.jpg</td>
 </tr>
</table>


### 3.3 Blueprint Subpages
Pages that are populated with data from the CSV file.

#### subpage.yml

```yaml
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
```



## 4 Options
You can find these settings in the file **index.php**

### 4.1 Default import mode 
Values: »update« or »skip«. Setting if options field is hidden or value is unset.

    'default_import_mode' => 'skip' 


### 4.2 Field separator in CSV file
Image name separator in CSV file is »,« (without space).
Do NOT use »,« here!

    'delimiter' => ";"



## 5. Credits

Based on [Kirby CSV Handler](https://github.com/texnixe/kirby-csv-handler) (Kirby 2) with some inspirations from Plugin [Kirby Link Field](https://github.com/OblikStudio/kirby-link-field) and other great plugins. Thanks to all!



## 6. Notice

This plugin is provided »as is«. Use it at your own risk. Please test the plugin carefully before using it in your production environment.

Feedback is welcome.



## 7. License

[MIT](http://www.opensource.org/licenses/mit-license.php)



## 8. Authors

Roland Dreger, www.rolanddreger.net


[PayPal](https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=roland%2edreger%40a1%2enet&lc=AT&item_name=Roland%20Dreger%20%2f%20Donation%20for%20script%20development%20Kirby-Data-Importer&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted) Link 


