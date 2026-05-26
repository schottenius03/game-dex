$(document).ready(function() {

    // Fetch games via AJAX based on search input
    function fetchGames(query = '') {
        $.ajax({
            url: '/GameDex/public/search.php',
            method: 'GET',
            dataType: 'json',
            data: { q: query },
            success: function(data) {
                const $grid = $('#gameGrid');
                let html = '';

                if (data.length === 0) {
                    $grid.html('<p>No games found matching your search.</p>');
                    return;
                }

                data.forEach(game => {
                    const image = game.image_url || 'assets/game-controller.png';
                    html += `
                        <a href="game.php?id=${game.id}" class="game-card">
                            <div class="card-image">
                                <img src="${image}" alt="${game.title}">
                            </div>
                            <div class="card-content">
                                <h3>${game.title}</h3>
                            </div>
                        </a>
                    `;
                });
                $grid.html(html);
            }
        });
    }

    // Trigger search on input
    $('#searchBar').on('input', function() {
        fetchGames($(this).val());
    });

    // Toggle password visibility
    $('.toggle-password').on('click', function() {
        const inputField = $(this).siblings('input');
        const type = inputField.attr('type') === 'password' ? 'text' : 'password';
        inputField.attr('type', type);
        $(this).toggleClass('visible');
    });

    // Form loading animation
    $('form').on('submit', function() {
        const btn = $(this).find('.btn-submit');
        if (btn.length) {
            btn.text('Saving...');
            btn.css('opacity', '0.7');
        }
    });

    // Profile form change detection
    const $profileForm = $('#profileForm');
    if ($profileForm.length) {
        const $saveBtn = $('#saveBtn');

        function getFormState() {
            const state = {};
            // Use a unique identifier by combining name and value
            $profileForm.find('input, select').each(function() {
                const $input = $(this);
                const type = $input.attr('type');
                const name = $input.attr('name');
                const val = $input.val();

                if (type === 'checkbox') {
                    // For arrays using name + value as key
                    state[name + '_' + val] = $input.is(':checked');
                } else if (type !== 'submit' && type !== 'hidden') {
                    // For text/select/email, save the current value
                    state[name] = val;
                }
            });
            return JSON.stringify(state);
        }

        // Store the original state on page load
        const initialState = getFormState();

        // Monitor input changes to enable/disable save button
        $profileForm.on('input change', function() {
            if (getFormState() !== initialState) {
                $saveBtn.prop('disabled', false).css({
                    'background-color': '#e7783c',
                    'color': '#ffffff',
                    'border': '1px solid #e7783c',
                    'cursor': 'pointer'
                });
            } else {
                $saveBtn.prop('disabled', true).css({
                    'background-color': '#2c2c35',
                    'color': 'rgba(255,255,255,0.4)',
                    'border': '1px solid #444',
                    'cursor': 'not-allowed'
                });
            }
        });
    }

    // Delete account modal logic using event delegation
    const $deleteTriggerBtn = $('#deleteTriggerBtn');
    if ($deleteTriggerBtn.length) {
        const $modalOverlay = $('#deleteModalOverlay');
        const $modalTitle = $('#modalTitle');
        const $btnContainer = $('#modalBtnContainer');
        const $hiddenDeleteForm = $('#hiddenDeleteForm');

        const closeModal = () => $modalOverlay.hide();

        // Handles button clicks within the modal via delegation
        $btnContainer.on('click', (e) => {
            const $target = $(e.target);
            if ($target.hasClass('btn-cancel-action')) {
                closeModal();
            } else if ($target.attr('id') === 'modalDeleteBtn') {
                $modalTitle.text("Are you REALLY sure?");
                $btnContainer.html(`
                    <button type="button" class="modal-btn btn-danger-action" id="confirmDelete">Delete</button>
                    <button type="button" class="modal-btn btn-cancel-action">Cancel</button>
                `);
            } else if ($target.attr('id') === 'confirmDelete') {
                $hiddenDeleteForm.submit();
            }
        });

        $deleteTriggerBtn.on('click', () => {
            $modalTitle.text("Are you sure you want to delete your account?");
            $btnContainer.html(`
                <button type="button" class="modal-btn btn-cancel-action">Cancel</button>
                <button type="button" class="modal-btn btn-danger-action" id="modalDeleteBtn">Delete</button>
            `);
            $modalOverlay.css('display', 'flex');
        });

        $modalOverlay.on('click', (e) => {
            if (e.target === $modalOverlay[0]) closeModal();
        });
    }
});