<template>
    <aside>
        <k-field 
            class="import-button"
            :label="button_headline"
            :help="button_help"
        >
            <k-button 
                ref="importButton" 
                theme="positiv"
                icon="import" 
                responsive="true"
                @click="__selectFile"
            >{{ button_label }}</k-button>
            <k-files-dialog ref="filesDialog" @submit="__importData"/>
        </k-field>
        <k-field
            :label="options_headline"
            :help="options_help"
            v-show="!options_disabled"
        >
            <k-input
                class="import-mode-radio"
                type="radio"
                name="radio"
                v-model="value"
                :options="options_text"
                @input="__onInput"
            />
        </k-field>
    </aside>
</template>


<script>
    export default {
        props: {
            button_headline: String,
            button_label: String,
            button_help: String,
            options_headline: String,
            options_text: Object,
            options_help: String,
            subpage_section: String,
            subpage_template: String,
            subpage_status: String,
            title_key_array: Array,
            image_page_slug: String,
            image_field_name: String,
            options_disabled: Boolean,
            default_import_mode: String,
            value: String,
            endpoints: Object
        },
        created() {
            /* Set default import mode if options are hidden */
            if(this.options_disabled) {
                this.value = this.default_import_mode;
            }
        }, 
        methods: {
            __onInput: function(value) {
                this.$emit("input", value);
            },
            __selectFile: function(_event) {
                this.$api
                    .get(this.endpoints.field + '/get/files')
                    .then(files => {
                        this.$refs.filesDialog.open(files, {
                            multiple: false
                        });
                    })
                    .catch(_error => {
                        console.log({ 'error': _error });
                    });
            },
            __importData(_filesDialogResultArray) {
                
                if(!_filesDialogResultArray || !(_filesDialogResultArray instanceof Array) || _filesDialogResultArray.length === 0) { return false; }
                
                /* Import mode: Value of field k-input-field */
                var _importMode = this.value;
                if(!_importMode) {
                    _importMode = this.default_import_mode;
                }

                switch(_importMode) {
                    case 'update':
                        var _updateData = true;
                        break;
                    case 'skip':
                        var _updateData = false;
                        break;
                    default:
                        console.log({ "error": "Import mode undefined." });
                        return false;
                }
                
                var _selectedFileID = _filesDialogResultArray[0].id;
                var _selectedFileURL = _filesDialogResultArray[0].url;
                if(!_selectedFileID || !_selectedFileURL) {
                    console.log({ "error": "File id oder url undefined." });
                    return false;
                }

                if(!this.$route || !this.$route.hasOwnProperty('params')) {
                    console.log({ "error": "$route property »params« undefined." });
                    return false;
                }

                var _pageName = this.$route.params.path;
                if(!_pageName) {
                    console.log({ "error": "Page path undefined." });
                    return false;
                }

                var _subPageTemplate = this.subpage_template;
                if(!_subPageTemplate) {
                    console.log({ "error": "Template for subpages is undefined." });
                    return false;
                }

                var _subpageStatus = (this.subpage_status || "draft");
                var _titleKeyArray = (this.title_key_array || []);
                var _imagePageSlug = (this.image_page_slug || "");
                var _imageFieldName = (this.image_field_name || "");

                var _postObj = { 
                    'id': _selectedFileID,
                    'url': _selectedFileURL,
                    'page': _pageName,
                    'template':_subPageTemplate,
                    'status':_subpageStatus,
                    'title_key_array':_titleKeyArray,
                    'image_page_slug':_imagePageSlug,
                    'image_field_name':_imageFieldName,
                    'update': _updateData
                };

                this.$api
                    .post(this.endpoints.field + '/import/data', _postObj)
                    .then(_response => {
                        if(
                            _response != null &&
                            _response.hasOwnProperty("data") && 
                            _response.data instanceof Array && 
                            _response.data.length !== 0
                        ) {
                            var _pageSection = __getItemByName(this.$root, this.subpage_section); 
                            if(_pageSection != null && _pageSection.hasOwnProperty("reload")) {
                                _pageSection.reload();
                            } else {
                                location.reload();
                            }
                        }
                    })
                    .catch(_error => {
                        console.log({ 'error': _error });
                    });
            }
        }
    };

    function __getItemByName(_parent, _targetName) {
        
        if(!_parent) { return null; }
        if(!_targetName || _targetName.constructor !== String) { return null; }
        
        var _target;
        var _children;
        var _child;
        var i;
        
        if(
            _parent.hasOwnProperty("_props") &&
            _parent._props.hasOwnProperty("name") &&
            _parent._props.name === _targetName
        ) {
            return _parent;
        }
        
        if(!_parent.hasOwnProperty("$children")) { 
            return null; 
        }
        
        _children = _parent.$children;
        if(!_children || !(_children instanceof Array) || _children.length === 0) {
            return null;
        }

        for(i=0; i<_children.length; i+=1) {
            
            _child = _children[i];
            if(!_child) {
                continue;
            }
    
            if(
                _child.hasOwnProperty("_props") &&
                _child._props.hasOwnProperty("name") &&
                _child._props.name === _targetName
            ) {
                return _child;
            }
            
            _target = __getItemByName(_child, _targetName);
            if(_target != null) {
                return _target;
            }
        }

        return null;
    } /* END function __getItemByName  */ 
</script>


<style lang="scss">
    
    $dark-background: #fff;
    
    .import-button {
        margin-bottom: 2rem;
    }
    .import-button .k-button-text {
        font-size: 1rem;
        color: #000000;
        margin-bottom: 1rem;
    }
    .import-mode-radio ul li {
        display: inline-block;
        padding-right: 1.5rem;
        padding-left: 1.5rem;
    }
</style>
