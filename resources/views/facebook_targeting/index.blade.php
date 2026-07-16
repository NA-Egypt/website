<x-layout>
    <x-backhead>{{ __('Facebook Targeting Area Mapper') }}</x-backhead>

    <!-- Leaflet.js CDN -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

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
        .badge-neighborhood { background-color: #007bff; color: white; }
        .badge-city { background-color: #ffc107; color: #212529; }
        .badge-default { background-color: #6c757d; color: white; }
        .badge-online { background-color: #e83e8c; color: white; }
        
        .radius-input {
            width: 80px;
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
                        <h5 class="mb-0 fw-bold"><i class="bi bi-sliders text-primary me-2"></i>{{ __('Actions & Stats') }}</h5>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-between">
                        <!-- Stats Grid -->
                        <div>
                            <div class="row g-2 mb-4">
                                <div class="col-6">
                                    <div class="p-3 border rounded text-center bg-light">
                                        <div class="text-muted small mb-1">{{ __('Total Groups') }}</div>
                                        <h3 class="fw-bold mb-0 text-dark">{{ count($groups) }}</h3>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 border rounded text-center bg-light">
                                        <div class="text-muted small mb-1">{{ __('Parsed Coords') }}</div>
                                        <h3 class="fw-bold mb-0 text-success">
                                            {{ count(array_filter($groups, fn($g) => $g['source'] === 'parsed')) }}
                                        </h3>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 border rounded text-center bg-light">
                                        <div class="text-muted small mb-1">{{ __('Fallbacks Used') }}</div>
                                        <h3 class="fw-bold mb-0 text-warning">
                                            {{ count(array_filter($groups, fn($g) => in_array($g['source'], ['neighborhood', 'city', 'default']))) }}
                                        </h3>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 border rounded text-center bg-light">
                                        <div class="text-muted small mb-1">{{ __('Online Groups') }}</div>
                                        <h3 class="fw-bold mb-0 text-danger">
                                            {{ count(array_filter($groups, fn($g) => $g['source'] === 'online')) }}
                                        </h3>
                                    </div>
                                </div>
                            </div>

                            <p class="text-muted small">
                                {{ __('This tool prepares group locations for Facebook Ads targeting. Shortened Google Maps links are resolved to absolute coordinates. Groups without coordinates fall back to their neighborhood average or city center.') }}
                            </p>
                        </div>

                        <!-- Sync Action Button -->
                        <div class="mt-4">
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
                        <div class="col-md-3">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search"></i></span>
                                <input type="text" id="searchBox" class="form-control border-start-0" placeholder="{{ __('Search groups, addresses...') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select id="filterCity" class="form-select form-select-sm">
                                <option value="">{{ __('All Cities') }}</option>
                                @foreach (array_unique(array_column($groups, 'city')) as $c)
                                    <option value="{{ $c }}">{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="filterSource" class="form-select form-select-sm">
                                <option value="">{{ __('All Sources') }}</option>
                                <option value="parsed">{{ __('Parsed Coordinates') }}</option>
                                <option value="neighborhood">{{ __('Neighborhood Fallback') }}</option>
                                <option value="city">{{ __('City Fallback') }}</option>
                                <option value="online">{{ __('Online / Virtual') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3 text-end">
                            <button type="submit" class="btn btn-success btn-sm w-100 rounded-pill shadow-sm d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-download"></i>
                                {{ __('Download Facebook CSV') }}
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
                                <tr class="group-row" 
                                    data-id="{{ $g['id'] }}"
                                    data-name="{{ $g['name'] }}"
                                    data-city="{{ $g['city'] }}"
                                    data-source="{{ $g['source'] }}"
                                    data-lat="{{ $g['lat'] }}"
                                    data-lng="{{ $g['lng'] }}">
                                    <td>
                                        <!-- Value structure: id:lat:lng:name -->
                                        <input type="checkbox" name="selected_groups[]" 
                                               value="{{ $g['id'] }}:{{ $g['lat'] }}:{{ $g['lng'] }}:{{ $g['name'] }}" 
                                               class="form-check-input row-checkbox" checked>
                                    </td>
                                    <td class="fw-bold">{{ $g['name'] }}</td>
                                    <td>{{ $g['city'] }}</td>
                                    <td>{{ $g['neighborhood'] }}</td>
                                    <td>
                                        @if ($g['location_url'])
                                            <a href="{{ $g['location_url'] }}" target="_blank" class="text-truncate d-inline-block" style="max-width: 150px;" title="{{ $g['location_url'] }}">
                                                <i class="bi bi-box-arrow-up-right me-1"></i>Link
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
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
                                        @elseif($g['source'] === 'neighborhood')
                                            <span class="badge badge-neighborhood">{{ __('Neighborhood Fallback') }}</span>
                                        @elseif($g['source'] === 'city')
                                            <span class="badge badge-city">{{ __('City Fallback') }}</span>
                                        @elseif($g['source'] === 'online')
                                            <span class="badge badge-online">{{ __('Online') }}</span>
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

    <!-- JS logic for Leaflet Map and table interaction -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize map centered on Egypt
            const map = L.map('map').setView([26.8206, 30.8025], 6);

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Store Leaflet marker & circle instances
            const mapElements = {};

            // Fetch group rows data
            const rows = document.querySelectorAll('.group-row');
            const groupData = [];

            rows.forEach(row => {
                const id = row.getAttribute('data-id');
                const name = row.getAttribute('data-name');
                const city = row.getAttribute('data-city');
                const source = row.getAttribute('data-source');
                const lat = parseFloat(row.getAttribute('data-lat'));
                const lng = parseFloat(row.getAttribute('data-lng'));
                const radiusInput = row.querySelector('.radius-input');
                const defaultRadius = parseFloat(radiusInput.value);

                if (!isNaN(lat) && !isNaN(lng)) {
                    // Create marker and circle on the map
                    const marker = L.marker([lat, lng]).addTo(map);
                    
                    // Style circle color based on source
                    let circleColor = '#6c757d';
                    if (source === 'parsed') circleColor = '#28a745';
                    else if (source === 'neighborhood') circleColor = '#007bff';
                    else if (source === 'city') circleColor = '#ffc107';
                    else if (source === 'online') circleColor = '#e83e8c';

                    const circle = L.circle([lat, lng], {
                        color: circleColor,
                        fillColor: circleColor,
                        fillOpacity: 0.15,
                        radius: defaultRadius * 1000 // Leaflet radius is in meters
                    }).addTo(map);

                    // Add popup details
                    const popupContent = `
                        <div style="font-family: Cairo, sans-serif;">
                            <h6 class="fw-bold mb-1">${name}</h6>
                            <p class="mb-1 text-muted small">${city}</p>
                            <p class="mb-1 small font-monospace">${lat.toFixed(5)}, ${lng.toFixed(5)}</p>
                            <span class="badge small mb-2" style="background-color: ${circleColor}; color: ${source === 'city' ? '#212529' : 'white'};">${source.toUpperCase()}</span>
                            <div class="mt-1">
                                <label class="small text-muted mb-0">Radius: <strong id="popup-radius-val-${id}">${defaultRadius}</strong> km</label>
                            </div>
                        </div>
                    `;
                    marker.bindPopup(popupContent);

                    // Save elements reference
                    mapElements[id] = { marker, circle };

                    groupData.push({ id, name, city, source, lat, lng, row, marker, circle });
                }
            });

            // Adjust bounds to fit all markers if we have any
            const latLngs = groupData.map(g => [g.lat, g.lng]);
            if (latLngs.length > 0) {
                map.fitBounds(L.latLngBounds(latLngs));
            }

            // Real-time Radius Change interaction
            document.querySelectorAll('.radius-input').forEach(input => {
                input.addEventListener('change', function () {
                    const id = this.getAttribute('data-group-id');
                    const newRadiusKm = parseFloat(this.value) || 5;
                    
                    if (mapElements[id]) {
                        // Update Leaflet circle radius
                        mapElements[id].circle.setRadius(newRadiusKm * 1000);
                        
                        // Update popup label if open
                        const popupLabel = document.getElementById(`popup-radius-val-${id}`);
                        if (popupLabel) {
                            popupLabel.innerText = newRadiusKm;
                        }
                    }
                });
            });

            // Filtering & Search
            const searchBox = document.getElementById('searchBox');
            const filterCity = document.getElementById('filterCity');
            const filterSource = document.getElementById('filterSource');

            function applyFilters() {
                const searchVal = searchBox.value.toLowerCase();
                const cityVal = filterCity.value;
                const sourceVal = filterSource.value;

                groupData.forEach(g => {
                    const matchesSearch = g.name.toLowerCase().includes(searchVal) || 
                                          g.row.querySelector('td:nth-child(4)').innerText.toLowerCase().includes(searchVal);
                    const matchesCity = !cityVal || g.city === cityVal;
                    const matchesSource = !sourceVal || g.source === sourceVal;

                    if (matchesSearch && matchesCity && matchesSource) {
                        g.row.style.display = '';
                        if (!map.hasLayer(g.marker)) {
                            g.marker.addTo(map);
                            g.circle.addTo(map);
                        }
                    } else {
                        g.row.style.display = 'none';
                        if (map.hasLayer(g.marker)) {
                            map.removeLayer(g.marker);
                            map.removeLayer(g.circle);
                        }
                    }
                });
            }

            searchBox.addEventListener('input', applyFilters);
            filterCity.addEventListener('change', applyFilters);
            filterSource.addEventListener('change', applyFilters);

            // Bulk toggle checkboxes
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.row-checkbox');

            selectAll.addEventListener('change', function () {
                const isChecked = this.checked;
                checkboxes.forEach(cb => {
                    // Only toggle visible rows' checkboxes
                    const row = cb.closest('tr');
                    if (row.style.display !== 'none') {
                        cb.checked = isChecked;
                    }
                });
            });
        });

        // Show Spinner / Loading State on Sync
        function showLoadingState() {
            const syncBtn = document.getElementById('syncBtn');
            const syncIcon = document.getElementById('syncIcon');
            const syncText = document.getElementById('syncText');
            const loadingMessage = document.getElementById('loadingMessage');

            syncBtn.disabled = true;
            syncIcon.classList.add('bi-spin', 'd-none');
            syncText.innerText = "{{ __('Processing...') }}";
            loadingMessage.classList.remove('d-none');
        }
    </script>
</x-layout>
