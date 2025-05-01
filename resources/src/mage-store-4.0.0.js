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
		var lp = urlParam('lp');
		// create Studio Store options
		var studioStoreOptions = {
				dct : 'storedemo',
				fields : {
					id : 'Product_Id',
					skuId: 'Sku',
					name : 'Product_Name',
					part: 'Partnumber',
					image : 'Small_Image',
					price : 'Minprice',
					desc : 'Short_Description',
					url: '',
					rating: 'Average_Rating',
					mfr : '',
					colorCount: 'Color_Count',
					Sku_Colors: 'Sku_Colors',
					Sku_Sizes: 'Sku_Sizes'
				},
				colorAttribute : 'Color',
				ratingsAttribute : 'Product Rating',
				sizeAttribute: 'Size',
				overlayFields: true,
				facetsExpanded : 4,
				value:  function(item,field){
					if ('Sku_Colors' == field){
						return item.Sku_Colors && JSON.parse(item.Sku_Colors);
					}
					else if ('Sku_Sizes' == field){
						return item.Sku_Sizes && JSON.parse(item.Sku_Sizes);
					}
					else if ('Small_Image' == field){
						return item.Small_Image && 'http://107.20.188.196/media/catalog/product' + item.Small_Image;
					}
					else {
						return item[field];
					}
				},
				prodTemplate: "../StudioStore/src/templates/mage-products-5.hbs"
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
			if (fields.sku_id){
				studioStoreOptions.fields.skuId = fields.skuId.replace(/\W/g, '_');
			}
			if (fields.name) {
				studioStoreOptions.fields.name = fields.name.replace(/\W/g,'_');
			}
			if (fields.part) {
				studioStoreOptions.fields.part = fields.part.replace(/\W/g,'_');
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
			if (fields.colorCount) {
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
			if (fields.fixedMenu){
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
			}
			if (fields.facetsExpanded){
				studioStoreOptions.facetsExpanded = fields.facetsExpanded;
			}
			if (fields.flyout){
				studioStoreOptions.flyout = {
						depth: fields.flyoutDepth
				};
			}
			if (fields.dynMenu){
				studioStoreOptions.dynMenu = {
						start: fields.dynStart,
						depth: fields.dynDepth
				};
			}
			if (fields.scrollValues){
				studioStoreOptions.scrollValues = {
						size: fields.scrollSize,
						search: fields.scrollSearch
				};
			}
		}
		
		// initializing SAYT for use in StudioStore
		var saytOptions = {
				delay : 100,
				dict : dct || 'StoreDemo',
				prompt : 'Search by EasyAsk',
				submitFctn : function(type, val, elt) {
					if (type == 'nav') {
						// code that is equivalent to a breadcrumbclick on val
						studioStore.executeBreadcrumbClick(val);
					} else {
						// 'search' is the id of the button
						$('#search').click();
					}
				},
				leftWidth : '60%',
				horizAlign : 'right',
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
					}, {
						size : 3,
						title : 'Features',
						type : 'FEATURES'
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

		// override processResults to hook in new navigation for color selector
		var superProcessResults = ea_store.prototype.processResults;
		ea_store.prototype.processResults = function(data,isSearch){
			var self = this;
			superProcessResults.call(self, data,isSearch);
			$('.ea-product-color-block-values .ea-color-swatch-tile').click(function(e){
				var node = this;
				var selectedNode = $(node).closest('.ea-product-color-block').find('.ea-selected');
				var selectedColor = $(selectedNode).attr('ea-val').toLowerCase();
				var parentNode = $(node).closest('.ea-color-swatch');
				var color = parentNode.attr('ea-val').toLowerCase();
				if(selectedColor != color){
					var imgNode = $(node).closest('.ea-product-cell').find('.ea-product-cell-image img.ea-product-cell-image');
					var url = $(imgNode).attr('src');
					var key = '-' + selectedColor + '_';
					$(imgNode).attr('src',url.replace(key,'-' + color + '_'));
//					window.console && console.log("switching from " + selectedColor + " to " + color);
					$(selectedNode).removeClass('ea-selected');
					$(parentNode).addClass('ea-selected');
				}
				$('#ea-product-color-tooltip').hide();
				e.preventDefault();
				return false;
			});
			$('.ea-product-color-block-values .ea-color-swatch').hover(function(e){
				var node = this;
				var pos = $(node).position();
				var width = $(node).outerWidth();
				var swatch = $('#ea-product-color-tooltip');
				var color = $(node).attr('ea-val');
				$(swatch).find('.ea-color-tooltip-title').text(color);
				$(swatch).find('.ea-color-tooltip-image').css('background-color',color.toLowerCase());
				var pos = $(node).offset();
				var width = $(node).outerWidth(true);
				var widthSwatch = $(swatch).outerWidth(true);
				var left = (pos.left + (width-widthSwatch)/2) + 'px';
				var top = (pos.top - $(swatch).outerHeight(true) - 8) + 'px';
				$(swatch).css({left: left, top: top, display: 'block'});
				e.preventDefault();
				return false;
			},function(e){
				$('#ea-product-color-tooltip').hide();
				e.preventDefault();
				return false; 
			});
			$('.ea-product-size-block-values .ea-size-swatch-tile').click(function(e){
				var node = this;
				var selectedNode = $(node).closest('.ea-product-size-block').find('.ea-selected');
				var selectedSize = $(selectedNode).attr('ea-val').toLowerCase();
				var parentNode = $(node).closest('.ea-size-swatch');
				var size = parentNode.attr('ea-val').toLowerCase();
				if(selectedSize != size){
//					window.console && console.log("switching from " + selectedSize + " to " + size);
					$(selectedNode).removeClass('ea-selected');
					$(parentNode).addClass('ea-selected');
				}
				e.preventDefault();
				return false;
			});

		};
		
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
				if (lp){
					studioStore.executeLP(lp);
				}
				else if ('CA_LandingPage' == requestData && question){
					studioStore.executeLP(question);
				}
				else if (studiopreview == "1") {
					studioStore.executeStudioPreview(catPath || 'All Products');
				} 
				else if ('CA_Search' == requestData && question) {
					studioStore.executeSearch(question, catPath || 'All Products');
				} 
				else {
					studioStore.executeBreadcrumbClick(catPath || 'All Products');
				}
//				if (window.parent && window.parent.Studio){
//					window.parent.Studio.setStudioStore(studioStore);
//				}
			});
//		}); // end blocker.done 
	});
});
requirejs.config({
	baseUrl: '../scripts',
	paths: {
		"jquery": "../scripts/jquery-3.3.1",
		"jquery/ui": "../scripts/jquery-ui.min",
		
//		"ea-sayt": "/EasyAsk/sayt/ea-sayt-4.0.0.min",
//		"ea-store": "/EasyAsk/StudioStore/src/ea-studiostore-5.0.0.min",
//		"ea-search-history": "/EasyAsk/scripts/ea-searchhistory-3.1.0.min",

		// non-mimified versions
		"ea-sayt": "../sayt/ea-sayt-4.0.0",
		"ea-store": "../StudioStore/src/ea-studiostore-4.0.1",
		"ea-search-history": "../scripts/ea-searchhistory-3.1.0",

		"handlebars": "../scripts/handlebars.amd",

		"ea-handlebars": "../scripts/ea-handlebars.min",
//		"ea-handlebars": "../scripts/ea-handlebars",

		"text": "text",
		"touch-punch": "../scripts/jquery.ui.touch-punch.min"
	}
});

