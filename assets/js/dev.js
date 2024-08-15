// Hides developer tools by using various methods.
(function() {
    // Disable right-click context menu
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        alert("Right-click is disabled.");
    });

    // Disable key combinations for developer tools
    document.addEventListener('keydown', function(e) {
        if (
            e.key === 'F12' || 
            (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'C' || e.key === 'V'))
        ) {
            e.preventDefault();
            e.stopPropagation();
            alert("Developer Tools are disabled.");
        }
    });

    // Disable certain mouse events (e.g., right-click and specific developer tools access)
    document.addEventListener('mousedown', function(e) {
        if (e.button === 2) {
            // Disable right mouse button
            e.preventDefault();
            alert("Right-click is disabled.");
        }
    });

    // Additional detection of specific keys to disable
    document.addEventListener('keydown', function(e) {
        if (
            (e.ctrlKey && e.shiftKey && (e.key === 'J' || e.key === 'U')) || // Ctrl+Shift+J (Console) or Ctrl+Shift+U (View Source)
            e.key === 'F12' || // F12 Key
            (e.ctrlKey && e.shiftKey && e.key === 'I') // Ctrl+Shift+I
        ) {
            e.preventDefault();
            e.stopPropagation();
            alert("Developer Tools are disabled.");
        }
    });

    // Disable specific keyboard shortcuts for copy and paste
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey && e.shiftKey && e.key === 'C') || // Ctrl+Shift+C
            (e.ctrlKey && e.shiftKey && e.key === 'V')  // Ctrl+Shift+V
        ) {
            e.preventDefault();
            e.stopPropagation();
            alert("Certain shortcuts are disabled.");
        }
    });
})();
