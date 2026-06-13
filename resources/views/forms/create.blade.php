<x-layout>

    <x-backhead>{{ __('messages.Create Form') ?? 'Create Form' }}</x-backhead>

    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 fw-bold" style="color: var(--text-primary);">{{ __('messages.New Form Builder') ?? 'Build a New Form' }}</h4>
            <a href="{{ route('forms.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-arrow-left"></i> {{ __('messages.Back to Forms') ?? 'Back' }}
            </a>
        </div>

        <form action="{{ route('forms.store') }}" method="POST" id="form-builder-form">
            @csrf
            <div class="row">
                <!-- Form Settings -->
                <div class="col-lg-4">
                    <div class="glass-card p-4 mb-4">
                        <h5 class="fw-bold mb-3" style="color: var(--text-primary);">{{ __('messages.Form Settings') ?? 'Form Settings' }}</h5>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">{{ __('messages.Form Title') ?? 'Form Title' }}</label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="e.g. Annual Member Survey" required value="{{ old('title') }}">
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label fw-semibold">{{ __('messages.Form Type') ?? 'Form Type' }}</label>
                            <select name="type" id="type" class="form-select" required>
                                <option value="survey">{{ __('messages.Survey') ?? 'Survey' }}</option>
                                <option value="event_registration">{{ __('messages.Event Registration') ?? 'Event Entry Registration' }}</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label fw-semibold">{{ __('messages.Form Status') ?? 'Form Status' }}</label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="draft" selected>{{ __('messages.Draft') ?? 'Draft' }}</option>
                                <option value="published">{{ __('messages.Published') ?? 'Published' }}</option>
                                <option value="unpublished">{{ __('messages.Unpublished') ?? 'Unpublished (Deactivated)' }}</option>
                            </select>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary rounded-pill py-2.5 fw-bold">
                                <i class="bi bi-cloud-arrow-up-fill me-1"></i> Save and Create Form
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Form Fields Configuration -->
                <div class="col-lg-8">
                    <div class="glass-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0" style="color: var(--text-primary);">{{ __('messages.Configure Fields') ?? 'Configure Fields' }}</h5>
                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="addField()">
                                <i class="bi bi-plus-circle-fill"></i> {{ __('messages.Add Field') ?? 'Add Field' }}
                            </button>
                        </div>

                        <div id="fields-container" class="d-flex flex-column gap-3">
                            <!-- Fields will be added here dynamically -->
                        </div>

                        <!-- Empty state -->
                        <div id="empty-state" class="text-center py-5 text-secondary border rounded-3 border-dashed" style="border-style: dashed !important; border-width: 2px !important; border-color: var(--glass-border) !important;">
                            <i class="bi bi-menu-app display-4 opacity-50"></i>
                            <p class="mt-2 mb-0">No fields added yet. Click "Add Field" to start building your form.</p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Template for adding fields dynamically -->
    <template id="field-template">
        <div class="field-item card p-3 border shadow-sm position-relative" style="background: rgba(255, 255, 255, 0.4) !important;">
            <div class="row g-3">
                <!-- Drag/Order controls -->
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
                    <label class="form-label fw-semibold small text-secondary">Field Label / Name</label>
                    <input type="text" class="form-control label-input" placeholder="e.g. Your Name" required>
                </div>

                <!-- Type selector -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-secondary">Field Input Type</label>
                    <select class="form-select type-select" onchange="handleTypeChange(this)" required>
                        <optgroup label="Standard Fields">
                            <option value="text">Single Line Text</option>
                            <option value="textarea">Paragraph/Textarea</option>
                            <option value="number">Number</option>
                            <option value="email">Email Address</option>
                            <option value="date">Date Selector</option>
                            <option value="select">Dropdown Choice (Select)</option>
                            <option value="checkbox">Checkbox Check</option>
                        </optgroup>
                        <optgroup label="Dynamic Database Controls">
                            <option value="groups">Groups Select</option>
                            <option value="cities">Cities Select</option>
                            <option value="neighborhoods">Neighborhoods Select</option>
                            <option value="committees">Service Committees Select</option>
                            <option value="servicebodies">Service Bodies Select</option>
                        </optgroup>
                    </select>
                </div>

                <!-- Required check & Delete -->
                <div class="col-md-4 d-flex align-items-center justify-content-between pt-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input required-checkbox" type="checkbox" role="switch">
                        <label class="form-check-label fw-semibold small text-secondary">Required Field</label>
                    </div>

                    <button type="button" class="btn btn-danger btn-sm rounded-pill px-3" onclick="removeField(this)">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </div>

                <!-- Options settings (conditionally shown for Dropdown/Checkbox) -->
                <div class="col-12 options-container d-none">
                    <label class="form-label fw-semibold small text-secondary">Options (comma separated list)</label>
                    <input type="text" class="form-control options-input" placeholder="Option 1, Option 2, Option 3">
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

            container.appendChild(clone);
            fieldIndex++;
            checkEmptyState();
            updateButtonStates();
        }

        function removeField(btn) {
            const item = btn.closest('.field-item');
            item.remove();
            checkEmptyState();
            updateButtonStates();
        }

        function handleTypeChange(select) {
            const row = select.closest('.row');
            const optionsContainer = row.querySelector('.options-container');
            if (select.value === 'select' || select.value === 'checkbox') {
                optionsContainer.classList.remove('d-none');
            } else {
                optionsContainer.classList.add('d-none');
            }
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
            });
            updateButtonStates();
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

        // Add initial field
        document.addEventListener('DOMContentLoaded', () => {
            addField();
        });
    </script>
</x-layout>
