/**
 * @preserve Copyright (c) 2000-2018 EasyAsk Technologies, Inc.  All Rights Reserved.
 * Use, reproduction, transfer, publication or disclosure is prohibited
 * except in accordance with the License Agreement.
 */

require(['jquery','ea-sayt','ea-store','touch-punch'],function($,ea_sayt,ea_store){
	$(function() {

		function urlParam(name,href){
		    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(href || window.location.href);
		    if (results==null){
		       return null;
		    }
		    else{
		       return decodeURIComponent(results[1]);
		    }
		};

		// parse parameters (if any)
		var sessionid = urlParam('sessionid');
		var studiopreview = urlParam('studiopreview');
		var storefields = urlParam('storefields');
		var dct = urlParam('dct');
		var requestData = urlParam('RequestData');
		var catPath = urlParam('CatPath') || urlParam('ea_path');
		var question = urlParam('question');
		// create Studio Store options
		var studioStoreOptions = {
				dct : 'amplify-rbs',
				server : 'https://demov16.easyaskondemand.com/',
				fields : {
					id : 'Product_Id',
					image : 'Product_Image',
					name : 'Product_Name',
					price : 'Price',
					desc : 'Short_Description',
					ptype : 'Type_Id',
					sizes : 'Sku_Sizes'
				},
				colorAttribute : 'Available Colors',
				ratingsAttribute : 'Product Rating',
				overlayFields: true,
				facetsExpanded : 4
		}
		if (sessionid) {
			studioStoreOptions['sessionid'] = sessionid;
		}
		if (dct) {
			studioStoreOptions['dct'] = dct;
		}
		var fields = '';
		if (storefields) {
			fields = $.parseJSON(storefields);
			// map generic field names to actual names from the dictionary
			if (fields.id) {
				studioStoreOptions.fields.id = fields.id.replace(/\W/g, '_');
			}
			if (fields.name) {
				studioStoreOptions.fields.name = fields.name.replace(/\W/g,'_');
			}
			if (fields.image) {
				studioStoreOptions.fields.image = fields.image.replace(/\W/g,'_');
			}
			if (fields.price) {
				studioStoreOptions.fields.price = fields.price.replace(/\W/g,'_');
			}
			if (fields.desc) {
				studioStoreOptions.fields.desc = fields.desc.replace(/\W/g,'_');
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
					label : fields.menu1Label,
					val : fields.menu1Value
				});
			}
			if (fields.menu2Label && fields.menu2Value) {
				studioStoreOptions.menu.push({
					label : fields.menu2Label,
					val : fields.menu2Value
				});
			}
			if (fields.menu3Label && fields.menu3Value) {
				studioStoreOptions.menu.push({
					label : fields.menu3Label,
					val : fields.menu3Value
				});
			}
			if (fields.facetsExpanded){
				studioStoreOptions.facetsExpanded = fields.facetsExpanded;
			}
		}

		// initializing SAYT for use in StudioStore
		var saytOptions = {
				delay : 100,
				dict : 'amplify-rbs',
				server : 'https://demov16.easyaskondemand.com',
				serverSearch : 'https://demov16.easyaskondemand.com',
				prompt : 'Search by EasyAsk',
				submitFctn : function(type, val, elt) {
					if (type == 'nav') {
                        href = window.location.origin + '/shop?ea_server_products=' + val;
                        window.location = href;
					} else {
                        // 'search' is the id of the button
                        $('#search').click();
                    }
				},
				leftWidth : '60%',
				horizAlign : 'left',
				search: {
					size: 5
				},
				products : {
					size: 3,
					value: function(item,field){
                        if (field == 'URL_Path'){
                           return window.location.protocol + "//" + window.location.host + "/shop/" + item[field] + "?variationId=" + item['Color_Code'];
                        }
                        return item[field];
					},

					fields : $.extend({},studioStoreOptions.fields,
								studioStoreOptions.fields.image?{thumbnail:studioStoreOptions.fields.image}:null,
											{link : 'ea_storedetails'}), // dummy name to support popup details
					sizes : {
						description : 115
					}
				},
				navigation : {
					size : 3,
					sections : [ {
						size : 2,
						title : 'Category',
						type : 'category'
					}, {
						size : 3,
						title : 'Color',
						type : 'COLOR'
					}, {
						size : 3,
						title : 'Size',
						type : 'SIZE'
					} ]
				},
				template: "../sayt/templates/leftprod.hbs"
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
				$('select.ea-select-sort-by option[value="Product Name,t"').attr('value', fields.name + ',t');
				$('select.ea-select-sort-by option[value="Product Name,f"').attr('value', fields.name + ',f');
			}
			if (fields.image) {
				saytOptions.products.fields.thumbnail = fields.image.replace(/\W/g, '_');
			}
			if (fields.price) {
				saytOptions.products.fields.price = fields.price.replace(/\W/g, '_');
				$('select.ea-select-sort-by option[value="Price,t"').attr('value', fields.price + ',t');
				$('select.ea-select-sort-by option[value="Price,f"').attr('value', fields.price + ',f');
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
				$('select.ea-select-sort-by option[value="Manufacturer,t"').attr('value', fields.mfr + ',t');
				$('select.ea-select-sort-by option[value="Manufacturer,f"').attr('value', fields.mfr + ',f');
			}
		}
		// maintain sessionid from Studio if present
		if (sessionid) {
			saytOptions['sessionid'] = sessionid;
		}
		var studioStore = new ea_store();


		// override SAYT function getResults to wrap detail click for store demo.  This involves retaining
		// the items that were used by SAYT (retrieved when the ea_storedetails pseudo field was retrieved) and
		// after all items have been processed then installing a click handler to retrieve the stored item and
		// use the store demo detail page display

		// applications that store a clickable href in the product area can eliminate this section

		if (saytOptions.products.fields.link == 'ea_storedetails'){
			// need to save the items in results to retrieve for default detail processing
			var studioStoreItemCache = [];  // for caching items
			saytOptions.products.value = function(item, fld) {
				if (fld == 'ea_storedetails') {
					studioStoreItemCache.push(item);
				}
				return item[fld];
			};

			function saytShowDetails(id,products,path) {
				var fields = products.fields;
				var value = products.value
				var idField = fields.id;
				if (idField) {
					var item = '';
					for (var i = 0; i < studioStoreItemCache.length; i++) {
						if (studioStoreItemCache[i][idField] == id) {
							item = studioStoreItemCache[i];
							break;
						}
					}
					if (item) {
						studioStore.showDetails(id,item,path);
					}
				}
			}
			var superGetResults = ea_sayt.prototype.getResults;
			ea_sayt.prototype.getResults = function(curOffset){
				studioStoreItemCache = []; // clear cache
				var self = this;
				$.when(superGetResults.call(self, curOffset)).done(function(){
					$('.ea-sug-product').each(function() {
						var id = $(this).attr('ea-prod-id');
						if (id) {
							$(this).find('a[href="#"]').click(function(e) {
								self.close(true);
								saytShowDetails(id,self.config.products,self.path);
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

//		var blocker = $.Deferred();
//		var extraProperties = [];
//		$.getJSON( '//ipinfo.io/json', function( data ) {
//			var propNames = ['ip','hostname','city','region','country','loc','org','postal'];
//			for(var i = 0; i < propNames.length; i++){
//				var val = data[propNames[i]];
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
			studioStore.init(studioStoreOptions).done(function(){
				$('.ea-search-area').eaAutoComplete(saytOptions);
				studioStore.templatesLoaded = true;
				$('a.ea-toplevel-nav').click(function(e) {
					studioStore.doToplevelArrangeBy(this);
					e.preventDefault();
					return false;
				});
				// hook overlay fields (if used) to click on logo
				if (studioStoreOptions.overlayFields){
					$('td.ea-logo').click(function(e){
						$('.ea-product-cell-container .ea-score-container').toggle();
						e.preventDefault()
						return false;
					});
				}
				if (studiopreview == "1") {
					studioStore.executeStudioPreview(catPath || 'All Products');
				}
				else if ('CA_Search' == requestData && question) {
					studioStore.executeSearch(question, catPath || 'All Products');
				}
				else {
					studioStore.executeBreadcrumbClick(catPath || 'All Products');
				}
			});
//		}); // end blocker.done
	});
});
requirejs.config({
	baseUrl: '/easyask-sayt/scripts',
	paths: {
		"jquery": "./jquery-3.3.1",
		"jquery/ui": "./jquery-ui.min",

		"ea-sayt": "../sayt/ea-sayt-4.0.0.min",
		"ea-store": "../src/ea-studiostore-4.0.0",
		"ea-search-history": "./ea-searchhistory-3.1.0.min",

		"handlebars": "./handlebars.amd",

		"ea-handlebars": "./ea-handlebars",

		"text": "text",
		"touch-punch": "./jquery.ui.touch-punch.min"
	}
});

