(function(factory){
	if (typeof define === 'function' && define.amd){
		define('hb-helpers',['handlebars'],factory);
	}
	else {
		factory(Handlebars);
	}
}(function(HB) {
	HB.registerHelper('ifCond', function (v1, operator, v2, options) {
		switch (operator) {
		case '==':
			return (v1 == v2) ? options.fn(this) : options.inverse(this);
		case '===':
			return (v1 === v2) ? options.fn(this) : options.inverse(this);
		case '!=':
			return (v1 != v2) ? options.fn(this) : options.inverse(this);
		case '!==':
			return (v1 !== v2) ? options.fn(this) : options.inverse(this);
		case '<':
			return (v1 < v2) ? options.fn(this) : options.inverse(this);
		case '<=':
			return (v1 <= v2) ? options.fn(this) : options.inverse(this);
		case '>':
			return (v1 > v2) ? options.fn(this) : options.inverse(this);
		case '>=':
			return (v1 >= v2) ? options.fn(this) : options.inverse(this);
		case '&&':
			return (v1 && v2) ? options.fn(this) : options.inverse(this);
		case '||':
			return (v1 || v2) ? options.fn(this) : options.inverse(this);
		default:
			return options.inverse(this);
		}
	});
	
	HB.registerHelper('math',function(v1, operator, v2, options){
		v1 = parseFloat(v1);
		v2 = parseFloat(v2);
		
		switch (operator) {
		case '+': return v1 + v2;
		case '-': return v1 - v2;
		case '/': return v1 / v2;
		case '*': return v1 * v2;
		}
	});
	// display helpers for studion store and sayt
	HB.registerHelper('colorToClass',function(clr){
		return 'ea-color-' + clr.toLowerCase().replace(/[\/&]/,"-");
	});
	HB.registerHelper('ratingTitle',function(val,range){
		return 'Average rating ' + parseFloat(val).toFixed(1) + ' out of ' + range.toFixed(1);
	});
	HB.registerHelper('starPosition',function(val){
		return -1 * (36 * val -1);
	});
	HB.registerHelper('ifLookup',function(obj,field,options){
		return obj[field]?options.fn(this):options.inverse(this);
	});
	HB.registerHelper('stars', function(a) {		
		return -1 < a.indexOf('.')?a.replace('.','-'):(a+'-0');
	});

	HB.registerHelper("debug", function(optionalValue) {
		console.log("Current Context");
		console.log("====================");
		console.log(this);

		if (optionalValue) {
			console.log("Value");
			console.log("====================");
			console.log(optionalValue);
		}
	});
}
));