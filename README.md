# Moodle Image Map Module (mod_imagemap)

Interactive image maps with clickable areas for course navigation and conditional display based on module completion.

**Version:** 1.0.1 (2026-01-30)  
**Moodle:** 4.1+  
**Status:** Alpha Release

---

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Documentation](#documentation)
- [Changelog](#changelog)
- [License](#license)

---

## Features

### Core Functionality

✅ **Interactive Canvas Editor** - Draw shapes directly on images  
✅ **Multiple Shape Types** - Rectangles, circles, polygons  
✅ **Flexible Linking** - Link to modules, sections, or external URLs  
✅ **Image Management** - Upload images or use Moodle repository  
✅ **Visual Feedback** - See shapes in real-time editor  

### Advanced Features

✅ **Conditional Display** - Show/hide areas based on module completion  
✅ **Custom Styling** - Full CSS support for active/inactive states  
✅ **CSS Validation** - Real-time validation with live preview  
✅ **Responsive Design** - Works on desktop and tablets  
✅ **Internationalization** - English and Portuguese (Brazil)  

### Developer Features

✅ **GDPR Compliance** - Privacy API implementation  
✅ **Event Logging** - Course module view events  
✅ **Proper Capabilities** - Granular permission system  
✅ **Database Schema** - Clean, normalized database design  

---

## Requirements

### Server

- **Moodle:** 4.1 or later (tested on 4.5.8)
- **PHP:** 7.2 or later (7.4+ recommended)
- **Database:** PostgreSQL, MySQL, or compatible

### Browser

- Chrome/Chromium (recommended)
- Firefox 60+
- Safari 12+
- Edge 79+

---

## Installation

### Method 1: Download & Extract

```bash
# Navigate to moodle plugins directory
cd /path/to/moodle/mod

# Clone or download the plugin
git clone https://github.com/your-repo/mod_imagemap.git imagemap

# Or extract zip file
unzip mod_imagemap.zip
```

### Method 2: Via Moodle UI

1. Site Admin → Plugins → Install plugins
2. Upload ZIP file
3. Follow prompts to complete installation

### Post-Installation

1. **Database Upgrade**
   - Site Admin → Notifications
   - Click "Upgrade Moodle database now"
   - This updates schema for CSS text fields

2. **Verify Installation**
   - Create a test course
   - Try adding an Image Map activity
   - Check plugin is listed in plugins directory

---

## Quick Start

### For Teachers

1. **Create Activity**
   ```
   Turn Editing On → Add Activity → Image Map
   ```

2. **Upload Image**
   ```
   Click File selector → Upload/Choose image
   ```

3. **Draw Areas**
   ```
   Click "Edit areas" → Select shape type → Click & drag on image
   ```

4. **Configure Links**
   ```
   Right-click shape → Select link type (Module/Section/URL)
   ```

5. **Add Styling (Optional)**
   ```
   Right-click shape → Enter CSS for active/inactive states
   ```

6. **Save & Display**
   ```
   Click "Save changes" → View to students
   ```

### For Students

1. **View Course Image Map**
   - Click Image Map in course
   - Click active areas to navigate
   - See conditional areas based on progress

---

## Documentation

### For Teachers & Instructors

📖 **[USER_GUIDE.md](USER_GUIDE.md)**
- Complete user guide with examples
- Step-by-step instructions
- CSS styling examples
- Troubleshooting

### For Developers

📖 **[IMPLEMENTATION.md](IMPLEMENTATION.md)**
- Technical architecture
- Database schema
- Plugin API details
- Development guidelines

📖 **[AGENTS.md](AGENTS.md)**
- AI agent guidelines
- Code structure overview
- Common tasks

### For System Administrators

📖 **[UPGRADE_INSTRUCTIONS.md](UPGRADE_INSTRUCTIONS.md)**
- Database migration steps
- Version upgrade process
- Rollback instructions

📖 **[CSS_TESTING.md](CSS_TESTING.md)**
- CSS validation guide
- Test cases
- Debugging tips

### Release Info

📖 **[CHANGELOG.md](CHANGELOG.md)**
- Version history
- Feature additions
- Bug fixes and improvements

---

## Key Files

| File | Purpose |
|------|---------|
| `view.php` | Student view - canvas with CSS overlays |
| `areas.php` | Teacher editor - interactive canvas editor |
| `area_save.php` | AJAX handler for saving areas |
| `lib.php` | Moodle hook implementations |
| `mod_form.php` | Activity settings form |
| `editor.js` | Canvas editor JavaScript logic |
| `editor.css` | Editor interface styles |
| `db/install.xml` | Database schema |
| `db/upgrade.php` | Database migrations |
| `lang/` | Language files (EN, PT-BR) |

---

## Configuration

### Global Settings

Currently, Image Map uses default Moodle module settings. No custom admin settings.

### Course-Level Settings

When creating an Image Map activity:
- **Name** - Activity name (required)
- **Description** - Activity intro text
- **Image** - Upload or select from repository (required)

### Area-Level Settings

Per clickable area:
- **Shape** - Rectangle, circle, or polygon
- **Link Type** - Module, section, or URL
- **Link Target** - Specific module/section/URL
- **Condition** - Optional completion condition
- **CSS Active** - Styling for active state
- **CSS Inactive** - Styling for inactive state

---

## Permissions

### Required Capabilities

| Capability | Role | Purpose |
|-----------|------|---------|
| `mod/imagemap:addinstance` | Teacher | Create new instances |
| `mod/imagemap:edit` | Teacher | Edit areas and settings |
| `mod/imagemap:view` | Student | View the activity |

### Auto-Assigned To

- **Addinstance:** Teacher, Course Creator
- **Edit:** Teacher, Course Creator
- **View:** Authenticated Users

---

## Database

### Tables

#### `imagemap`
Main activity instances
- Fields: id, course, name, intro, introformat, image, completionmodule, filteractive, filterinactive, timecreated, timemodified

#### `imagemap_area`
Individual clickable areas
- Fields: id, imagemap, shape, coords, title, link, linktype, conditioncmid, activefilter, inactivefilter, timecreated, timemodified

---

## CSS Support

### Supported Properties

- **Filters:** `brightness()`, `contrast()`, `grayscale()`, `opacity()`, etc.
- **Borders:** `border`, `border-radius`
- **Backgrounds:** `background`, `linear-gradient()`, `repeating-linear-gradient()`
- **Shadows:** `box-shadow`, `text-shadow`
- **Transforms:** `transform` (some browsers)
- **Effects:** `filter`, `mix-blend-mode`

### Limitations

- No animations (static CSS only)
- Limited to visible properties (not layout)
- Some advanced properties may vary by browser

### Examples

```css
/* Filter-based */
filter: brightness(1.2) opacity(0.9);

/* Full CSS */
border: 3px solid gold;
background: rgba(255, 215, 0, 0.2);
box-shadow: 0 0 20px rgba(255, 215, 0, 0.8);

/* Gradient */
background: linear-gradient(135deg, rgba(255,0,0,0.3), rgba(0,0,255,0.3));
```

See [USER_GUIDE.md](USER_GUIDE.md) for more examples.

---

## Browser Support

| Browser | Supported | Notes |
|---------|-----------|-------|
| Chrome/Chromium | ✅ Yes | Full support, recommended |
| Firefox | ✅ Yes | Full support |
| Safari | ✅ Yes | iOS 12+, macOS 12+ |
| Edge | ✅ Yes | Chromium-based (79+) |
| IE 11 | ❌ No | Canvas API limited |

---

## Known Issues

### Current Release (1.0.1)

- AMD module loading errors on some Moodle themes (not plugin fault)
- Very large images (>5MB) may load slowly
- Polygon resize can be imprecise with many vertices

### Workarounds

1. **Large images:** Use image compression tools before upload
2. **Polygon precision:** Reduce number of vertices or use rectangles/circles
3. **Browser issues:** Use Chrome/Firefox for best experience

---

## Performance

### Recommendations

- **Image size:** Keep under 2MB for optimal loading
- **Number of areas:** 10-50 per image recommended
- **CSS complexity:** Keep CSS simple and avoid nested selectors
- **Browser cache:** Clear periodically for updates

### Optimization

- Enable Moodle caching (Site Admin → Performance)
- Compress images before upload
- Use CDN for image delivery (optional)

---

## Accessibility

### Supported

- ✅ Keyboard navigation (Tab, Enter, Arrow keys)
- ✅ ARIA labels on interactive elements
- ✅ High contrast theme support
- ✅ Screen reader friendly HTML

### Limitations

- Canvas editor not fully accessible (visual tool)
- Students: Areas are clickable links (accessible)

### Improvements Planned

- Enhanced keyboard-only editing (future release)
- More ARIA descriptions
- VoiceOver/NVDA optimization

---

## Privacy

### GDPR Compliance

The plugin implements Moodle Privacy API:
- Data export: Includes image and area information
- Data deletion: Removes user-related data on request
- No cookies or tracking

**Privacy Provider:** `classes/privacy/provider.php`

---

## Support & Contribution

### Getting Help

1. **Documentation:** Check [USER_GUIDE.md](USER_GUIDE.md)
2. **Issues:** See [CHANGELOG.md](CHANGELOG.md) for known issues
3. **Troubleshooting:** See [CSS_TESTING.md](CSS_TESTING.md)

### Reporting Bugs

Include:
- Moodle version
- PHP version
- Browser/OS
- Steps to reproduce
- Screenshots (if applicable)

### Contributing

To contribute:
1. Fork repository
2. Create feature branch
3. Make changes with clear commits
4. Submit pull request
5. Wait for review

---

## License

This plugin is released under the [GNU General Public License v3](LICENSE)

Permissions: Commercial use, modification, distribution, private use  
Conditions: License notice, state changes  
Limitations: Liability, warranty

---

## Credits

**Maintainer:** Kelson C. M.  
**Contributors:** See CHANGELOG.md  
**Based on:** Moodle Plugin Architecture 4.x

---

## Changelog

### 1.0.1 (2026-01-30)
- ✨ Full CSS support (not just filters)
- 📚 Added comprehensive user guide
- 🐛 Fixed version number formatting
- 🗄️ Database schema upgrade to TEXT fields

### 1.0.0 (2026-01-29)
- 🎉 Initial public release
- ✅ All core features implemented

See [CHANGELOG.md](CHANGELOG.md) for full history.

---

**Status:** Active Development  
**Last Updated:** January 30, 2026
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
