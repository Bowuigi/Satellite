const dom = {
	userList: document.getElementById('user-list'),
}

// A small EDSL to create nested <details> elements with support for function-generated data
// type Data = Array<{
//   title: string;
//   children: (() => Record<string, string | Data>) | Record<string, string | Data>;
// }>;
function createDetailsTree(data, container) {
	for (const item of data) {
		const details = document.createElement('details');

		const summary = document.createElement('summary');
		summary.textContent = item.title;
		details.appendChild(summary);

		const renderChildren = children => {
			for (const value of Object.values(children)) {
				if (typeof value === 'string') {
					const dataChild = document.createElement('p');
					dataChild.textContent = value;
					details.appendChild(dataChild);
				} else {
					const recursionContainer = document.createElement('div');
					createDetailsTree(value, recursionContainer);
					details.appendChild(recursionContainer);
				}
			}
		};

		if (typeof item.children === 'function') {
			details.addEventListener('toggle', async function handler() {
				if (!this.open) return;
				renderChildren(await item.children());
				details.removeEventListener('toggle', handler);
			});
		} else {
			renderChildren(item.children);
		}

		container.appendChild(details);
	}
}

async function loadUsers() {
	const users = await fetch('/api/users/list.php').then(r => r.json());

	const tree = users.results.map(user => ({
		title: `Usuario '${user}'`,
		children: async() => {
			const userData = await fetch(`/api/users/view.php?name=${user}`).then(r => r.json());
			return {
				joinedAt: `Se unió el ${userData.joined_at} UTC`,
				filters: userData.filters.map(filter => ({
					title: `Filtro '${filter}'`,
					children: async() => {
						const filterData = await fetch(`/api/filters/view.php?user=${user}&filter=${filter}`).then(r => r.json());
						return {
							condition: `Condición: ${JSON.stringify(filterData.condition)}`,
							sortBy: `Órden: ${filterData.sort_by}`,
						};
					},
				}))
			};
		}
	}));

	createDetailsTree(tree, dom.userList);
}

loadUsers();
