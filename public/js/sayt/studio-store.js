/**
 * @preserve Copyright (c) 2011-2018 EasyAsk Technologies, Inc. All Rights Reserved.
 * Use, reproduction, transfer, publication or disclosure is prohibited
 * except in accordance with the License Agreement.
 * @version 4.0.0
 */


(function(factory){
    if (typeof define === 'function' && define.amd){
       define('ea-store',['jquery','ea-handlebars','ea-search-history','jquery/ui'],factory);
    }
    else {
       factory(window.$ea || window.eaj$183 || jQuery,null,window.EASearchHistory || null);
    }
}(function( $, HB, searchHistory ) {
	$.urlParam = function(name,href){
	    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(href || window.location.href);
	    if (results==null){
	       return null;
	    }
	    else{
	       return results[1];
	    }
	};
	EASearchDemo = function() {
	};
	EASearchDemo.prototype = {
			defaults: {
				navTemplate: "../src/templates/navigation.hbs",
				prodTemplate: "../src/templates/products.hbs",
				server: '',
				dct: 'EcomDemo',
				idInput: 'question',
				idButton: 'search',
				cols: 4,
				fields: {
					id : 'Product_Id',
					image : 'picture',
					name : 'Product_Name',
					price : 'Price',
					desc : 'Description',
					mfr : 'Manufacturer'
				},
				extraProperties: '',
				overlayFields: true,
				value: function(item,field){ return item[field]; }
			},
			ATTR_VALUE_TYPE_RANGE: 2,
			// state variables
			path: '', //'All Products',
			currentPageSize: 16,
			currentPage: 1,
			currentSort: '-default-',
			pageCount: 0,

			loadAndCompile: function(){
				var self = this;
				var res = $.Deferred();
				require(['text!' + self.config.navTemplate,'text!' + self.config.prodTemplate],function(templateNav,templateProd){
					self.compiledNav = HB.compile(templateNav);
					self.compiledProd = HB.compile(templateProd);
					res.resolve();
				});
				return res.promise();
			},
    		KEYCODE_ENTER: 13,
    		KEYCODE_NUMPAD_ENTER: 176,
			init: function(opts){
				var self = this;
				var options = $.extend(true,this.defaults,opts);
				self.config = options;
				var result = self.loadAndCompile();
				this.baseURL = options.server + '/EasyAsk/apps/Advisor.jsp?indexed=1&ie=UTF-8&rootprods=1&disp=json&dct=' + options.dct; // + '&defarrangeby=///NONE///';
				if (options.extraProperties && $.isArray(options.extraProperties)){
					for(var i = 0; i < options.extraProperties.length; i++){
						var prop = options.extraProperties[i];
						if (prop.name && prop.value){
							this.baseURL += ('&eap_' + prop.name + '=' + encodeURIComponent(prop.value));
						}
					}
				}
				this.basePromotionURL = options.server + '/EasyAsk/apps/CrossSellToResults.jsp?indexed=1&ie=UTF-8&rootprods=1&disp=json&dct=' + options.dct; // + '&defarrangeby=///NONE///';
				this.value = this.defaults.value;
				if (opts.sessionid){
					this.baseURL += '&sessionid=' + opts.sessionid;
					this.basePromotionURL += '&sessionid=' + opts.sessionid;
				}
				else {
					this.baseURL += '&oneshot=1';
					this.basePromotionURL += '&oneshot=1';
				}
				$('#' + options.idInput).bind('keydown',function(event){
					if (self.KEYCODE_ENTER == event.keyCode || self.KEYCODE_NUMPAD_ENTER == event.keyCode){
						window.setTimeout(function(){$('#'+options.idButton).click(); },100);
						event.preventDefault();
						return false;
					}
					return true;
				});
				$('#'+options.idButton).unbind('click').click(function(e){
					var inp = $.trim($('#'+options.idInput).val());
					if (inp){
//						if (searchHistory){
//							searchHistory.add(inp);
//						}
						self.executeSearch(inp, opts.shopUrl);
					}
					e.preventDefault();
					return false;
				});
				$('.ea-page-first').click(function(){self.pageOp('first');});
				$('.ea-page-next').click(function(){self.pageOp('next');});
				$('.ea-page-prev').click(function(){self.pageOp('prev');});
				$('.ea-page-last').click(function(){self.pageOp('last');});
				$('.ea-current-page').bind('keydown',function(event){
					if (self.KEYCODE_ENTER == event.keyCode || self.KEYCODE_NUMPAD_ENTER == event.keyCode){
						var val = $(this).val();
						if (!isNaN(val) && 0 < val && self.pageCount >= val) {
							self.gotoPage(val);
						}
						else {
							alert('Enter a number between 1 and ' + self.pageCount);
							return false;
						}
					}
				});
				$('.ea-select-results-per-page').change(function(evt){
					self.currentPageSize=$(this).val();
					self.currentPage=1;
					self.pageOp('first');
				});
				$('.ea-select-sort-by').change(function(evt){
					self.currentSort=$(this).val();
					self.currentPage=1;
					self.pageOp('first');
				});
				$('#tabs').tabs().hide();
				if (options.menu && options.menu.length){
					var html = '';
					for(var i = 0; i < options.menu.length; i++){
						var menu = options.menu[i];
						html += '<li class="ea-toplevel-menu-item">' +
						'<a class="ea-toplevel-nav" ea-group="' + menu.val + '" href="#">' +
						menu.label + '</a></li>';
					}
					$('.ea-toplevel-menu-bar').html(html);
				}
				else {
					$('.ea-toplevel-menu-area').hide(); // no menus
				}
				this.options = options; // remember them

				if ('onpopstate' in window){
					var self = this;
					window.onpopstate = function(event){
						if (event.state){
							window.console && console.log("popstate: " + event.state);
							self.executeCall(event.state);
						}
						return false;
					};
				}
				return result;
			},
			formatNumber: function(val, decimal, group){
				var re = '\\d(?=(\\d{' + (group || 3) + '})+' + (decimal > 0 ? '\\.' : '$') + ')';
				return Number(val).toFixed(Math.max(0, ~~decimal)).replace(new RegExp(re, 'g'), '$&,');
			},

			rawNumber: function(val,decimal){
				return Number(val).toFixed(Math.max(0,~~decimal));
			},

			addPath: function(cat){
				return '&CatPath=' + encodeURIComponent(this.path + (cat?('////'+cat):''));
			},

			addPath: function(){
				return '&CatPath=' + encodeURIComponent(this.seoPath);
			},

			formURL: function(){
				return this.baseURL + '&ResultsPerPage=' + this.currentPageSize + '&defsortcols=' + (this.currentSort == '-default-'?'':this.currentSort);
			},

			executeSearch: function(q,path){
				/*this.seoPath = path || ''; // || 'All Products';
				var url = this.formURL() + '&RequestAction=advisor&RequestData=CA_Search&q=' + encodeURIComponent(q) + this.addPath();
				this.invoke(url);*/
                href = (path ? path + '?search=1&q=' :window.location.origin + '/shop?search=1&q=') + q;
                // console.log(q,path);
                window.location = href;
			},

			executeAttribute: function(attr,val,path){
				var url = this.formURL() + '&RequestAction=advisor&RequestData=CA_AttributeSelected&CatPath=' + encodeURIComponent(path || this.path) + '&AttribSel=' + encodeURIComponent(attr + " = '" + val +"'");
				this.invoke(url);
			},

			executeSEORangeAttr: function(attr,val,node){
				var pathParts = this.seoPath?this.seoPath.split('/'):[];
				var key = node.substring(0,node.indexOf(':'));
				var newParts = [];
				for(var i = 0; i < pathParts.length; i++){
					var seg = pathParts[i];
					if (seg.indexOf(key) != 0) { // not the attribute select
						newParts.push(seg);
					}
				}
				newParts.push(key + ':' + val);
				this.executeBreadcrumbClick(newParts.join('/'));
			},
			executeRangeAttr: function(attr,val,node){
				if (node){
					this.executeSEORangeAttr(attr,val,node);
				}
				else {
					var pathParts = this.path.split('////');
					var key = 'AttribSelect='+attr+' = \'';
					var newParts = [];
					for(var i = 0; i < pathParts.length; i++){
						var seg = pathParts[i];
						if (seg.indexOf(key) != 0) { // not the attribute select
							newParts.push(seg);
						}
					}
					this.executeAttribute(attr,val,newParts.join('////'));
				}
			},


			executeMVAttribute:function(vals,path){
				var url = this.formURL() + '&RequestAction=advisor&RequestData=CA_AttributeSelected&CatPath=' + encodeURIComponent(path || this.path) + '&AttribSel=' + encodeURIComponent(vals);
				this.invoke(url);
			},

			executeToplevelArrangeBy: function(grpName){
				var url = this.formURL() + '&RequestAction=advisor&RequestData=CA_BreadcrumbClick&CatPath=&defarrangeby=' + encodeURIComponent(grpName);
				this.invoke(url);
			},

			executeBreadcrumbClick: function(bc){
				if (bc == 'All Products'){
					bc = '';
				}
				var url = this.formURL() + '&RequestAction=advisor&RequestData=CA_BreadcrumbClick&CatPath='+encodeURIComponent(bc);
				this.invoke(url);
			},

			executeStudioPreview: function(bc){
				var url = this.formURL() + '&studiopreview=1&RequestAction=advisor&RequestData=CA_BreadcrumbClick&CatPath='+encodeURIComponent(bc);
				this.invoke(url);
			},

			executeStudioPreviewSearch: function(q,bc){
				var url = this.formURL() + '&studiopreview=1&RequestAction=advisor&RequestData=CA_Search&CatPath='+encodeURIComponent(bc)+'&question='+encodeURIComponent(q);
				this.invoke(url);
			},

			gotoPage: function(val){
				var url = this.formURL() + '&RequestAction=navbar&RequestData=' + encodeURIComponent('page' + val)+ this.addPath();
				this.invoke(url);
			},

			pageOp: function(val){
				var url = this.formURL() + '&RequestAction=navbar&RequestData=' + encodeURIComponent(val)+ this.addPath() + '&currentpage='+this.currentPage;
				this.invoke(url);
			},

			updatePagination: function(desc){
				this.currentPage=desc.currentPage;
				this.pageCount = desc.pageCount;
				$('.ea-first-item').html(desc.firstItem);
				$('.ea-last-item').html(desc.lastItem);
				$('.ea-total-items').html(desc.totalItems);
				$('.ea-current-page').val(desc.currentPage);
				$('.ea-total-pages').html(desc.pageCount);
				if (1 == desc.pageCount){
					$('.ea-page-first, .ea-page-prev, .ea-page-next, .ea-page-last, .ea-current-page').attr('disabled','disabled').addClass('ea-disabled');
				}
				else {
					$('.ea-current-page').removeAttr('disabled').removeClass('ea-disabled');
					if (1 == desc.currentPage){
						$('.ea-page-first, .ea-page-prev').attr('disabled','disabled').addClass('ea-disabled');
					}
					else {
						$('.ea-page-first, .ea-page-prev').removeAttr('disabled').removeClass('ea-disabled');
					}
					if (desc.pageCount == desc.currentPage){
						$('.ea-page-next, .ea-page-last').attr('disabled','disabled').addClass('ea-disabled');
					}
					else {
						$('.ea-page-next, .ea-page-last').removeAttr('disabled').removeClass('ea-disabled');
					}
				}
				$('.ea-results-controls').show();
			},
			getNormalizedValue: function(val){
				var normalizedVal = (Math.round(parseFloat(val)*2)/2).toFixed(1);
				return normalizedVal;
			},
			findItem: function(id){
				var fields = this.options.fields;
				if (fields.id){  // need an id field
					var items = this.currentProducts;
					for(var i = 0; i < items.length; i++){
						if (items[i][fields.id] == id){
							return items[i];
						}
					}
				}
				return '';
			},
			findCarveOut: function(id){
				var fields = this.options.fields;
				if (fields.id){  // need an id field
					var items = this.currentCarveOuts;
					for(var i = 0; i < items.length; i++){
						if (items[i][fields.id] == id){
							return items[i];
						}
					}
				}
				return '';
			},

			findFeaturedItem: function(id){
				var fields = this.options.fields;
				if (fields.id){  // need an id field
					var items = this.featuredItems;
					for(var i = 0; i < items.length; i++){
						if (items[i][fields.id] == id){
							return items[i];
						}
					}
				}
				return '';
			},
			promotionTypes: ['Cross-Sell','Up-Sell','Down-Sell','Add-On','Common Item','Substitution','Complementary','Non-Complementary','Promotions'],

			getPromotions: function(id){
				var result = [];
				this.getPromotionByType(id,0,promotionTypes,results);
			},

			getItems: function(data){
				if (data && data.products){
					return data.products.items;
				}
				return null;
			},

			processPromotionResults: function(data,type,results){
				var items = this.getItems(data);
				if (items && items.length){
					results.push({type: type, items: items});
				}
			},

			getPromotionByType: function(id, idx, types, results){
				if (idx < types.length){
					var self = this;
					$.ajax({
						url: this.basePromotionURL,
						data: {
							q: id,
							type: types[idx]
						},
						type: 'POST',
						crossDomain: true,  // cross comain
						dataType: 'jsonp',  // handles cross domain
						success: function(data,textStatus, jqXHR){
							self.processPromotionResults(data,types[idx],results);
							self.getPromotionByType(id,idx+1,types,results);
						},
						error: function(data, textStatus, jqXHR){
							alert('error: ' + data);
						}
					});
				}
				else {
					this.processPromotions(id,results);
				}

			},
			htmlXSell: function(items){
				var fields = this.options.fields;
				if (fields.rating){
					for(var i = 0; i < items.length; i++){
						items[i].normalizedVal =  this.getNormalizedValue(this.value(items[i],fields.rating));
					}
				}

				var args = {
						prods: {items: items},
						fields: this.options.fields,
						opts: { overlayFields: false}
				};
				html = this.compiledProd(args);
				return html;
			},
			buildPromotionHTML: function(xsells){
				if (xsells && xsells.length){
					var ul = '';
					var divs = '';
					for(var i = 0; i < xsells.length; i++){
						ul += '<li><a href="#xsell-' + i + '">' + xsells[i].type + '</a></li>';
						divs += '<div id="xsell-' + i + '" class="ea-promotion-tab" style="padding: .5em 0em;">' + this.htmlXSell(xsells[i].items) + '</div>';
					}
					$('#tabs').tabs('destroy');
					$('#tabs').html('<ul>' + ul + '</ul>' + divs).tabs().show();
				}
				else {
					$('#tabs').hide();
				}
			},
			processPromotions: function(id,xsells){
				window.console && console.log('id: ' + id + ' xsells: ' + xsells.length);
				this.buildPromotionHTML(xsells);
			},

			showDetails: function(id,item, path){
				if (item){
                    window.location = window.location.origin + '/shop/product/' + id;
				}
			},

			getOverlayColumnName: function(dataDescription,name){
				if (name == 'EAFeatured Weight'){
					return 'EAWeight';
				}
				else if (dataDescription){
					for(var i = 0; i < dataDescription.length; i++){
						if (dataDescription[i].columnName == name){
							return dataDescription[i].tagName;
						}
					}
				}
				else if (name == 'EAShelfOrder'){
					return ''
				}
				else {
					return name;
				}
			},

			getOverlayColumnLabel: function(name){
				if (name == 'EAFeatured Weight' || name == 'EAWeight'){
					return 'Business';
				}
				else if (name == 'EAScore'){
					return 'Relevancy';
				}
				else if (name == 'EAPersonalization'){
					return 'Personalization';
				}
				return name;
			},

			insertNoResults: function(noResults){
				if (noResults && noResults.message){
					$('#ea-no-results').html(noResults.message).show();
				}
				else {
					$('#ea-no-results').hide();
				}
			},

			insertProducts: function(prods){
				if (prods){
					var html = '';
					var overlayInfo = null;
					if (this.options.overlayFields && prods && prods.itemDescription){
						var overlayInfo = [];
						var sort = prods.itemDescription.sortOrder;
						if (sort){
							var parts = sort.split(',');
							for(var i = 0; i < parts.length-1; i++) { // need 2
								var name = parts[i++]; // advance over direction
								var col = this.getOverlayColumnName(prods.itemDescription.dataDescription,name);
								if (col){
									overlayInfo.push({
										column: col,
										label: this.getOverlayColumnLabel(name)
									});
								}
							}
						}

					}
					var fields = this.options.fields;
					if (this.options.fields.rating){
						if (prods.groups){
							for(var i = 0; i < prods.groups.group.length; i++){
								var group = prods.groups.group[i];
								for(var j = 0; j < group.item.length; j++){
									group.item[j].normalizedVal = this.getNormalizedValue(this.value(group.item[j],fields.rating));
								}
							}
						}
						else {
							for(var i = 0; i < prods.items.length; i++){
								prods.items[i].normalizedVal =  this.getNormalizedValue(this.value(prods.items[i],fields.rating));
							}
						}
					}
					var args = {
							prods: prods,
							fields: this.options.fields,
							opts: this.options
					};
					if (overlayInfo && overlayInfo.length){
						args.oinfo = overlayInfo;
					}

					// convert any string representations of arrays to actual arrays, e.g., values
					for(var i = 0; i < prods.items.length; i++){
						if (prods.items[i].Sku_Colors){
							prods.items[i].Sku_Colors = JSON.parse(prods.items[i].Sku_Colors);
						}
						if (prods.items[i].Sku_Sizes){
							prods.items[i].Sku_Sizes = JSON.parse(prods.items[i].Sku_Sizes);
						}
					}

					html = this.compiledProd(args);
					$('#ea-products').html(html).show();
					if (!prods.groups){
						this.updatePagination(prods.itemDescription);
						this.currentProducts = prods.items; // remember them
					}
					else {
						this.currentProducts = [];
						for(var i = 0; i < prods.groups.group.length; i++){
							var group = prods.groups.group[i];
							for(var j = 0; j < group.item.length; j++){
								this.currentProducts.push(group.item[j]);
							}
						}
						$('.ea-results-controls').hide();
					}
				}
				else {
					$('#ea-products').hide();
					$('.ea-results-controls').hide();
					this.currentProducts = [];
				}
			},

			insertFeaturedProducts: function(fprods){
				if (fprods){
					var fields = this.options.fields;
					var html = '';
					for(var i = 0; i < fprods.items.length; i++){
						var fp = fprods.items[i];
						var url = fields.url?this.value(fp,fields.url):null;
						var idAttr = '';
						if (fields.id && fp[fields.id]){
							idAttr = ' ea-prod-id="' + this.htmlEncode(fp[fields.id]) + '"';
						}
						html += ('<tr><td class="ea-featured-product-image-area"' + idAttr +'><a href="' + (url || '#') +'"><img class="ea-featured-product-cell-image" src="' + fp[fields.image] + '"></a></td><td class="ea-featured-product-cell"><div class="ea-featured-product-name"><a href="' + (url || '#') +'"><div class="ea-product-cell-name">' + fp[fields.name] + '</div></a></div><div class="ea-featured-product-price">'+(fp[fields.price] || '&nbsp;')+'</div><div class="ea-featured-product-description">'+fp[fields.desc]+'</div></td></tr>');
					}
					if (html){
						html = '<table class="ea-featured-product-title"><tr><td class="ea-featured-product-title">Featured Products</td></tr></table><table class="ea-featured-product-contents">' + html + '</table>';
						$('#ea-featured-products').html(html).show();
						this.featuredItems = fprods.items;
						return;
					}
				}
				else {
					$('#ea-featured-products').hide();
					this.featuredItems = [];
				}
			},

			insertCarveOuts: function(carveouts){
				if(carveouts && carveouts.length){
					var fields = this.options.fields;
					var allCarveOutItems = [];
					var html = '';
					for(var i = 0; i < carveouts.length; i++){
						var carveout = carveouts[i];
						if (carveout.items){
							var name = carveout.attribute;
							html += '<div class="ea-carveout-area"><div class="ea-carveout-title">' + name + '</div><div style="clear:both;"></div>';
							var format = carveout.displayFormat;
							html += '<table>';
							for(var j = 0; j < carveout.items.length; j++){
								var item = carveout.items[j];
								var url = fields.url?this.value(item,fields.url):null;
								var idAttr = '';
								if (fields.id && this.value(item,fields.id)){
									idAttr = ' ea-prod-id="' + this.htmlEncode(this.value(item,fields.id)) + '"';
								}
								html += '<tr><td class="ea-carveout-cell" align="center"' + idAttr + '>';
								if (format == 'picture'){
									html += '<div class="ea-carveout-cell-image-area"><a href="' + (url || '#') +'"><img class="ea-carveout-cell-image" src="' + this.value(item,fields.image) + '"/></a></div>';
								}
								html += '<div class="ea-carveout-name-area"><a href="' + (url || '#') +'"><div class="ea-carveout-cell-name">' + this.value(item,fields.name) + '</div></a>';
								html += '</td></tr>';
								allCarveOutItems.push(item);
							}
							html+= '</table></div>';
						}
					}
					$('#ea-carveouts').html(html).show();
					this.currentCarveOuts = allCarveOutItems;
				}
				else {
					$('#ea-carveouts').hide();
					this.currentCarveOuts = [];
				}
			},

			SWATCHES_PER_LINE: 4,

			isLastNodeAttribute: function(bc, attrName){
				var nodes = bc[bc.length-1].path.split('////');
				return 0 == nodes[nodes.length-1].indexOf('AttribSelect=' + attrName + ' = \'');
			},
			insertNavigation: function(src,bc){
				var cats = src.categories;
				var attrs = src.attributes;
				var stateInfo = src.stateInfo;
				var commonAttrs = src.commonAttribute;
				var html = '';
				// fix attributes that are ratings to include a normalized value
				if (this.options.ratingsAttribute && src.attributes && src.attributes.attribute){
					for(var i = 0; i < src.attributes.attribute.length; i++){
						var attr = src.attributes.attribute[i];
						if (attr.name == this.options.ratingsAttribute){
							for(var j = 0; j < attr.attributeValueList.length; j++){
								attr.attributeValueList[j].normalizedVal = this.getNormalizedValue(attr.attributeValueList[j].attributeValue);
							}
							if (attr.initialAttributeValueList){
								for(var j = 0; j < attr.initialAttributeValueList.length; j++){
									attr.initialAttributeValueList[j].normalizedVal = this.getNormalizedValue(attr.initialAttributeValueList[j].attributeValue);
								}
							}
							break;
						}
					}
				}
				// fix range attributes to set correct min/max for slider
				if (src.attributes){
					for(var i = 0; i < src.attributes.attribute.length;i++){
						var attr = src.attributes.attribute[i];
						var av = attr.attributeValueList[0];
						if (av.valueType == this.ATTR_VALUE_TYPE_RANGE){
							var minRangeValue = av.minRangeValue;
							var maxRangeValue = av.maxRangeValue;
							if (!this.isLastNodeAttribute(bc,attr.name)){
								minRangeValue = av.minValue;
								maxRangeValue = av.maxValue;
							}
							av.effectiveMinRange = minRangeValue;
							av.effectiveMaxRange = maxRangeValue;
						}
					}
				}
				html = this.compiledNav({
					cats: src.categories,
					attrs: src.attributes,
					stateInfo: src.stateInfo,
					commonAttrs: src.commonAttribute,
					opts: this.options
				});
				$('#ea-nav').html(html);
			},

			doCategoryClick: function(node){
				var seoPath = $(node).attr('ea-seo-path');
				this.executeBreadcrumbClick(seoPath);
				return;
			},

			doAttributeClick: function(node){
				var seoPath = $(node).attr('ea-seo-path');
				this.executeBreadcrumbClick(seoPath);
				return;
			},

			getAllCheckedVals: function(node){
				var vals = [];
				$(node).closest('ul.ea-nav-block-values').find('input:checked.ea-attr').each(function(index,element){
					vals[$(element).attr('ea-val')] = true;
				});
				return vals;
			},

			doToplevelArrangeBy: function(node){
				var grpName = $(node).attr('ea-group');
				this.executeToplevelArrangeBy(grpName);
			},

			doBreadcrumbClick: function(node){
				var seoPath = $(node).attr('ea-seo-path');
				this.executeBreadcrumbClick(seoPath);
				return;
			},
			doBreadcrumbRemove: function(node){
				var seoPath = $(node).attr('ea-seo-path');
				this.executeBreadcrumbClick(seoPath);
				return;
			},
			bannerClick: function(node){
				var seoPath = $(node).attr('ea-seo-path');
				if (seoPath){
					this.executeBreadcrumbClick(seoPath);
				}
				else {
					var path = $(node).attr('ea-path');
					var attr = $(node).attr('ea-attr');
					if (attr){
						this.executeAttribute(attr,$(node).attr('ea-val'),path);
					}
					else {
						var srch = $(node).attr('ea-srch');
						if (srch){
							this.executeSearch(srch,path);
						}
						else {
							this.executeBreadcrumbClick(path);
						}
					}
				}
			},
			buildRangeSEOValue: function(a,b,c,d){
				return (a<0?'\\':'')+a+'-'+(b<0?'\\':'')+b+'-'+(c<0?'\\':'')+c+'-'+(d<0?'\\':'')+d;
			},
			buildRangeValue: function(a,b,c,d,node){
				// this is for non-seo encoded
				if (node){ // return seo
					return (a<0?'\\':'')+a+'-'+(b<0?'\\':'')+b+'-'+(c<0?'\\':'')+c+'-'+(d<0?'\\':'')+d;
				}
				else {
					return (a<0?'\\':'')+a+'@'+(b<0?'\\':'')+b+'@'+(c<0?'\\':'')+c+'@'+(d<0?'\\':'')+d;
				}
			},

			processRange: function(attrName,minInput,maxInput,minRange,maxRange,step,node){
				var rawMinVal = this.rawNumber(minInput.value.replace(/[^0-9\.\-]+/g,''),2);
				var rawMaxVal = this.rawNumber(maxInput.value.replace(/[^0-9\.\-]+/g,''),2);
				if (rawMinVal <= rawMaxVal - step){
					var val = this.buildRangeValue(rawMinVal,rawMaxVal, minRange,maxRange,node);
					this.executeRangeAttr(attrName,val,node);
				}
			},
			hookProductDetails: function(){
				var self = this;
				$('.ea-product-cell').each(function(){
					var id = $(this).attr('ea-prod-id');
					if (id){
						$(this).find('a[href="#"]').click(function(e){
							self.showDetails(id,self.findItem(id),self.seoPath);
							e.preventDefault();
							return false;
						});
					}
				});
			},

			hookFeaturedItems: function(){
				var self = this;
				$('.ea-featured-product-cell').each(function(){
					var id = $(this).attr('ea-prod-id');
					if (id){
						$(this).find('a[href="#"]').click(function(e){
							self.showDetails(id,self.findFeaturedItem(id));
							e.preventDefault();
							return false;
						});
					}
				});
			},

			hookCarveOutDetails: function(){
				var self = this;
				$('.ea-carveout-cell').each(function(){
					var id = $(this).attr('ea-prod-id');
					if (id){
						$(this).find('a[href="#"]').click(function(e){
							self.showDetails(id,self.findCarveOut(id));
							e.preventDefault();
							return false;
						});
					}
				});

			},
			hookNoResults: function() {
				var self = this;
				$('li.ea-no-results-link a').click(function(e){
					var node = this;
					self.doBreadcrumbClick(node);
					e.preventDefault();
					return false;
				});
			},

			hookNav: function(){
				var self = this;
				$('a.ea-cat').click(function(e){
					var node = this;
					self.doCategoryClick(node);
					e.preventDefault();
					return false;
				});
				$('input.ea-attr').click(function(){
					var next = $(this).next();
					if (next.length){
						$(next).click();
					}
				});
				$('.ea-color-swatch,.ea-size-swatch').click(function(e) {
					self.doAttributeClick(this);
					e.preventDefault();
					return false;
				});
				$('a.ea-attr').click(function(e){
					self.doAttributeClick(this);
					e.preventDefault();
					return false;
				});
				$('.ea-current-state a.ea-selected').click(function(e){
					self.doBreadcrumbRemove(this);
					e.preventDefault();
					return false;
				});
				$('.ea-current-state a.ea-left-hotspot').click(function(e){
					self.doBreadcrumbRemove($(this).next());
					e.preventDefault();
					return false;
				});
				$('a.ea-bc').click(function(e){
					var node = this;
					self.doBreadcrumbClick(node);
					e.preventDefault();
					return false;
				});

				// expand/collapse on headers except for current state
				$('a .ea-nav-title').click(function(){
					$(this).toggleClass('ea-collapse').parents('.ea-nav-block').find('.ea-nav-block-values').toggle();
					return false;
				});

				// expand/collapse on values
				$('ul.ea-nav-block-values').each(function(){
					var vals = $(this).children('div');
					if (2 == vals.length){
						vals.each(function(){
							$(this).find('.ea-nav-val-toggle a').each(function(_,togVal){
								$(togVal).click(function(){
									$(vals).toggle();
									return false;
								});
							});
						});
					}
				});
				// banner handlers
				$('a.ea-banner-anchor[ea-action]').each(function(index, val){
					$(val).click(function(e){
						var node = this;
						self.bannerClick(node);
						e.preventDefault();
						return false;
					});
				});
				$('.ea-banner-wrapper img').one('error',function(){
					$(this).addClass('image-not-found');
				});
				//                   setTimeout(function(){
				$('.ea-range-attr').each(function(){
					var min = $(this).attr('eaMin');
					var max = $(this).attr('eaMax');
					var attrName = $(this).attr('ea-attr');
					var minRange = $(this).attr('eaMinRange');
					var maxRange = $(this).attr('eaMaxRange');
					var step = Number($(this).attr('eaScale') || 1.0);
					var minInput = $(this).siblings('.ea-range-min')[0];
					var maxInput = $(this).siblings('.ea-range-max')[0];
					var node = $(this).attr('ea_node');
					var slider = $(this).slider({
						range: true,
						min: Number(minRange),
						max: Number(maxRange),
						step: step,
						values: [Number(min), Number(max) ],
						change: function( e, ui ) {
							var val = self.buildRangeValue(self.rawNumber(ui.values[0],2),self.rawNumber(ui.values[1],2), minRange,maxRange,node);
							self.executeRangeAttr(attrName,val,node);
							e.preventDefault();
							return false;
//							window.console && console.log('range: ' + ui.values[0] + ' - ' + ui.values[1]);
						},
						create: function(){
							minInput.value = self.formatNumber(min,2);
							maxInput.value = self.formatNumber(max,2);
						},
						slide: function(event, ui){
							var min = ui.values[0];
							var max = ui.values[1];
							if (min <= max - step){
								minInput.value = self.formatNumber(min,2);
								maxInput.value = self.formatNumber(max,2);
							}
							else {
								return false;
							}
						}
					});
					$(minInput).keypress(function(e){
						if (13 == e.which){
							self.processRange(attrName,minInput,maxInput,minRange,maxRange,step,node);
							e.preventDefault();
							return false;
						}
					}).change(function(e){
						self.processRange(attrName,minInput,maxInput,minRange,maxRange,step,node);
						e.preventDefault();
						return false;
					})
					$(maxInput).keypress(function(e){
						if (13 == e.which){
							self.processRange(attrName,minInput,maxInput,minRange,maxRange,step,node);
							e.preventDefault();
							return false;
						}
					}).change(function(e){
						self.processRange(attrName,minInput,maxInput,minRange,maxRange,step,node);
						e.preventDefault();
						return false;
					});

				});
				//                   },100);
				$('.ea-color-swatch,.ea-size-swatch').hover(function(){
					$(this).addClass('ea-active');
				}, function(){
					$(this).removeClass('ea-active');
				});
			},
			processMessage: function(msg){
				if (msg){
					$('#ea-msg').html(msg).show();
				}
				else {
					$('#ea-msg').hide();
				}
			},
			processBC: function(bc){
				html = '';
				for(var i = 0; i < bc.length; i++){
					if (0 < i){
						html += '&nbsp;/&nbsp;';
					}
					var title = '';
					if (2 == bc[i].navNodePathType){ // attribute
						var segs = bc[i].path.split('////');
						var lastSeg = segs[segs.length-1];
						if (0 == lastSeg.indexOf('AttribSelect=')){
							title = lastSeg.substring('AttribSelect='.length,lastSeg.indexOf(' = '));
							if (title){
								title = '<span class="ea-attr-name">' + this.htmlEncode(title) + '</span>:&nbsp;';
							}
						}
					}
					html += ('<a class="ea-bc" ea-seo-path="' + this.htmlEncode(bc[i].seoPath || '') + '" ea_bc="' + this.htmlEncode(bc[i].path) + '" href="#">'+title+this.htmlEncode(bc[i].value)+'</a>');
				}
				$('#ea-bc').html(html);
			},

			insertBanners: function(banners){
				// clear out old banners
				$('.ea-banner-zone').empty().hide();
				if (banners){
					for(var i = 0; i < banners.length; i++){
						if (banners[i].zone){
							$('#' + banners[i].zone).append(banners[i].html).show();
						}
					}
				}
			},
			isNonZeroResults: function(src){
				return src && src.products && src.products.itemDescription && 0 < src.products.itemDescription.totalItems;
			},
			isQueryModified: function(src){
				return src && src.itemsFoundByModifyingQuery;
			},
			recordUserSearch: function(data){
				// records user search if 1) was search, 2) returned non-zero results, 3) did not modify query
				var src = (data || {}).source;
				if (this.isNonZeroResults(src) && !this.isQueryModified(src)){
					searchHistory && searchHistory.add(src.originalQuestion);
				}
			},
			ERROR_REDIRECT: 5,
			PRESENTATION_ERROR: -1,
			processResults: function(data,isSearch){
				if (searchHistory && isSearch){
					this.recordUserSearch(data);
				}
				// check for re-direct
				if (data.displayFormat && data.displayFormat.presentation == this.PRESENTATION_ERROR && data.displayFormat.error == this.ERROR_REDIRECT){
					window.location.replace(data.errorMsg);
				}
				var ph = $('#question').val('').attr('placeholder');
				if (!ph){
					$('#question:not(:focus)').blur();  // cause emulated
					// placeholder if
					// needed
				}
				if (!data){
					return;
				}
				var src = data.source;
				if (!src){
					return;
				}
				var bc = src.navPath.navPathNodeList;
				this.processMessage(src.message);
				this.processBC(bc);
				this.path = bc[bc.length-1].path;
				this.seoPath = bc[bc.length-1].seoPath || '';
				this.insertProducts(src.products);
				this.hookProductDetails();
				this.insertNoResults(src.noResults);
				this.hookNoResults();
				this.insertBanners(src.displayBanners);
				this.insertFeaturedProducts(src.products && !src.products.groups?src.featuredProducts:null);
				this.hookFeaturedItems();
				this.insertCarveOuts(src.carveOuts);
				this.hookCarveOutDetails();
				this.insertNavigation(src,bc);
				this.hookNav();
				$('img.ea-product-cell-image, img.ea-featured-product-cell-image, img.ea-carveout-cell-image').one('error',function(){
					$(this).addClass('image-not-found');
				});

				var cnt = 0;
				if (this.options.facetsExpanded){
					cnt = this.options.facetsExpanded;
				}
				else if (src.attributes && src.attributes.isInitDispLimited){
					// compute number of items to show expanded/collapsed
					cnt = src.attributes.initDispLimit;
					if (src.categories){
						cnt++;
					}
				}
				if (1 < bc.length){
					cnt++;
				}
				if (cnt){
					$('a .ea-nav-title').each(function(idx,val){
						if (idx >= cnt){
							$(val).click();
						}
					});
				}
			},

			htmlEncode: function (str) {
				return String(str || '')
				.replace(/&/g, '&amp;')
				.replace(/"/g, '&quot;')
				.replace(/'/g, '&#39;')
				.replace(/</g, '&lt;')
				.replace(/>/g, '&gt;');
			},

			encodeSearch: function(s){
				var sb = '';
				if (s && 0 < s.length){
					sb = '-';
					for(var i = 0; i < s.length; i++){
						var ch = s[i];
						if (/\s/.test(ch)){
							sb += '-';
						}
						else if ('/' == ch){
							sb += '@';  // encode slashes (path sep as atSign
						}
						else if ('@' == ch){
							sb += '\\@';  // escape the escape char
						}
						else if ('-' == ch){
							sb += '\\-';  // escape the escape char
						}
						else {
							sb += ch;
						}
					}
				}
				return sb;
			},

			invoke: function(url){
				var path = $.urlParam('CatPath',url);
				if (!path){
					path = $.urlParam('ea_path',url);
				}
				if (path){
					path = decodeURIComponent(path);
				}
				var q = $.urlParam('q',url);
				if (q){
					if (path){
						path += '/';
					}
					path += this.encodeSearch(decodeURIComponent(q));
				}
				var attr = $.urlParam('AttribSel',url);
				if (attr){ // old style
					path += ('&AttribSel=' + decodeURIComponent(attr));
				}
				href = window.location.origin + window.location.pathname;

				// history.pushState(url,null,href + '?dct=' + this.config.dct + (path?'&ea_path=' + path:''));
				window.console && console.log("EA Studio Invoke: " + url);
				this.executeCall(url);
			},
			isSearchRequest: function(url){
				return url && -1 < url.indexOf('RequestAction=advisor') && -1 < url.indexOf('&RequestData=CA_Search');
			},
			executeCall: function(url){
				window.console && console.log("EA Studio ExecuteCall: " + url);
				var self = this;
				var isSearch = self.isSearchRequest(url);
				var params = {
						url: url,
						type: 'POST',
						crossDomain: true,  // cross comain
						dataType: 'jsonp',  // handles cross domain
						success: function(data,textStatus, jqXHR){
							self.processResults(data,isSearch);
						},
						error: function(data, textStatus, jqXHR){
							console.log('EA Studio Error: ', data);
                            alert('error: ' + JSON.stringify(data));
						}
				};
				$.ajax(params);
			}
	}
	return EASearchDemo;
}
));
