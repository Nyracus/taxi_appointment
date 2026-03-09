
(function () {
    const form = document.getElementById('appointmentForm');
    const dateInput = document.getElementById('appointment_date');
    const mechanicSelect = document.getElementById('mechanic_id');
    const mechanicStatus = document.getElementById('mechanicStatus');
    const formMessage = document.getElementById('formMessage');

    // Set min date
    const today = new Date().toISOString().split('T')[0];
    if (dateInput) dateInput.setAttribute('min', today);

    function clearMessage() {
        formMessage.className = 'form-message';
        formMessage.textContent = '';
        formMessage.classList.remove('show');
    }

    function showMessage(text, isError) {
        formMessage.className = 'form-message show ' + (isError ? 'error' : 'success');
        formMessage.textContent = text;
    }

    function clearFieldErrors() {
        document.querySelectorAll('.error').forEach(function (el) {
            if (el.id && el.id.startsWith('err_')) el.textContent = '';
        });
    }

    function escapeHtml(s) {
        const div = document.createElement('div');
        div.textContent = s;
        return div.innerHTML;
    }

    // Load mechanics 
    function loadMechanics() {
        const date = dateInput.value.trim();
        mechanicStatus.textContent = '';
        mechanicStatus.innerHTML = '';

        if (!date) {
            mechanicSelect.innerHTML = '<option value="">— Select date first —</option>';
            mechanicSelect.disabled = true;
            return;
        }

        mechanicSelect.disabled = true;
        mechanicSelect.innerHTML = '<option value="">Loading…</option>';
        mechanicStatus.textContent = 'Loading mechanics…';

        const url = 'api_mechanics.php?date=' + encodeURIComponent(date);
        fetch(url)
            .then(function (res) {
                const ct = res.headers.get('content-type') || '';
                if (!ct.includes('application/json')) {
                    return res.text().then(function (text) {
                        throw new Error('Server returned non-JSON. Import setup.sql in phpMyAdmin for database "taxi".');
                    });
                }
                return res.json();
            })
            .then(function (data) {
                if (data.error) {
                    mechanicSelect.innerHTML = '<option value="">— Select date first —</option>';
                    mechanicSelect.disabled = true;
                    mechanicStatus.innerHTML = '<span class="error">' + escapeHtml(data.error) + ' <button type="button" class="btn-link" id="retryMechanics">Retry</button></span>';
                    var retryBtn = document.getElementById('retryMechanics');
                    if (retryBtn) retryBtn.addEventListener('click', loadMechanics);
                    return;
                }
                const mechanics = data.mechanics || [];
                mechanicSelect.disabled = false;
                mechanicSelect.innerHTML = '<option value="">— Choose a mechanic —</option>';
                mechanics.forEach(function (m) {
                    const opt = document.createElement('option');
                    opt.value = m.id;
                    opt.textContent = m.name + ' (' + m.free_slots + ' of 4 slots free)';
                    if (!m.available) opt.disabled = true;
                    mechanicSelect.appendChild(opt);
                });
                mechanicStatus.textContent = mechanics.length + ' mechanic(s) loaded.';
            })
            .catch(function (err) {
                mechanicSelect.innerHTML = '<option value="">— Select date first —</option>';
                mechanicSelect.disabled = true;
                mechanicStatus.innerHTML = '<span class="error">Failed to load mechanics. ' + escapeHtml(err.message || 'Check database.') + ' <button type="button" class="btn-link" id="retryMechanics">Retry</button></span>';
                var retryBtn = document.getElementById('retryMechanics');
                if (retryBtn) retryBtn.addEventListener('click', loadMechanics);
            });
    }

    dateInput.addEventListener('change', loadMechanics);
    dateInput.addEventListener('input', loadMechanics);

    // Validation
    function validateForm() {
        clearFieldErrors();
        let valid = true;

        const name = document.getElementById('client_name').value.trim();
        if (!name) {
            document.getElementById('err_name').textContent = 'Name is required.';
            valid = false;
        }

        const address = document.getElementById('address').value.trim();
        if (!address) {
            document.getElementById('err_address').textContent = 'Address is required.';
            valid = false;
        }

        const phone = document.getElementById('phone').value.trim();
        if (!phone) {
            document.getElementById('err_phone').textContent = 'Phone is required.';
            valid = false;
        }

        const license = document.getElementById('car_license').value.trim();
        if (!license) {
            document.getElementById('err_license').textContent = 'Car license/registration is required.';
            valid = false;
        }

        const engine = document.getElementById('car_engine').value.trim();
        if (!engine) {
            document.getElementById('err_engine').textContent = 'Car engine number is required.';
            valid = false;
        } else if (!/^\d+$/.test(engine)) {
            document.getElementById('err_engine').textContent = 'Car engine number must contain only digits.';
            valid = false;
        }

        const date = dateInput.value.trim();
        if (!date) {
            document.getElementById('err_date').textContent = 'Appointment date is required.';
            valid = false;
        } else if (!/^\d{4}-\d{2}-\d{2}$/.test(date)) {
            document.getElementById('err_date').textContent = 'Please enter a valid date.';
            valid = false;
        }

        const mid = mechanicSelect.value.trim();
        if (!mid) {
            document.getElementById('err_mechanic').textContent = 'Please select a mechanic from the list.';
            valid = false;
        }

        return valid;
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        clearMessage();
        if (!validateForm()) return;

        const btn = form.querySelector('button[type="submit"]');
        btn.disabled = true;

        const formData = new FormData(form);
        fetch('submit_appointment.php', {
            method: 'POST',
            body: formData
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success) {
                    showMessage(data.message, false);
                    form.reset();
                    mechanicSelect.innerHTML = '<option value="">— Select date first —</option>';
                    mechanicSelect.disabled = true;
                    mechanicStatus.textContent = '';
                    loadMechanics();
                } else {
                    showMessage(data.message || 'Something went wrong.', true);
                }
            })
            .catch(function () {
                showMessage('Network error. Please try again.', true);
            })
            .finally(function () {
                btn.disabled = false;
            });
    });
})();
