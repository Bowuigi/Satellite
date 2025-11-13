const dom = {
	filter: document.getElementById('fce-filter'),
	filterMessage: document.getElementById('fce-filter-msg'),
	condition: document.getElementById('fce-condition'),
	conditionMessage: document.getElementById('fce-condition-msg'),
	sortOrder: document.getElementById('fce-sort-order'),
	next: document.getElementById('fce-next'),
	responseMessage: document.getElementById('fce-response-msg'),
};

dom.responseMessage.style.visibility = 'hidden';

// Very basic validation. The rest is handled by the server
function validateCondition(value) {
	let json;
	try { json = JSON.parse(value); } catch { return false; }
	return (Array.isArray(json) && json.every(v => !Array.isArray(v)));
}

async function performRequest(filter, condition, sortOrder) {
	const result = await fetch('/api/filters/edit.php', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
		},
		body: JSON.stringify({
			name: filter,
			sort_by: sortOrder,
			condition: JSON.parse(condition),
		}),
	});

	return (result.ok ? null : await result.text());
}

new ValidatedInput({
	submit: dom.next,
	fields: {
		filter: {
			element: dom.filter,
			hintElement: dom.filterMessage,
			condition: v => /^[a-z0-9\-_\\.]{1,50}$/.test(v),
			hint: 'Solo se admiten entre 1 y 50 letras minúsculas (sin ñ), números y los símbolos "-", "_" y "."',
		},
		condition: {
			element: dom.condition,
			hintElement: dom.conditionMessage,
			condition: validateCondition,
			hint: 'Debe ser un array de objetos JSON',
		}
	},
	onSubmit: async() => {
		const result = await performRequest(dom.filter.value, dom.condition.value, dom.sortOrder.value);
		if (result) {
			dom.responseMessage.textContent = result;
			dom.responseMessage.style.visibility = 'visible';
		} else {
			window.location.replace('/index.html');
		}
	}
});
