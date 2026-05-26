$(document).ready(function() {

    // Global state for filters
    let currentQuery = '';
    let currentPlatformId = null;
    let currentGenreId = null;

    // Fetch games via AJAX based on search input and filters
    function fetchGames() {
        $.ajax({
            url: '/GameDex/public/search.php',
            method: 'GET',
            dataType: 'json',
            data: { 
                q: currentQuery,
                platform_id: currentPlatformId,
                genre_id: currentGenreId
            },
            success: function(data) {
                const $grid = $('#gameGrid');
                let html = '';

                if (data.length === 0) {
                    // Display no results message
                    $grid.html('<p class="no-results-text">No games found matching your criteria.</p>');
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
        currentQuery = $(this).val();
        fetchGames();
    });

    // Handle platform filter click
    $('.platform-filter').on('click', function(e) {
        e.preventDefault();
        currentPlatformId = $(this).data('id');
        
        // Update button text to show selected platform
        $('.dropbtn').first().html($(this).text() + ' <span class="arrow">&#9663;</span>');
        
        fetchGames();
    });

    // Handle genre filter click
        $('.genre-filter').on('click', function(e) {
            e.preventDefault();
            currentGenreId = $(this).data('id');
            
            // Update genre button text to show selected genre
            $(this).closest('.dropdown').find('.dropbtn').html($(this).text() + ' <span class="arrow">&#9663;</span>');
            
            fetchGames();
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
            $profileForm.find('input, select').each(function() {
                const $input = $(this);
                const type = $input.attr('type');
                const name = $input.attr('name');
                const val = $input.val();

                if (type === 'checkbox') {
                    state[name + '_' + val] = $input.is(':checked');
                } else if (type !== 'submit' && type !== 'hidden') {
                    state[name] = val;
                }
            });
            return JSON.stringify(state);
        }

        const initialState = getFormState();

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

    // Delete account modal logic
    const $deleteTriggerBtn = $('#deleteTriggerBtn');
    if ($deleteTriggerBtn.length) {
        const $modalOverlay = $('#deleteModalOverlay');
        const $modalTitle = $('#modalTitle');
        const $btnContainer = $('#modalBtnContainer');
        const $hiddenDeleteForm = $('#hiddenDeleteForm');

        const closeModal = () => $modalOverlay.hide();

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