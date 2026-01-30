# Moodle Image Map Module (mod_imagemap)

Moodle module to handle conditional image maps based on completion of other modules.

## Description

The Image Map module allows teachers to create interactive images with clickable areas that link to course modules, sections, or external URLs. Areas can be conditionally displayed based on completion of other modules, making it perfect for creating interactive course navigation maps, visual learning paths, and gamified course experiences.

## Features

- **Image Upload/Repository**: Upload images or use images from Moodle repository
- **Interactive Areas**: Add clickable areas in shapes:
  - Circles
  - Rectangles  
  - Polygons
- **Flexible Linking**: Link areas to:
  - Course modules
  - Course sections
  - External URLs
- **Visual Drawing Interface**: Draw or modify shapes directly on the image using an interactive canvas editor
- **Conditional Display**: Optionally link areas to module completion criteria
  - Areas become active/inactive based on completion
  - Apply CSS filters to show visual feedback (e.g., grayscale when inactive)
- **Bilingual Support**: English and Portuguese (Brazil) language strings

## Requirements

- Moodle 3.9 or later
- PHP 7.2 or later

## Installation

1. Copy the plugin directory to `{moodle}/mod/imagemap`
2. Visit the Site Administration page to complete the installation
3. The plugin should now be available in the activity chooser

## Usage

### Creating an Image Map

1. Turn editing on in your course
2. Add an activity → Image Map
3. Enter a name and description
4. Upload an image file
5. Save and display

### Adding Areas

1. Click "Manage areas" on the Image Map page
2. Select a shape type (rectangle, circle, or polygon)
3. Draw the shape on the image:
   - **Rectangle**: Click and drag to create
   - **Circle**: Click center point and drag to set radius
   - **Polygon**: Click to add points, then click "Finish"
4. Fill in the area details:
   - Title (shown on hover)
   - Link type (module, section, or URL)
   - Link target (module ID, section number, or URL)
   - Optional completion condition (another module)
   - CSS filters for active/inactive states
5. Save the area

### Managing Areas

- View all areas in a table
- Delete areas as needed
- Areas are numbered in creation order

## License

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

## Author

Copyright © 2026 Kelson C. M.

## Support

For issues, feature requests, or contributions, please use the GitHub repository.
