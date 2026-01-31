# Image Map - Administrator Guide

Installation, configuration, and maintenance guide for Moodle administrators.

---

## Table of Contents

1. [Installation](#installation)
2. [Database Upgrade](#database-upgrade)
3. [Configuration](#configuration)
4. [Permissions & Roles](#permissions--roles)
5. [Troubleshooting](#troubleshooting)
6. [Maintenance](#maintenance)
7. [Security](#security)

---

## Installation

### Prerequisites

Before installing, verify:

```bash
# Check Moodle version
php -r "require 'version.php'; echo \$release;"

# Check PHP version (7.2+)
php -v

# Check database connection
mysql -u user -p -e "SELECT VERSION();"
```

### Installation Steps

#### 1. Download Plugin

**Option A: Git Clone**
```bash
cd /path/to/moodle/mod
git clone https://github.com/your-repo/mod_imagemap.git imagemap
cd imagemap
git checkout main
```

**Option B: Manual Download**
```bash
cd /path/to/moodle/mod
unzip mod_imagemap.zip
mv mod_imagemap-main imagemap
```

**Option C: Moodle UI**
- Site Admin → Plugins → Install plugins
- Upload ZIP file
- Follow prompts

#### 2. File Permissions

Set correct ownership and permissions:

```bash
# Set owner to web server user
chown -R www-data:www-data /path/to/moodle/mod/imagemap

# Set permissions
chmod -R 755 /path/to/moodle/mod/imagemap
chmod -R 644 /path/to/moodle/mod/imagemap/*.*
```

#### 3. Database Upgrade

**Critical Step:** The plugin requires database schema updates.

```bash
# Via web interface (recommended)
1. Log in as admin
2. Site Admin → Notifications
3. Click "Upgrade Moodle database now"
4. Wait for completion

# Via CLI
php admin/cli/upgrade.php
```

**What upgrades:**
- Creates `imagemap` table
- Creates `imagemap_area` table
- Changes `activefilter` field to TEXT (from VARCHAR 50)
- Changes `inactivefilter` field to TEXT (from VARCHAR 50)

#### 4. Verify Installation

```bash
# Check files exist
ls -la /path/to/moodle/mod/imagemap/

# Check tables created
mysql -u user -p moodle -e "SHOW TABLES LIKE 'mdl_imagemap%';"

# Should output:
# mdl_imagemap
# mdl_imagemap_area
```

#### 5. Test Activity

Create test course:
```
1. Create course
2. Add Image Map activity
3. Upload test image
4. Draw test area
5. Verify saving works
```

---

## Database Upgrade

### Version History

| Version | Date | Changes |
|---------|------|---------|
| 2026013001 | 2026-01-30 | CSS text fields, full CSS support |
| 2026012900 | 2026-01-29 | Initial schema |

### Automatic Upgrade

Moodle handles automatically on:
- `php admin/cli/upgrade.php`
- Web interface Notifications page
- Manual plugin installation

### Manual Migration (if needed)

```sql
-- Backup current data
CREATE TABLE imagemap_backup AS SELECT * FROM mdl_imagemap;
CREATE TABLE imagemap_area_backup AS SELECT * FROM mdl_imagemap_area;

-- Update schema
ALTER TABLE mdl_imagemap 
  MODIFY activefilter TEXT DEFAULT 'none',
  MODIFY inactivefilter TEXT DEFAULT 'grayscale(1) opacity(0.5)';

ALTER TABLE mdl_imagemap_area
  MODIFY activefilter TEXT DEFAULT 'none',
  MODIFY inactivefilter TEXT DEFAULT 'grayscale(1) opacity(0.5)';
```

### Rollback (if needed)

```sql
-- Restore from backup
DROP TABLE mdl_imagemap;
DROP TABLE mdl_imagemap_area;

RENAME TABLE imagemap_backup TO mdl_imagemap;
RENAME TABLE imagemap_area_backup TO mdl_imagemap_area;

-- Downgrade version
DELETE FROM mdl_config_plugins 
  WHERE plugin='mod_imagemap' AND name='version';
```

---

## Configuration

### Default Settings

No custom admin configuration needed. Uses Moodle defaults.

### Optional: Customize Via Database

```sql
-- Adjust max file upload size (in module context)
UPDATE mdl_config SET value = '10485760' 
  WHERE name = 'maxbytes' AND plugin = 'mod_imagemap';

-- Change default inactive filter
UPDATE mdl_config SET value = 'brightness(0.5)' 
  WHERE name = 'inactivefilter_default';
```

### Course-Level Settings

Teachers set per activity:
- Activity name
- Description
- Image file
- Area definitions (shapes, links, CSS)
- Conditional completion

### Global File Size Limits

Control via Moodle file settings:
```
Site Admin → Server → File settings
→ Max upload file size (affects all plugins)
→ Max zip file size
→ Zip on backup threshold
```

---

## Permissions & Roles

### Plugin Capabilities

The plugin defines three capabilities:

```php
// mod/imagemap:addinstance
// Allows user to create new Image Map instances
// Default: Teacher, Course Creator

// mod/imagemap:edit
// Allows user to edit areas and settings
// Default: Teacher, Course Creator

// mod/imagemap:view
// Allows user to view Image Map activities
// Default: Authenticated Users
```

### Role Assignment

#### Standard Roles

| Role | Can Add | Can Edit | Can View |
|------|---------|----------|----------|
| Admin | ✅ | ✅ | ✅ |
| Teacher | ✅ | ✅ | ✅ |
| Student | ❌ | ❌ | ✅ |
| Guest | ❌ | ❌ | ❌ |

#### Custom Roles

To create custom role:
1. Site Admin → Users → Roles → Define roles
2. Click "Create new role"
3. Assign capabilities:
   - `mod/imagemap:addinstance`
   - `mod/imagemap:edit`
   - `mod/imagemap:view`

---

## Troubleshooting

### Installation Issues

#### "Plugin not found"

```
Problem: Plugin directory not in right location
Solution:
  1. Verify path: /path/to/moodle/mod/imagemap/
  2. Check version.php exists
  3. Check file permissions (755)
  4. Clear Moodle cache
```

#### "Database error during upgrade"

```
Problem: Schema update failed
Solution:
  1. Check database backups exist
  2. Review error log: /var/log/moodle.log
  3. Manual upgrade via SQL (see section above)
  4. Contact database administrator
```

#### "File permissions denied"

```
Problem: Cannot write to plugin directory
Solution:
  1. Fix ownership: chown -R www-data:www-data /path/
  2. Fix permissions: chmod -R 755 /path/
  3. SELinux: restorecon -r /path/
  4. AppArmor: Check /var/log/audit/audit.log
```

### User Issues

#### "Image Map not appearing in activity chooser"

```
Solution:
  1. Site Admin → Plugins → Activities
  2. Look for "Image Map"
  3. If hidden, click eye icon to show
  4. If missing, check installation
  5. Clear cache: Site Admin → Purge all caches
```

#### "Areas not saving"

```
Check:
  1. Database upgrade completed?
  2. File permissions correct?
  3. Disk space available?
  4. PHP upload limit: php.ini max_upload_size
  5. Moodle maxbytes setting
  6. CSS validation (check for errors)
```

#### "CSS not applying"

```
Check:
  1. Browser console for JavaScript errors
  2. CSS validation (must have green border)
  3. Browser CSS limitations
  4. Cache: Ctrl+Shift+Del
  5. Try different browser
```

### Performance Issues

#### Slow Image Uploads

```
Optimize:
  1. Reduce image file size (<2MB)
  2. Use image compression tool
  3. Use dedicated file storage
  4. Enable Moodle caching
  5. Check disk I/O performance
```

#### Canvas Editor Slow

```
Optimize:
  1. Use smaller image
  2. Reduce number of areas
  3. Use dedicated server resources
  4. Check browser performance
  5. Close other tabs/applications
```

#### Database Slow Queries

```sql
-- Find slow queries
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 2;

-- Monitor plugin tables
SELECT COUNT(*) FROM mdl_imagemap;
SELECT COUNT(*) FROM mdl_imagemap_area;

-- Add index if many areas
CREATE INDEX idx_imagemap_id ON mdl_imagemap_area(imagemap);
```

---

## Maintenance

### Regular Tasks

#### Weekly
- Monitor error logs: `/var/log/moodle.log`
- Check disk space: `df -h`
- Verify backups: `ls -la /backup/`

#### Monthly
- Review plugin updates
- Check for security patches
- Verify database integrity: `mysqlcheck -u root -p moodle`

#### Quarterly
- Full database backup
- Security audit
- Performance review

### Backups

#### Backup Plugin Files
```bash
# Full backup
tar -czf /backup/mod_imagemap-$(date +%Y%m%d).tar.gz \
  /path/to/moodle/mod/imagemap/

# Incremental (new files only)
rsync -av --new-files /path/to/moodle/mod/imagemap/ \
  /backup/imagemap-incremental/
```

#### Backup Plugin Data
```bash
# Via Moodle
Site Admin → Courses → Backup

# Manual database backup
mysqldump -u user -p moodle \
  --tables mdl_imagemap mdl_imagemap_area \
  > /backup/imagemap_data_$(date +%Y%m%d).sql
```

#### Restore Process

```bash
# From file backup
tar -xzf /backup/mod_imagemap-20260130.tar.gz \
  -C /path/to/moodle/mod/

# From database backup
mysql -u user -p moodle < /backup/imagemap_data_20260130.sql

# Verify
php admin/cli/upgrade.php
```

### Cleanup Tasks

#### Remove Old Images
```sql
-- Find unreferenced images (optional, careful!)
SELECT fi.id, fi.filename FROM mdl_files fi
  LEFT JOIN mdl_imagemap im ON fi.filename = im.image
  WHERE im.id IS NULL AND fi.component = 'mod_imagemap'
  AND fi.filearea = 'content';
```

#### Clear Cache
```bash
# CLI method
php admin/cli/purge_caches.php

# Via UI
Site Admin → Development → Purge all caches
```

---

## Security

### File Upload Security

The plugin uses Moodle's file API which includes:
- ✅ MIME type validation
- ✅ File extension whitelist
- ✅ Quarantine of suspicious files
- ✅ Virus scanning (if configured)

### User Input Validation

All user input is validated:
- ✅ CSS: Validated client-side, no dangerous properties
- ✅ URLs: Validated before saving (requires http/https)
- ✅ Coordinates: Validated as integers
- ✅ Module IDs: Verified to exist in course

### Database Security

- ✅ Uses prepared statements (parameterized queries)
- ✅ CSRF tokens on all forms
- ✅ Capability checks on all actions
- ✅ Activity completion data verified

### Privacy & GDPR

The plugin implements Privacy API:
- ✅ Data export function
- ✅ Data deletion function
- ✅ No tracking or cookies
- ✅ Respects user preferences

**File:** `classes/privacy/provider.php`

### Access Control

Enforced via Moodle capabilities:
- ✅ Users can only view in enrolled courses
- ✅ Only teachers can edit
- ✅ Student viewing verified
- ✅ Completion checks validated

---

## Monitoring & Logging

### Enable Debug Logging

```php
// In config.php
@error_reporting(E_ALL | E_STRICT);
@ini_set('display_errors', '0');
$CFG->debug = DEBUG_DEVELOPER;
$CFG->debugdisplay = false;
$CFG->logfile = '/var/log/moodle.log';
```

### Check Logs

```bash
# View recent errors
tail -f /var/log/moodle.log | grep imagemap

# Search for specific errors
grep -i "imagemap" /var/log/moodle.log

# Count events
grep "imagemap" /var/log/moodle.log | wc -l
```

### Event Logging

The plugin logs:
- Activity creation
- Activity viewing
- Area modifications
- File uploads

**View logs:**
Site Admin → Reports → Event logs

---

## Support & Resources

### Documentation

- [USER_GUIDE.md](USER_GUIDE.md) - Teacher guide
- [QUICK_START.md](QUICK_START.md) - Quick 5-min guide
- [IMPLEMENTATION.md](IMPLEMENTATION.md) - Technical details
- [CHANGELOG.md](CHANGELOG.md) - Version history

### External Resources

- [Moodle Plugin Development](https://docs.moodle.org/dev/)
- [Moodle Security](https://docs.moodle.org/dev/Security)
- [Moodle Privacy API](https://docs.moodle.org/dev/Privacy_API)

### Getting Help

1. Check existing documentation
2. Review error logs
3. Search Moodle forums
4. Contact plugin maintainer
5. File issue on repository

---

**Version:** 1.0.1  
**Last Updated:** January 30, 2026  
**Status:** Maintained
