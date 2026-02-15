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

function renderReactionAdd(container) {
    const postId = container.dataset.postId;
    const storeUrl = container.dataset.storeUrl;
    const csrf = container.dataset.csrf;
    container.dataset.hasReaction = '0';
    container.innerHTML =
        '<button type="button" class="js-reaction-add inline-flex items-center justify-center p-0 text-gray-400 hover:text-red-500 transition" title="Like">' +
        '<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>' +
        '</svg></button>';
}

function renderReactionRemove(container) {
    const postId = container.dataset.postId;
    const destroyUrl = container.dataset.destroyUrl;
    const csrf = container.dataset.csrf;
    container.dataset.hasReaction = '1';
    container.innerHTML =
        '<button type="button" class="js-reaction-remove inline-flex items-center justify-center p-0 text-red-500 hover:text-red-600 transition" title="Remove reaction">' +
        '<svg class="w-5 h-5 shrink-0" fill="currentColor" stroke="currentColor" viewBox="0 0 24 24">' +
        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>' +
        '</svg></button>';
}

document.addEventListener('click', function (e) {
    const addBtn = e.target.closest('.js-reaction-add');
    const removeBtn = e.target.closest('.js-reaction-remove');
    const container = addBtn ? addBtn.closest('.js-reaction-container') : (removeBtn ? removeBtn.closest('.js-reaction-container') : null);
    if (!container) return;
    const postId = container.dataset.postId;
    const countEl = document.getElementById('engagement-reactions-count-' + postId);
    if (!countEl) return;

    if (addBtn) {
        addBtn.disabled = true;
        const storeUrl = container.dataset.storeUrl;
        const csrf = container.dataset.csrf;
        const body = new FormData();
        body.append('_token', csrf);
        body.append('type', 'like');
        fetch(storeUrl, {
            method: 'POST',
            body: body,
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        })
            .then(function (r) { return r.ok ? r.json() : r.json().then(function (j) { throw j; }); })
            .then(function (data) {
                countEl.textContent = data.count;
                renderReactionRemove(container);
            })
            .catch(function () { addBtn.disabled = false; });
    } else if (removeBtn) {
        removeBtn.disabled = true;
        const destroyUrl = container.dataset.destroyUrl;
        const csrf = container.dataset.csrf;
        const body = new FormData();
        body.append('_token', csrf);
        body.append('_method', 'DELETE');
        fetch(destroyUrl, {
            method: 'POST',
            body: body,
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        })
            .then(function (r) { return r.ok ? r.json() : r.json().then(function (j) { throw j; }); })
            .then(function (data) {
                countEl.textContent = data.count;
                renderReactionAdd(container);
            })
            .catch(function () { removeBtn.disabled = false; });
    }
});

window.toggleComments = toggleComments;
window.toggleCommentsMore = toggleCommentsMore;
