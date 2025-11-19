// Trip tracking functionality
class TripTracker {
    constructor(tripId, mapElementId) {
        this.tripId = tripId;
        this.mapElementId = mapElementId;
        this.map = null;
        this.currentLocationMarker = null;
        this.routePolyline = null;
        this.updateInterval = null;
    }

    // Initialize the map
    initMap(origin, destination) {
        // Create the map centered on the origin
        this.map = new google.maps.Map(document.getElementById(this.mapElementId), {
            zoom: 12,
            center: origin,
        });

        // Create directions service
        const directionsService = new google.maps.DirectionsService();
        const directionsRenderer = new google.maps.DirectionsRenderer({
            map: this.map,
            suppressMarkers: true,
            polylineOptions: {
                strokeColor: '#3b82f6', // blue-500
                strokeWeight: 5
            }
        });

        // Calculate and display route
        this.calculateAndDisplayRoute(directionsService, directionsRenderer, origin, destination);

        // Add markers for origin and destination
        new google.maps.Marker({
            position: origin,
            map: this.map,
            title: "Origen",
            icon: {
                url: "http://maps.google.com/mapfiles/ms/icons/green-dot.png"
            }
        });

        new google.maps.Marker({
            position: destination,
            map: this.map,
            title: "Destino",
            icon: {
                url: "http://maps.google.com/mapfiles/ms/icons/red-dot.png"
            }
        });
    }

    // Calculate and display route
    calculateAndDisplayRoute(directionsService, directionsRenderer, origin, destination) {
        directionsService.route({
            origin: origin,
            destination: destination,
            travelMode: google.maps.TravelMode.DRIVING,
        }).then((response) => {
            directionsRenderer.setDirections(response);
        }).catch((e) => {
            console.error("Directions request failed due to " + e);
        });
    }

    // Get current location from server
    getCurrentLocation() {
        fetch(`/trips/${this.tripId}/current-location`)
            .then(response => response.json())
            .then(data => {
                const position = {
                    lat: parseFloat(data.latitude),
                    lng: parseFloat(data.longitude)
                };
                
                this.showCurrentLocation(position, data.progress);
                this.updateLocationInfo(data);
            })
            .catch(error => {
                console.error("Error fetching current location:", error);
            });
    }

    // Show current location on map
    showCurrentLocation(position, progress) {
        // Create or update the current location marker
        if (this.currentLocationMarker) {
            this.currentLocationMarker.setPosition(position);
        } else {
            this.currentLocationMarker = new google.maps.Marker({
                position: position,
                map: this.map,
                title: "Ubicación actual del vehículo",
                icon: {
                    url: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
                },
                animation: google.maps.Animation.DROP
            });
        }

        // Center map on current location
        this.map.panTo(position);
    }

    // Update location information display
    updateLocationInfo(data) {
        document.getElementById('currentLat').textContent = parseFloat(data.latitude).toFixed(6);
        document.getElementById('currentLng').textContent = parseFloat(data.longitude).toFixed(6);
        document.getElementById('lastUpdate').textContent = new Date(data.timestamp).toLocaleString();
        document.getElementById('progress').textContent = (parseFloat(data.progress) * 100).toFixed(1) + '%';
        document.getElementById('locationInfo').classList.remove('hidden');
    }

    // Start automatic updates
    startTracking(interval = 30000) { // Default: update every 30 seconds
        this.updateInterval = setInterval(() => {
            this.getCurrentLocation();
        }, interval);
    }

    // Stop automatic updates
    stopTracking() {
        if (this.updateInterval) {
            clearInterval(this.updateInterval);
            this.updateInterval = null;
        }
    }
}

// Export for use in other files
window.TripTracker = TripTracker;