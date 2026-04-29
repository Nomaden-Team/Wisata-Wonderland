/* ================================================================
   user.js
   - Hamburger / mobile sidebar toggle
   - Toast notifikasi (sukses / gagal) baca dari ?status & ?msg
   - Konfirmasi + preview sebelum upload foto
   - Loading overlay selama upload berjalan
   - Konfirmasi hapus foto pakai modal (bukan alert browser)
   ================================================================ */
(function () {
    'use strict';


    function el(tag, attrs, html) {
        var n = document.createElement(tag);
        if (attrs) Object.keys(attrs).forEach(function (k) {
            if (k === 'class') n.className = attrs[k];
            else if (k === 'style') n.style.cssText = attrs[k];
            else n.setAttribute(k, attrs[k]);
        });
        if (html != null) n.innerHTML = html;
        return n;
    }


    function ensureToastWrap() {
        var w = document.querySelector('.uw-toast-wrap');
        if (!w) {
            w = el('div', { class: 'uw-toast-wrap' });
            document.body.appendChild(w);
        }
        return w;
    }

    function toast(opts) {
        var type  = opts.type || 'success';
        var title = opts.title || (type === 'error' ? 'Gagal' : 'Berhasil');
        var msg   = opts.msg || '';
        var icons = {
            success: '<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>',
            error:   '<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
            info:    '<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="13"/><line x1="12" y1="16" x2="12" y2="16"/></svg>'
        };
        var t = el('div', { class: 'uw-toast ' + type },
            '<div class="uw-toast-icon">' + (icons[type] || icons.success) + '</div>' +
            '<div class="uw-toast-body">' +
                '<div class="uw-toast-title">' + title + '</div>' +
                '<div class="uw-toast-msg">' + msg + '</div>' +
            '</div>' +
            '<button class="uw-toast-close" aria-label="Tutup">&times;</button>');
        ensureToastWrap().appendChild(t);
        requestAnimationFrame(function () { t.classList.add('show'); });
        var close = function () {
            t.classList.remove('show');
            setTimeout(function () { if (t.parentNode) t.parentNode.removeChild(t); }, 350);
        };
        t.querySelector('.uw-toast-close').addEventListener('click', close);
        setTimeout(close, opts.duration || 5000);
    }
    window.uwToast = toast;


    function ensureLoadingOverlay() {
        var o = document.querySelector('.uw-loading-overlay');
        if (!o) {
            o = el('div', { class: 'uw-loading-overlay' },
                '<div class="uw-loading-card">' +
                    '<div class="uw-spinner"></div>' +
                    '<div class="uw-loading-title">Mengupload Foto…</div>' +
                    '<div class="uw-loading-sub">Mohon tunggu sebentar</div>' +
                '</div>');
            document.body.appendChild(o);
        }
        return o;
    }
    function showLoading(title, sub) {
        var o = ensureLoadingOverlay();
        if (title) o.querySelector('.uw-loading-title').textContent = title;
        if (sub)   o.querySelector('.uw-loading-sub').textContent   = sub;
        o.classList.add('show');
    }
    function hideLoading() {
        var o = document.querySelector('.uw-loading-overlay');
        if (o) o.classList.remove('show');
    }
    window.uwShowLoading = showLoading;
    window.uwHideLoading = hideLoading;


    function confirmModal(opts) {
        return new Promise(function (resolve) {
            var imgUrl = opts.image || '';
            var overlay = el('div', { class: 'uw-confirm-overlay' },
                '<div class="uw-confirm-card">' +
                    (imgUrl ? '<div class="uw-confirm-preview" style="background-image:url(\'' + imgUrl + '\')"></div>' : '') +
                    '<div class="uw-confirm-body">' +
                        '<div class="uw-confirm-title">' + (opts.title || 'Konfirmasi') + '</div>' +
                        '<div class="uw-confirm-desc">' + (opts.desc || '') + '</div>' +
                        (opts.meta ? '<div class="uw-confirm-meta">' + opts.meta + '</div>' : '') +
                    '</div>' +
                    '<div class="uw-confirm-actions">' +
                        '<button type="button" class="uw-btn-cancel">' + (opts.cancelText || 'Batal') + '</button>' +
                        '<button type="button" class="uw-btn-confirm">' + (opts.confirmText || 'Konfirmasi') + '</button>' +
                    '</div>' +
                '</div>');
            document.body.appendChild(overlay);
            requestAnimationFrame(function () { overlay.classList.add('show'); });
            var done = function (val) {
                overlay.classList.remove('show');
                setTimeout(function () { if (overlay.parentNode) overlay.parentNode.removeChild(overlay); }, 200);
                resolve(val);
            };
            overlay.querySelector('.uw-btn-cancel').addEventListener('click', function () { done(false); });
            overlay.querySelector('.uw-btn-confirm').addEventListener('click', function () { done(true); });
            overlay.addEventListener('click', function (e) { if (e.target === overlay) done(false); });
        });
    }
    window.uwConfirm = confirmModal;


    function formatBytes(b) {
        if (b < 1024) return b + ' B';
        if (b < 1024*1024) return (b/1024).toFixed(1) + ' KB';
        return (b/1024/1024).toFixed(2) + ' MB';
    }


    function bindUploadInput(input) {
        if (!input || input.dataset.uwBound) return;
        input.dataset.uwBound = '1';
        var form = input.form;
        if (!form) return;


        input.removeAttribute('onchange');
        input.onchange = null;

        input.addEventListener('change', function () {
            var file = input.files && input.files[0];
            if (!file) return;


            var maxBytes = 5 * 1024 * 1024;
            if (file.size > maxBytes) {
                toast({ type: 'error', title: 'File terlalu besar',
                        msg: 'Ukuran maksimal 5 MB. File kamu ' + formatBytes(file.size) + '.' });
                input.value = '';
                return;
            }
            var allowed = ['image/jpeg', 'image/png', 'image/webp'];
            if (allowed.indexOf(file.type) === -1) {
                toast({ type: 'error', title: 'Format tidak didukung',
                        msg: 'Gunakan JPG, PNG, atau WEBP.' });
                input.value = '';
                return;
            }

            var url = URL.createObjectURL(file);
            confirmModal({
                title: 'Upload foto ini?',
                desc:  'Foto kamu akan dikirim untuk ditinjau admin sebelum tampil di galeri.',
                meta:  '<strong>' + file.name + '</strong> · ' + formatBytes(file.size),
                image: url,
                confirmText: 'Ya, Upload',
                cancelText:  'Batal'
            }).then(function (ok) {
                URL.revokeObjectURL(url);
                if (!ok) {
                    input.value = '';
                    return;
                }
                form.submit();
            });
        });
    }


    function bindDeleteForms() {
        var forms = document.querySelectorAll('.ud-foto-delete-form');
        forms.forEach(function (f) {
            if (f.dataset.uwBound) return;
            f.dataset.uwBound = '1';

            f.removeAttribute('onsubmit');
            f.onsubmit = null;
            f.addEventListener('submit', function (e) {
                e.preventDefault();
                var img = f.parentNode && f.parentNode.querySelector('img');
                confirmModal({
                    title: 'Hapus foto ini?',
                    desc:  'Foto akan dihapus permanen dari galeri kamu.',
                    image: img ? img.src : '',
                    confirmText: 'Ya, Hapus',
                    cancelText:  'Batal'
                }).then(function (ok) {
                    if (ok) {
                        f.submit();
                    }
                });
            });
        });
    }


    function bindSidebarToggle() {
        var sidebar = document.querySelector('.ud-sidebar');
        var toggle  = document.querySelector('.ud-mobile-toggle');
        if (!sidebar || !toggle) return;

        var backdrop = document.querySelector('.ud-sidebar-backdrop');
        if (!backdrop) {
            backdrop = el('div', { class: 'ud-sidebar-backdrop' });
            document.body.appendChild(backdrop);
        }
        var open  = function () { sidebar.classList.add('show'); backdrop.classList.add('show'); };
        var close = function () { sidebar.classList.remove('show'); backdrop.classList.remove('show'); };
        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            sidebar.classList.contains('show') ? close() : open();
        });
        backdrop.addEventListener('click', close);
        sidebar.querySelectorAll('a').forEach(function (a) { a.addEventListener('click', close); });
    }


    function showFlashFromUrl() {
        var p = new URLSearchParams(window.location.search);
        var status = p.get('status');
        if (!status) return;
        var msg = p.get('msg') || '';
        if (status === 'success') {
            toast({ type: 'success', title: 'Berhasil',
                    msg: msg || 'Operasi berhasil dilakukan.' });
        } else if (status === 'error') {
            toast({ type: 'error', title: 'Gagal',
                    msg: msg || 'Terjadi kesalahan, coba lagi.' });
        }

        if (history.replaceState) {
            p.delete('status'); p.delete('msg');
            var qs = p.toString();
            history.replaceState({}, '', window.location.pathname + (qs ? '?' + qs : ''));
        }
    }


    function init() {
        document.querySelectorAll('input[type="file"][name="foto"]').forEach(bindUploadInput);
        bindDeleteForms();
        bindSidebarToggle();
        showFlashFromUrl();

        hideLoading();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
