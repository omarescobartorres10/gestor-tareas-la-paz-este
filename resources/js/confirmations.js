// Confirmation dialogs consistency
// Provides better UX with custom confirmation dialogs

/**
 * Show a confirmation dialog with custom message
 * @param {string} message - The confirmation message
 * @param {string} type - Type of action: 'danger', 'warning', 'info'
 * @returns {boolean} - User's confirmation choice
 */
function confirmAction(message, type = 'warning') {
    const icons = {
        danger: '⚠️',
        warning: '⚡',
        info: 'ℹ️'
    };
    
    const icon = icons[type] || icons.warning;
    return confirm(`${icon} ${message}\n\n¿Estás seguro de continuar?`);
}

/**
 * Confirm task archival
 */
function confirmArchive() {
    return confirmAction(
        'Esta tarea será archivada y no aparecerá en las listas principales.',
        'warning'
    );
}

/**
 * Confirm task unarchival
 */
function confirmUnarchive() {
    return confirmAction(
        'Esta tarea será desarchivada y volverá a las listas activas.',
        'info'
    );
}

/**
 * Confirm comment deletion
 */
function confirmDeleteComment() {
    return confirmAction(
        'Este comentario será eliminado permanentemente.',
        'danger'
    );
}

/**
 * Confirm user deactivation
 */
function confirmDeactivateUser(userName) {
    return confirmAction(
        `El usuario "${userName}" será desactivado y no podrá acceder al sistema.`,
        'danger'
    );
}

/**
 * Confirm user activation
 */
function confirmActivateUser(userName) {
    return confirmAction(
        `El usuario "${userName}" será activado y podrá acceder al sistema.`,
        'info'
    );
}

/**
 * Attach confirmation to forms with data-confirm attribute
 * Usage: <form data-confirm="delete">...</form>
 */
document.addEventListener('DOMContentLoaded', function() {
    // Handle forms with confirmation
    document.querySelectorAll('form[data-confirm]').forEach(form => {
        form.addEventListener('submit', function(e) {
            const confirmType = this.dataset.confirm;
            const confirmMessage = this.dataset.confirmMessage || '¿Continuar con esta acción?';
            
            if (!confirmAction(confirmMessage, confirmType)) {
                e.preventDefault();
                return false;
            }
        });
    });

    // Handle links with confirmation
    document.querySelectorAll('a[data-confirm]').forEach(link => {
        link.addEventListener('click', function(e) {
            const confirmType = this.dataset.confirm;
            const confirmMessage = this.dataset.confirmMessage || '¿Continuar con esta acción?';
            
            if (!confirmAction(confirmMessage, confirmType)) {
                e.preventDefault();
                return false;
            }
        });
    });
});

// Export for use in other scripts
window.confirmAction = confirmAction;
window.confirmArchive = confirmArchive;
window.confirmUnarchive = confirmUnarchive;
window.confirmDeleteComment = confirmDeleteComment;
window.confirmDeactivateUser = confirmDeactivateUser;
window.confirmActivateUser = confirmActivateUser;
