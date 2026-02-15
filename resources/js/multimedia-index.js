function toggleComments(postId) {
    const commentsSection = document.getElementById('comments-' + postId);
    if (commentsSection.classList.contains('hidden')) {
        commentsSection.classList.remove('hidden');
    } else {
        commentsSection.classList.add('hidden');
    }
}

function toggleCommentsMore(postId, moreCount) {
    const moreEl = document.getElementById('comments-more-' + postId);
    const btn = document.getElementById('comments-more-btn-' + postId);
    if (!moreEl || !btn) return;
    if (moreEl.classList.contains('hidden')) {
        moreEl.classList.remove('hidden');
        btn.textContent = 'View less';
    } else {
        moreEl.classList.add('hidden');
        btn.textContent = 'View more (' + moreCount + ')';
    }
}

function escapeHtml(s) {
    const div = document.createElement('div');
    div.textContent = s;
    return div.innerHTML;
}

document.querySelectorAll('.js-comment-form').forEach(function (form) {
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const postId = form.dataset.postId;
        const input = form.querySelector('.js-comment-input');
        const submitBtn = form.querySelector('button[type="submit"]');
        const listEl = document.getElementById('comments-list-' + postId);
        const countEl = document.getElementById('comments-count-' + postId);
        if (!listEl || !countEl || !input) return;
        submitBtn.disabled = true;
        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        })
            .then(function (r) {
                return r.ok ? r.json() : r.json().then(function (j) {
                    throw j;
                });
            })
            .then(function (data) {
                const c = data.comment;
                const wrap = document.createElement('div');
                wrap.className = 'flex justify-end';
                const inner = document.createElement('div');
                inner.className = 'rounded-2xl border p-3 w-full border-indigo-200 bg-indigo-50/70';
                inner.innerHTML =
                    '<div class="text-xs text-indigo-600 font-medium">' +
                    escapeHtml(c.user_name) + ' \u2022 ' + escapeHtml(c.created_at_human) +
                    '</div><p class="mt-1 text-sm text-gray-800"></p>';
                inner.querySelector('p').textContent = c.body;
                wrap.appendChild(inner);
                listEl.insertBefore(wrap, listEl.firstChild);
                countEl.textContent = data.total_count;
                const engagementCount = document.getElementById('engagement-comments-count-' + postId);
                if (engagementCount) engagementCount.textContent = data.total_count;
                input.value = '';
            })
            .catch(function (err) {
                if (err && err.message) alert(err.message || 'Failed to post comment.');
            })
            .finally(function () {
                submitBtn.disabled = false;
            });
    });
});

window.toggleComments = toggleComments;
window.toggleCommentsMore = toggleCommentsMore;
