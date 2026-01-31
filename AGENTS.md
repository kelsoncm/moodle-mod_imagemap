# Image Map Module (mod_imagemap) - Agent Guidelines

## Overview

This document provides comprehensive guidelines for AI agents working on the **mod_imagemap** module - a Moodle activity plugin that enables interactive image maps with conditional display based on module completion.

### Quick Facts

- **Module Type**: Activity Module (mod_imagemap)
- **Version**: 1.0.0
- **Moodle Compatibility**: 4.1+ (requires version 2023_04_10_00+)
- **PHP Requirement**: 7.2+
- **Language Support**: English, Portuguese (Brazil)
- **Maturity Level**: Alpha

---

## Project Structure

### Core Files

| File | Purpose | Lines | Key Functions |
|------|---------|-------|----------------|
| `version.php` | Plugin metadata & version info | ~35 | Version constants |
| `lib.php` | Module API & core functionality | ~210 | `imagemap_supports()`, `imagemap_add_instance()`, `imagemap_update_instance()`, `imagemap_delete_instance()` |
| `mod_form.php` | Activity settings form | - | Form definition with image upload |
| `view.php` | Main display page | - | Renders HTML image map with clickable areas |
| `index.php` | Instance listing | - | Lists all imagemap activities in course |

### Area Management

| File | Purpose |
|------|---------|
| `areas.php` | Interactive canvas editor for drawing/editing shapes (362 lines) |
| `area_save.php` | AJAX handler for saving area definitions |

### Database

| File | Purpose |
|------|---------|
| `db/install.xml` | Schema: `imagemap` and `imagemap_area` tables |
| `db/upgrade.php` | Database migration scripts |
| `db/access.php` | Capability definitions (permissions) |

### Supporting Components

| Directory | Purpose |
|-----------|---------|
| `classes/event/` | Event logging classes for Moodle event system |
| `classes/privacy/` | GDPR/privacy compliance provider |
| `lang/` | Language string translations |
| `pix/` | Icons and images |
| `styles.css` | UI styling |

---

## Database Schema

### Table: `imagemap`

Stores main imagemap activity instances.

**Key Fields:**
- `id` - Primary key
- `course` - Course ID
- `name` - Activity name
- `intro` - Description/intro text
- `introformat` - Text format (HTML, Markdown, etc.)
- `image` - Stored file reference (file area: 'content')
- `completionmodule` - Reference module for completion-based display
- `filteractive` - CSS filter for active areas
- `filterinactive` - CSS filter for inactive areas
- `timecreated` - Creation timestamp
- `timemodified` - Last modification timestamp
- `usermodified` - User ID who last modified

### Table: `imagemap_area`

Stores individual clickable areas within an imagemap.

**Key Fields:**
- `id` - Primary key
- `imagemap` - Foreign key to imagemap table
- `shape` - Shape type: 'circle', 'rectangle', 'polygon'
- `coords` - Coordinates JSON (e.g., `{"x1": 10, "y1": 20, ...}`)
- `title` - Area label/title
- `link` - Link destination (module, section, or URL)
- `linktype` - Type of link: 'module', 'section', 'url'
- `timecreated` - Creation timestamp
- `timemodified` - Last modification timestamp

---

## Core Features & Implementation

### 1. Image Management

**Component**: `mod_form.php` + File API

- Teachers upload images via standard Moodle file picker
- Supports Moodle repository integration
- File stored in 'content' file area
- Image dimensions preserved for accurate coordinate mapping

### 2. Shape Types

Three shape definitions are supported:

#### Rectangle
```json
{"x1": 10, "y1": 20, "x2": 100, "y2": 150}
```

#### Circle
```json
{"cx": 50, "cy": 50, "r": 30}
```

#### Polygon
```json
{"points": [[10, 20], [50, 10], [100, 30], [80, 80]]}
```

### 3. Link Types

**Module Link**
- Links to specific course module
- Stored as module ID
- Conditional: Can be set as completion trigger

**Section Link**
- Links to course section
- Stored as section ID

**External URL**
- Direct links to external websites
- Stored as full URL

### 4. Conditional Display

**Mechanism**:
1. Teacher assigns a "completion module" to the imagemap
2. System tracks if current user has completed that module
3. Areas appear:
   - **Active** (clear): If user completed the module
   - **Inactive** (filtered): If not completed yet

**CSS Filters**:
- `filteractive` - Default: `none` (normal display)
- `filterinactive` - Default: `grayscale(100%)` (grayed out)

---

## Key Functions & APIs

### Activity Module Functions

**`imagemap_supports($feature)`**
- Checks if module supports specific Moodle feature
- Returns boolean

**`imagemap_add_instance($data, $mform = null)`**
- Creates new imagemap instance
- Returns instance ID

**`imagemap_update_instance($data, $mform = null)`**
- Updates existing imagemap
- Handles file updates and database changes

**`imagemap_delete_instance($id)`**
- Removes imagemap and related data
- Cascades to areas table

### Event Classes

Located in `classes/event/`:
- Event logging for auditing
- Moodle event system integration

### Privacy Provider

Located in `classes/privacy/`:
- GDPR data export/deletion support
- Privacy API implementation

---

## Development Workflow

### Adding Features

1. **Database Changes**
   - Update `db/install.xml` if schema needs modification
   - Create upgrade script in `db/upgrade.php`

2. **Language Strings**
   - Add to `lang/en/imagemap.php` (English)
   - Add to `lang/pt_br/imagemap.php` (Portuguese Brazil)
   - Use format: `$string['key'] = 'Translation';`

3. **Capabilities**
   - Define in `db/access.php`
   - Use naming convention: `mod/imagemap:capability`

4. **File Structure**
   - Follow Moodle coding standards
   - PHP files: PSR-2 style
   - Copyright header required

### Testing Checklist

- [ ] Create imagemap instance
- [ ] Upload and save image
- [ ] Draw areas (all shape types)
- [ ] Test area linking (module, section, URL)
- [ ] Test completion-based display
- [ ] Verify visual filters applied
- [ ] Test deletion (cascade to areas)
- [ ] Verify privacy exports
- [ ] Check event logging

---

## Important Locations & Patterns

### File Upload/Retrieval
```php
// Stored in 'content' file area
$context = context_module::instance($cm->id);
$fs = get_file_storage();
$files = $fs->get_area_files($context->id, 'mod_imagemap', 'content');
```

### Database Queries
```php
// Always use global $DB with prepared statements
global $DB;
$record = $DB->get_record('imagemap', ['id' => $id]);
$areas = $DB->get_records('imagemap_area', ['imagemap' => $id]);
```

### Context & Permissions
```php
$context = context_module::instance($cm->id);
require_capability('mod/imagemap:edit', $context);
```

---

## Common Tasks

### Task: Add a New Area Field

1. Add field to `db/install.xml` (imagemap_area table)
2. Create upgrade script in `db/upgrade.php`
3. Update `area_save.php` to handle new field
4. Update `areas.php` to display/edit field
5. Add language strings if needed
6. Test migration on fresh install and upgrade

### Task: Add New Shape Type

1. Define coordinate structure (document above)
2. Update `areas.php` canvas drawing logic
3. Update `view.php` rendering logic
4. Add language string for shape name
5. Update area validation
6. Add test case

### Task: Modify Completion Logic

1. Edit `lib.php` completion check logic
2. Update `view.php` filter application
3. Test with different completion states
4. Verify event triggering

---

## Debugging Tips

### Enable Debug Output
```php
// In any file
define('DEBUG_DEVELOPER', true);
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Database Logging
```php
$DB->set_debug(true); // Log all queries
```

### AJAX Debugging (areas.php)
- Check browser console (F12 → Console)
- Check Moodle logs: Site Administration → Logs
- Verify POST data in Network tab

### File System Issues
```php
// Check file existence
$context = context_module::instance($cm->id);
$fs = get_file_storage();
$file = $fs->get_file($context->id, 'mod_imagemap', 'content', 0, '/', 'image.png');
if (!$file) {
    throw new moodle_exception('File not found');
}
```

---

## References & Standards

- **Moodle Plugin API**: https://docs.moodle.org/dev/Plugin_types
- **File API**: https://docs.moodle.org/dev/File_API
- **Database API**: https://docs.moodle.org/dev/Database_API
- **Coding Standards**: https://docs.moodle.org/dev/Coding_style
- **GDPR Compliance**: https://docs.moodle.org/dev/Privacy_API

---

## Contact & Contribution

- **Maintainer**: Kelson C. M.
- **License**: GNU GPL v3 or later
- **Repository**: See CHANGELOG.md and CONTRIBUTING.md

---

**Last Updated**: January 2026  
**Status**: Alpha Release (1.0.0)
