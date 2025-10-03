let EA_SEARCH_INIT = false;

function urlParam(name, href) {
    let results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(href || window.location.href);
    if (results == null) {
        return null;
    } else {
        return decodeURIComponent(results[1]);
    }
}

document.getElementById("question").addEventListener("focus", function () {
    if (!EA_SEARCH_INIT) {
        document.getElementById("question").setAttribute("disabled", "");
        setTimeout(function () {
            document.getElementById("question").removeAttribute("disabled");
            document.getElementById("question").setAttribute("autofocus", "");
            document.getElementById("question").focus();
        }, 300);
        EA_SEARCH_INIT = true;
        require(['jquery', 'ea-sayt', 'ea-store', 'touch-punch'],
            function ($, ea_sayt, ea_store) {
                $(function () {
                    // parse parameters (if any)
                    let storefields = '';
                    let requestData = urlParam('RequestData');
                    let catPath = AMPLIFY_SAYT_CAT_PATH;
                    let question = urlParam('q');
                    // create Studio Store options
                    let studioStoreOptions = {
                        dct: '{{ config('amplify.sayt.dictionary.dictionary') }}',
                        server: '{{config('amplify.sayt.dictionary.protocol')}}://{{config('amplify.sayt.dictionary.host')}}',
                        fields: {
                            id: '{{ config('amplify.sayt.sayt_product_id', 'Product_Id') }}',
                            image: '{{ config('amplify.sayt.sayt_product_image', 'Product_Image') }}',
                            name: '{{ config('amplify.sayt.sayt_product_name', 'Product_Name') }}',
                            price: '{{ config('amplify.sayt.sayt_product_price', 'Price') }}',
                            desc: '{{ config('amplify.sayt.sayt_product_description', 'Short_Description') }}',
                            ptype: '{{ config('amplify.sayt.sayt_product_type', 'Type_Id') }}',
                            sizes: '{{ config('amplify.sayt.sayt_product_sizes', 'Sku_Sizes') }}'
                        },
                        colorAttribute: 'Available Colors',
                        ratingsAttribute: 'Product Rating',
                        overlayFields: true,
                        facetsExpanded: 4,
                        shopUrl: '{{ $shopUrl }}',
                    }
                    let fields = '';
                    if (storefields) {
                        fields = $.parseJSON(storefields);
                        // map generic field names to actual names from the dictionary
                        if (fields.id) {
                            studioStoreOptions.fields.id = fields.id.replace(/\W/g, '_');
                        }
                        if (fields.name) {
                            studioStoreOptions.fields.name = fields.name.replace(/\W/g, '_');
                        }
                        if (fields.image) {
                            studioStoreOptions.fields.image = fields.image.replace(/\W/g, '_');
                        }
                        if (fields.price) {
                            studioStoreOptions.fields.price = fields.price.replace(/\W/g, '_');
                        }
                        if (fields.desc) {
                            studioStoreOptions.fields.desc = fields.desc.replace(/\W/g, '_');
                        }
                        if (fields.url) {
                            studioStoreOptions.fields.url = fields.url.replace(/\W/g, '_');
                        }
                        if (fields.rating) {
                            studioStoreOptions.fields.rating = fields.rating.replace(/\W/g, '_');
                        }
                        if (fields.mfr) {
                            studioStoreOptions.fields.mfr = fields.mfr.replace(/\W/g, '_');
                        }
                        if (fields.colorCOunt) {
                            studioStoreOptions.fields.colorCount = fields.colorCount.replace(/\W/g, '_');
                        }
                        if (fields.attrColor) {
                            studioStoreOptions.colorAttribute = fields.attrColor;
                        }
                        if (fields.attrRating) {
                            studioStoreOptions.ratingAttribute = fields.attrRating;
                        }
                        if (fields.attrSize) {
                            studioStoreOptions.sizeAttribute = fields.attrSize;
                        }
                        studioStoreOptions.menu = [];
                        if (fields.menu1Label && fields.menu1Value) {
                            studioStoreOptions.menu.push({
                                label: fields.menu1Label,
                                val: fields.menu1Value
                            });
                        }
                        if (fields.menu2Label && fields.menu2Value) {
                            studioStoreOptions.menu.push({
                                label: fields.menu2Label,
                                val: fields.menu2Value
                            });
                        }
                        if (fields.menu3Label && fields.menu3Value) {
                            studioStoreOptions.menu.push({
                                label: fields.menu3Label,
                                val: fields.menu3Value
                            });
                        }
                        if (fields.facetsExpanded) {
                            studioStoreOptions.facetsExpanded = fields.facetsExpanded;
                        }
                    }

                    // initializing SAYT for use in StudioStore
                    let saytOptions = {
                        delay: 100,
                        dict: '{{ config('amplify.sayt.dictionary.dictionary') }}',
                        server: '{{config('amplify.sayt.dictionary.protocol')}}://{{config('amplify.sayt.dictionary.host')}}',
                        serverSearch: '{{config('amplify.sayt.dictionary.protocol')}}://{{config('amplify.sayt.dictionary.host')}}',
                        prompt: 'Search by EasyAsk',
                        submitFctn: function (type, val, elt) {
                            if (type === 'nav') {
                                window.location = '{{ $shopUrl }}/' + AMPLIFY_SAYT_CAT_PATH + '/' + val;
                            } else {
                                // 'search' is the id of the button
                                $('#search').click();
                            }
                        },
                        leftWidth: '60%',
                        horizAlign: 'right',
                        relativeToInput: true,
                        yOffset: -5,
                        search: {
                            size: 5
                        },
                        products: {
                            size: 3,
                            value: function (item, field) {
                                if (field === 'URL_Path') {
                                    return "{{ $shopUrl }}/" + item[field] + '/' + AMPLIFY_SAYT_CAT_PATH;
                                }
                                return item[field];
                            },

                            fields: $.extend({}, studioStoreOptions.fields,
                                studioStoreOptions.fields.image ? {thumbnail: studioStoreOptions.fields.image} : null,
                                {link: 'ea_storedetails'}), // dummy name to support popup details
                            sizes: {
                                description: 115
                            }
                        },
                        navigation: {
                            size: 3,
                            sections: [
                                {
                                    size: 2,
                                    title: 'Category',
                                    type: 'category'
                                }, {
                                    size: 3,
                                    title: 'Color',
                                    type: 'COLOR'
                                }, {
                                    size: 3,
                                    title: 'Size',
                                    type: 'SIZE'
                                }
                            ]
                        },
                        template: "./templates/leftprod.hbs"
                    };

                    // within StudioStore, generic field names may be mapped to actual dictionary names.
                    // This updates SAYT fields to those names and adjusts select values that reference those fields
                    if (fields) {
                        if (fields.id) {
                            saytOptions.products.fields.id = fields.id.replace(/\W/g, '_');
                        }
                        if (fields.name) {
                            saytOptions.products.fields.name = fields.name.replace(/\W/g, '_');
                            // fix the sort by selectors
                            $('select.ea-select-sort-by option[value="Product Name,t"]').attr('value', fields.name + ',t');
                            $('select.ea-select-sort-by option[value="Product Name,f"]').attr('value', fields.name + ',f');
                        }
                        if (fields.image) {
                            saytOptions.products.fields.thumbnail = fields.image.replace(/\W/g, '_');
                        }
                        if (fields.price) {
                            saytOptions.products.fields.price = fields.price.replace(/\W/g, '_');
                            $('select.ea-select-sort-by option[value="Price,t"]').attr('value', fields.price + ',t');
                            $('select.ea-select-sort-by option[value="Price,f"]').attr('value', fields.price + ',f');
                        }
                        if (fields.desc) {
                            saytOptions.products.fields.description = fields.desc.replace(/\W/g, '_');
                        }
                        if (fields.url) {
                            saytOptions.products.fields.link = fields.url.replace(/\W/g, '_');
                        }
                        if (fields.rating) {
                            saytOptions.products.fields.rating = fields.rating.replace(/\W/g, '_');
                        }
                        if (fields.colorCount) {
                            saytOptions.products.fields.colorCount = fields.colorCount.replace(/\W/g, '_');
                        }

                        if (fields.mfr) {
                            $('select.ea-select-sort-by option[value="Manufacturer,t"]').attr('value', fields.mfr + ',t');
                            $('select.ea-select-sort-by option[value="Manufacturer,f"]').attr('value', fields.mfr + ',f');
                        }
                    }
                    let studioStore = new ea_store();


                    // override SAYT function getResults to wrap detail click for store demo.  This involves retaining
                    // the items that were used by SAYT (retrieved when the ea_storedetails pseudo field was retrieved) and
                    // after all items have been processed then installing a click handler to retrieve the stored item and
                    // use the store demo detail page display

                    // applications that store a clickable href in the product area can eliminate this section

                    if (saytOptions.products.fields.link === 'ea_storedetails') {
                        // need to save the items in results to retrieve for default detail processing
                        let studioStoreItemCache = [];  // for caching items
                        saytOptions.products.value = function (item, fld) {
                            if (fld === 'ea_storedetails') {
                                studioStoreItemCache.push(item);
                            }
                            return item[fld];
                        };

                        function saytShowDetails(id, products, path) {
                            let fields = products.fields;
                            let value = products.value
                            let idField = fields.id;
                            if (idField) {
                                let item = '';
                                for (let i = 0; i < studioStoreItemCache.length; i++) {
                                    if (studioStoreItemCache[i][idField] == id) {
                                        item = studioStoreItemCache[i];
                                        break;
                                    }
                                }
                                if (item) {
                                    studioStore.showDetails(id, item, path);
                                }
                            }
                        }

                        let superGetResults = ea_sayt.prototype.getResults;
                        ea_sayt.prototype.getResults = function (curOffset) {
                            studioStoreItemCache = []; // clear cache
                            let self = this;
                            $.when(superGetResults.call(self, curOffset)).done(function () {
                                $('.ea-sug-product').each(function () {
                                    let id = $(this).attr('ea-prod-id');
                                    if (id) {
                                        $(this).find('a[href="#"]').click(function (e) {
                                            self.close(true);
                                            saytShowDetails(id, self.config.products, self.path);
                                            e.preventDefault();
                                            return false;
                                        });
                                    }
                                });
                            });
                        }
                    }

                    // this is an example of processing that needs to be done to compute information needed for SAYT, e.g., additional
                    // properties that should be logged with SAYT calls.  This sample simply retrieves information about the requesting
                    // IP, and geographic information location.  It is offered as a sample and should not be used without getting
                    // explicting acceptance by the caller (especially in Europe).

                    // The system waits until the information is available, then continues.  If no processing is needed, this
                    // code is not needed.

                    //		let blocker = $.Deferred();
                    //		let extraProperties = [];
                    //		$.getJSON( '//ipinfo.io/json', function( data ) {
                    //			let propNames = ['ip','hostname','city','region','country','loc','org','postal'];
                    //			for(let i = 0; i < propNames.length; i++){
                    //				let val = data[propNames[i]];
                    //				if (val){
                    //					extraProperties.push({
                    //						name: propNames[i],
                    //						value: val
                    //					});
                    //				}
                    //			}
                    //			blocker.resolve();
                    //		});
                    //		blocker.promise();

                    //		blocker.done(function(){
                    //			studioStoreOptions.extraProperties = extraProperties;
                    // init the Studio Store application and  when done loading, execute the default operation
                    studioStore.init(studioStoreOptions).done(function () {
                        $('.ea-search-area').eaAutoComplete(saytOptions);
                        studioStore.templatesLoaded = true;
                        $('a.ea-toplevel-nav').click(function (e) {
                            studioStore.doToplevelArrangeBy(this);
                            e.preventDefault();
                            return false;
                        });
                        // hook overlay fields (if used) to click on logo
                        if (studioStoreOptions.overlayFields) {
                            $('td.ea-logo').click(function (e) {
                                $('.ea-product-cell-container .ea-score-container').toggle();
                                e.preventDefault()
                                return false;
                            });
                        }

                        if ('CA_Search' === requestData && question) {
                            studioStore.executeSearch(question, catPath || 'All Products');
                        } else {
                            studioStore.executeBreadcrumbClick(catPath || 'All Products');
                        }
                    });
//		}); // end blocker.done
                });
            });
        requirejs.config({
            baseUrl: '/vendor/easyask-sayt/js',
            paths: {
                "jquery": "./jquery-3.2.1.min",
                "jquery/ui": "./jquery-ui.min",
                "ea-sayt": "./sayt",
                "ea-store": "./studio-store",
                "ea-search-history": "./ea-searchhistory-3.1.0.min",
                "handlebars": "./handlebars.amd",
                "ea-handlebars": "./ea-handlebars",
                "text": "text",
                "touch-punch": "./jquery.ui.touch-punch.min"
            }
        });
    }
});
