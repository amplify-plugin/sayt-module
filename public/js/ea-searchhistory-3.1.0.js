/**
 * @preserve Copyright (c) 2000-2017 EasyAsk Technologies, Inc.  All Rights Reserved.
 * Use, reproduction, transfer, publication or disclosure is prohibited 
 * except in accordance with the License Agreement.
 */

(function(factory){
    if (typeof define === 'function' && define.amd){
       define('ea-search-history',[],factory);
    }
    else {
       factory();
    }
}(function( ) {
    EASearchHistory = {
        searchHistoryKey: 'ea-sh',
        searchHistorySize: 100,
        getAll: function(){
            var searches = (localStorage && JSON && JSON.parse(localStorage.getItem(this.searchHistoryKey)) || "[]");
            if (!Array.isArray(searches)){
                searches = [];
            }
            return searches;
        },
        add: function(term){
            if (localStorage && JSON && term){
                var existing = this.getAll();
                // see if it's there an move to end
                var lc = term.toLowerCase();
                for(var i = 0; i < existing.length; i++){
                    if (existing[i].toLowerCase() == lc){
                        existing.splice(i,1);
                        break;
                    }
                }
                existing.unshift(term);
                if (this.searchHistorySize && this.searchHistorySize < existing.length ){
                    existing.splice(1,existing.length - this.searchHistorySize);
                }
                localStorage.setItem(this.searchHistoryKey,JSON.stringify(existing));
            }
        },
        clear: function(){
            localStorage && localStorage.removeItem(this.searchHistoryKey);}
    };
    return EASearchHistory;
}));
