# Testing Guide for mod_imagemap

## Manual Testing Procedure

### Installation Testing

1. **Install the module**
   ```
   - Copy the plugin to {moodle}/mod/imagemap
   - Visit Site Administration → Notifications
   - Verify the plugin installs without errors
   - Check that tables are created: mdl_imagemap and mdl_imagemap_area
   ```

2. **Verify module appears**
   ```
   - Turn editing on in a test course
   - Click "Add an activity or resource"
   - Verify "Image Map" appears in the activity list
   ```

### Basic Functionality Testing

1. **Create an Image Map instance**
   ```
   - Add an Image Map activity
   - Enter name: "Test Image Map"
   - Enter description
   - Upload a test image (e.g., 800x600px PNG/JPG)
   - Save and display
   - Verify image is displayed
   ```

2. **Add areas with different shapes**
   
   **Rectangle:**
   ```
   - Click "Manage areas"
   - Select shape: Rectangle
   - Draw a rectangle on the image by clicking and dragging
   - Fill in form:
     - Title: "Test Rectangle"
     - Link type: URL
     - Link target: https://example.com
   - Save
   - Verify area appears in the list
   ```

   **Circle:**
   ```
   - Select shape: Circle
   - Click center point and drag to set radius
   - Fill in form:
     - Title: "Test Circle"
     - Link type: Section
     - Link target: 1 (first section)
   - Save
   - Verify area appears in the list
   ```

   **Polygon:**
   ```
   - Select shape: Polygon
   - Click multiple points (at least 3)
   - Click "Finish"
   - Fill in form:
     - Title: "Test Polygon"
     - Link type: Module
     - Link target: [ID of another module]
   - Save
   - Verify area appears in the list
   ```

3. **Test clickable areas**
   ```
   - Return to the image map view page
   - Hover over areas - verify tooltips show
   - Click on areas - verify navigation works
   ```

### Conditional Display Testing

1. **Create a completion-enabled module**
   ```
   - Create a Page activity with completion enabled
   - Note its module ID
   ```

2. **Create conditional area**
   ```
   - In Image Map, add a new area
   - Set completion condition to the Page module
   - Set active filter: "none"
   - Set inactive filter: "grayscale(1) opacity(0.5)"
   - Save
   ```

3. **Test inactive state**
   ```
   - As a student, view the image map
   - Verify the conditional area appears grayed out
   - Try to click it - should not be clickable
   ```

4. **Test active state**
   ```
   - Complete the Page module
   - Return to the image map
   - Verify the area is now active (not grayed out)
   - Click it - should navigate to target
   ```

### Area Management Testing

1. **Delete areas**
   ```
   - Go to "Manage areas"
   - Delete an area
   - Confirm deletion
   - Verify it's removed from the list
   - Return to view - verify it's not visible
   ```

2. **Multiple areas**
   ```
   - Add several areas overlapping
   - Verify all are clickable
   - Verify tooltips work for each
   ```

### Permissions Testing

1. **Student access**
   ```
   - Log in as student
   - View image map - should work
   - Verify "Manage areas" button is NOT visible
   - Try to access areas.php directly - should be denied
   ```

2. **Teacher access**
   ```
   - Log in as editing teacher
   - View image map
   - Verify "Manage areas" button IS visible
   - Can manage all areas
   ```

### Error Handling Testing

1. **No image uploaded**
   ```
   - Create image map without uploading image
   - Save
   - View - should show "No image has been uploaded" message
   ```

2. **Invalid coordinates**
   ```
   - Try to create area with malformed coordinates
   - Should handle gracefully
   ```

3. **Invalid module ID**
   ```
   - Create area linking to non-existent module ID
   - Should handle gracefully without errors
   ```

### Browser Compatibility Testing

Test in:
- Chrome/Chromium (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

Verify:
- Canvas drawing works
- Image maps are clickable
- CSS filters apply correctly
- Mobile responsive

### Localization Testing

1. **Portuguese (Brazil)**
   ```
   - Change Moodle language to Portuguese (Brazil)
   - Verify all strings are translated
   - Create and manage areas
   - All UI should be in Portuguese
   ```

## Expected Results

All tests should pass without errors. The module should:
- Install cleanly
- Create clickable image maps
- Support all three shape types
- Handle conditional display based on completion
- Enforce proper permissions
- Work in both English and Portuguese
- Be responsive and work on mobile devices

## Known Limitations

- Module does not track user interactions (by design, for privacy)
- Areas are rendered using HTML image maps (limited styling options)
- Canvas editor requires JavaScript enabled
- Very complex polygons may have performance issues
