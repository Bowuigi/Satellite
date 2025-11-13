function element(tag, container, classes, callback) {
	const elem = document.createElement(tag);
	elem.className = classes;
	callback(elem);
	container.appendChild(elem);
}

async function attachMenu() {
	const me = await fetch('/api/auth/me.php').then(r => r.json());
	if (!me.logged_in) return;

	// Menu
	let isMenuOpen = false;
	let menuElement;
	element('div', document.body, 'column surface elevation-4', menu => {
		menuElement = menu;

		menu.style.fontSize = '24pt';
		menu.style.position = 'fixed';
		menu.style.width = '15rem';
		menu.style.top = '1rem';
		menu.style.bottom = '1rem';
		menu.style.right = '5rem';
		menu.style.textAlign = 'center';
		menu.style.gap = 0;
		menu.style.zIndex = 90;
		menu.style.display = 'none';

		// Lista de usuarios
		element('button', menu, 'hfill p-1 surface elevation-1', users => {
			users.textContent = 'Lista de usuarios';
			users.style.fontSize = '1rem';
			users.style.borderBottom = '1px solid var(--color-border)';

			users.addEventListener('click', async() => {
				window.location.replace('/users.html');
			})
		});

		// Editar filtros
		element('button', menu, 'hfill p-1 surface elevation-1', edit => {
			edit.textContent = 'Editar filtros';
			edit.style.fontSize = '1rem';
			edit.style.borderBottom = '1px solid var(--color-border)';

			edit.addEventListener('click', async() => {
				window.location.replace('/filter.html');
			})
		});

		// Seleccionar filtro
		element('p', menu, 'hfill p-1 surface elevation-1', select => {
			select.textContent = '↓ Seleccionar filtro ↓';
			select.style.fontSize = '1rem';
			select.style.borderBottom = '1px solid var(--color-border)';
		});

		// Lista de filtros
		element('div', menu, 'flex-xl', filterList => {
			filterList.style.overflowY = 'scroll';
			filterList.style.fontSize = '1rem';

			for (filter of me.filters) {
				element('div', filterList, 'hfill row', container => {
					container.style.gap = 0;
					container.style.borderBottom = '1px solid var(--color-border)';

					// Usar filtro
					element('button', container, 'flex-xl p-1 surface elevation-1', it => {
						it.textContent = filter;
						it.style.fontSize = '1rem';
						it.addEventListener('click', () => window.location.href = `/index.html?filter=${filter}`);
					});

					// Borrar filtro
					element('button', container, 'p-1', it => {
						it.textContent = 'X';
						it.style.backgroundColor = '#e60000';
						it.style.color = 'white';
						it.style.fontWeight = 700;
						it.style.width = '2rem';
						it.style.fontSize = '1rem';
						it.addEventListener('click', async() => {
							if (!confirm(`¿Confirma el borrado del filtro '${filter}'?`)) return;
							await fetch('/api/filters/remove.php', {
								method: 'POST',
								headers: {
									'Content-Type': 'application/json',
								},
								body: JSON.stringify({name: filter}),
							});
							filterList.removeChild(container);
						});
					})
				});
			}
		});

		// Cerrar sesión
		element('button', menu, 'hfill p-1', logout => {
			logout.textContent = 'Cerrar sesión';
			logout.style.backgroundColor = '#e60000';
			logout.style.color = 'white';
			logout.style.fontWeight = 700;

			logout.addEventListener('click', async() => {
				await fetch('/api/auth/logout.php', { method: 'POST' });
				window.location.replace('/login.html');
			})
		})
	})

	// Abrir menú
	element('div', document.body, 'row', container => {
		container.style.width = 'fit-content';
		element('button', container, 'p-1 elevation-4 round-1 accent-dark accent-dark-hover', btn => {
			btn.type = 'button';
			btn.textContent = "≡";
			btn.style.fontSize = '24pt';
			btn.style.position = 'fixed';
			btn.style.width = '3.2rem';
			btn.style.top = '1rem';
			btn.style.right = '1rem';
			btn.addEventListener('click', () => {
				isMenuOpen = !isMenuOpen;
				if (isMenuOpen) {
					menuElement.style.display = '';
				} else {
					menuElement.style.display = 'none';
				}
			});
		});
	})
}

attachMenu();
