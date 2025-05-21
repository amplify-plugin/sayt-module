(function(factory){
	if (typeof define === 'function' && define.amd){
		define('store-hb-partials',['handlebars'],factory);
	}
	else {
		factory(Handlebars);
	}
}(function(HB) {
	HB.registerPartial('attr-vals',
		'{{#if singleValued}}'+
			'<li class="ea-nav-value">'+
				'<a class="ea-attr ea-nav-value-link" seo_path="{{seoPath}}" ea_attr="{{name}}" ea_val="{{attributeValue}}" href="#">{{attributeValue}}{{#ifCond productCount ">" 0}} ({{productCount}}){{/ifCond}}</a>'+
			'</li>'+
		'{{#else}}'+
			'<li class="ea-nav-value ea-attr">'+
				'<input class="ea-attr" type="checkbox"{{#if selected}} checked{{/if}} ea_attr="{{name}}" ea_val="{{attributeValue}}">'+
				'<a class="ea-attr ea-nav-value-link" seo_path="{{seoPath}}" ea_attr="{{name}}" ea_val="{{attributeValue}}" href="#">{{attributeValue}}{{#ifCond productCount ">" 0}} ({{productCount}}){{/ifCond}}</a>'+
			'</li>'+
		'{{/if}}');
})
);
