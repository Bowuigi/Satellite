const urlParams = new URLSearchParams(window.location.search);

const dom = {
	title: document.getElementById('post-title'),
	content: document.getElementById('post-content'),
	contentMessage: document.getElementById('post-content-msg'),
	next: document.getElementById('post-next'),
	responseMessage: document.getElementById('post-response-msg'),
};

dom.responseMessage.style.visibility = 'hidden';

if (urlParams.has('parent')) {
	dom.title.textContent = 'Nueva respuesta';
} else {
	dom.title.textContent = 'Nueva publicación';
}

async function performRequest(content) {
	const result = await fetch(`/api/posts/new.php`, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
		},
		body: JSON.stringify({
			parent: urlParams.has('parent') ? urlParams.get('parent') : null,
			content
		}),
	});

	return (result.ok ? null : await result.text());
}

new ValidatedInput({
	submit: dom.next,
	fields: {
		content: {
			element: dom.content,
			hintElement: dom.contentMessage,
			condition: v => v.length >= 1 && v.length <= 5000,
			hint: 'Debe tener entre 1 y 5000 caracteres'
		}
	},
	onSubmit: async() => {
		const result = await performRequest(dom.content.value);
		if (result) {
			dom.responseMessage.textContent = result;
			dom.responseMessage.style.visibility = 'visible';
		} else {
			window.location.replace('/index.html');
		}
	}
});
