class ValidatedInput {
	#dependencies = null;
	#submitElement = null;

	constructor(spec) {
		this.#dependencies = new Set();
		this.#submitElement = spec.submit;

		for (const [field, fspec] of Object.entries(spec.fields)) {
			fspec.hintElement.textContent = fspec.hint;

			const validate = value => {
				if (fspec.condition(value)) {
					fspec.hintElement.style.display = 'none';
					this.#removeDependency(field);
				} else {
					fspec.hintElement.style.display = '';
					this.#addDependency(field);
				}
			};

			fspec.element.addEventListener('input', ev => validate(ev.target.value));
			validate(fspec.element.value);
			fspec.hintElement.style.display = 'none';
		}

		spec.submit.addEventListener('click', async() => await spec.onSubmit());
	}

	#addDependency(dependency) {
		if (this.#dependencies.size === 0) {
			this.#submitElement.disabled = true;
		}

		this.#dependencies.add(dependency);
	}

	#removeDependency(dependency) {
		this.#dependencies.delete(dependency);

		if (this.#dependencies.size === 0) {
			this.#submitElement.disabled = false;
		}
	}
}
