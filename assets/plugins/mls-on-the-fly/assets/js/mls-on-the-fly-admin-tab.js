jQuery(document).ready(function ($) {
    // Shared variable to track the height across all divs
    let sharedHeight = '50%'; // Default height

    // Function to set the same height for all divs
    function setSharedHeight(height) {
        sharedHeight = height;
        $('#mls-on-the-fly-admin-tab-content-raw-data').css('height', sharedHeight);
        $('#mls-on-the-fly-admin-tab-content-requests').css('height', sharedHeight);
        $('#mls-on-the-fly-admin-tab-content-errors').css('height', sharedHeight);
    }

    // Function to update tab button styling
    function updateTabButtonStyling(activeTabClass) {
        // First reset all buttons to inactive state
        $('.tab-raw-data, .tab-requests, .tab-errors').removeClass('mls-on-the-fly-admin-bar-tab-button').addClass('mls-on-the-fly-admin-bar-tab-button-deactive');
        
        // Then set the active tab buttons
        $('.' + activeTabClass).removeClass('mls-on-the-fly-admin-bar-tab-button-deactive').addClass('mls-on-the-fly-admin-bar-tab-button');
    }

    // Event handlers for menu items and tab buttons
    $("li#wp-admin-bar-show-raw-data, .tab-raw-data").on("click", function(event) {
        event.preventDefault();
        $('#mls-on-the-fly-admin-tab-content-raw-data').show();
        $('#mls-on-the-fly-admin-tab-content-requests, #mls-on-the-fly-admin-tab-content-errors').hide();
        
        // Update active tab styling
        updateTabButtonStyling('tab-raw-data');
    });

    $("li#wp-admin-bar-monitor-mls-requests, .tab-requests").on("click", function(event) {
        event.preventDefault();
        $('#mls-on-the-fly-admin-tab-content-requests').show();
        $('#mls-on-the-fly-admin-tab-content-raw-data, #mls-on-the-fly-admin-tab-content-errors').hide();
        
        // Update active tab styling
        updateTabButtonStyling('tab-requests');
    });

    $("li#wp-admin-bar-php-errors, .tab-errors").on("click", function(event) {
        event.preventDefault();
        $('#mls-on-the-fly-admin-tab-content-errors').show();
        $('#mls-on-the-fly-admin-tab-content-requests, #mls-on-the-fly-admin-tab-content-raw-data').hide();
        
        // Update active tab styling
        updateTabButtonStyling('tab-errors');
    });

    // Close button handlers
    $("#mls-on-the-fly-admin-tab-content-raw-data-close").on("click", function() {
        $('#mls-on-the-fly-admin-tab-content-raw-data').hide();
    });

    $("#mls-on-the-fly-admin-tab-content-requests-close").on("click", function() {
        $('#mls-on-the-fly-admin-tab-content-requests').hide();
    });

    $("#mls-on-the-fly-admin-tab-content-errors-close").on("click", function() {
        $('#mls-on-the-fly-admin-tab-content-errors').hide();
    });

    // Function to add resizing capability to all divs with synchronized height
    function makeResizable(divId, handleId) {
        const resizableDiv = document.getElementById(divId);
        const resizeHandle = document.getElementById(handleId);
        
        if (!resizableDiv || !resizeHandle) return; // Safety check
        
        let isResizing = false;
        let startY, startHeight;

        resizeHandle.addEventListener("mousedown", function(e) {
            isResizing = true;
            startY = e.clientY;
            startHeight = parseInt(window.getComputedStyle(resizableDiv).height, 10);
            document.body.style.cursor = "ns-resize";
            e.preventDefault(); // Prevent text selection during resize
        });

        document.addEventListener("mousemove", function(e) {
            if (!isResizing) return;
            const dy = startY - e.clientY; // Reverse direction for resizing from the top
            const newHeight = `${startHeight + dy}px`;
            
            // Update the shared height and apply to all divs
            setSharedHeight(newHeight);
        });

        document.addEventListener("mouseup", function() {
            if (isResizing) {
                isResizing = false;
                document.body.style.cursor = "default";
            }
        });
    }

    // Make all divs resizable with synchronized height
    makeResizable("mls-on-the-fly-admin-tab-content-raw-data", "resize-handle-raw");
    makeResizable("mls-on-the-fly-admin-tab-content-requests", "resize-handle-requests");
    makeResizable("mls-on-the-fly-admin-tab-content-errors", "resize-handle-errors");
});