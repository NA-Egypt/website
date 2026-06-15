<x-layout>

    <x-backhead>{{ __('messages.Create Form') ?? 'Create Form' }}</x-backhead>

    <div class="container-fluid px-4 pb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 fw-bold" style="color: var(--text-primary);">{{ __('messages.Forms Creator') ?? 'Forms Creator' }}</h4>
            <a href="{{ route('forms.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-arrow-left"></i> {{ __('messages.Back to Forms') ?? 'Back' }}
            </a>
        </div>

        <form action="{{ route('forms.store') }}" method="POST" id="form-builder-form">
            @csrf
            <div class="row g-4">
                <!-- Left Column: Settings and Field Builder Configuration -->
                <div class="col-lg-6">
                    <!-- Form Metadata Settings -->
                    <div class="glass-card p-4 mb-4">
                        <h5 class="fw-bold mb-3 d-flex align-items-center gap-2" style="color: var(--text-primary);">
                            <i class="bi bi-sliders text-primary"></i> {{ __('messages.Form Settings') ?? 'Form Settings' }}
                        </h5>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold small">{{ __('messages.Form Title') ?? 'Form Title' }}</label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="{{ __('messages.e.g. Annual Member Survey') ?? 'e.g. Annual Member Survey' }}" required value="{{ old('title') }}" oninput="updatePreview()">
                        </div>

                        <div class="mb-3">
                            <label for="settings_subtitle" class="form-label fw-semibold small">{{ __('messages.Form Subtitle') ?? 'Form Subtitle' }}</label>
                            <input type="text" name="settings[subtitle]" id="settings_subtitle" class="form-control" placeholder="{{ __('messages.e.g. Please take 5 minutes to fill out this form') ?? 'e.g. Please take 5 minutes to fill out this form' }}" value="{{ old('settings.subtitle') }}" oninput="updatePreview()">
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="type" class="form-label fw-semibold small">{{ __('messages.Form Type') ?? 'Form Type' }}</label>
                                <select name="type" id="type" class="form-select" required onchange="updatePreview()">
                                    <option value="survey">{{ __('messages.Survey') ?? 'Survey' }}</option>
                                    <option value="event_registration">{{ __('messages.Event Registration') ?? 'Event Entry Registration' }}</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label fw-semibold small">{{ __('messages.Form Status') ?? 'Form Status' }}</label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="draft" selected>{{ __('messages.Draft') ?? 'Draft' }}</option>
                                    <option value="published">{{ __('messages.Published') ?? 'Published' }}</option>
                                    <option value="unpublished">{{ __('messages.Unpublished') ?? 'Unpublished (Deactivated)' }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3 mt-3" id="icon-selector-group">
                            <label for="settings_icon" class="form-label fw-semibold small">{{ __('messages.Form Header Icon') ?? 'Form Header Icon' }}</label>
                            <select name="settings[icon]" id="settings_icon" class="form-select" onchange="updatePreview()">
                                <option value="bi-clipboard2-data">📋 Clipboard Data</option>
                                <option value="bi-chat-left-text">💬 Chat Left Text</option>
                                <option value="bi-card-checklist">☑️ Card Checklist</option>
                                <option value="bi-emoji-smile">😊 Emoji Smile</option>
                                <option value="bi-star">⭐ Star</option>
                                <option value="bi-envelope">✉️ Envelope</option>
                                <option value="bi-hand-thumbs-up">👍 Hand Thumbs Up</option>
                                <option value="bi-heart">❤️ Heart</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="settings_emails" class="form-label fw-semibold small">{{ __('messages.Notification Emails') ?? 'Notification Emails (comma separated, max 3)' }}</label>
                            <input type="text" name="settings[emails]" id="settings_emails" class="form-control" placeholder="e.g. admin1@example.com, admin2@example.com">
                        </div>
                    </div>

                    <!-- Fields Configurator -->
                    <div class="glass-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0 d-flex align-items-center gap-2" style="color: var(--text-primary);">
                                <i class="bi bi-list-task text-primary"></i> {{ __('messages.Configure Fields') ?? 'Configure Fields' }}
                            </h5>
                            <button type="button" class="btn btn-sm btn-success rounded-pill px-3 d-flex align-items-center gap-1" onclick="addField()">
                                <i class="bi bi-plus-circle-fill"></i> {{ __('messages.Add Field') ?? 'Add Field' }}
                            </button>
                        </div>

                        <div id="fields-container" class="d-flex flex-column gap-3 mb-4">
                            <!-- Dynamically added fields config cards -->
                        </div>

                        <div id="empty-state" class="text-center py-5 text-secondary border rounded-3 border-dashed" style="border-style: dashed !important; border-width: 2px !important; border-color: var(--glass-border) !important;">
                            <i class="bi bi-menu-app display-4 opacity-50"></i>
                            <p class="mt-2 mb-0">{{ __('messages.No fields added yet. Click "Add Field" to begin.') ?? 'No fields added yet. Click "Add Field" to begin.' }}</p>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary rounded-pill py-2.5 fw-bold shadow-sm" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border: none;">
                                <i class="bi bi-cloud-arrow-up-fill me-1"></i> {{ __('messages.Save and Create Form') ?? 'Save and Create Form' }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Real-Time Live Preview -->
                <div class="col-lg-6 d-none d-lg-block">
                    <div class="position-sticky" style="top: 90px; z-index: 10;">
                        <div class="glass-card p-4 mb-2 bg-light border-0">
                            <div class="d-flex justify-content-between align-items-center pb-2 border-bottom mb-3" style="border-color: var(--glass-border) !important;">
                                <h6 class="fw-bold mb-0 text-secondary d-flex align-items-center gap-2">
                                    <i class="bi bi-eye-fill text-success"></i> {{ __('messages.Real-time Live Preview') ?? 'Real-time Live Preview' }}
                                </h6>
                                <span class="badge bg-success rounded-pill px-2">Live</span>
                            </div>

                            <!-- Mockup Container mimicking the public form wrapper -->
                            <div class="card shadow border-0 rounded-4 overflow-hidden" style="background: white; border: 1px solid rgba(0,0,0,0.05) !important;">
                                <div id="preview-header" class="text-center py-5 px-4 border-bottom" style="background: rgba(255, 255, 255, 0.3); border-color: rgba(0, 0, 0, 0.05) !important;">
                                    <div id="preview-icon-wrapper" class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary shadow-sm animate__animated animate__zoomIn" style="width: 72px; height: 72px; background-color: rgba(37, 99, 235, 0.1) !important;">
                                        <i id="preview-icon" class="bi bi-clipboard2-data" style="font-size: 2.25rem;"></i>
                                    </div>
                                    <h4 id="preview-title" class="fw-bold mb-2" style="color: #0f172a !important; font-size: 1.5rem; letter-spacing: -0.5px;">Form Title</h4>
                                    <p id="preview-subtitle" class="text-secondary small mb-3" style="display: none;"></p>
                                    <span id="preview-type-badge" class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: rgba(37, 99, 235, 0.1) !important; border-color: rgba(37, 99, 235, 0.2) !important;">Survey</span>
                                </div>

                                <div class="card-body p-4">
                                    <div id="preview-fields-container" class="d-flex flex-column gap-3">
                                        <!-- Real-time mockup elements rendered here -->
                                    </div>
                                    <div class="d-grid mt-4">
                                        <button type="button" class="btn btn-primary rounded-pill py-2 fw-bold disabled" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border: none; opacity: 0.8;">
                                            {{ __('messages.Submit Form') ?? 'Submit Form' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Template for fields configuration card -->
    <template id="field-template">
        <div class="field-item card p-3 border shadow-sm position-relative" style="background: rgba(255, 255, 255, 0.4) !important; border-color: var(--glass-border) !important; transition: all 0.2s;">
            <div class="row g-3">
                <!-- Order controls -->
                <div class="col-1 d-flex flex-column justify-content-center align-items-center gap-1">
                    <button type="button" class="btn btn-sm btn-light border p-1 move-up-btn" onclick="moveFieldUp(this)" title="Move Up">
                        <i class="bi bi-arrow-up"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-light border p-1 move-down-btn" onclick="moveFieldDown(this)" title="Move Down">
                        <i class="bi bi-arrow-down"></i>
                    </button>
                </div>

                <!-- Label -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-secondary">{{ __('messages.Field Label / Name') ?? 'Field Label / Name' }}</label>
                    <input type="text" class="form-control label-input" placeholder="{{ __('messages.e.g. Your Name') ?? 'e.g. Your Name' }}" required oninput="updatePreview()">
                </div>

                <!-- Type selector -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-secondary">{{ __('messages.Field Input Type') ?? 'Field Input Type' }}</label>
                    <select class="form-select type-select" onchange="handleTypeChange(this)" required>
                        <optgroup label="{{ __('messages.Standard Fields') ?? 'Standard Fields' }}">
                            <option value="text">{{ __('messages.Single Line Text') ?? 'Single Line Text' }}</option>
                            <option value="textarea">{{ __('messages.Paragraph/Textarea') ?? 'Paragraph/Textarea' }}</option>
                            <option value="phone">{{ __('messages.Phone Number') ?? 'Phone Number' }}</option>
                            <option value="number">{{ __('messages.Number') ?? 'Number' }}</option>
                            <option value="email">{{ __('messages.Email Address') ?? 'Email Address' }}</option>
                            <option value="date">{{ __('messages.Date Selector') ?? 'Date Selector' }}</option>
                            <option value="select">{{ __('messages.Dropdown Choice (Select)') ?? 'Dropdown Choice (Select)' }}</option>
                            <option value="checkbox">{{ __('messages.Checkbox Check') ?? 'Checkbox Check' }}</option>
                            <option value="static_text">{{ __('messages.Static Text Block') ?? 'Static Text Block' }}</option>
                            <option value="section_header">{{ __('messages.Section Header') ?? 'Section Header' }}</option>
                        </optgroup>
                        <optgroup label="{{ __('messages.Dynamic Database Controls') ?? 'Dynamic Database Controls' }}">
                            <option value="groups">{{ __('messages.Groups Select') ?? 'Groups Select' }}</option>
                            <option value="cities">{{ __('messages.Cities Select') ?? 'Cities Select' }}</option>
                            <option value="neighborhoods">{{ __('messages.Neighborhoods Select') ?? 'Neighborhoods Select' }}</option>
                            <option value="committees">{{ __('messages.Service Committees Select') ?? 'Service Committees Select' }}</option>
                            <option value="servicebodies">{{ __('messages.Service Bodies Select') ?? 'Service Bodies Select' }}</option>
                        </optgroup>
                    </select>
                </div>

                <!-- Required check & Delete -->
                <div class="col-md-4 d-flex align-items-center justify-content-between pt-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input required-checkbox" type="checkbox" role="switch" onchange="updatePreview()">
                        <label class="form-check-label fw-semibold small text-secondary">{{ __('messages.Required') ?? 'Required' }}</label>
                    </div>

                    <button type="button" class="btn btn-danger btn-sm rounded-pill px-3" onclick="removeField(this)">
                        <i class="bi bi-trash"></i> {{ __('messages.Delete') ?? 'Delete' }}
                    </button>
                </div>

                <!-- Options settings (conditionally shown for Dropdown/Checkbox) -->
                <div class="col-12 options-container d-none">
                    <label class="form-label fw-semibold small text-secondary">{{ __('messages.Options (comma separated list)') ?? 'Options (comma separated list)' }}</label>
                    <input type="text" class="form-control options-input" placeholder="{{ __('messages.Option 1, Option 2, Option 3') ?? 'Option 1, Option 2, Option 3' }}" oninput="updatePreview()">
                </div>

                <!-- Placeholder setting -->
                <div class="col-md-6 placeholder-settings-container">
                    <label class="form-label fw-semibold small text-secondary">{{ __('messages.Field Placeholder') ?? 'Field Placeholder' }}</label>
                    <input type="text" class="form-control placeholder-input" placeholder="e.g. Enter your value..." oninput="updatePreview()">
                </div>

                <!-- Description setting -->
                <div class="col-md-6 description-settings-container">
                    <label class="form-label fw-semibold small text-secondary">{{ __('messages.Field Description / Help Text') ?? 'Field Description' }}</label>
                    <input type="text" class="form-control description-input" placeholder="e.g. Help text displayed below label" oninput="updatePreview()">
                </div>

                <!-- Static Text Formatting settings (conditionally shown for static_text) -->
                <div class="col-12 formatting-settings-container d-none">
                    <div class="d-flex flex-wrap gap-4 align-items-center bg-light p-2.5 rounded-3 border">
                        <div class="form-check">
                            <input class="form-check-input bold-checkbox" type="checkbox" onchange="updatePreview()">
                            <label class="form-check-label fw-semibold small text-secondary">{{ __('messages.Bold') ?? 'Bold' }}</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input italic-checkbox" type="checkbox" onchange="updatePreview()">
                            <label class="form-check-label fw-semibold small text-secondary">{{ __('messages.Italic') ?? 'Italic' }}</label>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label class="fw-semibold small text-secondary mb-0">{{ __('messages.Alignment') ?? 'Alignment' }}</label>
                            <select class="form-select form-select-sm align-select" style="width: auto;" onchange="updatePreview()">
                                <option value="left">{{ __('messages.Left') ?? 'Left' }}</option>
                                <option value="center">{{ __('messages.Center') ?? 'Center' }}</option>
                                <option value="right">{{ __('messages.Right') ?? 'Right' }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <script>
        let fieldIndex = 0;

        function checkEmptyState() {
            const container = document.getElementById('fields-container');
            const emptyState = document.getElementById('empty-state');
            if (container.children.length === 0) {
                emptyState.classList.remove('d-none');
            } else {
                emptyState.classList.add('d-none');
            }
        }

        function addField() {
            const container = document.getElementById('fields-container');
            const template = document.getElementById('field-template');
            const clone = template.content.cloneNode(true);
            
            // Set dynamic attributes and names
            const item = clone.querySelector('.field-item');
            
            // Label input
            const labelInput = clone.querySelector('.label-input');
            labelInput.name = `fields[${fieldIndex}][label]`;
            
            // Type select
            const typeSelect = clone.querySelector('.type-select');
            typeSelect.name = `fields[${fieldIndex}][type]`;
            
            // Required checkbox
            const requiredCheckbox = clone.querySelector('.required-checkbox');
            requiredCheckbox.name = `fields[${fieldIndex}][required]`;
            requiredCheckbox.value = "1";
            
            // Options input
            const optionsInput = clone.querySelector('.options-input');
            optionsInput.name = `fields[${fieldIndex}][options]`;

            // Placeholder input
            const placeholderInput = clone.querySelector('.placeholder-input');
            placeholderInput.name = `fields[${fieldIndex}][placeholder]`;

            // Description input
            const descriptionInput = clone.querySelector('.description-input');
            descriptionInput.name = `fields[${fieldIndex}][description]`;

            // Formatting checkboxes & select
            const boldCheckbox = clone.querySelector('.bold-checkbox');
            boldCheckbox.name = `fields[${fieldIndex}][bold]`;
            boldCheckbox.value = "1";

            const italicCheckbox = clone.querySelector('.italic-checkbox');
            italicCheckbox.name = `fields[${fieldIndex}][italic]`;
            italicCheckbox.value = "1";

            const alignSelect = clone.querySelector('.align-select');
            alignSelect.name = `fields[${fieldIndex}][align]`;

            container.appendChild(clone);
            fieldIndex++;
            checkEmptyState();
            updateButtonStates();
            updatePreview();
        }

        function removeField(btn) {
            const item = btn.closest('.field-item');
            item.remove();
            checkEmptyState();
            updateButtonStates();
            updatePreview();
        }

        function handleTypeChange(select) {
            const row = select.closest('.row');
            const optionsContainer = row.querySelector('.options-container');
            const placeholderContainer = row.querySelector('.placeholder-settings-container');
            const formattingContainer = row.querySelector('.formatting-settings-container');
            const requiredSwitch = row.querySelector('.required-checkbox')?.closest('.form-switch');

            if (select.value === 'select' || select.value === 'checkbox') {
                optionsContainer.classList.remove('d-none');
            } else {
                optionsContainer.classList.add('d-none');
            }

            if (select.value === 'static_text') {
                formattingContainer.classList.remove('d-none');
                placeholderContainer.classList.add('d-none');
                if (requiredSwitch) requiredSwitch.style.display = 'none';
            } else if (select.value === 'section_header') {
                formattingContainer.classList.add('d-none');
                placeholderContainer.classList.add('d-none');
                if (requiredSwitch) requiredSwitch.style.display = 'none';
            } else {
                formattingContainer.classList.add('d-none');
                placeholderContainer.classList.remove('d-none');
                if (requiredSwitch) requiredSwitch.style.display = 'block';
            }

            updatePreview();
        }

        function moveFieldUp(btn) {
            const item = btn.closest('.field-item');
            const previous = item.previousElementSibling;
            if (previous) {
                item.parentNode.insertBefore(item, previous);
                reorderNames();
            }
        }

        function moveFieldDown(btn) {
            const item = btn.closest('.field-item');
            const next = item.nextElementSibling;
            if (next) {
                item.parentNode.insertBefore(next, item);
                reorderNames();
            }
        }

        function reorderNames() {
            const container = document.getElementById('fields-container');
            const items = container.querySelectorAll('.field-item');
            items.forEach((item, index) => {
                item.querySelector('.label-input').name = `fields[${index}][label]`;
                item.querySelector('.type-select').name = `fields[${index}][type]`;
                item.querySelector('.required-checkbox').name = `fields[${index}][required]`;
                item.querySelector('.options-input').name = `fields[${index}][options]`;
                item.querySelector('.placeholder-input').name = `fields[${index}][placeholder]`;
                item.querySelector('.description-input').name = `fields[${index}][description]`;
                item.querySelector('.bold-checkbox').name = `fields[${index}][bold]`;
                item.querySelector('.italic-checkbox').name = `fields[${index}][italic]`;
                item.querySelector('.align-select').name = `fields[${index}][align]`;
            });
            updateButtonStates();
            updatePreview();
        }

        function updateButtonStates() {
            const container = document.getElementById('fields-container');
            const items = container.querySelectorAll('.field-item');
            items.forEach((item, index) => {
                const upBtn = item.querySelector('.move-up-btn');
                const downBtn = item.querySelector('.move-down-btn');
                
                upBtn.disabled = (index === 0);
                downBtn.disabled = (index === items.length - 1);
            });
        }

        // Live Preview Renderer
        function updatePreview() {
            // Update Title
            const titleInput = document.getElementById('title');
            const previewTitle = document.getElementById('preview-title');
            previewTitle.textContent = titleInput.value.trim() || 'Untitled Form';

            // Update Subtitle
            const subtitleInput = document.getElementById('settings_subtitle');
            const previewSubtitle = document.getElementById('preview-subtitle');
            if (previewSubtitle && subtitleInput) {
                previewSubtitle.textContent = subtitleInput.value.trim();
                previewSubtitle.style.display = subtitleInput.value.trim() ? 'block' : 'none';
            }

            // Update Type Badge and Icon visibility
            const typeSelect = document.getElementById('type');
            const previewTypeBadge = document.getElementById('preview-type-badge');
            const iconSelectorGroup = document.getElementById('icon-selector-group');
            const previewIconWrapper = document.getElementById('preview-icon-wrapper');
            const previewIcon = document.getElementById('preview-icon');
            const settingsIcon = document.getElementById('settings_icon');

            previewTypeBadge.textContent = typeSelect.options[typeSelect.selectedIndex].text;

            if (typeSelect.value === 'survey') {
                if (iconSelectorGroup) iconSelectorGroup.style.setProperty('display', 'block', 'important');
                if (previewIconWrapper) previewIconWrapper.style.setProperty('display', 'flex', 'important');
                
                // Update icon class
                if (previewIcon && settingsIcon) {
                    previewIcon.className = 'bi ' + (settingsIcon.value || 'bi-clipboard2-data');
                }

                // Set Primary (blue) style for badge
                previewTypeBadge.className = 'badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 fw-bold text-uppercase';
                previewTypeBadge.style.setProperty('background-color', 'rgba(37, 99, 235, 0.1)', 'important');
                previewTypeBadge.style.setProperty('border-color', 'rgba(37, 99, 235, 0.2)', 'important');
            } else {
                if (iconSelectorGroup) iconSelectorGroup.style.setProperty('display', 'none', 'important');
                if (previewIconWrapper) previewIconWrapper.style.setProperty('display', 'none', 'important');

                // Set Success (green) style for badge
                previewTypeBadge.className = 'badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 fw-bold text-uppercase';
                previewTypeBadge.style.setProperty('background-color', 'rgba(16, 185, 129, 0.1)', 'important');
                previewTypeBadge.style.setProperty('border-color', 'rgba(16, 185, 129, 0.2)', 'important');
            }

            // Render Fields Mockup
            const fieldsContainer = document.getElementById('fields-container');
            const previewFieldsContainer = document.getElementById('preview-fields-container');
            previewFieldsContainer.innerHTML = ''; // Reset

            const items = fieldsContainer.querySelectorAll('.field-item');
            if (items.length === 0) {
                const emptyMsg = document.createElement('p');
                emptyMsg.className = 'text-center text-muted fst-italic my-4';
                emptyMsg.textContent = 'Add fields to see live preview';
                previewFieldsContainer.appendChild(emptyMsg);
                return;
            }

            items.forEach(item => {
                const label = item.querySelector('.label-input').value.trim() || 'Untitled Field';
                const type = item.querySelector('.type-select').value;
                const required = item.querySelector('.required-checkbox').checked;
                const optionsVal = item.querySelector('.options-input').value.trim();
                const placeholder = item.querySelector('.placeholder-input').value.trim();
                const description = item.querySelector('.description-input').value.trim();
                const bold = item.querySelector('.bold-checkbox')?.checked;
                const italic = item.querySelector('.italic-checkbox')?.checked;
                const align = item.querySelector('.align-select')?.value || 'left';

                const fieldGroup = document.createElement('div');
                fieldGroup.className = 'mb-3';

                if (type === 'static_text') {
                    const staticText = document.createElement('div');
                    staticText.textContent = label;
                    staticText.style.fontWeight = bold ? 'bold' : 'normal';
                    staticText.style.fontStyle = italic ? 'italic' : 'normal';
                    staticText.style.textAlign = align;
                    staticText.className = 'text-dark p-2 border border-dashed rounded-3';
                    staticText.style.background = 'rgba(0,0,0,0.01)';
                    fieldGroup.appendChild(staticText);
                } else if (type === 'section_header') {
                    const sectionHeader = document.createElement('div');
                    sectionHeader.className = 'mt-4 mb-2 pb-2 border-bottom';
                    const title = document.createElement('h6');
                    title.className = 'fw-bold text-primary mb-1 d-flex align-items-center gap-1';
                    title.innerHTML = '<i class="bi bi-folder2-open"></i> ' + label;
                    sectionHeader.appendChild(title);
                    if (description) {
                        const desc = document.createElement('p');
                        desc.className = 'text-muted small mb-0';
                        desc.textContent = description;
                        sectionHeader.appendChild(desc);
                    }
                    fieldGroup.appendChild(sectionHeader);
                } else {
                    const fieldLabel = document.createElement('label');
                    fieldLabel.className = 'form-label fw-semibold small mb-1';
                    fieldLabel.innerHTML = label + (required ? ' <span class="text-danger">*</span>' : '');
                    fieldGroup.appendChild(fieldLabel);

                    if (description) {
                        const desc = document.createElement('div');
                        desc.className = 'text-muted small mb-2';
                        desc.style.fontSize = '0.8rem';
                        desc.textContent = description;
                        fieldGroup.appendChild(desc);
                    }

                    let inputControl;
                    if (type === 'textarea') {
                        inputControl = document.createElement('textarea');
                        inputControl.rows = 2;
                        inputControl.className = 'form-control bg-light border-0 small';
                        inputControl.placeholder = placeholder || 'Textarea response...';
                        inputControl.disabled = true;
                    } else if (type === 'phone') {
                        inputControl = document.createElement('div');
                        inputControl.className = 'input-group input-group-sm';
                        inputControl.style.direction = 'ltr';
                        const span = document.createElement('span');
                        span.className = 'input-group-text bg-light border';
                        span.textContent = '🇪🇬 +20';
                        const phoneInp = document.createElement('input');
                        phoneInp.type = 'tel';
                        phoneInp.className = 'form-control bg-light border-start-0';
                        phoneInp.placeholder = placeholder || '123 456 7890';
                        phoneInp.disabled = true;
                        inputControl.appendChild(span);
                        inputControl.appendChild(phoneInp);
                    } else if (type === 'select') {
                        inputControl = document.createElement('select');
                        inputControl.className = 'form-select bg-light border-0 small';
                        inputControl.disabled = true;
                        const defaultOption = document.createElement('option');
                        defaultOption.text = placeholder || 'Choose an option...';
                        inputControl.appendChild(defaultOption);
                        if (optionsVal) {
                            const options = optionsVal.split(',');
                            options.forEach(opt => {
                                const option = document.createElement('option');
                                option.text = opt.trim();
                                inputControl.appendChild(option);
                            });
                        }
                    } else if (type === 'checkbox') {
                        inputControl = document.createElement('div');
                        inputControl.className = 'd-flex flex-column gap-1';
                        if (optionsVal) {
                            const options = optionsVal.split(',');
                            options.forEach((opt, idx) => {
                                const wrap = document.createElement('div');
                                wrap.className = 'form-check';
                                const check = document.createElement('input');
                                check.type = 'checkbox';
                                check.className = 'form-check-input';
                                check.disabled = true;
                                const checkLbl = document.createElement('label');
                                checkLbl.className = 'form-check-label small';
                                checkLbl.textContent = opt.trim();
                                wrap.appendChild(check);
                                wrap.appendChild(checkLbl);
                                inputControl.appendChild(wrap);
                            });
                        } else {
                            const wrap = document.createElement('div');
                            wrap.className = 'form-check';
                            const check = document.createElement('input');
                            check.type = 'checkbox';
                            check.className = 'form-check-input';
                            check.disabled = true;
                            const checkLbl = document.createElement('label');
                            checkLbl.className = 'form-check-label small';
                            checkLbl.textContent = 'Check item';
                            wrap.appendChild(check);
                            wrap.appendChild(checkLbl);
                            inputControl.appendChild(wrap);
                        }
                    } else if (['groups', 'cities', 'neighborhoods', 'committees', 'servicebodies'].includes(type)) {
                        inputControl = document.createElement('select');
                        inputControl.className = 'form-select bg-light border-0 small';
                        inputControl.disabled = true;
                        const opt = document.createElement('option');
                        opt.text = placeholder || `[Dynamic List: ${type.charAt(0).toUpperCase() + type.slice(1)}]`;
                        inputControl.appendChild(opt);
                    } else {
                        inputControl = document.createElement('input');
                        inputControl.type = type;
                        inputControl.className = 'form-control bg-light border-0 small';
                        inputControl.placeholder = placeholder || `Enter ${type}...`;
                        inputControl.disabled = true;
                    }

                    fieldGroup.appendChild(inputControl);
                }

                previewFieldsContainer.appendChild(fieldGroup);
            });
        }

        // Add initial field
        document.addEventListener('DOMContentLoaded', () => {
            addField();
        });
    </script>
</x-layout>
