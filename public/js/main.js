$(document).ready(function() {

    // Global state for filters
    let currentQuery = '';
    let currentPlatformId = null;
    let currentGenreId = null;

    // Toggle Wishlist functionality via AJAX
    $(document).on('click', '.wishlist-btn', function(e) {
        e.preventDefault();
        const $btn = $(this);
        const gameId = $btn.data('id');
        // Check if the game is being added or removed from the wishlist
        const isAdding = !$btn.hasClass('active'); 

        $.ajax({
            url: '/GameDex/public/wishlist_toggle.php',
            method: 'POST',
            data: { 
                game_id: gameId,
                action: isAdding ? 'add' : 'remove'
            },
            success: function(response) {
                // Parse response if it comes as a string
                const res = typeof response === 'string' ? JSON.parse(response) : response;
                
                if (res.success) {
                    $btn.toggleClass('active'); 
                    
                    // Remove card
                    if (window.location.pathname.includes('wishlist.php') && res.action === 'removed') {
                        $btn.closest('.game-card').fadeOut(300, function() {
                            $(this).remove();
                            // If the list is empty, display a message
                            if ($('.game-grid .game-card').length === 0) {
                                $('.container').append('<p>You haven\'t added any games to your wishlist yet.</p>');
                            }
                        });
                    }
                } else {
                    alert(res.message || 'You must be logged in to manage your wishlist!');
                }
            },
            error: function() {
                // Handle server or connectivity errors
                alert('Something went wrong, please try again.');
            }
        });
    });

    // Update the visual state of the reset button based on active filters
    function updateResetButton() {
        const isFiltered = currentQuery !== '' || currentPlatformId !== null || currentGenreId !== null;
        $('#resetFilters').toggleClass('active', isFiltered);
    }

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
                    $grid.html('<p class="no-results-text">No games found matching your criteria.</p>');
                    return;
                }

                data.forEach(game => {
                    const image = game.image_url || 'assets/game-controller.png';
                    const rating = game.rating_data ? parseFloat(game.rating_data.avg).toFixed(1) : '0.0';
                    
                    // Build platform badges
                    let platformBadges = '';
                    if (game.platforms && game.platforms.length > 0) {
                        game.platforms.forEach(p => {
                            platformBadges += `<span class="badge badge-platform">${p.name}</span>`;
                        });
                    } else {
                        platformBadges = '<span class="badge badge-platform">N/A</span>';
                    }
                    
                    html += `
                        <div class="game-card">
                            <button class="wishlist-btn ${game.is_wishlisted ? 'active' : ''}" data-id="${game.id}">
                                <span class="wishlist-icon-outline">♡</span>
                                <span class="wishlist-icon-filled">♥</span>
                            </button>
                            <a href="game.php?id=${game.id}" class="card-link">
                                <div class="card-image">
                                    <img src="${image}" alt="${game.title}">
                                </div>
                                <div class="card-content">
                                    <h3>${game.title}</h3>
                                    <div class="card-meta">
                                        <div class="game-card-elements">
                                            ${platformBadges}
                                        </div>
                                        <span class="rating">${rating}</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    `;
                });
                $grid.html(html);
            }
        });
    }

    // Trigger search on input
    $('#searchBar').on('input', function() {
        currentQuery = $(this).val();
        updateResetButton();
        fetchGames();
    });

    // Handle platform filter click
    $('.platform-filter').on('click', function(e) {
        e.preventDefault();
        currentPlatformId = $(this).data('id');
        $('.dropbtn').first().html($(this).text() + ' <span class="arrow">&#9663;</span>');
        updateResetButton();
        fetchGames();
    });

    // Handle genre filter click
    $('.genre-filter').on('click', function(e) {
        e.preventDefault();
        currentGenreId = $(this).data('id');
        $(this).closest('.dropdown').find('.dropbtn').html($(this).text() + ' <span class="arrow">&#9663;</span>');
        updateResetButton();
        fetchGames();
    });

    // Reset all filters and UI components
    $('#resetFilters').on('click', function() {
        if (!$(this).hasClass('active')) return;
        currentQuery = '';
        currentPlatformId = null;
        currentGenreId = null;
        $('#searchBar').val('');
        $('.dropbtn').not('#resetFilters').each(function() {
            const defaultText = $(this).data('default');
            $(this).html(defaultText + ' <span class="arrow">&#9663;</span>');
        });
        updateResetButton();
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

    // Toggle dropdown on click
    $('.dropbtn').on('click', function(e) {
        e.stopPropagation(); // Prevent drop down to close down
        $(this).siblings('.dropdown-content').toggleClass('show');
    });

    // CLose dropdown alternatives if click somewhere else 
    $(window).on('click', function() {
        $('.dropdown-content').removeClass('show');
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