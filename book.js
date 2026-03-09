
(function () {
    const form = document.getElementById('appointmentForm');
    const mechanicSelect = document.getElementById('mechanic_id');
    const mechanicStatus = document.getElementById('mechanicStatus');
    const formMessage = document.getElementById('formMessage');
    const date = typeof window.bookingDate === 'string' ? window.bookingDate.trim() : '';

    function clearMessage() {
        if (!formMessage) return;
        formMessage.className = 'form-message';
        formMessage.textContent = '';
        formMessage.classList.remove('show');
    }

    function showMessage(text, isError) {
        if (!formMessage) return;
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

    function loadMechanics() {
        if (!date) {
            mechanicSelect.innerHTML = '<option value="">— No date —</option>';
            mechanicSelect.disabled = true;
            return;
        }

        mechanicStatus.textContent = 'Loading mechanics…';
        mechanicSelect.innerHTML = '<option value="">Loading…</option>';
        mechanicSelect.disabled = true;

        const url = 'api_mechanics.php?date=' + encodeURIComponent(date);
        fetch(url)
            .then(function (res) {
                const ct = res.headers.get('content-type') || '';
                if (!ct.includes('application/json')) {
                    return res.text().then(function () {
                        throw new Error('Server returned non-JSON. Import setup.sql in phpMyAdmin for database "taxi".');
                    });
                }
                return res.json();
            })
            .then(function (data) {
                if (data.error) {
                    mechanicSelect.innerHTML = '<option value="">— Error —</option>';
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
                mechanicStatus.textContent = mechanics.length + ' mechanic(s) available.';
            })
            .catch(function (err) {
                mechanicSelect.innerHTML = '<option value="">— Error —</option>';
                mechanicSelect.disabled = true;
                mechanicStatus.innerHTML = '<span class="error">Failed to load mechanics. ' + escapeHtml(err.message || 'Check database.') + ' <button type="button" class="btn-link" id="retryMechanics">Retry</button></span>';
                var retryBtn = document.getElementById('retryMechanics');
                if (retryBtn) retryBtn.addEventListener('click', loadMechanics);
            });
    }

    // Load mechanics
    loadMechanics();

    function validateForm() {
        clearFieldErrors();
        var valid = true;

        var name = document.getElementById('client_name').value.trim();
        if (!name) {
            document.getElementById('err_name').textContent = 'Name is required.';
            valid = false;
        }

        var address = document.getElementById('address').value.trim();
        if (!address) {
            document.getElementById('err_address').textContent = 'Address is required.';
            valid = false;
        }

        var phone = document.getElementById('phone').value.trim();
        if (!phone) {
            document.getElementById('err_phone').textContent = 'Phone is required.';
            valid = false;
        } else if (!/^01[3-9][0-9]{8}$/.test(phone)) {
            document.getElementById('err_phone').textContent = 'Phone must be 11 digits and start with 013–019.';
            valid = false;
        }

        var license = document.getElementById('car_license').value.trim();
        if (!license) {
            document.getElementById('err_license').textContent = 'Car license/registration is required.';
            valid = false;
        } else if (!/^[A-Za-z]{3,15}\s+(Metro\s+)?[A-Za-z]{1,3}-\d{1,4}$/.test(license)) {
            document.getElementById('err_license').textContent = 'Use a format like: Dhaka Metro Ga-1234.';
            valid = false;
        }

        var engine = document.getElementById('car_engine').value.trim();
        if (!engine) {
            document.getElementById('err_engine').textContent = 'Car engine number is required.';
            valid = false;
        } else if (!/^\d{6,12}$/.test(engine)) {
            document.getElementById('err_engine').textContent = 'Car engine number must be 6–12 digits.';
            valid = false;
        }

        var mid = mechanicSelect.value.trim();
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

        var btn = form.querySelector('button[type="submit"]');
        btn.disabled = true;

        var formData = new FormData(form);
        fetch('submit_appointment.php', {
            method: 'POST',
            body: formData
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success) {
                    showMessage(data.message, false);
                    form.reset();
                    form.querySelector('input[name="appointment_date"]').value = date;
                    mechanicSelect.innerHTML = '<option value="">— Choose a mechanic —</option>';
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
