function formatValue(element, value, field, fieldMeta) {


	var plainText = value;
	var links = JSTextUtilities.ParseLinks(plainText).concat(JSTextUtilities.ParseUrls(plainText));


	var formattedText = JSTextUtilities.ReplaceParseResults(plainText, function(link) {



		return '<a href="' + link.url + '" target="_blank" class="">' +
			(link.type === 'url' ? link.url : link.text) +
			'</a>';


	}, links, { /*opts*/ });

	element.innerHTML = formattedText;
}