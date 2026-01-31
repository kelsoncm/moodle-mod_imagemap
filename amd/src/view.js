define([], function () {
    return {
        init: function (imagemapId, areas, imageSrc) {
            var canvas = document.getElementById('imagemap-canvas-' + imagemapId);
            var overlaysContainer = document.getElementById('imagemap-overlays-' + imagemapId);
            if (!canvas || !overlaysContainer) return;

            var ctx = canvas.getContext('2d');
            var img = new Image();

            img.onload = function () {
                canvas.width = img.width;
                canvas.height = img.height;
                overlaysContainer.style.width = img.width + 'px';
                overlaysContainer.style.height = img.height + 'px';
                drawImageMap();
            };

            img.src = imageSrc;

            function drawImageMap() {
                // Draw base image
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, 0, 0);

                // Clear existing overlays
                overlaysContainer.innerHTML = '';

                // Create CSS overlays for each area
                areas.forEach(function (area, index) {
                    var coords = area.coords.split(',').map(function (v) { return parseFloat(v); });
                    var overlay = document.createElement('div');
                    overlay.className = 'imagemap-area-overlay';
                    overlay.style.position = 'absolute';
                    overlay.style.pointerEvents = area.active && area.url ? 'auto' : 'none';
                    overlay.style.cursor = area.active && area.url ? 'pointer' : 'not-allowed';
                    overlay.title = area.title || '';
                    overlay.dataset.areaIndex = index;

                    // Apply custom CSS
                    var cssText = area.active ? (area.activefilter || '') : (area.inactivefilter || 'filter: grayscale(1) opacity(0.5);');
                    if (cssText && cssText !== 'none') {
                        // Check if it's a filter or full CSS
                        if (cssText.indexOf(':') === -1 && cssText.indexOf('(') !== -1) {
                            // It's a filter value without property name
                            overlay.style.filter = cssText;
                        } else {
                            // It's full CSS
                            cssText.split(';').forEach(function (rule) {
                                if (!rule.trim()) return;
                                var parts = rule.split(':');
                                if (parts.length === 2) {
                                    var prop = parts[0].trim();
                                    var value = parts[1].trim();
                                    overlay.style.setProperty(prop, value);
                                }
                            });
                        }
                    }

                    // Position and clip the overlay based on shape
                    if (area.shape === 'rect' && coords.length >= 4) {
                        var x1 = Math.min(coords[0], coords[2]);
                        var y1 = Math.min(coords[1], coords[3]);
                        var w = Math.abs(coords[2] - coords[0]);
                        var h = Math.abs(coords[3] - coords[1]);
                        overlay.style.left = x1 + 'px';
                        overlay.style.top = y1 + 'px';
                        overlay.style.width = w + 'px';
                        overlay.style.height = h + 'px';
                    } else if (area.shape === 'circle' && coords.length >= 3) {
                        var cx = coords[0], cy = coords[1], r = coords[2];
                        overlay.style.left = (cx - r) + 'px';
                        overlay.style.top = (cy - r) + 'px';
                        overlay.style.width = (r * 2) + 'px';
                        overlay.style.height = (r * 2) + 'px';
                        overlay.style.borderRadius = '50%';
                    } else if (area.shape === 'poly' && coords.length >= 6) {
                        var minX = Math.min.apply(null, coords.filter(function (v, i) { return i % 2 === 0; }));
                        var maxX = Math.max.apply(null, coords.filter(function (v, i) { return i % 2 === 0; }));
                        var minY = Math.min.apply(null, coords.filter(function (v, i) { return i % 2 === 1; }));
                        var maxY = Math.max.apply(null, coords.filter(function (v, i) { return i % 2 === 1; }));

                        overlay.style.left = minX + 'px';
                        overlay.style.top = minY + 'px';
                        overlay.style.width = (maxX - minX) + 'px';
                        overlay.style.height = (maxY - minY) + 'px';

                        // Create polygon clip path
                        var clipPath = 'polygon(';
                        for (var i = 0; i < coords.length; i += 2) {
                            if (i > 0) clipPath += ', ';
                            clipPath += ((coords[i] - minX) / (maxX - minX) * 100) + '% ';
                            clipPath += ((coords[i + 1] - minY) / (maxY - minY) * 100) + '%';
                        }
                        clipPath += ')';
                        overlay.style.clipPath = clipPath;
                        overlay.style.webkitClipPath = clipPath;
                    }

                    // Create inner element for background if CSS includes background
                    var inner = document.createElement('div');
                    inner.style.width = '100%';
                    inner.style.height = '100%';
                    inner.style.pointerEvents = 'none';
                    overlay.appendChild(inner);

                    // Add click handler
                    if (area.active && area.url) {
                        overlay.addEventListener('click', function () {
                            window.location.href = area.url;
                        });
                    }

                    overlaysContainer.appendChild(overlay);
                });
            }
        }
    };
});