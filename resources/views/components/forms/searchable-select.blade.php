@props([
    'name',
    'id' => null,
    'label' => null,
    'options' => [],
    'value' => null,
    'placeholder' => null,
    'emptyOption' => null,
    'itemValueKey' => 'id',
    'itemLabelKey' => null,
    'class' => '',
])

@php
    $inputId = $id ?? $name;
    $uniqueComponentId = 'searchable_select_' . $inputId . '_' . \Illuminate\Support\Str::random(6);
    $currentLocale = app()->getLocale();
    $selectedValue = old($name, $value ?? request($name));
    $selectedLabel = '';
    $placeholderText = $placeholder ?? ($emptyOption ?? __('messages.Select...') ?? 'Select...');

    // Normalize options into a standard array of ['value' => ..., 'label' => ...]
    $normalizedOptions = [];

    // Add empty option if provided
    if ($emptyOption !== null) {
        $normalizedOptions[] = [
            'value' => '',
            'label' => $emptyOption,
        ];
    }

    foreach ($options as $key => $option) {
        $val = '';
        $lbl = '';

        if (is_object($option)) {
            $val = $option->{$itemValueKey} ?? $key;
            if ($itemLabelKey) {
                $lbl = $option->{$itemLabelKey} ?? '';
            } else {
                if ($currentLocale === 'ar') {
                    $lbl = $option->ar_name ?? $option->name ?? $option->en_name ?? '';
                } else {
                    $lbl = $option->en_name ?? $option->name ?? $option->ar_name ?? '';
                }
            }
        } elseif (is_array($option)) {
            $val = $option[$itemValueKey] ?? $key;
            if ($itemLabelKey && isset($option[$itemLabelKey])) {
                $lbl = $option[$itemLabelKey];
            } else {
                if ($currentLocale === 'ar') {
                    $lbl = $option['ar_name'] ?? $option['name'] ?? $option['en_name'] ?? (string)$option;
                } else {
                    $lbl = $option['en_name'] ?? $option['name'] ?? $option['ar_name'] ?? (string)$option;
                }
            }
        } else {
            $val = $key;
            $lbl = (string)$option;
        }

        $normalizedOptions[] = [
            'value' => (string)$val,
            'label' => (string)$lbl,
        ];

        if ((string)$val === (string)$selectedValue && (string)$val !== '') {
            $selectedLabel = (string)$lbl;
        }
    }

    if ((string)$selectedValue === '' && $emptyOption !== null) {
        $selectedLabel = $emptyOption;
    }
@endphp

<div class="searchable-select-wrapper position-relative {{ $class }}" id="{{ $uniqueComponentId }}">
    @if($label)
        <label for="{{ $inputId }}_search_input" class="form-label fw-semibold text-muted small mb-2">{{ $label }}</label>
    @endif

    {{-- Hidden Input for form submission --}}
    <input type="hidden" name="{{ $name }}" id="{{ $inputId }}" value="{{ $selectedValue }}" class="searchable-select-hidden-input">

    {{-- Visible Search / Display Input --}}
    <div class="position-relative">
        <input type="text"
               id="{{ $inputId }}_search_input"
               class="form-control bg-light py-2 rounded-3 searchable-select-search-input pe-5"
               placeholder="{{ $placeholderText }}"
               value="{{ $selectedLabel }}"
               autocomplete="off"
               style="border-color: #e2e8f0; font-size: 0.95rem; cursor: pointer;">

        {{-- Control Icons Container (Clear & Chevron) --}}
        <div class="position-absolute top-50 end-0 translate-middle-y d-flex align-items-center gap-1 pe-3" style="pointer-events: none;">
            <button type="button" class="btn btn-link p-0 text-muted searchable-select-clear-btn border-0 {{ $selectedValue !== null && $selectedValue !== '' ? '' : 'd-none' }}" style="pointer-events: auto; text-decoration: none; font-size: 0.85rem;" title="{{ __('messages.Clear') ?? 'Clear' }}">
                <i class="bi bi-x-circle-fill text-secondary"></i>
            </button>
            <i class="bi bi-chevron-down text-muted small searchable-select-chevron-icon transition-transform"></i>
        </div>
    </div>

    {{-- Dropdown Menu --}}
    <div class="searchable-select-dropdown shadow-lg rounded-3 border d-none position-absolute w-100 mt-1"
         style="z-index: 1060; max-height: 260px; overflow-y: auto; background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-color: rgba(0, 0, 0, 0.08) !important;">
        
        <ul class="list-unstyled mb-0 py-1 searchable-select-options-list">
            @foreach($normalizedOptions as $opt)
                <li class="searchable-select-item px-3 py-2 d-flex align-items-center justify-content-between {{ (string)$opt['value'] === (string)$selectedValue ? 'active-item' : '' }}"
                    data-value="{{ $opt['value'] }}"
                    data-label="{{ $opt['label'] }}"
                    style="cursor: pointer; font-size: 0.92rem; transition: background-color 0.15s ease;">
                    <span class="searchable-select-item-text text-truncate">{{ $opt['label'] }}</span>
                    @if((string)$opt['value'] === (string)$selectedValue)
                        <i class="bi bi-check2 text-primary fw-bold ms-2 searchable-select-check-icon"></i>
                    @else
                        <i class="bi bi-check2 text-primary fw-bold ms-2 searchable-select-check-icon d-none"></i>
                    @endif
                </li>
            @endforeach
            <li class="searchable-select-no-results px-3 py-3 text-muted text-center small d-none">
                <i class="bi bi-search me-1"></i> {{ __('messages.No results found') ?? 'No results found' }}
            </li>
        </ul>
    </div>
</div>

<style>
    .searchable-select-wrapper .searchable-select-item:hover {
        background-color: rgba(59, 130, 246, 0.08) !important;
        color: #1d4ed8 !important;
    }
    .searchable-select-wrapper .searchable-select-item.active-item {
        background-color: rgba(59, 130, 246, 0.12) !important;
        color: #1d4ed8 !important;
        font-weight: 600;
    }
    .searchable-select-wrapper .searchable-select-item.keyboard-focus {
        background-color: rgba(59, 130, 246, 0.15) !important;
        outline: none;
    }
    .searchable-select-chevron-icon {
        transition: transform 0.2s ease;
    }
    .searchable-select-wrapper.open .searchable-select-chevron-icon {
        transform: rotate(180deg);
    }
</style>

<script>
(function() {
    function initSearchableSelect(wrapperId) {
        const wrapper = document.getElementById(wrapperId);
        if (!wrapper || wrapper.dataset.searchableInitialized === 'true') return;
        wrapper.dataset.searchableInitialized = 'true';

        const hiddenInput = wrapper.querySelector('.searchable-select-hidden-input');
        const searchInput = wrapper.querySelector('.searchable-select-search-input');
        const clearBtn = wrapper.querySelector('.searchable-select-clear-btn');
        const dropdown = wrapper.querySelector('.searchable-select-dropdown');
        const optionsList = wrapper.querySelector('.searchable-select-options-list');
        const items = wrapper.querySelectorAll('.searchable-select-item');
        const noResults = wrapper.querySelector('.searchable-select-no-results');

        let originalLabel = searchInput.value;
        let activeKeyboardIndex = -1;

        function openDropdown() {
            // Close other searchable selects first
            document.querySelectorAll('.searchable-select-wrapper.open').forEach(other => {
                if (other !== wrapper) {
                    other.classList.remove('open');
                    const otherDropdown = other.querySelector('.searchable-select-dropdown');
                    if (otherDropdown) otherDropdown.classList.add('d-none');
                }
            });

            wrapper.classList.add('open');
            dropdown.classList.remove('d-none');
            filterOptions(searchInput.value.trim());
            searchInput.select();
        }

        function closeDropdown() {
            wrapper.classList.remove('open');
            dropdown.classList.add('d-none');
            clearKeyboardFocus();
            // Restore chosen label if user typed something but didn't pick
            const curVal = hiddenInput.value;
            let matched = false;
            items.forEach(item => {
                if (item.dataset.value === curVal) {
                    searchInput.value = item.dataset.label;
                    matched = true;
                }
            });
            if (!matched && curVal === '') {
                searchInput.value = '';
            }
        }

        function selectItem(item) {
            const val = item.dataset.value;
            const label = item.dataset.label;

            hiddenInput.value = val;
            searchInput.value = val === '' && label === @json($emptyOption) ? label : label;
            originalLabel = searchInput.value;

            items.forEach(i => {
                const check = i.querySelector('.searchable-select-check-icon');
                if (i === item && val !== '') {
                    i.classList.add('active-item');
                    if (check) check.classList.remove('d-none');
                } else if (i === item && val === '') {
                    i.classList.add('active-item');
                    if (check) check.classList.add('d-none');
                } else {
                    i.classList.remove('active-item');
                    if (check) check.classList.add('d-none');
                }
            });

            if (val !== '') {
                clearBtn.classList.remove('d-none');
            } else {
                clearBtn.classList.add('d-none');
            }

            hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
            closeDropdown();
        }

        function filterOptions(query) {
            let visibleCount = 0;
            const q = query.toLowerCase();

            items.forEach(item => {
                const label = item.dataset.label.toLowerCase();
                if (q === '' || label.includes(q)) {
                    item.classList.remove('d-none');
                    visibleCount++;
                } else {
                    item.classList.add('d-none');
                }
            });

            if (visibleCount === 0) {
                noResults.classList.remove('d-none');
            } else {
                noResults.classList.add('d-none');
            }
            clearKeyboardFocus();
        }

        function clearKeyboardFocus() {
            items.forEach(i => i.classList.remove('keyboard-focus'));
            activeKeyboardIndex = -1;
        }

        function getVisibleItems() {
            return Array.from(items).filter(i => !i.classList.contains('d-none'));
        }

        // Search input events
        searchInput.addEventListener('click', function(e) {
            e.stopPropagation();
            if (wrapper.classList.contains('open')) {
                // keep open
            } else {
                openDropdown();
            }
        });

        searchInput.addEventListener('input', function() {
            if (!wrapper.classList.contains('open')) {
                wrapper.classList.add('open');
                dropdown.classList.remove('d-none');
            }
            filterOptions(this.value.trim());
            if (this.value.trim() !== '') {
                clearBtn.classList.remove('d-none');
            }
        });

        searchInput.addEventListener('keydown', function(e) {
            const visible = getVisibleItems();

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (!wrapper.classList.contains('open')) {
                    openDropdown();
                    return;
                }
                if (visible.length === 0) return;
                activeKeyboardIndex = (activeKeyboardIndex + 1) % visible.length;
                visible.forEach((item, idx) => {
                    item.classList.toggle('keyboard-focus', idx === activeKeyboardIndex);
                    if (idx === activeKeyboardIndex) {
                        item.scrollIntoView({ block: 'nearest' });
                    }
                });
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (!wrapper.classList.contains('open')) {
                    openDropdown();
                    return;
                }
                if (visible.length === 0) return;
                activeKeyboardIndex = (activeKeyboardIndex - 1 + visible.length) % visible.length;
                visible.forEach((item, idx) => {
                    item.classList.toggle('keyboard-focus', idx === activeKeyboardIndex);
                    if (idx === activeKeyboardIndex) {
                        item.scrollIntoView({ block: 'nearest' });
                    }
                });
            } else if (e.key === 'Enter') {
                if (wrapper.classList.contains('open')) {
                    e.preventDefault();
                    if (activeKeyboardIndex >= 0 && activeKeyboardIndex < visible.length) {
                        selectItem(visible[activeKeyboardIndex]);
                    } else if (visible.length === 1) {
                        selectItem(visible[0]);
                    } else {
                        closeDropdown();
                    }
                }
            } else if (e.key === 'Escape') {
                closeDropdown();
            } else if (e.key === 'Tab') {
                closeDropdown();
            }
        });

        // Clear button
        clearBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            hiddenInput.value = '';
            searchInput.value = @json($emptyOption) ?? '';
            originalLabel = searchInput.value;
            clearBtn.classList.add('d-none');

            items.forEach(i => {
                i.classList.remove('active-item');
                const check = i.querySelector('.searchable-select-check-icon');
                if (check) check.classList.add('d-none');
                if (i.dataset.value === '') {
                    i.classList.add('active-item');
                }
            });

            filterOptions('');
            hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
            searchInput.focus();
        });

        // Item click
        items.forEach(item => {
            item.addEventListener('click', function(e) {
                e.stopPropagation();
                selectItem(this);
            });
        });

        // Global click-outside listener
        document.addEventListener('click', function(e) {
            if (!wrapper.contains(e.target)) {
                closeDropdown();
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initSearchableSelect(@json($uniqueComponentId));
        });
    } else {
        initSearchableSelect(@json($uniqueComponentId));
    }
})();
</script>
