/* --- Login and signup toggling --- */
const dom = {
	username: document.getElementById('ls-username'),
	usernameMessage: document.getElementById('ls-username-msg'),
	password: document.getElementById('ls-password'),
	passwordMessage: document.getElementById('ls-password-msg'),
	toggle: document.getElementById('ls-toggle'),
	title: document.getElementById('ls-title'),
	next: document.getElementById('ls-next'),
	responseMessage: document.getElementById('ls-response-msg'),
};

let isLogin = true;
const isLoginMessages = {
	[true]: {
		title: "Iniciar sesión",
		toggle: "¿No está registrado? Haga click aquí",
		next: "Iniciar sesión",
		responseMessage: "",
	},
	[false]: {
		title: "Crear una cuenta",
		toggle: "¿Ya tiene una cuenta? Haga click aquí",
		next: "Registrarse",
		responseMessage: "",
	},
};

// Initial message
dom.responseMessage.style.display = 'none';
for ([elem, msg] of Object.entries(isLoginMessages[isLogin])) {
	dom[elem].textContent = msg;
}

// Swap message on click
dom.toggle.addEventListener('click', () => {
	isLogin = !isLogin;

	dom.responseMessage.style.display = 'none';
	for ([elem, msg] of Object.entries(isLoginMessages[isLogin])) {
		dom[elem].textContent = msg;
	}
});

/* --- API calling --- */
async function performRequest(username, password) {
	const verb = isLogin ? 'login' : 'signup';

	const result = await fetch(`/api/auth/${verb}.php`, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
		},
		body: JSON.stringify({username, password}),
	});

	return (result.ok ? null : await result.text());
}

new ValidatedInput({
	submit: dom.next,
	fields: {
		username: {
			element: dom.username,
			hintElement: dom.usernameMessage,
			condition: v => /^[a-z0-9\-_\\.]{1,50}$/.test(v),
			hint: 'Solo se admiten entre 1 y 50 letras minúsculas (sin ñ), números y los símbolos "-", "_" y "."',
		},
		password: {
			element: dom.password,
			hintElement: dom.passwordMessage,
			condition: value => value.length >= 6 && value.length <= 100,
			hint: 'Debe tener entre 6 y 100 caracteres',
		}
	},
	onSubmit: async() => {
		const result = await performRequest(dom.username.value, dom.password.value);
		if (result) {
			dom.responseMessage.textContent = result;
			dom.responseMessage.style.display = '';
		} else {
			window.location.replace('/index.html');
		}
	}
});
