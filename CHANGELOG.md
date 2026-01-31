# Changelog

All notable changes to the Image Map module will be documented in this file.

## [1.0.0-alpha] - 2026-01-30

### Added
- Initial release of the Image Map module for Moodle
- Image upload with file manager integration
- Interactive canvas-based area editor
- Support for three shape types:
  - Rectangles (click and drag)
  - Circles (click center and drag radius)
  - Polygons (click points and finish)
- Flexible linking options:
  - Link to course modules
  - Link to course sections
  - Link to external URLs
- Conditional display based on module completion
  - Areas can be active or inactive based on completion criteria
  - CSS filters for visual feedback (e.g., grayscale when inactive)
- Area management interface:
  - Create areas by drawing on image
  - Delete areas
  - View all areas in a list
- Full bilingual support:
  - English (en)
  - Portuguese Brazil (pt_br)
- GDPR compliance with privacy provider
- Proper capability definitions for security
- Event logging for course module views
- Database schema with two tables:
  - imagemap (main instances)
  - imagemap_area (clickable areas)

### Features
- Teacher can upload or select image from repository
- Teacher can draw clickable areas directly on the image
- Teacher can configure each area with:
  - Shape (rectangle, circle, polygon)
  - Title (tooltip)
  - Destination (module, section, URL)
  - Completion condition (optional)
  - Active/inactive visual filters
- Students can view interactive image maps
- Clickable areas navigate to configured destinations
- Areas can be conditionally shown based on module completion
- Visual feedback through CSS filters for active/inactive states

### Technical Details
- Compatible with Moodle 3.9+
- PHP 7.2+
- Uses Moodle File API for image storage
- Uses HTML5 Canvas for drawing interface
- Uses HTML image maps for clickable areas
- Follows Moodle coding standards
- Includes proper event classes for logging
- GDPR compliant (no personal data stored)

### Security
- Capability-based access control
- Session key validation for form submissions
- Proper context checks for file access
- XSS risk assessment for capabilities

## [Unreleased]

### Planned Features
- Area editing (modify existing areas)
- Sortable areas (change z-index)
- Preview mode for testing
- Backup and restore support
- More CSS filter presets
- Accessibility improvements (keyboard navigation)
- Analytics integration
- Bulk area operations
