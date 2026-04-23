document.addEventListener('DOMContentLoaded', () => {
    const collection = document.getElementById('photo-collection');
    const addBtn = document.getElementById('add-photo');
    const limitMsg = document.getElementById('photo-limit-msg');
    const MAX = parseInt(collection.dataset.max, 10);

    let index = parseInt(collection.dataset.index, 10);

    function countEntries() {
        return collection.querySelectorAll('.photo-entry').length;
    }

    function displayCountMax() {
        const atMax = countEntries() >= MAX;
        addBtn.style.display = atMax ? 'none' : '';
        limitMsg.style.display = atMax ? '' : 'none';
    }

    addBtn.addEventListener('click', () => {
        if (countEntries() >= MAX) return;

        const prototype = collection.dataset.prototype;
        const html = prototype.replace(/__name__/g, index++);

        const entry = document.createElement('div');
        entry.className = 'photo-entry input-group mb-2';
        entry.innerHTML = html;

        entry.querySelector('input[type="file"]')?.classList.add('form-control');

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn btn-outline-danger remove-photo';
        removeBtn.textContent = '✕';
        entry.appendChild(removeBtn);

        collection.appendChild(entry);
        collection.dataset.index = index;
        displayCountMax();
    });

    collection.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-photo')) {
            e.target.closest('.photo-entry').remove();
            displayCountMax();
        }
    });

    displayCountMax();
});
