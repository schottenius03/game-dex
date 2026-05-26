document.addEventListener('DOMContentLoaded', () => {

    // Toggle password visibility
    const passwordToggles = document.querySelectorAll('.toggle-password');
    passwordToggles.forEach(button => {
        button.addEventListener('click', function() {
            const inputField = this.parentElement.querySelector('input');
            inputField.type = inputField.type === 'password' ? 'text' : 'password';
            this.classList.toggle('visible');
        });
    });

    // Form loading animation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const btn = this.querySelector('.btn-submit');
            if (btn) {
                btn.disabled = true;
                let dots = 0;
                setInterval(() => {
                    dots = (dots % 3) + 1;
                    btn.textContent = 'Loading' + '.'.repeat(dots);
                }, 500);
            }
        });
    });

    // Profile form change detection
    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        const saveBtn = document.getElementById('saveBtn');

        function getFormState() {
            const state = {};
            const inputs = profileForm.querySelectorAll('input[type="checkbox"], select, input[type="email"]');
            inputs.forEach(input => {
                if (input.type === 'checkbox') {
                    state[input.name + '_' + input.value] = input.checked;
                } else {
                    state[input.name] = input.value;
                }
            });
            return JSON.stringify(state);
        }

        const initialState = getFormState();

        profileForm.addEventListener('input', function() {
            if (getFormState() !== initialState) {
                saveBtn.disabled = false;
                saveBtn.style.backgroundColor = '#e7783c';
                saveBtn.style.color = '#ffffff';
                saveBtn.style.border = '1px solid #e7783c';
                saveBtn.style.cursor = 'pointer';
            } else {
                saveBtn.disabled = true;
                saveBtn.style.backgroundColor = '#2c2c35';
                saveBtn.style.color = 'rgba(255,255,255,0.4)';
                saveBtn.style.border = '1px solid #444';
                saveBtn.style.cursor = 'not-allowed';
            }
        });
    }

    // Delete account modal logic using event delegation
    const deleteTriggerBtn = document.getElementById('deleteTriggerBtn');
    if (deleteTriggerBtn) {
        const modalOverlay = document.getElementById('deleteModalOverlay');
        const modalTitle = document.getElementById('modalTitle');
        const btnContainer = document.getElementById('modalBtnContainer');
        const hiddenDeleteForm = document.getElementById('hiddenDeleteForm');

        const closeModal = () => modalOverlay.style.display = 'none';

        // Handles button clicks within the modal via delegation
        btnContainer.addEventListener('click', (e) => {
            if (e.target.classList.contains('btn-cancel-action')) {
                closeModal();
            } else if (e.target.id === 'modalDeleteBtn') {
                modalTitle.innerText = "Are you REALLY sure?";
                btnContainer.innerHTML = `
                    <button type="button" class="modal-btn btn-danger-action" id="confirmDelete">Delete</button>
                    <button type="button" class="modal-btn btn-cancel-action">Cancel</button>
                `;
            } else if (e.target.id === 'confirmDelete') {
                hiddenDeleteForm.submit();
            }
        });

        deleteTriggerBtn.addEventListener('click', () => {
            modalTitle.innerText = "Are you sure you want to delete your account?";
            btnContainer.innerHTML = `
                <button type="button" class="modal-btn btn-cancel-action">Cancel</button>
                <button type="button" class="modal-btn btn-danger-action" id="modalDeleteBtn">Delete</button>
            `;
            modalOverlay.style.display = 'flex';
        });

        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) closeModal();
        });
    }
});