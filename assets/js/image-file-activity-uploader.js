document.addEventListener('DOMContentLoaded', () => {
    const collection = document.getElementById('photo-collection');
    if (!collection) return;

    const form = collection.closest('form');
    const addBtn = document.getElementById('add-photo');
    const limitMsg = document.getElementById('photo-limit-msg');
    const MAX = parseInt(collection.dataset.max, 10);
    let index = parseInt(collection.dataset.index, 10);

    function countActive() {
        return collection.querySelectorAll('.photo-entry').length;
    }

    function updateVisibility() {
        const atMax = countActive() >= MAX;
        if (addBtn) addBtn.style.display = atMax ? 'none' : '';
        if (limitMsg) limitMsg.style.display = atMax ? '' : 'none';
    }

    function makeNewSlot() {
        const prototype = collection.dataset.prototype;
        const html = prototype.replace(/__name__/g, index++);

        const entry = document.createElement('div');
        entry.className = 'photo-entry photo-new';

        const inner = document.createElement('div');
        inner.style.display = 'none';
        inner.innerHTML = html;
        entry.appendChild(inner);

        const fileInput = inner.querySelector('input[type="file"]');

        const placeholder = document.createElement('span');
        placeholder.className = 'photo-placeholder';
        placeholder.textContent = '+';
        entry.appendChild(placeholder);

        const preview = document.createElement('img');
        preview.className = 'photo-preview';
        preview.style.display = 'none';
        entry.appendChild(preview);

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'remove-photo';
        removeBtn.textContent = '✕';
        entry.appendChild(removeBtn);

        entry.addEventListener('click', (e) => {
            if (e.target === removeBtn) return;
            fileInput.click();
        });

        fileInput.addEventListener('change', () => {
            const file = fileInput.files[0];
            if (!file) return;
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
            placeholder.style.display = 'none';
        });

        collection.dataset.index = index;
        return entry;
    }

    if (addBtn) {
        addBtn.addEventListener('click', () => {
            if (countActive() >= MAX) return;
            const entry = makeNewSlot();
            collection.appendChild(entry);
            updateVisibility();
        });
    }

    collection.addEventListener('click', (e) => {
        if (!e.target.classList.contains('remove-photo')) return;
        const entry = e.target.closest('.photo-entry');
        if (entry.classList.contains('photo-existing')) {
            const deleteInput = entry.querySelector('input[type="hidden"]');
            if (deleteInput) {
                deleteInput.disabled = false;
                form.appendChild(deleteInput);
            }
        }
        entry.remove();
        updateVisibility();
    });

    if (countActive() === 0) {
        const entry = makeNewSlot();
        collection.appendChild(entry);
        updateVisibility();
    } else {
        updateVisibility();
    }
});
