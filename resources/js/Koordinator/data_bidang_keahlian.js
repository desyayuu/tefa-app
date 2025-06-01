
        // Sample data bidang keahlian (dalam implementasi nyata, ini akan dari database)
        const bidangKeahlianData = [
            { id: 1, nama: 'Full-Stack Development' },
            { id: 2, nama: 'Front-End Development' },
            { id: 3, nama: 'Back-End Development' },
            { id: 4, nama: 'Mobile Development' },
            { id: 5, nama: 'UI/UX Design' },
            { id: 6, nama: 'Database Management' },
            { id: 7, nama: 'DevOps' },
            { id: 8, nama: 'Machine Learning' },
            { id: 9, nama: 'Data Science' },
            { id: 10, nama: 'Cybersecurity' },
            { id: 11, nama: 'Cloud Computing' },
            { id: 12, nama: 'Artificial Intelligence' }
        ];

        let selectedBidangKeahlian = [];
        let filteredData = [...bidangKeahlianData];

        // DOM Elements
        const dropdownBidangKeahlian = document.getElementById('dropdownBidangKeahlian');
        const dropdownMenuBidang = document.getElementById('dropdownMenuBidang');
        const searchInput = document.getElementById('searchBidangKeahlian');
        const selectedContainer = document.getElementById('selectedBidangContainer');
        const emptyState = document.getElementById('emptyState');
        const bidangCounter = document.getElementById('bidangCounter');
        const dropdownIcon = document.getElementById('dropdownIcon');

        // Initialize
        renderDropdownItems();
        updateSelectedDisplay();

        // Event Listeners
        dropdownBidangKeahlian.addEventListener('click', function(e) {
            if (e.target !== searchInput) {
                toggleDropdown();
            }
        });

        searchInput.addEventListener('input', function() {
            filterBidangKeahlian(this.value);
        });

        searchInput.addEventListener('focus', function() {
            showDropdown();
        });

        // Click outside to close dropdown
        document.addEventListener('click', function(e) {
            if (!dropdownBidangKeahlian.contains(e.target) && !dropdownMenuBidang.contains(e.target)) {
                hideDropdown();
            }
        });

        function toggleDropdown() {
            if (dropdownMenuBidang.style.display === 'none') {
                showDropdown();
            } else {
                hideDropdown();
            }
        }

        function showDropdown() {
            dropdownMenuBidang.style.display = 'block';
            dropdownBidangKeahlian.classList.add('active');
            dropdownIcon.classList.replace('fa-chevron-down', 'fa-chevron-up');
        }

        function hideDropdown() {
            dropdownMenuBidang.style.display = 'none';
            dropdownBidangKeahlian.classList.remove('active');
            dropdownIcon.classList.replace('fa-chevron-up', 'fa-chevron-down');
            searchInput.value = '';
            filteredData = [...bidangKeahlianData];
            renderDropdownItems();
        }

        function filterBidangKeahlian(searchTerm) {
            filteredData = bidangKeahlianData.filter(item => 
                item.nama.toLowerCase().includes(searchTerm.toLowerCase()) &&
                !selectedBidangKeahlian.some(selected => selected.id === item.id)
            );
            renderDropdownItems();
            showDropdown();
        }

        function renderDropdownItems() {
            const availableItems = filteredData.filter(item => 
                !selectedBidangKeahlian.some(selected => selected.id === item.id)
            );

            if (availableItems.length === 0) {
                dropdownMenuBidang.innerHTML = `
                    <div class="dropdown-item-bidang disabled">
                        <i class="fas fa-search me-2"></i>
                        Tidak ada bidang keahlian yang tersedia
                    </div>
                `;
                return;
            }

            dropdownMenuBidang.innerHTML = availableItems.map(item => `
                <button class="dropdown-item-bidang" onclick="addBidangKeahlian(${item.id}, '${item.nama}')">
                    <i class="fas fa-plus-circle me-2 text-success"></i>
                    ${item.nama}
                </button>
            `).join('');
        }

        function addBidangKeahlian(id, nama) {
            if (!selectedBidangKeahlian.some(item => item.id === id)) {
                selectedBidangKeahlian.push({ id, nama });
                updateSelectedDisplay();
                renderDropdownItems();
                searchInput.value = '';
                searchInput.focus();
            }
        }

        function removeBidangKeahlian(id) {
            selectedBidangKeahlian = selectedBidangKeahlian.filter(item => item.id !== id);
            updateSelectedDisplay();
            renderDropdownItems();
        }

        function updateSelectedDisplay() {
            if (selectedBidangKeahlian.length === 0) {
                selectedContainer.innerHTML = `
                    <div class="empty-state" id="emptyState">
                        <i class="fas fa-plus-circle me-2"></i>
                        Belum ada bidang keahlian yang dipilih
                    </div>
                `;
                selectedContainer.classList.remove('has-items');
            } else {
                selectedContainer.innerHTML = selectedBidangKeahlian.map(item => `
                    <span class="bidang-keahlian-tag">
                        <i class="fas fa-code me-2"></i>
                        ${item.nama}
                        <button class="remove-tag" onclick="removeBidangKeahlian(${item.id})" title="Hapus">
                            <i class="fas fa-times"></i>
                        </button>
                    </span>
                `).join('');
                selectedContainer.classList.add('has-items');
            }
            
            bidangCounter.textContent = selectedBidangKeahlian.length;
        }

        // Keyboard navigation
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideDropdown();
            }
        });