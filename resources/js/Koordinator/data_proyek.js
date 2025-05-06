document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts
    const alerts = document.querySelectorAll('.alert-dismissible');
    
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 3000); 
    });
    
    // Show/hide leader options based on selected type
    const leaderTypeSelect = document.getElementById('leader_type');
    const dosenSection = document.getElementById('dosen_leader_section');
    const profesionalSection = document.getElementById('profesional_leader_section');
    const dosenSelect = document.getElementById('dosen_leader_id');
    const profesionalSelect = document.getElementById('profesional_leader_id');
    
    if (leaderTypeSelect) {
        console.log('Leader type select found');
        leaderTypeSelect.addEventListener('change', function() {
            const leaderType = this.value;
            console.log('Leader type changed to:', leaderType);
            
            // Hide both sections first
            dosenSection.style.display = 'none';
            profesionalSection.style.display = 'none';
            
            // Disable both selects to prevent submitting the wrong value
            dosenSelect.disabled = true;
            profesionalSelect.disabled = false; // Change to false here to debug
            
            // Show appropriate section based on selection
            if (leaderType === 'Dosen') {
                console.log('Showing dosen section');
                dosenSection.style.display = 'block';
                dosenSelect.disabled = false;
            } else if (leaderType === 'Profesional') {
                console.log('Showing profesional section');
                profesionalSection.style.display = 'block';
                profesionalSelect.disabled = false;
            }
        });
    } else {
        console.log('Leader type select not found!');
    }
});