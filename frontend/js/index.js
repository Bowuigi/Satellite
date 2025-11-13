const urlParams = new URLSearchParams(window.location.search);
const dom = {
	base: document.getElementById('base'),
};

function element(tag, container, classes, callback) {
	const elem = document.createElement(tag);
	elem.className = classes;
	callback(elem);
	container.appendChild(elem);
}

function renderPost({id, author, posted_at, content, score, stance}) {
	const container = document.createElement('div');
	container.className = "post";

	element('div', container, 'hfill row', top => {
		top.style.marginBottom = '0.2rem';
		element('span', top, 'text-bold', it => { it.textContent = author; })
		element('span', top, '', it => { it.textContent = `${posted_at} UTC`; })
	});

	element('p', container, 'hfill', text => {
		text.textContent = content;
	});

	element('div', container, 'hfill row', bot => {
		bot.style.marginTop = '0.5rem';
		bot.style.alignItems = 'center';

		let newScore = score;
		let scoreElem = null;
		let upElem = null;
		let downElem = null;

		element('button', bot, 'text-bold', it => {
			it.textContent = '↑';
			it.style.fontSize = '14pt';
			it.style.width = '1.7rem';
			it.style.height = '1.7rem';
			it.style.textAlign = 'center';
			it.style.verticalAlign = 'center';
			it.className = `round-1 ${stance === 'up' ? 'accent-dark accent-dark-hover' : 'accent-muted accent-muted-hover'}`;
			upElem = it;
			it.addEventListener('click', async() => {
				switch (stance) {
					case 'up':
						stance = 'none';
						newScore -= 1;
						break;
					case 'down':
						stance = 'up';
						newScore += 2;
						break;
					case 'none':
						stance = 'up';
						newScore += 1;
						break;
				}
				scoreElem.textContent = newScore;
				upElem.className = `round-1 ${stance === 'up' ? 'accent-dark accent-dark-hover' : 'accent-muted accent-muted-hover'}`;
				downElem.className = `round-1 ${stance === 'down' ? 'accent-dark accent-dark-hover' : 'accent-muted accent-muted-hover'}`;

				await fetch('/api/posts/vote.php', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						post: id,
						stance: stance,
					}),
				});
			});
		})
		element('span', bot, 'text-bold', it => {
			it.textContent = score;
			scoreElem = it;
		})
		element('button', bot, 'text-bold', it => {
			it.textContent = '↓';
			it.style.fontSize = '14pt';
			it.style.width = '1.8rem';
			it.style.height = '1.8rem';
			it.style.textAlign = 'center';
			it.style.verticalAlign = 'middle';
			it.className = `round-1 ${stance === 'down' ? 'accent-dark accent-dark-hover' : 'accent-muted accent-muted-hover'}`;
			downElem = it;
			it.addEventListener('click', async() => {
				switch (stance) {
					case 'up':
						stance = 'down';
						newScore -= 2;
						break;
					case 'down':
						stance = 'none';
						newScore += 1;
						break;
					case 'none':
						stance = 'down';
						newScore -= 1;
						break;
				}
				scoreElem.textContent = newScore;
				upElem.className = `round-1 ${stance === 'up' ? 'accent-dark accent-dark-hover' : 'accent-muted accent-muted-hover'}`;
				downElem.className = `round-1 ${stance === 'down' ? 'accent-dark accent-dark-hover' : 'accent-muted accent-muted-hover'}`;

				await fetch('/api/posts/vote.php', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						post: id,
						stance: stance,
					}),
				});
			});
		})
		element('button', bot, 'text-bold', it => {
			it.textContent = 'Responder';
			it.className = 'accent-muted round-1';
			it.style.padding = '0.30rem';
			it.addEventListener('click', () => window.location.href = `/post.html?parent=${id}`);
		})
	});

	element('button', container, 'p-1 link-like', loader => {
		loader.textContent = '+ Cargar respuestas';
		loader.addEventListener('click', async() => {
			const children = await fetch(`/api/view.php?parent=${id}`).then(r => r.json());
			container.append(renderThread(children));
			container.removeChild(loader);
		});
	})

	return container;
}

function renderThread(posts) {
	const container = document.createElement('div');
	container.className = "thread";

	for (const post of posts.children) {
		container.appendChild(renderPost({
			id: post.id,
			author: post.author,
			posted_at: post.posted_at,
			content: post.content,
			score: post.score,
			stance: post.stance
		}));
	}
	
	return container;
}

async function firstRender() {
	const url = urlParams.has('filter') ? `/api/view.php?filter=${urlParams.get('filter')}` : '/api/view.php'
	const posts = await fetch(url).then(r => r.json());
	const rendered = renderThread(posts);
	dom.base.appendChild(rendered);
}

firstRender();
