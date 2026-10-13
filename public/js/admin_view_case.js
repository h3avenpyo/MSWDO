// Handles inline editing for admin_view_case

document.addEventListener('DOMContentLoaded', function () {
    const editBtn = document.querySelector('.btn-primary.ml-2');
    let editing = false;
    let originalHtml = {};

    if (editBtn) {
        editBtn.addEventListener('click', function (e) {
            e.preventDefault();
            if (!editing) {
                enterEditMode();
            } else {
                saveCaseEdit();
            }
        });
    }

    function enterEditMode() {
        editing = true;
        editBtn.innerHTML = '<i class="fas fa-save"></i> Save';

        // Map field names to <th> text and input types
        const fields = [
            {th: 'Case Number', name: 'case_number', type: 'text'},
            {th: 'Name', name: 'victim_name', type: 'text'},
            {th: 'Age', name: 'victim_age', type: 'number'},
            {th: 'Gender', name: 'victim_gender', type: 'text'},
            {th: 'Contact', name: 'victim_contact', type: 'text'},
            {th: 'Address', name: 'victim_address', type: 'text'},
            {th: 'Status', name: 'status', type: 'select'},
            {th: 'Type of Abuse', name: 'abuse_type', type: 'text'},
            {th: 'Barangay', name: 'barangay_name', type: 'text'},
            {th: 'Name', name: 'abuser_name', type: 'text', section: 'Perpetrator'},
            {th: 'Age', name: 'abuser_age', type: 'number', section: 'Perpetrator'},
            {th: 'Relationship to Victim', name: 'abuse_relationship', type: 'text'},
            {th: 'Date of Incident', name: 'incident_date', type: 'date'},
            {th: 'Time of Incident', name: 'incident_time', type: 'time'},
            {th: 'Location', name: 'incident_location', type: 'text'},
            {th: 'Details', name: 'incident_details', type: 'text'},
            {th: 'Name', name: 'witness_name', type: 'text', section: 'Witness'},
            {th: 'Contact', name: 'witness_contact', type: 'text', section: 'Witness'},
        ];

        // Find all tables in the card
        document.querySelectorAll('.table-bordered').forEach(table => {
            for (const row of table.rows) {
                const th = row.querySelector('th');
                const td = row.querySelector('td');
                if (!th || !td) continue;
                const label = th.textContent.trim();
                const field = fields.find(f => f.th === label);
                if (field) {
                    originalHtml[field.name] = td.innerHTML;
                    if (field.type === 'select' && field.name === 'status') {
                        td.innerHTML = `<select name="status" class="form-control">
                            <option value="Pending" ${td.textContent.trim() === 'Pending' ? 'selected' : ''}>Pending</option>
                            <option value="Case Filed" ${td.textContent.trim() === 'Case Filed' ? 'selected' : ''}>Case Filed</option>
                        </select>`;
                    } else {
                        td.innerHTML = `<input type="${field.type}" class="form-control" name="${field.name}" value="${td.textContent.trim()}">`;
                    }
                }
            }
        });
    }

    function saveCaseEdit() {
        // Collect data from inputs
        let data = {};
        document.querySelectorAll('input.form-control, select.form-control').forEach(input => {
            data[input.name] = input.value;
        });

        // Send AJAX request to update (implement endpoint in controller)
        fetch(window.location.pathname + '/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(res => {
            if (res.success) {
                if (window.notify) { notify.success('Case updated successfully!'); }
                window.location.reload();
            } else {
                if (window.notify) { notify.error('Failed to update case.'); }
            }
        })
        .catch(() => {
            if (window.notify) { notify.error('Error updating case.'); }
        });
    }

});
