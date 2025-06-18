
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tools Selection Interface</title>

    <style>
        .dropdown-tools {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }

        .dropdown-tools:hover {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .dropdown-tools.active {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .search-input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 14px;
        }

        .dropdown-menu-tools {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-height: 200px;
            overflow-y: auto;
        }

        .dropdown-item-tools {
            border: none;
            background: none;
            padding: 10px 15px;
            width: 100%;
            text-align: left;
            display: flex;
            align-items: center;
            transition: background-color 0.2s;
            cursor: pointer;
        }

        .dropdown-item-tools:hover {
            background-color: #f8f9fa;
        }

        .dropdown-item-tools.disabled {
            color: #6c757d;
            cursor: not-allowed;
        }

        .selected-tools-container {
            min-height: 60px;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 15px;
            background-color: #f8f9fa;
        }

        .selected-tools-container.has-items {
            border-style: solid;
            border-color: #28a745;
            background-color: #f8fff9;
        }

        .tool-tag {
            display: inline-flex;
            align-items: center;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 8px 12px;
            border-radius: 20px;
            margin: 4px;
            font-size: 13px;
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(0, 123, 255, 0.3);
            transition: all 0.3s ease;
        }

        .custom-tool-tag {
            background: linear-gradient(135deg, #17a2b8 0%, #117a8b 100%);
            box-shadow: 0 2px 4px rgba(23, 162, 184, 0.3);
        }

        .tool-tag:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.4);
        }

        .remove-tag {
            background: none;
            border: none;
            color: white;
            margin-left: 8px;
            padding: 0;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .remove-tag:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .empty-state {
            color: #6c757d;
            font-style: italic;
            text-align: center;
        }

        .custom-tool-form {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            border: 2px dashed #dee2e6;
            margin-top: 15px;
        }

        .custom-tool-form.show {
            border-color: #17a2b8;
            background: #f0fdff;
        }

        .btn-add-custom {
            background: linear-gradient(135deg, #17a2b8 0%, #117a8b 100%);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            transition: all 0.3s ease;
        }

        .btn-add-custom:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(23, 162, 184, 0.3);
            color: white;
        }

        .btn-toggle-custom {
            background: linear-gradient(135deg, #6f42c1 0%, #5a2d91 100%);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            transition: all 0.3s ease;
        }

        .btn-toggle-custom:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(111, 66, 193, 0.3);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-body">
                <!-- Tools Section -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Tools & Teknologi:</label>
                    
                    <!-- Dropdown untuk tools yang sudah ada -->
                    <div class="dropdown position-relative mb-3">
                        <div class="dropdown-tools" id="dropdownTools">
                            <div class="d-flex align-items-center justify-content-between">
                                <input type="text" 
                                    class="search-input" 
                                    id="searchTools"
                                    placeholder="Cari atau pilih tools/teknologi...">
                                <i class="fas fa-chevron-down" id="dropdownIconTools"></i>
                            </div>
                        </div>
                        
                        <div class="dropdown-menu-tools position-absolute w-100 mt-1" id="dropdownMenuTools" style="display: none; z-index: 1000;">
                            <!-- Dynamic content akan diisi di sini -->
                        </div>
                    </div>

                    <!-- Button untuk toggle custom tool form -->
                    <div class="text-center mb-3">
                        <button type="button" class="btn-toggle-custom" id="btnToggleCustom">
                            <i class="fas fa-plus-circle me-2"></i>
                            Tambah Tool Custom
                        </button>
                    </div>

                    <!-- Form untuk custom tool -->
                    <div class="custom-tool-form" id="customToolForm" style="display: none;">
                        <h6 class="mb-3">
                            <i class="fas fa-tools me-2 text-info"></i>
                            Tambah Tool/Teknologi Custom
                        </h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Tool/Teknologi:</label>
                                <input type="text" class="form-control" id="customToolName" placeholder="Contoh: Adobe XD, Sketch, etc.">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Deskripsi (Opsional):</label>
                                <input type="text" class="form-control" id="customToolDescription" placeholder="Deskripsi singkat...">
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary btn-sm me-2" id="btnCancelCustom">
                                <i class="fas fa-times me-1"></i>
                                Batal
                            </button>
                            <button type="button" class="btn-add-custom" id="btnAddCustomTool">
                                <i class="fas fa-plus me-1"></i>
                                Tambah Tool
                            </button>
                        </div>
                    </div>

                    <!-- Area untuk menampilkan tools yang dipilih -->
                    <div class="mb-3">
                        <label class="form-label">Tools & Teknologi Terpilih:</label>
                        <div class="selected-tools-container" id="selectedToolsContainer">
                            <div class="empty-state" id="emptyStateTools">
                                <i class="fas fa-plus-circle me-2"></i>
                                Belum ada tools yang dipilih
                            </div>
                        </div>
                    </div>
                    
                    <!-- Info counter tools -->
                    <div class="text-end mb-2">
                        <small class="text-muted">
                            <span id="toolsCounter">0</span> tools dipilih
                        </small>
                    </div>
                </div>

                <!-- Hidden inputs untuk tools -->
                <div id="hiddenToolsInputs">
                    <!-- Hidden inputs tools akan ditambahkan di sini via JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simulasi data tools dari database
        const toolsData = [
            { tool_id: '1', nama_tool: 'Adobe Photoshop', deskripsi_tool: 'Image editing software' },
            { tool_id: '2', nama_tool: 'Figma', deskripsi_tool: 'UI/UX design tool' },
            { tool_id: '3', nama_tool: 'Visual Studio Code', deskripsi_tool: 'Code editor' },
            { tool_id: '4', nama_tool: 'Docker', deskripsi_tool: 'Containerization platform' },
            { tool_id: '5', nama_tool: 'Git', deskripsi_tool: 'Version control system' },
            { tool_id: '6', nama_tool: 'Postman', deskripsi_tool: 'API testing tool' },
            { tool_id: '7', nama_tool: 'MySQL', deskripsi_tool: 'Database management system' },
            { tool_id: '8', nama_tool: 'React', deskripsi_tool: 'JavaScript library' }
        ];

        // State variables
        let selectedTools = [];
        let filteredToolsData = [...toolsData];

        // DOM Elements
        const dropdownTools = document.getElementById('dropdownTools');
        const dropdownMenuTools = document.getElementById('dropdownMenuTools');
        const searchToolsInput = document.getElementById('searchTools');
        const selectedToolsContainer = document.getElementById('selectedToolsContainer');
        const toolsCounter = document.getElementById('toolsCounter');
        const dropdownIconTools = document.getElementById('dropdownIconTools');
        const hiddenToolsInputsContainer = document.getElementById('hiddenToolsInputs');

        // Custom tool elements
        const btnToggleCustom = document.getElementById('btnToggleCustom');
        const customToolForm = document.getElementById('customToolForm');
        const customToolName = document.getElementById('customToolName');
        const customToolDescription = document.getElementById('customToolDescription');
        const btnAddCustomTool = document.getElementById('btnAddCustomTool');
        const btnCancelCustom = document.getElementById('btnCancelCustom');

        // Initialize
        initializeTools();

        function initializeTools() {
            renderToolsDropdownItems();
            updateToolsDisplay();
            updateToolsHiddenInputs();
        }

        // Event Listeners untuk Tools
        dropdownTools.addEventListener('click', function(e) {
            if (e.target !== searchToolsInput) {
                toggleDropdown('tools');
            }
        });

        searchToolsInput.addEventListener('input', function() {
            filterTools(this.value);
        });

        searchToolsInput.addEventListener('focus', function() {
            showDropdown('tools');
        });

        // Custom tool event listeners
        btnToggleCustom.addEventListener('click', function() {
            toggleCustomToolForm();
        });

        btnCancelCustom.addEventListener('click', function() {
            hideCustomToolForm();
        });

        btnAddCustomTool.addEventListener('click', function() {
            addCustomTool();
        });

        // Enter key support for custom tool
        customToolName.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                addCustomTool();
            }
        });

        // Click outside to close dropdown
        document.addEventListener('click', function(e) {
            if (!dropdownTools.contains(e.target) && !dropdownMenuTools.contains(e.target)) {
                hideDropdown('tools');
            }
        });

        function toggleDropdown(type) {
            if (type === 'tools') {
                if (dropdownMenuTools.style.display === 'none') {
                    showDropdown('tools');
                } else {
                    hideDropdown('tools');
                }
            }
        }

        function showDropdown(type) {
            if (type === 'tools') {
                dropdownMenuTools.style.display = 'block';
                dropdownTools.classList.add('active');
                dropdownIconTools.classList.replace('fa-chevron-down', 'fa-chevron-up');
            }
        }

        function hideDropdown(type) {
            if (type === 'tools') {
                dropdownMenuTools.style.display = 'none';
                dropdownTools.classList.remove('active');
                dropdownIconTools.classList.replace('fa-chevron-up', 'fa-chevron-down');
                searchToolsInput.value = '';
                filteredToolsData = [...toolsData];
                renderToolsDropdownItems();
            }
        }

        function filterTools(searchTerm) {
            filteredToolsData = toolsData.filter(item => 
                item.nama_tool.toLowerCase().includes(searchTerm.toLowerCase()) &&
                !selectedTools.some(selected => selected.tool_id === item.tool_id && !selected.is_custom)
            );
            renderToolsDropdownItems();
            showDropdown('tools');
        }

        function renderToolsDropdownItems() {
            const availableItems = filteredToolsData.filter(item => 
                !selectedTools.some(selected => selected.tool_id === item.tool_id && !selected.is_custom)
            );

            if (availableItems.length === 0) {
                dropdownMenuTools.innerHTML = `
                    <div class="dropdown-item-tools disabled">
                        <i class="fas fa-search me-2"></i>
                        Tidak ada tools yang tersedia
                    </div>
                `;
                return;
            }

            dropdownMenuTools.innerHTML = availableItems.map(item => `
                <button class="dropdown-item-tools" onclick="addTool('${item.tool_id}', '${item.nama_tool.replace(/'/g, "\\'")}', false)">
                    <i class="fas fa-plus-circle me-2 text-success"></i>
                    <div>
                        <div>${item.nama_tool}</div>
                        ${item.deskripsi_tool ? `<small class="text-muted">${item.deskripsi_tool}</small>` : ''}
                    </div>
                </button>
            `).join('');
        }

        function toggleCustomToolForm() {
            if (customToolForm.style.display === 'none') {
                showCustomToolForm();
            } else {
                hideCustomToolForm();
            }
        }

        function showCustomToolForm() {
            customToolForm.style.display = 'block';
            customToolForm.classList.add('show');
            btnToggleCustom.innerHTML = '<i class="fas fa-minus-circle me-2"></i>Tutup Form Custom';
            customToolName.focus();
        }

        function hideCustomToolForm() {
            customToolForm.style.display = 'none';
            customToolForm.classList.remove('show');
            btnToggleCustom.innerHTML = '<i class="fas fa-plus-circle me-2"></i>Tambah Tool Custom';
            customToolName.value = '';
            customToolDescription.value = '';
        }

        function addCustomTool() {
            const toolName = customToolName.value.trim();
            if (!toolName) {
                alert('Nama tool harus diisi!');
                customToolName.focus();
                return;
            }

            // Check if custom tool already exists
            if (selectedTools.some(tool => tool.is_custom && tool.nama.toLowerCase() === toolName.toLowerCase())) {
                alert('Tool custom dengan nama tersebut sudah ada!');
                customToolName.focus();
                return;
            }

            const customTool = {
                tool_id: null,
                nama: toolName,
                deskripsi: customToolDescription.value.trim(),
                is_custom: true
            };

            selectedTools.push(customTool);
            updateToolsDisplay();
            updateToolsHiddenInputs();
            
            // Reset form
            customToolName.value = '';
            customToolDescription.value = '';
            customToolName.focus();

            // Show success feedback
            showToast('Tool custom berhasil ditambahkan!', 'success');
        }

        function addTool(id, nama, isCustom = false) {
            if (!selectedTools.some(item => item.tool_id === id && item.is_custom === isCustom)) {
                selectedTools.push({ 
                    tool_id: id, 
                    nama: nama, 
                    is_custom: isCustom,
                    deskripsi: ''
                });
                updateToolsDisplay();
                updateToolsHiddenInputs();
                renderToolsDropdownItems();
                searchToolsInput.value = '';
                searchToolsInput.focus();
            }
        }

        function removeTool(id, isCustom) {
            if (isCustom) {
                selectedTools = selectedTools.filter(item => !(item.is_custom && item.nama === id));
            } else {
                selectedTools = selectedTools.filter(item => !(item.tool_id === id && !item.is_custom));
            }
            updateToolsDisplay();
            updateToolsHiddenInputs();
            renderToolsDropdownItems();
        }

        function updateToolsDisplay() {
            if (selectedTools.length === 0) {
                selectedToolsContainer.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-plus-circle me-2"></i>
                        Belum ada tools yang dipilih
                    </div>
                `;
                selectedToolsContainer.classList.remove('has-items');
            } else {
                selectedToolsContainer.innerHTML = selectedTools.map(item => {
                    const tagClass = item.is_custom ? 'custom-tool-tag' : 'tool-tag';
                    const icon = item.is_custom ? 'fas fa-star' : 'fas fa-tools';
                    const removeId = item.is_custom ? item.nama : item.tool_id;
                    
                    return `
                        <span class="tool-tag ${tagClass}">
                            <i class="${icon} me-2"></i>
                            ${item.nama}
                            ${item.is_custom ? '<small class="ms-1">(custom)</small>' : ''}
                            <button class="remove-tag" onclick="removeTool('${removeId}', ${item.is_custom})" title="Hapus">
                                <i class="fas fa-times"></i>
                            </button>
                        </span>
                    `;
                }).join('');
                selectedToolsContainer.classList.add('has-items');
            }
            
            toolsCounter.textContent = selectedTools.length;
        }

        function updateToolsHiddenInputs() {
            hiddenToolsInputsContainer.innerHTML = '';
            
            selectedTools.forEach((item, index) => {
                if (item.is_custom) {
                    // Custom tool
                    const hiddenToolId = document.createElement('input');
                    hiddenToolId.type = 'hidden';
                    hiddenToolId.name = 'custom_tools[]';
                    hiddenToolId.value = JSON.stringify({
                        nama: item.nama,
                        deskripsi: item.deskripsi
                    });
                    hiddenToolsInputsContainer.appendChild(hiddenToolId);
                } else {
                    // Regular tool
                    const hiddenToolId = document.createElement('input');
                    hiddenToolId.type = 'hidden';
                    hiddenToolId.name = 'tools[]';
                    hiddenToolId.value = item.tool_id;
                    hiddenToolsInputsContainer.appendChild(hiddenToolId);
                }
            });
        }

        function showToast(message, type = 'info') {
            // Simple toast notification
            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'success' ? 'success' : 'info'} position-fixed`;
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; animation: slideIn 0.3s ease;';
            toast.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
                ${message}
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

        // Public function untuk reset tools
        window.resetTools = function() {
            selectedTools = [];
            updateToolsDisplay();
            updateToolsHiddenInputs();
            renderToolsDropdownItems();
            hideCustomToolForm();
        };

        // Add some sample data for demo
        setTimeout(() => {
            addTool('3', 'Visual Studio Code', false);
            addCustomTool();
            customToolName.value = 'Adobe XD';
            customToolDescription.value = 'Prototyping tool for UX design';
            addCustomTool();
        }, 1000);
    </script>

    <style>
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</body>
</html>