document.addEventListener('DOMContentLoaded', () => {
    const collection = document.getElementById('photo-collection');
    if (!collection) return;

    const form = collection.closest('form');
    console.log('form found:', form);

    const addSlot = document.getElementById('add-photo-slot');
    const addBtn = document.getElementById('add-photo');
    const limitMsg = document.getElementById('photo-limit-msg');
    const MAX = parseInt(collection.dataset.max, 10);

    let index = parseInt(collection.dataset.index, 10);

    function countActive() {
        return collection.querySelectorAll('.photo-entry:not(#add-photo-slot)').length;
    }

    function updateVisibility() {
        const atMax = countActive() >= MAX;
        addSlot.style.display = atMax ? 'none' : '';
        limitMsg.style.display = atMax ? '' : 'none';
    }

    addBtn.addEventListener('click', () => {
        if (countActive() >= MAX) return;

        const prototype = collection.dataset.prototype;
        const html = prototype.replace(/__name__/g, index++);

        const entry = document.createElement('div');
        entry.className = 'photo-entry photo-new';
        entry.innerHTML = html;

        entry.querySelector('input[type="file"]')?.classList.add('form-control');

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'remove-photo';
        removeBtn.textContent = '✕';
        entry.appendChild(removeBtn);

        collection.insertBefore(entry, addSlot);
        collection.dataset.index = index;
        updateVisibility();
    });

    collection.addEventListener('click', (e) => {
        if (!e.target.classList.contains('remove-photo')) return;

        const entry = e.target.closest('.photo-entry');
        console.log('removing entry:', entry);
        console.log('is existing:', entry.classList.contains('photo-existing'));

        if (entry.classList.contains('photo-existing')) {
            const deleteInput = entry.querySelector('input[type="hidden"]');
            console.log('deleteInput found:', deleteInput);
            if (deleteInput) {
                deleteInput.disabled = false;
                console.log('input name:', deleteInput.name, 'value:', deleteInput.value);
                form.appendChild(deleteInput);
                console.log('input moved to form, disabled:', deleteInput.disabled);
            }
            entry.remove();
        } else {
            entry.remove();
        }

        updateVisibility();
    });

    form.addEventListener('submit', (e) => {
        console.log('form submitting...');
        const formData = new FormData(form);
        console.log('deleteImages:', formData.getAll('deleteImages[]'));

        // Log ALL form fields
        for (let [key, value] of formData.entries()) {
            if (key.includes('delete')) {
                console.log(key, value);
            }
        }
    });

    updateVisibility();
});
