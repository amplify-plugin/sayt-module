/**
 * @preserve Copyright (c) 2000-2018 EasyAsk Technologies, Inc.  All Rights Reserved.
 * Use, reproduction, transfer, publication or disclosure is prohibited
 * except in accordance with the License Agreement.
 * @version 4.0.0
 * @reference indexed=1&ie=UTF-8&disp=json&RequestAction=advisor&RequestData=CA_Search&CatPath='+AMPLIFY_SAYT_CAT_PATH+'&defarrangeby=////NONE////
 */
(function (factory) {
    if (typeof define === 'function' && define.amd) {
        define('ea-sayt', ['jquery', 'handlebars', 'ea-search-history', 'hb-helpers'], factory);
    } else {
        factory(window.$ea || window.eaj$183 || jQuery, null, window.EASearchHistory || null, null);
    }
}(function ($, HB, searchHistory, _undefined) {
        var pluginId = "ea_sayt";
        var sayt = function (selector, options) {
            this.pluginId = pluginId;
            this.selector = selector;
            this.$element = $(selector);
            this.$input = this.$element.find('input');
            this.options = options;
        }
        sayt.prototype = {
            defaults: {
                defaultImage: './vendor/sayt/images/no-image-placeholder.png',
                sort: 'weight',
                reduce: 'cluster',
                matchAllSearchWords: true,
                matchAnySuggestionWord: true,
                currentSite: '',

                template: './templates/leftprod.hbs',
                userSuggestions: {
                    size: 8
                },
                submitFctn: function () {
                },
                navClass: 'ea-sug-nav-link',
                dataPath: 'eapath',
                suggestionClass: 'ea-search-suggestion',
                sectionAreaClass: 'ea-sug-area',
                delay: 200,
                minLength: 3,
                server: '',
                url: '/EasyAsk/AutoComplete-3.0.0.jsp',
                defaultSearchParams: {},
                serverSearch: '',
                urlSearch: '/EasyAsk/apps/Advisor.jsp',
                search: {
                    size: 8
                },
                products: {
                    size: 3,
                    value: function (item, field) {
                        return item[field];
                    },
                    fields: {
                        id: 'Product_Id',
                        thumbnail: 'picture',
                        name: 'Product_Name',
                        description: 'Description',
                        price: 'Price',
                        mfr: 'Manufacturer'
                    }
                },
                navigation: {
                    size: 2,
                    sections: [{
                        type: 'category',
                        size: 5,
                        title: 'Category'
                    }, {
                        type: 'COLOR',
                        size: 4,
                        title: 'Color'
                    }, {
                        type: 'SIZE',
                        size: 3,
                        title: 'Size'
                    }]
                },
                pageInfo: 'EA_PAGE_INFO',
                hideOnBlur: true

            },
            ENTER: 13,
            ARROW_DOWN: 40,
            ARROW_UP: 38,

            init: function () {
                var self = this;
                this.$input.attr({
                    spellcheck: 'false',
                    autocomplete: 'off'
                });
                self.config = $.extend(true, {}, self.defaults, self.options);
                if (self.config.sessionid) {
                    self.config.defaultSearchParams.sessionid = self.config.sessionid;
                } else {
                    self.config.defaultSearchParams.oneshot = '1';
                }

                self.build();
                self.loadAndCompile().done(function (obj) {
                    self.initEvents();
                });
                this.$txtNode = $('<span style="display:none;"></span>'); // for removing htmls
                var pageInfo = window[self.config.pageInfo] || {};
                if (pageInfo.userSearch) {
                    self.addUserSuggestion(pageInfo.userSearch);
                }
                return self;
            },

            close: function (clearInput) {
                this.$suggestionContainer.hide();
                if (clearInput) {
                    this.$input.val('');
                }
            },

            reposition: function () {
                var self = this;
                var width = self.$element.outerWidth();
                var sugWidth = self.$suggestionContainer.children().first().outerWidth();
                var left = 0;
                if (self.config.horizAlign == 'right') {
                    left = width - sugWidth - 1;
                } else if (self.config.horizAlign == 'center') {
                    left = (width - sugWidth - 1) / 2;
                }
                left += (self.config.xOffset || 0);
                self.$suggestionContainer.css('left', left + 'px');
            },

            build: function () {
                var self = this;
                var height = self.$element.outerHeight() + (self.config.yOffset || 0);
                self.$suggestionContainer = $('<div class="ea-autocomplete-wrapper" style="left:0px;top:' + height + 'px;"><div>');
                self.$element.append(self.$suggestionContainer);
            },

            loadAndCompile: function () {
                var self = this;
                var res = $.Deferred();
                require(['text!' + self.config.template], function (template) {
                    self.compiled = HB.compile(template, {
                        noEscape: true
                    });
                    res.resolve();
                })
                return res.promise();
            },

            initEvents: function () {
                var self = this;

                self.$input.off("keydown").on("keydown", function (event) {
                    self.handleKeyDown(event);
                });
                self.$input.blur(function (event) {
                    if (self.cancelBlur) {
                        delete self.cancelBlur;
                        return;
                    }
                    if (self.config.hideOnBlur) {
                        self.$suggestionContainer.hide();
                    }
                }).focus(function (event) {
                    self.processInput();
                });
                if (!self.config.sectionAreaClass) {
                    self.$suggestionContainer.on("mousedown", function (event) {
                        self.cancelBlur = true;
                        self.$suggestionContainer.delay(function () {
                            delete self.cancelBlur;
                        });
                    })
                }
            },

            arrowKey: function (event) {
                var curr = this.$suggestionContainer.find('.ea-search-suggestion');
                if (curr.length) {
                    var inputVal = null;
                    var currOffset = -1;
                    for (var i = 0; i < curr.length; i++) {
                        if ($(curr[i]).hasClass('ea-selected')) {
                            currOffset = i;
                            $(curr[i]).removeClass('ea-selected');
                            break;
                        }
                    }
                    var self = this;
                    var val = self.getCurrentSuggests();
                    if (event.keyCode == this.ARROW_UP) {
                        if (-1 == currOffset) {
                            currOffset = curr.length - 1;
                        } else {
                            currOffset--;
                            if (currOffset < 0) {
                                inputVal = val.input;
                            }
                        }
                    } else if (event.keyCode == this.ARROW_DOWN) {
                        if (-1 == currOffset) {
                            currOffset = 0;
                        } else {
                            currOffset++;
                            if (currOffset >= curr.length) {
                                inputVal = val.input;
                            }
                        }
                    }
                    if (inputVal) {// restore to typed in
                        self.$input.val(inputVal);
                    } else {
                        inputVal = val.suggests[currOffset].sug;
                        self.$input.val(inputVal);
                        self.getResults(currOffset);
                    }
                }
                event.preventDefault();
                return false;
            },

            // facet helper to locate an attribute in the results
            findAttribute: function (res, name) {
                var attributes = (res.source.attributes && res.source.attributes.attribute) || [];
                if (attributes.length) {
                    for (var i = 0; i < attributes.length; i++) {
                        if (attributes[i].name == name) {
                            // only if the attribute is not a slider
                            return attributes[i].attributeValueList[0].valueType == 1 ? attributes[i] : null;
                        }
                    }
                }
                return null;
            },

            // extract appropriate facet information
            createFacet: function (section, res, currentSuggest) {
                if (section.type.toLowerCase() == 'category') {
                    var categories = (res.source.categories && res.source.categories.categoryList) || [];
                    if (categories.length) {
                        var result = {
                            title: section.title,
                            count: categories.length,
                            vals: []
                        };
                        for (var i = 0; i < categories.length && i < section.size; i++) {
                            result.vals.push({
                                name: categories[i].name,
                                productCount: categories[i].productCount,
                                path: categories[i].seoPath
                            });
                        }
                        return result;
                    }
                } else {
                    var attribute = this.findAttribute(res, section.type);
                    if (attribute) {
                        var attrVals = attribute.attributeValueList;
                        var result = {
                            title: section.title,
                            count: attrVals.length,
                            vals: []
                        };
                        for (var i = 0; i < attrVals.length && i < section.size; i++) {
                            result.vals.push({
                                name: attrVals[i].attributeValue,
                                productCount: attrVals[i].productCount,
                                path: attrVals[i].seoPath
                            });
                        }
                        return result;
                    }
                }
                return null
            },

            // extract (simplify) facet information based on the restrictions in the configuration
            createFacets: function (res, currentSuggest) {
                var result = [];
                if (res.source && res.source.products && res.source.products.items) { // only if products
                    var navdef = this.config.navigation;
                    if (navdef && (res.source.categories || res.source.attributes)) {
                        var limit = navdef.size;
                        for (var i = 0; i < navdef.sections.length && result.length < limit; i++) {
                            var section = navdef.sections[i];
                            var facet = this.createFacet(section, res, currentSuggest);
                            if (facet) {
                                result.push(facet);
                            }
                        }
                    }
                }
                return result;
            },

            textOnly: function (val) {
                if (val) {
                    try {
                        return this.$txtNode.html(val).text();
                    } catch (err) {
                    }
                }
                return val;
            },

            trimString: function (val, len) {
                if (val && len) {
                    if (val.length < len) {
                        return val;
                    } else {
                        var sub = val.substring(0, len);
                        var idx = sub.lastIndexOf(' ');
                        if (-1 < idx) {
                            sub = sub.substring(0, idx);
                        }
                        sub += '...';
                        return sub;
                    }
                }
                return val;
            },

            parseCurrency: function (value) {
                if (typeof value !== 'string') return value;

                let cleaned = value.replace(/[^0-9.,-]/g, '').trim();

                cleaned = (cleaned.match(/^\d{1,3}(\.\d{3})*,\d+$/))
                    ? cleaned.replace(/\./g, '').replace(',', '.')
                    : cleaned.replace(/,/g, '');

                const num = parseFloat(cleaned);

                return Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: AMPLIFY_BASE_CURRENCY || 'USD'
                }).format(isNaN(num) ? null : num);
            },

            parseImagePath: function (productImage) {
                if (productImage) {
                    return productImage;
                }
                return this.config.defaultImage;
            },

            createCanonicalItem: function (item) {
                var fields = this.config.products.fields;
                var sizes = this.config.products.sizes;
                var value = this.config.products.value;
                var canonicalItem = {}
                for (let field in fields) {
                    if (fields.hasOwnProperty(field)) {
                        if (sizes[field]) {
                            canonicalItem[field] = this.trimString(this.textOnly(value(item, fields[field])), sizes[field]);
                        } else if (field === 'price') {
                            canonicalItem[field] = this.parseCurrency(value(item, fields[field]));
                        } else if (['image', 'thumbnail'].includes(field)) {
                            canonicalItem[field] = this.parseImagePath(value(item, fields[field]));
                        } else {
                            canonicalItem[field] = value(item, fields[field]);
                        }
                    }
                }
                return canonicalItem;
            },

            hookSuggestions: function () {
                var self = this;
                self.$suggestionContainer.find('.' + self.config.suggestionClass).each(function (idx, elt) {
                    $(elt).click(function (event) {
                        self.$suggestionContainer.hide();
                        var search = $(elt).text().trim();
                        self.$input.val(search);
                        self.config.submitFctn('search', search, elt);
                        event.preventDefault();
                        return false;
                    });
                });
            },
            // returns a deferred that can be used to wait on for post results processing
            getResults: function (currOffset) {
                var self = this;
                var suggests = self.getCurrentSuggests();
                var def = $.Deferred();
                var currentSuggest = suggests.suggests[currOffset].sug;
                $.when(self.search(currentSuggest).done(function (res) {
                    var facets = self.createFacets(res, currentSuggest);
                    var args = {
                        offset: currOffset,
                        sug: suggests,
                        prod: res,
                        facets: facets,
                        fields: self.config.products.fields
                    };
                    if (res && res.source && res.source.products && res.source.products.items) {
                        var canonicalItems = [];
                        for (var i = 0; i < res.source.products.items.length; i++) {
                            canonicalItems.push(self.createCanonicalItem(res.source.products.items[i]));
                        }
                        args.canonicalItems = canonicalItems;
                    }

                    var html = self.compiled(args);
                    self.$suggestionContainer.html(html).show();
                    if (self.config.sectionAreaClass) {
                        self.$suggestionContainer.find('.' + self.config.sectionAreaClass).on("mousedown", function (event) {
                            self.cancelBlur = true;
                            self.$suggestionContainer.delay(function () {
                                delete self.cancelBlur;
                            });
                        });
                    }
                    self.reposition();
                    self.$suggestionContainer.find('.' + self.config.navClass).each(function (idx, elt) {
                        $(elt).click(function (event) {
                            self.$suggestionContainer.hide();
                            self.config.submitFctn('nav', $(elt).data(self.config.dataPath), elt);
                            event.preventDefault();
                            return false;
                        });
                    });
                    self.hookSuggestions();
                    def.resolve();
                }));
                return def.promise();
            },

            setCurrentSuggests: function (val) {
                this.currentSuggests = val;
            },

            getCurrentSuggests: function () {
                return this.currentSuggests;
            },

            processInput: function () {
                var self = this
                var x = self.$input.val();
                if (x && x.length >= self.config.minLength) {
                    $.when(self.suggest(x)).done(function (val) {
                        self.setCurrentSuggests(val); // remember it
                        if (val.suggests && val.suggests.length) {
                            self.getResults(0); // drive results from first suggestion
                        } else { // no suggestions
                            self.$suggestionContainer.hide();
                        }
                    });
                } else if (self.config.userSuggestions) {
                    var suggests = self.getUserSuggestions(x, self.config.userSuggestions.size || 5);
                    if (suggests && suggests.length) {
                        var sug = {
                            offset: -1,
                            suggests: suggests
                        };
                        var html = self.compiled({sug: sug});
                        self.setCurrentSuggests(sug);
                        self.$suggestionContainer.html(html).show();
                        self.reposition(sug);
                        if (self.config.sectionAreaClass) {
                            self.$suggestionContainer.find('.' + self.config.sectionAreaClass).on("mousedown", function (event) {
                                self.cancelBlur = true;
                                self.$suggestionContainer.delay(function () {
                                    delete self.cancelBlur;
                                });
                            });
                        }
                        self.hookSuggestions();
                    } else {
                        self.$suggestionContainer.hide();
                    }
                } else { // empty input
                    self.$suggestionContainer.hide();
                }

            },

            handleKeyDown: function (event) {
                var self = this;
                clearTimeout(self.timer);
                if (self.ENTER == event.keyCode) {
                    self.$suggestionContainer.hide();
                    self.config.submitFctn('search', self.$input.val(), self.$input);
                    event.preventDefault();
                    return false;
                } else if (self.ARROW_UP == event.keyCode || self.ARROW_DOWN == event.keyCode) {
                    return self.arrowKey(event);
                }
                self.timer = setTimeout(function () {
                    var x = self.$input.val();
                    if (x && x.length >= self.config.minLength) {
                        $.when(self.suggest(x)).done(function (val) {
                            self.setCurrentSuggests(val); // remember it
                            if (val.suggests && val.suggests.length) {
                                self.getResults(0); // drive results from first suggestion
                            } else { // no suggestions
                                self.$suggestionContainer.hide();
                            }
                        });
                    } else if (self.config.userSuggestions) {
                        var suggests = self.getUserSuggestions(x, self.config.userSuggestions.size || 5);
                        if (suggests && suggests.length) {
                            var sug = {
                                offset: -1,
                                suggests: suggests
                            };
                            var html = self.compiled({sug: sug});
                            self.setCurrentSuggests(sug);
                            self.$suggestionContainer.html(html).show();
                            self.reposition(sug);
                        } else {
                            self.$suggestionContainer.hide();
                        }
                    } else { // empty input
                        self.$suggestionContainer.hide();
                    }
                }, self.config.delay);
            },

            suggest: function (val) {
//    			window.console && console.log('generate suggestions on: "' + val +'"');
                var self = this;
                var def = $.Deferred();
                $.ajax({
                    url: self.config.server + self.config.url,
                    dataType: "jsonp",
                    data: {
                        dct: self.config.dict,
                        num: self.config.search.size,
                        key: self.$input.val(),
                        sort: self.config.sort,
                        reduce: self.config.reduce,
                        match: self.config.matchAllSearchWords,
                        anchor: self.config.matchAnySuggestionWord,
                        site: self.config.currentSite
                    },
                    success: function (data) {
                        // transform to new format
                        var res = {};
                        res.input = data.input;

                        if (data.suggests && data.suggests.length) {
                            res.suggests = [];
                            for (var i = 0; i < data.suggests.length; i++) {
                                var suggest = data.suggests[i];
                                res.suggests.push({
                                    sug: suggest.val,
                                    lhs: suggest.val.substring(0, suggest.start),
                                    hit: suggest.val.substring(suggest.start, suggest.end),
                                    rhs: suggest.val.substring(suggest.end)
                                });
                            }
                        }
                        def.resolve(res);
                    }
                });
                return def.promise();

            },

            search: function (q) {
                var self = this;
                var def = $.Deferred();

                self.config.defaultSearchParams.q = q;
                self.config.defaultSearchParams.ResultsPerPage = self.config.products.size;
                self.config.defaultSearchParams.dct = this.config.dict;
                self.config.defaultSearchParams.customer = 'easayt';

                $.ajax({
                    url: self.config.serverSearch + self.config.urlSearch,
                    dataType: 'jsonp',
                    data: self.config.defaultSearchParams,
                    success: function (res) {
                        def.resolve(res);
                    },
                    failure: function (data) {
                        self.pendingProds = false;
                        self.prods = null;
                        self.addSearch(s);
                    }
                });
                return def.promise();
            },

            getUserSuggestions: function (term, maxEntries) {
                var searches = (searchHistory && searchHistory.getAll()) || [];
                var result = [];
                if (term) { // could be empty
                    for (var i = 0; i < searches.length && result.length < maxEntries; i++) {
                        if (0 == searches[i].toLowerCase().indexOf(term)) { // begins with
                            result.push({
                                hit: searches[i].substring(0, term.length),
                                rhs: searches[i].substring(term.length),
                                sug: searches[i]
                            });
                        }
                    }
                }
                // see if you need to add others
                for (var i = 0; i < searches.length && result.length < maxEntries; i++) {
                    var found = false;
                    for (var j = 0; j < result.length; j++) {
                        if (searches[i] == result[j].sug) {
                            found = true;
                            break;
                        }
                    }
                    if (!found) {
                        result.push({
                            sug: searches[i],
                            rhs: searches[i]
                        });
                    }
                }
                return result;
            },

            addUserSuggestion: function (sug) {
                if (sug && searchHistory) {
                    searchHistory.add(sug);
                }
            },

            clearUserSuggestions: function () {
                if (searchHistory) {
                    searchHistory.clear();
                }
            }

        };

        sayt.defaults = sayt.prototype.defaults;
        $.fn.eaAutoComplete = function (options) {
            return this.each(function (elt) {
                var inst = $(this).data(pluginId);
                if (!inst) {
                    var inst = new sayt(this, options).init();
                    $(this).data(pluginId, inst);
                }
            });
        }
        return sayt;
    }
));
