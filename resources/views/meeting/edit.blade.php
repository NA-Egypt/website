<x-layout>
    
    <x-backhead>{{__('messages.Edit') . ' ' . __('messages.Meeting')}}</x-backhead>

    <div class="container-fluid glass-card p-4 p-md-5 mt-4 mb-5 mx-auto" style="max-width: 900px;">
        <form action="{{ route('meeting.update', $meeting->id) }}" method="post" class="meeting-form">
                    @csrf
                    @method('PUT')

                    @auth
                        @can('is-super-admin')
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">{{ __('Group Type') }}</label>
                                        <select id="group-type-selector" class="form-select rounded-pill shadow-sm">
                                            <option value="legacy" {{ $meeting->direct_online_group_id ? '' : 'selected' }}>{{ __('Standard Group') }}</option>
                                            <option value="direct_online" {{ $meeting->direct_online_group_id ? 'selected' : '' }}>{{ __('Direct Online Group') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div id="legacy-group-container" style="display: {{ $meeting->direct_online_group_id ? 'none' : 'block' }};">
                                <x-forms.select :$groups name="group_id" label="{{ __('messages.Group Name') }}" value="{{ $meeting->group_id }}" />
                            </div>
                            <div id="direct-online-group-container" style="display: {{ $meeting->direct_online_group_id ? 'block' : 'none' }};">
                                <x-forms.select :groups="$directOnlineGroups" name="direct_online_group_id" label="Direct Online Group Name" value="{{ $meeting->direct_online_group_id }}" />
                            </div>
                        @else
                            <input type="hidden" name="group_id"  value="{{ $meeting->group_id }}"/>
                        @endcan
                    @endauth

                    <div class="mb-4">
                        <h6 class="fw-bold mb-3 mt-4" style="color: var(--text-primary);"><i class="bi bi-chat-left-text me-2 text-info"></i> {{ __('messages.Meeting Topic') }}</h6>
                        <div class="d-flex flex-wrap align-items-center gap-3 glass-card p-3 rounded-4" style="border: 1px solid var(--glass-border); background: rgba(255,255,255,0.4);">
                            <div style="flex: 1; min-width: 200px;">
                                <select id="topic-selector" class="form-select rounded-pill shadow-sm" style="border: 1px solid var(--glass-border); background: rgba(255,255,255,0.7);">
                                    <option value="">{{ __('messages.Select Topic') ?? 'Select Topic' }}</option>
                                    @foreach ($topics as $topic)
                                        <option value="{{ $topic->id }}" data-name="{{ app()->getLocale() === 'ar' ? $topic->ar_name : $topic->en_name }}" data-is-business="{{ $topic->en_name === 'Group Business Meeting' ? 'true' : 'false' }}">
                                            {{ app()->getLocale() === 'ar' ? $topic->ar_name : $topic->en_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="selected-topics-container" class="d-flex flex-wrap gap-2" style="flex: 2; min-width: 250px; min-height: 38px; align-items: center;">
                                @php
                                    $selectedTopics = old('topics', isset($meeting) ? $meeting->topics->pluck('id')->toArray() : []);
                                @endphp
                                @foreach($selectedTopics as $selectedId)
                                    @php
                                        $selectedTopic = $topics->firstWhere('id', $selectedId);
                                    @endphp
                                    @if($selectedTopic)
                                        <div class="badge rounded-pill p-2 d-flex align-items-center shadow-sm topic-tag" data-is-business="{{ $selectedTopic->en_name === 'Group Business Meeting' ? 'true' : 'false' }}" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); font-size: 0.85rem;">
                                            <span style="margin-inline-end: 0.5rem;">{{ app()->getLocale() === 'ar' ? $selectedTopic->ar_name : $selectedTopic->en_name }}</span>
                                            <input type="hidden" name="topics[]" value="{{ $selectedTopic->id }}">
                                            <button type="button" class="btn-close btn-close-white remove-topic" style="font-size: 0.5rem; margin-inline-start: 0.5rem;" aria-label="Close"></button>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
    
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <x-forms.select :$days name="day_id" label="{{ __('messages.Day') }}" value="{{ $meeting->day_id }}" />
                        </div>
                        <div class="col-md-4">
                            <x-forms.datetime-picker name="start_time" label="{{ __('messages.From') }}" type="time" value="{{ $meeting->start_time }}" col="col-12" />
                        </div>
                        <div class="col-md-4">
                            <x-forms.datetime-picker name="end_time" label="{{ __('messages.To') }}" type="time" value="{{ $meeting->end_time }}" col="col-12" />
                        </div>
                    </div>
                    
                    <div class="mb-4 mt-3">
                        <h6 class="fw-bold mb-3" style="color: var(--text-primary);"><i class="bi bi-calendar-week me-2 text-warning"></i> {{ __('Recurrence') }}</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @php
                                $recurrences = [
                                    'weekly' => __('messages.Weekly'),
                                    '1st' => __('messages.1st'),
                                    '2nd' => __('messages.2nd'),
                                    '3rd' => __('messages.3rd'),
                                    '4th' => __('messages.4th'),
                                    '5th' => __('messages.5th'),
                                    'last' => __('messages.last'),
                                ];
                                $selectedRecurrence = old('recurrence', $meeting->recurrence ?? ['weekly']);
                            @endphp
                            @foreach($recurrences as $val => $label)
                                <div class="form-check form-check-inline glass-card p-2 rounded-3 m-0 d-flex align-items-center" style="border: 1px solid var(--glass-border); background: rgba(255,255,255,0.4);">
                                    <input class="form-check-input mt-0" type="checkbox" name="recurrence[]" id="recurrence-{{ $val }}" value="{{ $val }}" {{ is_array($selectedRecurrence) && in_array($val, $selectedRecurrence) ? 'checked' : '' }} style="margin-inline-end: 0.5rem; width: 1.2em; height: 1.2em;">
                                    <label class="form-check-label text-secondary fw-medium mb-0" for="recurrence-{{ $val }}" style="font-size: 0.9rem;">
                                        {{ $label }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
    
                    <x-forms.textarea name="notes" label="{{ __('messages.Notes') }}">{{ $meeting->notes }}</x-forms.textarea>

                    <h6 class="fw-bold mb-3 mt-4" style="color: var(--text-primary);"><i class="bi bi-sliders me-2 text-primary"></i> {{ __('messages.MeetingSettings')}}</h6>
                    <div class="row row-cols-1 row-cols-md-3 g-3 mb-4">
                        {{-- Type Switch --}}
                        <div class="col">
                            <div class="glass-card p-3 rounded-4 transition-hover h-100 d-flex flex-column align-items-center justify-content-center text-center" style="border: 1px solid var(--glass-border); background: rgba(0,0,0,0.01);">
                                <div class="widgets-icons text-white mb-2 shadow-sm mx-auto" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); border-radius: 12px; width: 40px; height: 40px; font-size: 1.2rem;">
                                    <i class="bi bi-door-closed"></i>
                                </div>
                                <span class="fw-bold text-secondary mb-2" style="font-size: 0.85rem;">{{__("messages.Type")}}</span>
                                <div class="form-check form-switch fs-5 mb-2 d-flex justify-content-center m-0 p-0">
                                    <input type="hidden" name="type" value="open">
                                    <input class="form-check-input ms-0 mt-0" type="checkbox" id="meeting-type" name="type" value="closed" {{ old('type', $meeting->type ?? 'closed') === 'closed' ? 'checked' : '' }}>
                                </div>
                                <span class="badge rounded-pill fw-medium" id="label-type" style="background-color: rgba(139, 92, 246, 0.1); color: #7c3aed;">
                                    {{ old('type', $meeting->type ?? 'closed') === 'closed' ? __('messages.closed') : __('messages.open') }}
                                </span>
                            </div>
                        </div>

                        {{-- Language Switch --}}
                        <div class="col">
                            <div class="glass-card p-3 rounded-4 transition-hover h-100 d-flex flex-column align-items-center justify-content-center text-center" style="border: 1px solid var(--glass-border); background: rgba(0,0,0,0.01);">
                                <div class="widgets-icons text-white mb-2 shadow-sm mx-auto" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); border-radius: 12px; width: 40px; height: 40px; font-size: 1.2rem;">
                                    <i class="bi bi-globe"></i>
                                </div>
                                <span class="fw-bold text-secondary mb-2" style="font-size: 0.85rem;">{{__("messages.Language")}}</span>
                                <div class="form-check form-switch fs-5 mb-2 d-flex justify-content-center m-0 p-0">
                                    <input type="hidden" name="lang" value="english">
                                    <input class="form-check-input ms-0 mt-0" type="checkbox" id="lang-switch" name="lang" value="arabic" {{ old('lang', $meeting->lang ?? 'arabic') === 'arabic' ? 'checked' : '' }}>
                                </div>
                                <span class="badge rounded-pill fw-medium" id="label-lang" style="background-color: rgba(14, 165, 233, 0.1); color: #0284c7;">
                                    {{ old('lang', $meeting->lang ?? 'arabic') === 'arabic' ? __("messages.arabic") : __("messages.english") }}
                                </span>
                            </div>
                        </div>

                        {{-- Status Switch --}}
                        <div class="col">
                            <div class="glass-card p-3 rounded-4 transition-hover h-100 d-flex flex-column align-items-center justify-content-center text-center" style="border: 1px solid var(--glass-border); background: rgba(0,0,0,0.01);">
                                <div class="widgets-icons text-white mb-2 shadow-sm mx-auto" style="background: linear-gradient(135deg, #10b981, #059669); border-radius: 12px; width: 40px; height: 40px; font-size: 1.2rem;">
                                    <i class="bi bi-activity"></i>
                                </div>
                                <span class="fw-bold text-secondary mb-2" style="font-size: 0.85rem;">{{__("messages.Status")}}</span>
                                <div class="form-check form-switch fs-5 mb-2 d-flex justify-content-center m-0 p-0">
                                    <input type="hidden" name="status" value="suspended">
                                    <input class="form-check-input ms-0 mt-0" type="checkbox" id="status-switch" name="status" value="available" {{ old('status', $meeting->status ?? 'available') === 'available' ? 'checked' : '' }}>
                                </div>
                                <span class="badge rounded-pill fw-medium" id="label-status" style="background-color: rgba(16, 185, 129, 0.1); color: #059669;">
                                    {{ old('status', $meeting->status ?? 'available') === 'available' ? __("messages.available") : __("messages.suspended") }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 mt-4" style="color: var(--text-primary);"><i class="bi bi-ui-checks-grid me-2 text-success"></i> {{__("messages.Options")}}</h6>
                    <div class="row row-cols-2 row-cols-md-3 g-3 mb-5 options-section">
                        @foreach ($options as $option)
                            <div class="col">
                                <label class="glass-card p-2 rounded-3 transition-hover h-100 d-flex align-items-center" style="border: 1px solid var(--glass-border); background: rgba(255,255,255,0.4); cursor: pointer;" for="option-{{ $option->id }}">
                                    <div class="form-check w-100 mb-0 d-flex align-items-center m-0 p-0">
                                        <input type="checkbox" name="options[]" value="{{ $option->id }}" class="form-check-input mt-0" style="width: 1.2em; height: 1.2em; margin-inline-start: 0; margin-inline-end: 0.5rem;" id="option-{{ $option->id }}" {{ in_array($option->id, old('options', $meeting->options->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}>
                                        <span class="text-secondary fw-medium" style="font-size: 0.9rem; flex: 1; margin-inline-start: 0.5rem;">
                                            @if(app()->getLocale() === 'ar')
                                                {{$option->ar_name}}
                                            @else
                                                {{$option->en_name}}
                                            @endif
                                        </span>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-end">
                        <x-forms.normal-button color='primary' name="{{ __('messages.Update') }}" class="rounded-pill px-5 py-2 shadow-sm" />
                    </div>
                </form>
    </div>

    <script>
        let translations = {
            open: @json(__('messages.open')),
            closed: @json(__('messages.closed')),
            arabic: @json(__('messages.arabic')),
            english: @json(__('messages.english')),
            Available: @json(__('messages.available')),
            Suspended: @json(__('messages.suspended')),
            group_business_meeting_exclusive: @json(__('messages.group_business_meeting_exclusive')),
        };
        document.addEventListener('DOMContentLoaded', function () {
            // Topics Tag Manager
            const topicSelector = document.getElementById('topic-selector');
            const container = document.getElementById('selected-topics-container');
            const typeSwitch = document.getElementById('meeting-type');
            const typeLabel = document.getElementById('label-type');

            function syncMeetingTypeForBusiness() {
                if (!container || !typeSwitch) return;
                const businessTag = container.querySelector('.topic-tag[data-is-business="true"]');
                if (businessTag) {
                    typeSwitch.checked = true;
                    typeSwitch.disabled = true;
                    if (typeLabel) {
                        typeLabel.textContent = translations.closed;
                    }
                } else {
                    typeSwitch.disabled = false;
                }
            }

            if(topicSelector && container) {
                topicSelector.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (!selectedOption.value) return;

                    const id = selectedOption.value;
                    const name = selectedOption.getAttribute('data-name');
                    const isBusiness = selectedOption.getAttribute('data-is-business') === 'true';

                    // Check if there are already other topics selected
                    const existingTags = container.querySelectorAll('.topic-tag');
                    if (existingTags.length > 0) {
                        const hasBusinessSelected = Array.from(existingTags).some(tag => tag.getAttribute('data-is-business') === 'true');
                        if (isBusiness || hasBusinessSelected) {
                            alert(translations.group_business_meeting_exclusive);
                            this.value = '';
                            return;
                        }
                    }

                    // Check if already added
                    if (!container.querySelector(`input[value="${id}"]`)) {
                        // Create tag
                        const tag = document.createElement('div');
                        tag.className = 'badge rounded-pill p-2 d-flex align-items-center shadow-sm topic-tag';
                        tag.style.background = 'linear-gradient(135deg, #0ea5e9, #0284c7)';
                        tag.style.fontSize = '0.85rem';
                        tag.setAttribute('data-is-business', isBusiness ? 'true' : 'false');
                        tag.innerHTML = `
                            <span style="margin-inline-end: 0.5rem;">${name}</span>
                            <input type="hidden" name="topics[]" value="${id}">
                            <button type="button" class="btn-close btn-close-white remove-topic" style="font-size: 0.5rem; margin-inline-start: 0.5rem;" aria-label="Close"></button>
                        `;
                        container.appendChild(tag);
                        syncMeetingTypeForBusiness();
                    }

                    // Reset selector
                    this.value = '';
                });

                // Event delegation for removing topics
                container.addEventListener('click', function(e) {
                    if (e.target.classList.contains('remove-topic')) {
                        e.target.closest('.topic-tag').remove();
                        syncMeetingTypeForBusiness();
                    }
                });
            }

            const switches = [
                {
                    input: document.getElementById('meeting-type'),
                    label: document.getElementById('label-type'),
                    on: translations.closed,
                    off: translations.open
                },
                {
                    input: document.getElementById('lang-switch'),
                    label: document.getElementById('label-lang'),
                    on: translations.arabic,
                    off: translations.english
                },
                {
                    input: document.getElementById('status-switch'),
                    label: document.getElementById('label-status'),
                    on: translations.Available,
                    off: translations.Suspended
                }
            ];

            switches.forEach(({input, label, on, off}) => {
                // update on load
                label.textContent = input.checked ? on : off;

                // update when toggled
                input.addEventListener('change', function() {
                    label.textContent = input.checked ? on : off;
                });
            });

            syncMeetingTypeForBusiness();

            // Recurrence checkbox exclusivity logic
            const weeklyCheckbox = document.getElementById('recurrence-weekly');
            const monthlyCheckboxes = [
                document.getElementById('recurrence-1st'),
                document.getElementById('recurrence-2nd'),
                document.getElementById('recurrence-3rd'),
                document.getElementById('recurrence-4th'),
                document.getElementById('recurrence-5th'),
                document.getElementById('recurrence-last')
            ];

            function updateRecurrenceState(e) {
                const trigger = e ? e.target : null;
                const anyMonthlyChecked = monthlyCheckboxes.some(cb => cb && cb.checked);
                
                if (trigger === weeklyCheckbox && weeklyCheckbox.checked) {
                    monthlyCheckboxes.forEach(cb => {
                        if (cb) {
                            cb.checked = false;
                            cb.disabled = true;
                        }
                    });
                } else if (anyMonthlyChecked) {
                    if (weeklyCheckbox) {
                        weeklyCheckbox.checked = false;
                        weeklyCheckbox.disabled = true;
                    }
                    monthlyCheckboxes.forEach(cb => {
                        if (cb) cb.disabled = false;
                    });
                } else {
                    if (weeklyCheckbox) {
                        weeklyCheckbox.disabled = false;
                    }
                    monthlyCheckboxes.forEach(cb => {
                        if (cb) cb.disabled = false;
                    });
                }
            }

            if (weeklyCheckbox) {
                weeklyCheckbox.addEventListener('change', updateRecurrenceState);
            }
            monthlyCheckboxes.forEach(cb => {
                if (cb) {
                    cb.addEventListener('change', updateRecurrenceState);
                }
            });

            // Initialize state on load
            if (weeklyCheckbox && weeklyCheckbox.checked) {
                monthlyCheckboxes.forEach(cb => {
                    if (cb) {
                        cb.checked = false;
                        cb.disabled = true;
                    }
                });
            } else if (monthlyCheckboxes.some(cb => cb && cb.checked)) {
                if (weeklyCheckbox) {
                    weeklyCheckbox.checked = false;
                    weeklyCheckbox.disabled = true;
                }
            }

            // Group Type switching logic
            const groupTypeSelector = document.getElementById('group-type-selector');
            const legacyContainer = document.getElementById('legacy-group-container');
            const directOnlineContainer = document.getElementById('direct-online-group-container');
            const legacySelect = legacyContainer ? legacyContainer.querySelector('select') : null;
            const directOnlineSelect = directOnlineContainer ? directOnlineContainer.querySelector('select') : null;

            const optionsHeader = document.querySelector('h6 i.bi-ui-checks-grid') ? document.querySelector('h6 i.bi-ui-checks-grid').closest('h6') : null;
            const optionsSection = document.querySelector('.options-section');
            const langSwitch = document.getElementById('lang-switch');
            const labelLang = document.getElementById('label-lang');
            const hiddenLangInput = document.querySelector('input[type="hidden"][name="lang"]');

            function handleGroupTypeChange() {
                if (!groupTypeSelector) return;
                
                const businessTopicOption = document.querySelector('#topic-selector option[data-is-business="true"]');
                const topicSelector = document.getElementById('topic-selector');

                if (groupTypeSelector.value === 'direct_online') {
                    if (legacyContainer) legacyContainer.style.display = 'none';
                    if (directOnlineContainer) directOnlineContainer.style.display = 'block';
                    if (legacySelect) legacySelect.value = '';
                    
                    // Hide meeting options
                    if (optionsHeader) optionsHeader.style.display = 'none';
                    if (optionsSection) {
                        optionsSection.style.display = 'none';
                        optionsSection.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
                    }
                    
                    // Lock language to Arabic
                    if (langSwitch) {
                        langSwitch.checked = true;
                        langSwitch.disabled = true;
                        if (labelLang) labelLang.textContent = translations.arabic;
                    }
                    if (hiddenLangInput) {
                        hiddenLangInput.value = 'arabic';
                    }

                    // Disable Group Business Meeting topic
                    if (businessTopicOption) {
                        businessTopicOption.disabled = true;
                        businessTopicOption.style.display = 'none';
                        if (topicSelector && topicSelector.value == businessTopicOption.value) {
                            topicSelector.value = '';
                        }
                    }
                    const businessTag = container ? container.querySelector('.topic-tag[data-is-business="true"]') : null;
                    if (businessTag) {
                        businessTag.remove();
                        if (typeSwitch) {
                            typeSwitch.disabled = false;
                        }
                    }
                } else {
                    if (legacyContainer) legacyContainer.style.display = 'block';
                    if (directOnlineContainer) directOnlineContainer.style.display = 'none';
                    if (directOnlineSelect) directOnlineSelect.value = '';
                    
                    // Show meeting options
                    if (optionsHeader) optionsHeader.style.display = 'block';
                    if (optionsSection) optionsSection.style.display = 'flex';
                    
                    // Unlock language switch
                    if (langSwitch) {
                        langSwitch.disabled = false;
                    }
                    if (hiddenLangInput) {
                        hiddenLangInput.value = 'english';
                    }

                    // Enable Group Business Meeting topic
                    if (businessTopicOption) {
                        businessTopicOption.disabled = false;
                        businessTopicOption.style.display = 'block';
                    }
                }
            }

            if (groupTypeSelector) {
                groupTypeSelector.addEventListener('change', handleGroupTypeChange);
                handleGroupTypeChange();
            }
        });
    </script>
</x-layout>

<style>
/* RTL and transition styles */
.transition-hover {
    transition: all 0.3s ease;
}
.transition-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.05) !important;
}
.widgets-icons {
    display: flex;
    align-items: center;
    justify-content: center;
}
[dir="rtl"] .form-check-input {
    float: right !important;
    margin-right: -1.5em;
    margin-left: 0;
}
</style>
