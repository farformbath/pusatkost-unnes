import React, { useEffect, useRef } from 'react';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import Supercluster from 'supercluster';
import { Listing } from '@/types/listing';

interface MapViewProps {
    listings?: Listing[];
    singleListing?: Listing;
    className?: string;
    height?: string;
}

interface ListingPointProperties {
    cluster: false;
    listing: Listing;
    index: number;
}

type ListingFeature = Supercluster.PointFeature<ListingPointProperties>;

/**
 * Official Campus & Landmark Coordinates around Universitas Negeri Semarang (UNNES), Sekaran, Gunungpati.
 * Located at Gedung Rektorat UNNES (Sekaran).
 */
export const UNNES_CAMPUS_COORDINATES: [number, number] = [-7.0494, 110.3984];

export const AREA_COORDINATES: Record<string, [number, number]> = {
    sekaran: [-7.0498, 110.3950],
    banaran: [-7.0520, 110.3985],
    'taman siswa': [-7.0475, 110.3970],
    patemon: [-7.0550, 110.3850],
    trangkil: [-7.0440, 110.4030],
    kalisegoro: [-7.0360, 110.3740],
    gunungpati: [-7.0540, 110.3950],
    menoreh: [-7.0250, 110.3920],
    sampangan: [-7.0250, 110.3920],
    unnes: UNNES_CAMPUS_COORDINATES,
};

/**
 * GeoJSON boundary representing the verified campus perimeter of Universitas Negeri Semarang (UNNES).
 */
export const UNNES_CAMPUS_GEOJSON: GeoJSON.Feature<GeoJSON.Polygon> = {
    type: 'Feature',
    properties: {
        name: 'Universitas Negeri Semarang',
        campus: 'Kampus Utama UNNES',
        city: 'Semarang',
    },
    geometry: {
        type: 'Polygon',
        coordinates: [
            [
                [110.389794, -7.047945],
                [110.38959, -7.048424],
                [110.389547, -7.049883],
                [110.389698, -7.050194],
                [110.390727, -7.050726],
                [110.390807, -7.051001],
                [110.390872, -7.051896],
                [110.391365, -7.052513],
                [110.391344, -7.05279],
                [110.391001, -7.053344],
                [110.390972, -7.053592],
                [110.392077, -7.053555],
                [110.39242, -7.053342],
                [110.392549, -7.052889],
                [110.392484, -7.052267],
                [110.392656, -7.052112],
                [110.393117, -7.052059],
                [110.393214, -7.052043],
                [110.393477, -7.0515],
                [110.394067, -7.051787],
                [110.394603, -7.051803],
                [110.394678, -7.051266],
                [110.394426, -7.051202],
                [110.39493, -7.050483],
                [110.39536, -7.049402],
                [110.39552, -7.049344],
                [110.39566, -7.049386],
                [110.395687, -7.05011],
                [110.396132, -7.050148],
                [110.396191, -7.05043],
                [110.396298, -7.050627],
                [110.397006, -7.050568],
                [110.397017, -7.050925],
                [110.397746, -7.050962],
                [110.398148, -7.051303],
                [110.398261, -7.050983],
                [110.398422, -7.050941],
                [110.398684, -7.050962],
                [110.398727, -7.050755],
                [110.398851, -7.050845],
                [110.398915, -7.051026],
                [110.398947, -7.051207],
                [110.398899, -7.051372],
                [110.398792, -7.051335],
                [110.398304, -7.051511],
                [110.39818, -7.051793],
                [110.398512, -7.051953],
                [110.3987, -7.051865],
                [110.398944, -7.051892],
                [110.398809, -7.052294],
                [110.398756, -7.052738],
                [110.399013, -7.05306],
                [110.399488, -7.053127],
                [110.399466, -7.053361],
                [110.399729, -7.053542],
                [110.400239, -7.053917],
                [110.400464, -7.053963],
                [110.400499, -7.053835],
                [110.400485, -7.053606],
                [110.400731, -7.053542],
                [110.401374, -7.052962],
                [110.401641, -7.05224],
                [110.401781, -7.052144],
                [110.401931, -7.05224],
                [110.402215, -7.052671],
                [110.402966, -7.052975],
                [110.403127, -7.052767],
                [110.403642, -7.052921],
                [110.403749, -7.052751],
                [110.403829, -7.052501],
                [110.403706, -7.05241],
                [110.403545, -7.052394],
                [110.403331, -7.052346],
                [110.403288, -7.052171],
                [110.403202, -7.05207],
                [110.403336, -7.051633],
                [110.403272, -7.051479],
                [110.403121, -7.05133],
                [110.402473, -7.050968],
                [110.402054, -7.050936],
                [110.402086, -7.050691],
                [110.40199, -7.05044],
                [110.401716, -7.05035],
                [110.401448, -7.050227],
                [110.401405, -7.049967],
                [110.401202, -7.049716],
                [110.400832, -7.049525],
                [110.400531, -7.049205],
                [110.400445, -7.048934],
                [110.400537, -7.048758],
                [110.400231, -7.048433],
                [110.400049, -7.048109],
                [110.399528, -7.047784],
                [110.39933, -7.047864],
                [110.399201, -7.0478],
                [110.399014, -7.047688],
                [110.398279, -7.047395],
                [110.397485, -7.047172],
                [110.396723, -7.04698],
                [110.396402, -7.046996],
                [110.396209, -7.047012],
                [110.39608, -7.047544],
                [110.396085, -7.047747],
                [110.396064, -7.047938],
                [110.395989, -7.048098],
                [110.395683, -7.048306],
                [110.395485, -7.048338],
                [110.395243, -7.048348],
                [110.39505, -7.048327],
                [110.394771, -7.047326],
                [110.394825, -7.04674],
                [110.39461, -7.046405],
                [110.393999, -7.046421],
                [110.392621, -7.046623],
                [110.392653, -7.047587],
                [110.392519, -7.048167],
                [110.392187, -7.048236],
                [110.391752, -7.048295],
                [110.391307, -7.048146],
                [110.390524, -7.048119],
                [110.38984, -7.047933],
                [110.389794, -7.047945], // Closing ring
            ],
        ],
    },
};

const getListingCoords = (listing: Listing, index: number): [number, number] => {
    if (listing.latitude !== null && listing.latitude !== undefined && listing.longitude !== null && listing.longitude !== undefined) {
        const lat = Number(listing.latitude);
        const lng = Number(listing.longitude);
        if (!isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0) {
            return [lat, lng];
        }
    }
    const areaKey = (listing.area || '').toLowerCase().trim();
    const baseCoords = AREA_COORDINATES[areaKey] || UNNES_CAMPUS_COORDINATES;
    // Add deterministic slight jitter only if multiple items share fallback area without custom lat/lng
    const jitterLat = Math.sin(index * 1.7) * 0.001;
    const jitterLng = Math.cos(index * 1.7) * 0.001;
    return [baseCoords[0] + jitterLat, baseCoords[1] + jitterLng];
};

export const MapView: React.FC<MapViewProps> = ({
    listings = [],
    singleListing,
    className = '',
    height = '420px',
}) => {
    const mapContainerRef = useRef<HTMLDivElement>(null);
    const mapInstanceRef = useRef<L.Map | null>(null);

    useEffect(() => {
        if (!mapContainerRef.current) return;

        // Cleanup existing map instance if any
        if (mapInstanceRef.current) {
            mapInstanceRef.current.remove();
            mapInstanceRef.current = null;
        }

        const defaultCenter: [number, number] = singleListing
            ? getListingCoords(singleListing, 0)
            : UNNES_CAMPUS_COORDINATES;

        const defaultZoom = singleListing ? 16 : 15;

        const map = L.map(mapContainerRef.current, {
            center: defaultCenter,
            zoom: defaultZoom,
            scrollWheelZoom: false,
        });

        mapInstanceRef.current = map;

        // OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19,
        }).addTo(map);

        // 1. UNNES Primary Landmark Popup Content
        const unnesPopupContent = `
            <div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; width: 230px; padding: 4px;">
                <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 4px;">
                    <span style="font-size: 16px;">🎓</span>
                    <div style="font-weight: 800; font-size: 13px; color: #0f172a; line-height: 1.2;">
                        Universitas Negeri Semarang (UNNES)
                    </div>
                </div>
                <div style="font-size: 11px; color: #64748b; margin-bottom: 8px; line-height: 1.3;">
                    Gedung Rektorat • Sekaran, Gunungpati, Kota Semarang
                </div>
                <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 6px 8px; font-size: 10.5px; color: #0369a1; margin-bottom: 8px; line-height: 1.3;">
                    📍 Kawasan Kampus Utama UNNES Sekaran
                </div>
                <button id="btn-focus-unnes" type="button" style="
                    width: 100%;
                    background-color: #0284c7;
                    color: #ffffff;
                    border: none;
                    padding: 7px 10px;
                    border-radius: 6px;
                    font-size: 11px;
                    font-weight: 700;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 4px;
                ">
                    🔍 Lihat Kost di Sekitar UNNES
                </button>
            </div>
        `;

        // 2. Official Calibrated Polygon Boundary Highlight for UNNES Campus Area (Area-only highlight)
        const unnesBoundaryLayer = L.geoJSON(UNNES_CAMPUS_GEOJSON, {
            style: {
                color: '#0284c7', // Crisp boundary outline
                weight: 2.5,
                opacity: 0.9,
                fillColor: '#38bdf8', // Translucent blue area fill
                fillOpacity: 0.14,
                lineJoin: 'round',
                lineCap: 'round',
            },
            onEachFeature: (_feature, layer) => {
                layer.bindPopup(unnesPopupContent);
                layer.bindTooltip('Universitas Negeri Semarang (UNNES)', {
                    sticky: true,
                    direction: 'top',
                    opacity: 0.95,
                });
            },
        }).addTo(map);

        // Handle click on "Lihat Kost di Sekitar UNNES" inside popup
        map.on('popupopen', () => {
            const btn = document.getElementById('btn-focus-unnes');
            if (btn) {
                btn.onclick = () => {
                    map.fitBounds(unnesBoundaryLayer.getBounds(), { padding: [50, 50], maxZoom: 16 });
                };
            }
        });

        // 3. Helper for Individual Kos Price Pins
        const createPriceIcon = (price: string, isSingle = false) => {
            return L.divIcon({
                className: 'custom-map-pin',
                html: `
                    <div style="
                        background-color: ${isSingle ? '#1e293b' : '#FF6565'};
                        color: #ffffff;
                        font-weight: 700;
                        font-size: 11px;
                        padding: 4px 9px;
                        border-radius: 9999px;
                        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.22);
                        border: 2px solid #ffffff;
                        white-space: nowrap;
                        display: flex;
                        align-items: center;
                        gap: 4px;
                        cursor: pointer;
                        transform: translate(-50%, -50%);
                        transition: transform 0.15s ease, box-shadow 0.15s ease;
                        user-select: none;
                    ">
                        <span style="font-size: 11px;">🏠</span>
                        <span>${price}</span>
                    </div>
                `,
                iconSize: [0, 0],
                iconAnchor: [0, 0],
            });
        };

        // 4. Helper for Mamikos-style Circular Cluster Badges
        const createClusterIcon = (count: number) => {
            let size = 38;
            let fontSize = 13;
            let ringSpread = 4;
            let ringOpacity = 0.22;

            if (count >= 25) {
                size = 50;
                fontSize = 15;
                ringSpread = 6;
                ringOpacity = 0.32;
            } else if (count >= 10) {
                size = 44;
                fontSize = 14;
                ringSpread = 5;
                ringOpacity = 0.26;
            }

            return L.divIcon({
                className: 'mamikos-cluster-pin',
                html: `
                    <div style="
                        width: ${size}px;
                        height: ${size}px;
                        border-radius: 50%;
                        background: linear-gradient(135deg, #FF6565 0%, #E04848 100%);
                        color: #ffffff;
                        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                        font-weight: 800;
                        font-size: ${fontSize}px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        border: 2px solid #ffffff;
                        box-shadow: 0 4px 14px rgba(255, 101, 101, 0.45), 0 0 0 ${ringSpread}px rgba(255, 101, 101, ${ringOpacity});
                        transform: translate(-50%, -50%);
                        cursor: pointer;
                        transition: transform 0.18s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.18s ease;
                        user-select: none;
                    ">
                        <span>${count}</span>
                    </div>
                `,
                iconSize: [0, 0],
                iconAnchor: [0, 0],
            });
        };

        const createListingPopupContent = (listing: Listing) => {
            const imageUrl = listing.primary_image?.url
                || (listing.images && listing.images.length > 0 ? listing.images[0].url : '/images/logo.jpg');

            return `
                <div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; width: 210px; padding: 2px;">
                    <img src="${imageUrl}" alt="${listing.name}" style="width: 100%; height: 105px; object-fit: cover; border-radius: 8px; margin-bottom: 6px;" />
                    <div style="font-weight: 700; font-size: 12.5px; color: #0f172a; line-height: 1.3; margin-bottom: 2px;">
                        ${listing.name}
                    </div>
                    <div style="font-size: 11px; color: #64748b; margin-bottom: 6px;">
                        Area ${listing.area} • Kos ${listing.gender_type}
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid #f1f5f9; padding-top: 5px; margin-top: 4px;">
                        <span style="font-weight: 800; font-size: 12px; color: #FF6565;">
                            ${listing.formatted_starting_price}
                        </span>
                        <a href="/kos/${listing.slug}" style="font-weight: 700; font-size: 11px; color: #0284c7; text-decoration: none; padding: 2px 6px; border-radius: 4px; background-color: #f0f9ff;">
                            Detail →
                        </a>
                    </div>
                </div>
            `;
        };

        // If rendering a single listing on detail page
        if (singleListing) {
            const coords = getListingCoords(singleListing, 0);
            const marker = L.marker(coords, {
                icon: createPriceIcon(singleListing.formatted_starting_price || 'Lokasi Kos', true),
                zIndexOffset: 1000,
            }).addTo(map);

            const popupContent = `
                <div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; min-width: 180px; padding: 4px;">
                    <div style="font-weight: 700; font-size: 13px; color: #0f172a; margin-bottom: 2px;">
                        ${singleListing.name}
                    </div>
                    <div style="font-size: 11px; color: #64748b; margin-bottom: 6px;">
                        Area ${singleListing.area} • ${singleListing.address}
                    </div>
                    <div style="font-weight: 800; font-size: 12px; color: #FF6565;">
                        ${singleListing.formatted_starting_price} / bulan
                    </div>
                </div>
            `;
            marker.bindPopup(popupContent).openPopup();
            return () => {
                map.remove();
                mapInstanceRef.current = null;
            };
        }

        // Multiple listings mode: Supercluster setup
        if (listings.length === 0) {
            return () => {
                map.remove();
                mapInstanceRef.current = null;
            };
        }

        // Layer group for dynamic clusters & markers
        const markerLayerGroup = L.layerGroup().addTo(map);

        // Convert listings into GeoJSON Point Features
        const points: ListingFeature[] = listings.map((listing, idx) => {
            const [lat, lng] = getListingCoords(listing, idx);
            return {
                type: 'Feature',
                properties: {
                    cluster: false,
                    listing,
                    index: idx,
                },
                geometry: {
                    type: 'Point',
                    coordinates: [lng, lat], // GeoJSON standard: [lng, lat]
                },
            };
        });

        // Initialize Supercluster with pixel-based spatial clustering
        const clusterIndex = new Supercluster<ListingPointProperties>({
            radius: 60, // Cluster radius in pixels
            maxZoom: 17, // At zoom 17+ listings expand into individual markers
            minPoints: 2,
        });

        clusterIndex.load(points);

        // Function to re-compute and render visible clusters/markers on map viewport change
        const updateClusters = () => {
            if (!mapInstanceRef.current) return;
            markerLayerGroup.clearLayers();

            const bounds = map.getBounds();
            const sw = bounds.getSouthWest();
            const ne = bounds.getNorthEast();
            const currentZoom = Math.round(map.getZoom());

            // Get clusters for current bounding box & zoom
            const bbox: [number, number, number, number] = [sw.lng, sw.lat, ne.lng, ne.lat];
            const clusters = clusterIndex.getClusters(bbox, currentZoom);

            clusters.forEach((feature) => {
                const [lng, lat] = feature.geometry.coordinates;

                if (feature.properties.cluster) {
                    // It's a cluster feature
                    const clusterProps = feature.properties as Supercluster.ClusterProperties;
                    const count = clusterProps.point_count;
                    const clusterId = clusterProps.cluster_id;

                    // If we've reached max zoom (or cluster points have identical coords), spiderfy them
                    if (currentZoom >= 18) {
                        const leaves = clusterIndex.getLeaves(clusterId, Infinity);
                        const totalLeaves = leaves.length;
                        const spiderRadius = 0.00035; // ~35-40 meters offset

                        leaves.forEach((leaf, leafIdx) => {
                            const angle = (2 * Math.PI * leafIdx) / totalLeaves;
                            const offsetLat = lat + spiderRadius * Math.sin(angle);
                            const offsetLng = lng + (spiderRadius * Math.cos(angle)) / Math.cos((lat * Math.PI) / 180);

                            const leafListing = leaf.properties.listing;
                            const leafMarker = L.marker([offsetLat, offsetLng], {
                                icon: createPriceIcon(leafListing.formatted_starting_price || 'Lihat Kos'),
                                zIndexOffset: 800,
                            });
                            leafMarker.bindPopup(createListingPopupContent(leafListing));
                            markerLayerGroup.addLayer(leafMarker);
                        });
                        return;
                    }

                    // Standard Mamikos cluster badge
                    const marker = L.marker([lat, lng], {
                        icon: createClusterIcon(count),
                        zIndexOffset: 1000 + count,
                    });

                    marker.on('click', () => {
                        const expansionZoom = clusterIndex.getClusterExpansionZoom(clusterId);
                        if (expansionZoom > currentZoom) {
                            map.setView([lat, lng], expansionZoom, { animate: true });
                        } else {
                            // Expand with leaves bounding box
                            const leaves = clusterIndex.getLeaves(clusterId, Infinity);
                            if (leaves.length > 0) {
                                const leafBounds = L.latLngBounds(
                                    leaves.map((l) => [l.geometry.coordinates[1], l.geometry.coordinates[0]])
                                );
                                if (leafBounds.isValid() && !leafBounds.getNorthEast().equals(leafBounds.getSouthWest())) {
                                    map.fitBounds(leafBounds, { padding: [50, 50], maxZoom: 18 });
                                } else {
                                    map.setView([lat, lng], currentZoom + 1, { animate: true });
                                }
                            }
                        }
                    });

                    markerLayerGroup.addLayer(marker);
                } else {
                    // It's an individual listing marker
                    const listing = feature.properties.listing;
                    const marker = L.marker([lat, lng], {
                        icon: createPriceIcon(listing.formatted_starting_price || 'Lihat Kos'),
                        zIndexOffset: 500,
                    });

                    marker.bindPopup(createListingPopupContent(listing));
                    markerLayerGroup.addLayer(marker);
                }
            });
        };

        // Initial cluster render
        updateClusters();

        // Fit map bounds to encompass UNNES and all initial listings
        const initialBounds = L.latLngBounds([UNNES_CAMPUS_COORDINATES]);
        points.forEach((p) => {
            initialBounds.extend([p.geometry.coordinates[1], p.geometry.coordinates[0]]);
        });

        if (initialBounds.isValid()) {
            map.fitBounds(initialBounds, { padding: [40, 40], maxZoom: 15 });
        }

        // Attach viewport listeners for seamless supercluster updates
        map.on('moveend', updateClusters);
        map.on('zoomend', updateClusters);

        return () => {
            map.off('moveend', updateClusters);
            map.off('zoomend', updateClusters);
            if (mapInstanceRef.current) {
                mapInstanceRef.current.remove();
                mapInstanceRef.current = null;
            }
        };
    }, [listings, singleListing]);

    return (
        <div className={`relative overflow-hidden rounded-2xl border border-slate-200/80 shadow-xs z-0 ${className}`}>
            <div ref={mapContainerRef} style={{ width: '100%', height }} />
        </div>
    );
};

