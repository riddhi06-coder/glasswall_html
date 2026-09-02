/*
 * Front-end validation + AJAX submit for the Contact and Careers forms.
 * - inline errors below each field, red highlight
 * - submit button disabled + "Submitting..." while sending
 * - posts to the PHP endpoint, shows the returned status / field errors
 */
(function () {
    var NAME_RE  = /^[A-Za-z][A-Za-z .'\-]*$/;   // letters, space, . ' -  (no digits)
    var EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    var MAX_RESUME = 3 * 1024 * 1024;             // 3 MB

    function field(form, name) { return form.querySelector('[name="' + name + '"]'); }
    function val(form, name) { var el = field(form, name); return el ? el.value.trim() : ''; }

    function setError(form, name, msg) {
        var slot = form.querySelector('.form-error[data-for="' + name + '"]');
        var el = field(form, name);
        if (slot) slot.textContent = msg || '';
        if (el) el.classList.toggle('field-invalid', !!msg);
    }
    function clearErrors(form) {
        form.querySelectorAll('.form-error').forEach(function (e) { e.textContent = ''; });
        form.querySelectorAll('.field-invalid').forEach(function (e) { e.classList.remove('field-invalid'); });
    }
    function status(form, msg, type) {
        var s = form.querySelector('[data-status]');
        if (!s) return;
        s.textContent = msg || '';
        s.className = 'form-status' + (type ? ' ' + type : '');
    }
    function setSubmitting(btn, on) {
        if (!btn) return;
        var txt = btn.querySelector('.tp-btn-text');
        if (on) {
            btn.disabled = true;
            if (txt) { btn.setAttribute('data-orig', txt.textContent.trim()); txt.textContent = 'Submitting...'; }
        } else {
            btn.disabled = false;
            if (txt && btn.hasAttribute('data-orig')) txt.textContent = btn.getAttribute('data-orig');
        }
    }

    // Keep phone fields numeric (max 10) + clear an error as the user types.
    document.addEventListener('input', function (e) {
        var el = e.target;
        if (!el || !el.name) return;
        if (el.name === 'phone' || el.name === 'contact_no') {
            el.value = el.value.replace(/\D/g, '').slice(0, 10);
        }
        if (el.classList && el.classList.contains('field-invalid')) {
            var form = el.closest('form');
            if (form) setError(form, el.name, '');
        }
    });

    function validateContact(form) {
        var firstBad = null;
        function fail(f, m) { setError(form, f, m); if (!firstBad) firstBad = f; }
        if (!NAME_RE.test(val(form, 'name')) || val(form, 'name').length < 2) fail('name', 'Please enter a valid name (letters only).');
        if (!EMAIL_RE.test(val(form, 'email'))) fail('email', 'Please enter a valid email address.');
        if (val(form, 'company') === '') fail('company', 'Please enter your company.');
        if (!/^\d{10}$/.test(val(form, 'phone'))) fail('phone', 'Please enter a valid 10-digit phone number.');
        if (val(form, 'message').length < 5) fail('message', 'Please enter your message.');
        focusFirst(form, firstBad);
        return !firstBad;
    }

    function validateCareer(form) {
        var firstBad = null;
        function fail(f, m) { setError(form, f, m); if (!firstBad) firstBad = f; }
        if (!NAME_RE.test(val(form, 'first_name')) || val(form, 'first_name').length < 2) fail('first_name', 'Please enter a valid first name (letters only).');
        if (!NAME_RE.test(val(form, 'last_name')) || val(form, 'last_name').length < 2) fail('last_name', 'Please enter a valid last name (letters only).');
        if (!EMAIL_RE.test(val(form, 'email'))) fail('email', 'Please enter a valid email address.');
        if (!/^\d{10}$/.test(val(form, 'contact_no'))) fail('contact_no', 'Please enter a valid 10-digit contact number.');
        var fileEl = field(form, 'resume');
        var f = fileEl && fileEl.files && fileEl.files[0];
        if (!f) {
            fail('resume', 'Please upload your resume.');
        } else {
            var ext = (f.name.split('.').pop() || '').toLowerCase();
            if (['pdf', 'doc', 'docx'].indexOf(ext) < 0) fail('resume', 'Resume must be a PDF, DOC or DOCX file.');
            else if (f.size > MAX_RESUME) fail('resume', 'Resume is too large (max 3 MB).');
        }
        focusFirst(form, firstBad);
        return !firstBad;
    }

    function focusFirst(form, name) {
        if (!name) return;
        var el = field(form, name);
        if (el && el.focus) el.focus();
    }

    function attach(form, validate) {
        if (!form || form.dataset.formInit) return;
        form.dataset.formInit = '1';
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            clearErrors(form);
            status(form, '', '');
            if (!validate(form)) { status(form, 'Please correct the highlighted fields.', 'error'); return; }

            var btn = form.querySelector('button[type="submit"]');
            setSubmitting(btn, true);

            fetch(form.getAttribute('action'), { method: 'POST', body: new FormData(form) })
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    if (res && res.ok) {
                        // Redirect to the thank-you page (message adapts to the form type).
                        var type = form.id === 'jobApplicationForm' ? 'careers' : 'contact';
                        var url = 'thank-you.html?type=' + type;
                        if (type === 'careers') {
                            var roleEl = form.querySelector('[name="job_role"]');
                            if (roleEl && roleEl.value) url += '&role=' + encodeURIComponent(roleEl.value);
                        }
                        status(form, res.message || 'Submitted successfully. Redirecting…', 'success');
                        window.location.href = url;
                        return;
                    } else if (res && res.errors) {
                        Object.keys(res.errors).forEach(function (k) { setError(form, k, res.errors[k]); });
                        focusFirst(form, Object.keys(res.errors)[0]);
                        status(form, 'Please correct the highlighted fields.', 'error');
                    } else {
                        status(form, (res && res.message) || 'Something went wrong. Please try again.', 'error');
                    }
                })
                .catch(function () { status(form, 'Network error. Please try again.', 'error'); })
                .finally(function () { setSubmitting(btn, false); });
        });
    }

    // When the Apply modal opens, capture the job role from the clicked job card.
    function initJobModal() {
        var modal = document.getElementById('jobApplyModal');
        if (!modal || modal.dataset.roleInit) return;
        modal.dataset.roleInit = '1';
        modal.addEventListener('show.bs.modal', function (e) {
            var btn = e.relatedTarget, role = '';
            if (btn) {
                var content = btn.closest('.tp-services-content') || btn.closest('.accordion-collapse');
                var titleEl = content && content.querySelector('.tp-services-title');
                if (titleEl) role = titleEl.textContent.trim().replace(/\s+/g, ' ');
            }
            var input = document.getElementById('jobRole');
            if (input) input.value = role;
        });
    }

    function init() {
        attach(document.getElementById('contactForm'), validateContact);
        attach(document.getElementById('jobApplicationForm'), validateCareer);
        initJobModal();
    }
    if (document.readyState !== 'loading') init();
    document.addEventListener('DOMContentLoaded', init);
    setTimeout(init, 80);
})();
