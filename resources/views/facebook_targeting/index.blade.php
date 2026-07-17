<x-layout>
    <x-backhead>{{ __('Facebook Targeting Area Mapper') }}</x-backhead>

    <!-- Google Maps JS SDK -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAJ15C_GQbFUD1oqhVSZQDsVamHRoPkmhE" async defer></script>

    <style>
        #map {
            height: 500px;
            width: 100%;
            border-radius: 12px;
            border: 1px solid var(--glass-border);
            box-shadow: var(--neon-glow);
            z-index: 1;
        }
        .badge-parsed { background-color: #28a745; color: white; }
        .badge-fallback { background-color: #fd7e14; color: white; }
        .badge-neighborhood { background-color: #007bff; color: white; }
        .badge-city { background-color: #ffc107; color: #212529; }
        .badge-default { background-color: #6c757d; color: white; }
        
        .radius-input {
            width: 80px;
        }
        .row-imprecise {
            background-color: rgba(253, 126, 20, 0.05) !important;
        }
        .warning-border {
            border-left: 4px solid #fd7e14 !important;
        }
    </style>

    <div class="container-fluid py-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4 mb-4">
            <!-- Map Visualization Card -->
            <div class="col-lg-8">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-map-fill text-primary me-2"></i>{{ __('Targeting Areas Map') }}</h5>
                        <div class="text-muted small">{{ __('Circles represent the ad targeting radius') }}</div>
                    </div>
                    <div class="card-body p-3">
                        <div id="map"></div>
                    </div>
                </div>
            </div>

            <!-- Controls and Stats Card -->
            <div class="col-lg-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-transparent py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-sliders text-primary me-2"></i>{{ __('Targeting Metrics') }}</h5>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-between">
                        <!-- Stats Grid -->
                        <div>
                            <div class="row g-2 mb-4">
                                <div class="col-6">
                                    <div class="p-3 border rounded text-center bg-light">
                                        <div class="text-muted small mb-1{{ app()->getLocale() === 'ar' ? ' me-1' : '' }}">{{ __('Active Groups') }}</div>
                                        <h3 class="fw-bold mb-0 text-dark" id="stat-total-groups">{{ count($groups) }}</h3>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 border rounded text-center bg-light">
                                        <div class="text-muted small mb-1{{ app()->getLocale() === 'ar' ? ' me-1' : '' }}">{{ __('Accuracy Rate') }}</div>
                                        <h3 class="fw-bold mb-0 text-success" id="stat-accuracy-rate">0%</h3>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 border rounded text-center bg-light" id="stat-overlap-card">
                                        <div class="text-muted small mb-1{{ app()->getLocale() === 'ar' ? ' me-1' : '' }}">{{ __('Overlapping Areas') }}</div>
                                        <h3 class="fw-bold mb-0 text-warning" id="stat-overlap-count">0</h3>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 border rounded text-center bg-light">
                                        <div class="text-muted small mb-1{{ app()->getLocale() === 'ar' ? ' me-1' : '' }}">{{ __('Needs Precise Link') }}</div>
                                        <h3 class="fw-bold mb-0 text-danger" id="stat-imprecise-count">0</h3>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-warning border-0 small mb-4 py-2.5">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                {{ __('Targeting overlapping regions increases ad spend competition against your own groups.') }}
                            </div>
                            
                            <p class="text-muted small">
                                {{ __('To improve ad accuracy and reduce budget waste, add specific Google Maps links to groups currently utilizing City or Neighborhood fallback coordinates.') }}
                            </p>
                        </div>

                        <!-- Sync Action Button -->
                        <div class="mt-4">
                            @if(auth()->user()->hasRole('super admin'))
                            <form action="{{ route('facebook-targeting.sync') }}" method="POST" onsubmit="showLoadingState()">
                                @csrf
                                <button type="submit" id="syncBtn" class="btn btn-primary w-100 py-2.5 rounded-pill shadow-sm d-flex align-items-center justify-content-center gap-2">
                                    <i id="syncIcon" class="bi bi-arrow-repeat"></i>
                                    <span id="syncText">{{ __('Sync & Resolve URLs') }}</span>
                                </button>
                            </form>
                            <div id="loadingMessage" class="text-center text-primary mt-2 small d-none">
                                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                {{ __('Resolving redirects and geocoding, please wait...') }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Table Card -->
        <div class="card shadow-sm border-0">
            <form action="{{ route('facebook-targeting.download') }}" method="POST" id="downloadForm">
                @csrf
                <div class="card-header bg-transparent py-3">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search"></i></span>
                                <input type="text" id="searchBox" class="form-control border-start-0" placeholder="{{ __('Search groups, addresses...') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select id="filterCity" class="form-select form-select-sm">
                                <option value="">{{ __('All Cities') }}</option>
                                @foreach (array_unique(array_column($groups, 'city')) as $c)
                                    <option value="{{ $c }}">{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="filterSource" class="form-select form-select-sm">
                                <option value="">{{ __('All Sources') }}</option>
                                <option value="parsed">{{ __('Parsed Coordinates') }}</option>
                                <option value="fallback">{{ __('Any Fallback') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2 text-start">
                            <div class="form-check form-switch mt-1">
                                <input class="form-check-input" type="checkbox" id="toggleImpreciseOnly">
                                <label class="form-check-label small" for="toggleImpreciseOnly">{{ __('Imprecise Only') }}</label>
                            </div>
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="submit" class="btn btn-success btn-sm w-100 rounded-pill shadow-sm d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-download"></i>
                                {{ __('Download CSV') }}
                            </button>
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="button" id="getStaticMapBtn" class="btn btn-info btn-sm w-100 rounded-pill shadow-sm text-white d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-link-45deg"></i>
                                {{ __('Copy Signed Map URL') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0 text-center" id="groupsTable">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" class="form-check-input" id="selectAll" checked>
                                </th>
                                <th>{{ __('Group Name') }}</th>
                                <th>{{ __('City') }}</th>
                                <th>{{ __('Neighborhood') }}</th>
                                <th>{{ __('Google Maps Link') }}</th>
                                <th>{{ __('Coordinates') }}</th>
                                <th>{{ __('Source') }}</th>
                                <th>{{ __('Targeting Radius (km)') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groups as $g)
                                @php
                                    $isImprecise = in_array($g['source'], ['neighborhood_avg', 'city_preset', 'default']);
                                @endphp
                                <tr class="group-row {{ $isImprecise ? 'row-imprecise warning-border' : '' }}" 
                                    data-id="{{ $g['id'] }}"
                                    data-name="{{ $g['name'] }}"
                                    data-city="{{ $g['city'] }}"
                                    data-source="{{ $g['source'] }}"
                                    data-imprecise="{{ $isImprecise ? 'true' : 'false' }}"
                                    data-lat="{{ $g['lat'] }}"
                                    data-lng="{{ $g['lng'] }}">
                                    <td>
                                        <input type="checkbox" name="selected_groups[]" 
                                               value="{{ $g['id'] }}:{{ $g['lat'] }}:{{ $g['lng'] }}:{{ $g['name'] }}" 
                                               class="form-check-input row-checkbox" checked>
                                    </td>
                                    <td class="fw-bold">
                                        {{ $g['name'] }}
                                        @if ($isImprecise)
                                            <i class="bi bi-exclamation-triangle-fill text-warning ms-1" title="{{ __('Fallback location used') }}"></i>
                                        @endif
                                    </td>
                                    <td>{{ $g['city'] }}</td>
                                    <td>{{ $g['neighborhood'] }}</td>
                                    <td>
                                        @if ($g['location_url'])
                                            <a href="{{ $g['location_url'] }}" target="_blank" class="text-truncate d-inline-block" style="max-width: 150px;" title="{{ $g['location_url'] }}">
                                                <i class="bi bi-box-arrow-up-right me-1"></i>Link
                                            </a>
                                        @else
                                            <span class="text-danger small"><i class="bi bi-link-45deg"></i>{{ __('Missing') }}</span>
                                        @endif
                                    </td>
                                    <td class="small font-monospace">
                                        @if ($g['lat'] && $g['lng'])
                                            {{ round($g['lat'], 6) }}, {{ round($g['lng'], 6) }}
                                        @else
                                            <span class="text-danger">{{ __('None') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($g['source'] === 'parsed')
                                            <span class="badge badge-parsed">{{ __('Parsed') }}</span>
                                        @elseif($g['source'] === 'neighborhood_db')
                                            <span class="badge badge-neighborhood" title="{{ __('Neighborhood coordinates set in DB') }}">{{ __('Neighborhood (DB)') }}</span>
                                        @elseif($g['source'] === 'neighborhood_avg')
                                            <span class="badge badge-fallback" title="{{ __('Neighborhood Center average fallback') }}">{{ __('Neighborhood (Avg)') }}</span>
                                        @elseif($g['source'] === 'city_db')
                                            <span class="badge badge-neighborhood" title="{{ __('City coordinates set in DB') }}">{{ __('City (DB)') }}</span>
                                        @elseif($g['source'] === 'city_preset')
                                            <span class="badge badge-fallback" title="{{ __('City Center fallback preset') }}">{{ __('City (Preset)') }}</span>
                                        @else
                                            <span class="badge badge-default">{{ __('Default') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center align-items-center">
                                            <input type="number" 
                                                   name="radii[{{ $g['id'] }}]" 
                                                   class="form-control form-control-sm radius-input text-center" 
                                                   value="{{ $g['radius'] }}" 
                                                   min="1" 
                                                   max="80" 
                                                   data-group-id="{{ $g['id'] }}">
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>

    <!-- JS logic for Google Maps, real-time metrics, and overlap checking -->
    <script>
        let map;
        const mapElements = {};
        const groupData = [];

        function initMap() {
            // Initialize Google Map centered on Egypt
            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: 26.8206, lng: 30.8025 },
                zoom: 6,
                mapTypeControl: true,
                streetViewControl: false,
                fullscreenControl: true
            });

            // Fetch group rows data
            const rows = document.querySelectorAll('.group-row');
            const bounds = new google.maps.LatLngBounds();
            let hasBounds = false;

            rows.forEach(row => {
                const id = row.getAttribute('data-id');
                const name = row.getAttribute('data-name');
                const city = row.getAttribute('data-city');
                const source = row.getAttribute('data-source');
                const imprecise = row.getAttribute('data-imprecise') === 'true';
                const lat = parseFloat(row.getAttribute('data-lat'));
                const lng = parseFloat(row.getAttribute('data-lng'));
                const radiusInput = row.querySelector('.radius-input');
                const defaultRadius = parseFloat(radiusInput.value);

                if (!isNaN(lat) && !isNaN(lng)) {
                    const position = { lat, lng };

                    // Add Google Marker
                    const marker = new google.maps.Marker({
                        position: position,
                        map: map,
                        title: name
                    });

                    // Define circle color
                    let circleColor = '#28a745'; // green for parsed
                    if (imprecise) circleColor = '#fd7e14'; // orange for fallbacks

                    // Add Google Circle
                    const circle = new google.maps.Circle({
                        strokeColor: circleColor,
                        strokeOpacity: 0.8,
                        strokeWeight: 1.5,
                        fillColor: circleColor,
                        fillOpacity: 0.15,
                        map: map,
                        center: position,
                        radius: defaultRadius * 1000 // in meters
                    });

                    // Add Info Window popup
                    const popupContent = `
                        <div style="font-family: Cairo, sans-serif; min-width: 150px;">
                            <h6 class="fw-bold mb-1">${name}</h6>
                            <p class="mb-1 text-muted small">${city}</p>
                            <p class="mb-1 small font-monospace">${lat.toFixed(5)}, ${lng.toFixed(5)}</p>
                            <span class="badge small mb-2" style="background-color: ${circleColor}; color: white;">${source.toUpperCase()}</span>
                            ${imprecise ? `<div class="text-warning small mb-2"><i class="bi bi-exclamation-triangle"></i> Fallback Area</div>` : ''}
                            <div class="mt-1">
                                <label class="small text-muted mb-0">Radius: <strong id="popup-radius-val-${id}">${defaultRadius}</strong> km</label>
                            </div>
                        </div>
                    `;

                    const infoWindow = new google.maps.InfoWindow({
                        content: popupContent
                    });

                    marker.addListener('click', () => {
                        infoWindow.open(map, marker);
                    });

                    bounds.extend(position);
                    hasBounds = true;

                    mapElements[id] = { marker, circle, lat, lng, source, imprecise };

                    groupData.push({ 
                        id, 
                        name, 
                        city, 
                        source, 
                        imprecise, 
                        lat, 
                        lng, 
                        row, 
                        checkbox: row.querySelector('.row-checkbox'),
                        radiusInput,
                        marker, 
                        circle 
                    });
                }
            });

            // Adjust bounds to fit all markers
            if (hasBounds) {
                map.fitBounds(bounds);
            }

            // Real-time Metrics & Overlap Calculator
            function calculateMetrics() {
                let totalSelected = 0;
                let preciseSelected = 0;
                let impreciseCount = 0;

                const activeGroups = groupData.filter(g => {
                    const rowVisible = g.row.style.display !== 'none';
                    const isChecked = g.checkbox.checked;
                    
                    if (rowVisible && isChecked) {
                        totalSelected++;
                        if (!g.imprecise) preciseSelected++;
                        return true;
                    }
                    return false;
                });

                groupData.forEach(g => {
                    if (g.row.style.display !== 'none' && g.imprecise) {
                        impreciseCount++;
                    }
                });

                // Calculate Overlapping Areas among active groups
                let overlapCount = 0;
                const overlappingGroupIds = new Set();

                // Haversine formula helper
                function getHaversineDistance(lat1, lon1, lat2, lon2) {
                    const R = 6371;
                    const dLat = (lat2 - lat1) * Math.PI / 180;
                    const dLon = (lon2 - lon1) * Math.PI / 180;
                    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                              Math.sin(dLon / 2) * Math.sin(dLon / 2);
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                    return R * c;
                }

                for (let i = 0; i < activeGroups.length; i++) {
                    const gA = activeGroups[i];
                    const radA = parseFloat(gA.radiusInput.value) || 5;

                    for (let j = i + 1; j < activeGroups.length; j++) {
                        const gB = activeGroups[j];
                        const radB = parseFloat(gB.radiusInput.value) || 5;

                        const distance = getHaversineDistance(gA.lat, gA.lng, gB.lat, gB.lng);
                        if (distance < (radA + radB)) {
                            overlappingGroupIds.add(gA.id);
                            overlappingGroupIds.add(gB.id);
                        }
                    }
                }

                overlapCount = overlappingGroupIds.size;

                // Update Google Map circle styles based on overlap state
                groupData.forEach(g => {
                    if (mapElements[g.id]) {
                        const circleObj = mapElements[g.id].circle;
                        if (overlappingGroupIds.has(g.id)) {
                            // Set dashed style for overlapping circles
                            circleObj.setOptions({
                                strokePattern: [5, 10], // approximated pattern if supported
                                strokeWeight: 2.5
                            });
                        } else {
                            circleObj.setOptions({
                                strokePattern: null,
                                strokeWeight: 1.5
                            });
                        }
                    }
                });

                // Update UI elements
                const accuracyRate = totalSelected > 0 ? Math.round((preciseSelected / totalSelected) * 100) : 0;
                document.getElementById('stat-total-groups').innerText = totalSelected;
                document.getElementById('stat-accuracy-rate').innerText = `${accuracyRate}%`;
                document.getElementById('stat-imprecise-count').innerText = impreciseCount;
                document.getElementById('stat-overlap-count').innerText = overlapCount;

                const overlapCard = document.getElementById('stat-overlap-card');
                if (overlapCount > 0) {
                    overlapCard.classList.add('bg-warning-subtle');
                } else {
                    overlapCard.classList.remove('bg-warning-subtle');
                }
            }

            // Real-time Radius Change
            document.querySelectorAll('.radius-input').forEach(input => {
                input.addEventListener('change', function () {
                    const id = this.getAttribute('data-group-id');
                    const newRadiusKm = parseFloat(this.value) || 5;
                    
                    if (mapElements[id]) {
                        mapElements[id].circle.setRadius(newRadiusKm * 1000);
                        const popupLabel = document.getElementById(`popup-radius-val-${id}`);
                        if (popupLabel) popupLabel.innerText = newRadiusKm;
                    }
                    calculateMetrics();
                });
            });

            document.querySelectorAll('.row-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', calculateMetrics);
            });

            // Filters implementation
            const searchBox = document.getElementById('searchBox');
            const filterCity = document.getElementById('filterCity');
            const filterSource = document.getElementById('filterSource');
            const toggleImpreciseOnly = document.getElementById('toggleImpreciseOnly');

            function applyFilters() {
                const searchVal = searchBox.value.toLowerCase();
                const cityVal = filterCity.value;
                const sourceVal = filterSource.value;
                const impreciseOnly = toggleImpreciseOnly.checked;

                groupData.forEach(g => {
                    const matchesSearch = g.name.toLowerCase().includes(searchVal) || 
                                          g.row.querySelector('td:nth-child(4)').innerText.toLowerCase().includes(searchVal);
                    const matchesCity = !cityVal || g.city === cityVal;
                    
                    let matchesSource = true;
                    if (sourceVal === 'parsed') {
                        matchesSource = !g.imprecise;
                    } else if (sourceVal === 'fallback') {
                        matchesSource = g.imprecise;
                    }

                    const matchesImpreciseToggle = !impreciseOnly || g.imprecise;

                    if (matchesSearch && matchesCity && matchesSource && matchesImpreciseToggle) {
                        g.row.style.display = '';
                        g.marker.setMap(map);
                        g.circle.setMap(map);
                    } else {
                        g.row.style.display = 'none';
                        g.marker.setMap(null);
                        g.circle.setMap(null);
                    }
                });

                calculateMetrics();
            }

            searchBox.addEventListener('input', applyFilters);
            filterCity.addEventListener('change', applyFilters);
            filterSource.addEventListener('change', applyFilters);
            toggleImpreciseOnly.addEventListener('change', applyFilters);

            // Select All toggle
            const selectAll = document.getElementById('selectAll');
            selectAll.addEventListener('change', function () {
                const isChecked = this.checked;
                groupData.forEach(g => {
                    if (g.row.style.display !== 'none') {
                        g.checkbox.checked = isChecked;
                    }
                });
                calculateMetrics();
            });

            // AJAX signed Static Map URL generation
            const getStaticMapBtn = document.getElementById('getStaticMapBtn');
            getStaticMapBtn.addEventListener('click', function () {
                const originalText = this.innerHTML;
                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Generating...';

                // Gather checked row data
                const selectedGroups = [];
                const radii = {};

                groupData.forEach(g => {
                    if (g.row.style.display !== 'none' && g.checkbox.checked) {
                        selectedGroups.push(`${g.id}:${g.lat}:${g.lng}:${g.name}`);
                        radii[g.id] = g.radiusInput.value;
                    }
                });

                if (selectedGroups.length === 0) {
                    alert("{{ __('Please select at least one group to generate the static map.') }}");
                    this.disabled = false;
                    this.innerHTML = originalText;
                    return;
                }

                // Send POST request
                fetch("{{ route('facebook-targeting.static-map') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        selected_groups: selectedGroups,
                        radii: radii
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.url) {
                        navigator.clipboard.writeText(data.url)
                            .then(() => {
                                alert("{{ __('Signed Static Map URL copied to clipboard successfully!') }}");
                            })
                            .catch(err => {
                                console.error('Failed to copy text: ', err);
                                alert("{{ __('Could not copy to clipboard automatically. URL: ') }}" + data.url);
                            });
                    } else {
                        alert("{{ __('Failed to generate Static Map URL.') }}");
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("{{ __('An error occurred while generating the map URL.') }}");
                })
                .finally(() => {
                    this.disabled = false;
                    this.innerHTML = originalText;
                });
            });

            calculateMetrics();
        }

        // Trigger Google Maps Callback
        window.addEventListener('load', () => {
            if (typeof google !== 'undefined') {
                initMap();
            } else {
                console.error("Google Maps SDK failed to load.");
            }
        });

        // Show Spinner / Loading State on Sync
        function showLoadingState() {
            const syncBtn = document.getElementById('syncBtn');
            const syncIcon = document.getElementById('syncIcon');
            const syncText = document.getElementById('syncText');
            const loadingMessage = document.getElementById('loadingMessage');

            if (syncBtn) syncBtn.disabled = true;
            if (syncIcon) syncIcon.classList.add('bi-spin', 'd-none');
            if (syncText) syncText.innerText = "{{ __('Processing...') }}";
            if (loadingMessage) loadingMessage.classList.remove('d-none');
        }
    </script>
</x-layout>
