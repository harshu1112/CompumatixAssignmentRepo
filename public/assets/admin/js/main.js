// Ticket Management System - Main JavaScript

// Get CSRF Token
function getCsrfToken() {
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    return metaTag ? metaTag.content : '';
}

// Toast notification helper
function showToast(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
    alertDiv.style.zIndex = '9999';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}

// AJAX: Add Comment
document.addEventListener('DOMContentLoaded', function() {
    const commentForm = document.getElementById('commentForm');
    
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const commentInput = form.querySelector('#comment');
            const submitBtn = form.querySelector('button[type="submit"]');
            const commentsContainer = document.getElementById('commentsContainer');
            
            // Get form action from data-action attribute or action attribute
            const formAction = form.getAttribute('data-action') || form.action;
            
            if (!formAction || formAction.includes('__TICKET_ID__')) {
                showToast('error', 'Please select a ticket first');
                return;
            }
            
            // Disable submit button
            submitBtn.disabled = true;
            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Posting...';
            
            // Remove previous errors
            commentInput.classList.remove('is-invalid');
            const feedbackDiv = form.querySelector('.invalid-feedback');
            if (feedbackDiv) {
                feedbackDiv.textContent = '';
            }
            
            fetch(formAction, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    comment: commentInput.value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove "no comments" message if exists
                    const noCommentsMsg = document.getElementById('noCommentsMessage');
                    if (noCommentsMsg) {
                        noCommentsMsg.remove();
                    }
                    
                    // Create new comment HTML
                    const commentHtml = `
                        <div class="card mb-3 comment-item">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">${data.comment.user.name}</h6>
                                        <small class="text-muted">${data.comment.created_at_human}</small>
                                    </div>
                                </div>
                                <p class="mt-2 mb-0">${data.comment.comment}</p>
                            </div>
                        </div>
                    `;
                    
                    // Add comment to top of container
                    if (commentsContainer) {
                        commentsContainer.insertAdjacentHTML('afterbegin', commentHtml);
                    }
                    
                    // Clear form
                    commentInput.value = '';
                    
                    // Show success message
                    showToast('success', data.message);
                } else {
                    throw new Error(data.message || 'Failed to post comment');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                commentInput.classList.add('is-invalid');
                if (feedbackDiv) {
                    feedbackDiv.textContent = error.message || 'Failed to post comment. Please try again.';
                }
                showToast('error', 'Failed to post comment. Please try again.');
            })
            .finally(() => {
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
            });
        });
    }
});

// AJAX: Change Status
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('statusSelect');
    
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            const ticketId = this.dataset.ticketId;
            const newStatus = this.value;
            const select = this;
            
            // Disable select
            select.disabled = true;
            
            fetch(`/tickets/${ticketId}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', data.message);
                } else {
                    throw new Error(data.message || 'Failed to update status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'Failed to update status. Please try again.');
                // Revert select to previous value
                location.reload();
            })
            .finally(() => {
                select.disabled = false;
            });
        });
    }
});

// Modal: Dynamic form action for add comment modal
document.addEventListener('DOMContentLoaded', function() {
    const ticketIdSelect = document.getElementById('ticket_id');
    const modalCommentForm = document.getElementById('commentForm');
    const addCommentModal = document.getElementById('addCommentModal');
    
    if (ticketIdSelect && modalCommentForm) {
        ticketIdSelect.addEventListener('change', function() {
            const ticketId = this.value;
            const baseUrl = modalCommentForm.getAttribute('data-base-url');
            if (ticketId && baseUrl) {
                modalCommentForm.action = baseUrl.replace('__TICKET_ID__', ticketId);
            }
        });
    }
    
    // Reset form when modal closes
    if (addCommentModal) {
        addCommentModal.addEventListener('hidden.bs.modal', function() {
            if (modalCommentForm) {
                const baseUrl = modalCommentForm.getAttribute('data-base-url');
                if (baseUrl) {
                    modalCommentForm.action = baseUrl;
                }
                modalCommentForm.reset();
            }
        });
    }
});
