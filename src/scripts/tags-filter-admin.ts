document.addEventListener('DOMContentLoaded', () => {
    const input  = document.getElementById('filter_tags_search') as HTMLInputElement | null;
    const list   = document.getElementById('filter_tags_datalist') as HTMLDataListElement | null;
    const ul     = document.getElementById('filter_tags_list') as HTMLUListElement | null;
    const hidden = document.getElementById('filter_tags_hidden') as HTMLInputElement | null;
    const btn    = document.getElementById('filter_tags_add') as HTMLButtonElement | null;
    if (!input || !list || !ul || !hidden || !btn) return;

    const updateHidden = (): void => {
        const ids = Array.from(ul.querySelectorAll('li')).map(li => li.dataset.id ?? '');
        hidden.value = ids.join(',');
    };

    const addTag = (): void => {
        const name = input.value.trim();
        if (!name) return;

        const opt = Array.from(list.options).find(o => o.value.toLowerCase() === name.toLowerCase());
        if (!opt) { input.value = ''; return; }

        const id = opt.dataset.id;
        if (!id) return;

        if (ul.querySelector(`li[data-id="${id}"]`)) return;

        const li = document.createElement('li');
        li.textContent = `${name} ✕`;
        li.dataset.id = id;
        li.style.cssText = 'display:inline-block;margin:2px 4px 2px 0;padding:4px 8px;background:#f0f0f0;border-radius:4px;cursor:pointer;';
        ul.appendChild(li);

        input.value = '';
        updateHidden();
    };

    btn.addEventListener('click', addTag);

    input.addEventListener('keydown', (e: KeyboardEvent) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            addTag();
        }
    });

    ul.addEventListener('click', (e: MouseEvent) => {
        const target = e.target as HTMLElement;
        if (target.tagName === 'LI') {
            target.remove();
            updateHidden();
        }
    });
});
