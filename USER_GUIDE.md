# Image Map Module - User Guide

Complete guide for teachers and administrators using the Image Map activity in Moodle.

---

## Table of Contents

1. [Overview](#overview)
2. [Getting Started](#getting-started)
3. [Creating an Image Map](#creating-an-image-map)
4. [Editing Areas](#editing-areas)
5. [Linking Areas](#linking-areas)
6. [Conditional Display](#conditional-display)
7. [Styling with CSS](#styling-with-css)
8. [Examples](#examples)
9. [Troubleshooting](#troubleshooting)

---

## Overview

The **Image Map** module allows you to create interactive images in your Moodle course. Students can click on different areas of an image to navigate to course modules, sections, or external resources. Teachers can optionally show/hide areas based on whether students have completed other activities.

### Key Features

✅ Upload your own images or use Moodle repository  
✅ Draw clickable areas directly on the image  
✅ Create circles, rectangles, and polygons  
✅ Link to modules, sections, or external URLs  
✅ Conditionally display areas based on completion  
✅ Customize colors and appearance with CSS  

---

## Getting Started

### System Requirements

- Moodle 4.1 or later
- PHP 7.2 or later
- Modern web browser (Chrome, Firefox, Safari, Edge)

### Permissions

You need the following capabilities:
- `mod/imagemap:addinstance` - Create new Image Map activities
- `mod/imagemap:edit` - Edit areas and settings
- `mod/imagemap:view` - View the Image Map

---

## Creating an Image Map

### Step 1: Add Activity

1. **Turn on editing** in your course
2. Click **Add an activity or resource**
3. Select **Image Map**
4. Fill in the form:
   - **Activity name** - Name shown in course (required)
   - **Description** - Optional description of the activity
   - **Image** - Click to upload or select from repository (required)

### Step 2: Image Requirements

- **Recommended size**: 600-1200px width
- **Supported formats**: JPG, PNG, GIF, WebP
- **Quality**: Use high-quality images (resolution appropriate for viewing)
- **Tip**: Avoid very large files (>5MB) for faster loading

### Step 3: Save

Click **Save and display** to create the activity.

---

## Editing Areas

### Open the Editor

1. Click your Image Map in the course
2. Click **Edit areas** button (pencil icon)
3. Canvas editor loads with your image

### Draw Shapes

#### Rectangle
```
1. Click [Rectangle] button
2. Click and drag on image to create rectangle
3. Release to place
4. Resize by dragging corners/edges
```

#### Circle
```
1. Click [Circle] button
2. Click at center point
3. Drag to set radius
4. Release
5. Resize by dragging edge handles
```

#### Polygon (Custom Shape)
```
1. Click [Polygon] button
2. Click to add first point
3. Click to add more points
4. Double-click last point to finish
5. Resize by dragging vertices
```

### Edit Shapes

**Right-click** on any shape to open the edit form:
- Change link destination
- Set completion condition
- Adjust styling (colors, filters)

**Drag** a shape to reposition it

**Resize** by dragging handles:
- Corners and edges (rectangles)
- Edge points (circles)
- Any vertex (polygons)

### Add/Remove Polygon Vertices

While editing a polygon:
- **Add vertex**: Double-click an edge
- **Remove vertex**: Double-click a vertex handle (minimum 3 vertices required)

### Delete a Shape

1. Right-click the shape
2. Click **Delete** in the form
3. Confirm deletion

---

## Linking Areas

### Link Types

Each area can link to one of three destinations:

#### 1. Course Module
Link to any activity or resource in your course.

**Example uses:**
- Navigation: Click to open assignment
- Learning path: Click to view next lesson
- Prerequisites: Link to required activities

**How to set:**
1. Right-click area → Edit
2. Under **Link type**, select **Module**
3. Choose module from the dropdown
4. Click **Save changes**

#### 2. Course Section
Link to a course section/topic.

**Example uses:**
- Visual syllabus: Show all course sections
- Course map: Navigate between topics
- Chapter overview: Click section name

**How to set:**
1. Right-click area → Edit
2. Under **Link type**, select **Section**
3. Choose section from the dropdown
4. Click **Save changes**

#### 3. External URL
Link to any external website or resource.

**Example uses:**
- Links to external resources
- Video platform links (YouTube, Vimeo)
- Documentation or guides
- Third-party tools

**How to set:**
1. Right-click area → Edit
2. Under **Link type**, select **URL**
3. Paste full URL (include http:// or https://)
4. Click **Save changes**

**Valid URL examples:**
```
https://www.youtube.com/watch?v=...
https://example.com/resource
http://external-library.org/page
```

---

## Conditional Display

### What is Conditional Display?

You can make areas appear **active** (clickable) or **inactive** (grayed out) based on whether students have completed specific activities.

**Use case:** Show optional resources only after completing prerequisites.

### How to Set Up

1. Right-click an area → Edit
2. Under **Condition for visibility**, select a module
3. Leave **"none"** to always show area as active

### Behavior

**If condition is set:**
- ✅ Area shows **ACTIVE** (bright) when student completes the module
- ❌ Area shows **INACTIVE** (grayed/faded) when not completed

**If condition is NOT set:**
- ✅ Area always shows **ACTIVE** (bright)

### Example Scenario

```
Course: Biology 101

Area 1: "Photosynthesis Lesson"
├─ Link to: Module (Photosynthesis Lecture)
└─ Condition: None (always active)

Area 2: "Quiz: Test Your Knowledge"
├─ Link to: Module (Chapter 1 Quiz)
└─ Condition: Complete (Photosynthesis Lecture)
  → Only shows active AFTER student completes lecture

Area 3: "Final Project"
├─ Link to: Module (Final Project)
└─ Condition: Complete (Chapter 1 Quiz)
  → Only shows active AFTER student completes quiz
```

---

## Styling with CSS

### Overview

You can customize how areas look when **ACTIVE** and **INACTIVE** using CSS.

### Default Styles

**Active areas:** Normal brightness, full color  
**Inactive areas:** Grayed out (grayscale filter), 50% opacity

### Custom CSS

#### For Active Areas

Click **Edit areas** → right-click area → **CSS when active**

**Examples:**

```css
/* Bright glow effect */
filter: brightness(1.2);
```

```css
/* Border with background */
border: 3px solid #00ff00;
background: rgba(0, 255, 0, 0.2);
```

```css
/* Golden shadow */
box-shadow: 0 0 20px rgba(255, 215, 0, 0.8);
```

```css
/* Gradient background */
background: linear-gradient(45deg, rgba(255, 0, 0, 0.3), rgba(0, 0, 255, 0.3));
```

#### For Inactive Areas

Click **Edit areas** → right-click area → **CSS when inactive**

**Examples:**

```css
/* Default: grayscale + opacity */
filter: grayscale(1) opacity(0.5);
```

```css
/* Darker look */
filter: brightness(0.7) saturate(0.5);
```

```css
/* Transparent overlay */
background: rgba(0, 0, 0, 0.5);
opacity: 0.6;
```

### CSS Validation

The editor shows:
- 🟢 **Green border** = Valid CSS
- 🔴 **Red border** = Invalid CSS (won't be applied)

### Preview Box

A small preview box appears when you enter CSS, showing how it will look.

### Advanced CSS Examples

#### Checkered Pattern
```css
background: repeating-linear-gradient(
  45deg,
  #ff6b6b,
  #ff6b6b 10px,
  #ff8787 10px,
  #ff8787 20px
);
```

#### Animated Border (Active)
```css
border: 3px solid #4ecdc4;
box-shadow: 0 0 10px rgba(78, 205, 196, 0.6);
animation: pulse 2s infinite;
```

#### Blur Inactive
```css
filter: blur(3px) opacity(0.4);
```

---

## Examples

### Example 1: Course Navigation Map

**Goal:** Create a visual course structure showing modules and prerequisites

**Setup:**

1. Find/create a course structure image (or use included example)
2. Add Image Map to course
3. Upload image
4. Draw areas for each section:
   - Introduction → Links to Intro module
   - Chapter 1 → Condition: Complete Intro
   - Chapter 2 → Condition: Complete Chapter 1
   - Final Project → Condition: Complete Chapter 2

**Styling:**
```
Active: filter: brightness(1.1);
Inactive: filter: grayscale(1) opacity(0.5);
```

### Example 2: Interactive Learning Diagram

**Goal:** Let students explore an anatomy diagram

**Setup:**

1. Use anatomy/diagram image
2. Create circle areas for each part
3. Link each area to detailed resource:
   - Brain → Link to Brain module
   - Heart → Link to Circulatory System
   - Lungs → Link to Respiratory System

**Styling:**
```
Active: border: 2px solid #2ecc71; background: rgba(46, 204, 113, 0.15);
Inactive: border: 1px dotted #999; background: rgba(0, 0, 0, 0.2);
```

### Example 3: Skill Tree / Game-like Progress

**Goal:** Gamify course progression

**Setup:**

1. Create/find skill tree image
2. Skill 1 (Basic) → Always active, link to lesson
3. Skill 2 (Intermediate) → Condition: Complete Skill 1
4. Skill 3 (Advanced) → Condition: Complete Skill 2
5. Boss Challenge → Condition: Complete Skill 3

**Styling:**
```
Active: 
  border: 3px solid gold;
  box-shadow: 0 0 15px rgba(255, 215, 0, 0.7);

Inactive:
  filter: grayscale(1);
  opacity: 0.3;
```

---

## Troubleshooting

### Image Not Showing

**Problem:** Image appears broken or missing

**Solutions:**
1. Check image file size (limit usually 5MB)
2. Try uploading again via File Manager
3. Check image format is supported (JPG, PNG, GIF, WebP)
4. Clear browser cache (Ctrl+Shift+Delete)

### Areas Not Visible on Image

**Problem:** Drew areas but can't see them

**Solutions:**
1. Canvas editor uses light outline - they're there!
2. Refresh the page
3. Check edit form to see area coordinates
4. Try right-clicking on the image to find shapes

### Links Don't Work

**Problem:** Areas link to wrong place or not clickable

**Solutions:**
1. Check **Link type** is set correctly (Module/Section/URL)
2. For URLs: Make sure to include `http://` or `https://`
3. Check target module/section still exists (not deleted)
4. Try right-clicking area to verify link in edit form
5. Clear browser cache and try again

### CSS Not Applying

**Problem:** CSS changes don't show effect

**Solutions:**
1. Check for **red border** (validation error) in textarea
2. Verify CSS syntax is correct
3. Try simpler CSS first: `border: 1px solid red;`
4. Don't use special characters without escaping
5. Refresh page after saving

### Areas Disappear When Student Completes Activity

**Problem:** Inactive areas completely hidden instead of grayed out

**Solutions:**
1. This is intentional conditional display
2. If you want areas always visible, set **Condition: none**
3. Adjust CSS for inactive to make visible but different:
   ```css
   filter: grayscale(1) opacity(0.5);
   ```

### Editor Loading Slowly

**Problem:** Canvas editor takes long to open

**Solutions:**
1. Check image file size (reduce if > 2MB)
2. Use image compression tool to reduce file size
3. Try different browser (Firefox, Chrome)
4. Clear browser cache
5. Check internet connection

### Database Upgrade Error

**Problem:** Getting "value too long" error when saving

**Solution:** 
- Admin needs to run: **Site Admin → Notifications → Upgrade Database**
- This converts CSS fields from 50 chars to unlimited length

---

## FAQ

**Q: Can I change area styling after creation?**  
A: Yes! Right-click any area → Edit to change anything (shape, link, CSS, condition)

**Q: What happens if linked module is deleted?**  
A: Area remains but shows warning in edit form. Update the link to new destination.

**Q: Can students edit areas?**  
A: No, only teachers with edit capability can modify areas.

**Q: Can I use the same image in multiple areas?**  
A: Yes, you can have multiple Image Map activities. Each has its own areas.

**Q: What's the maximum number of areas?**  
A: No hard limit, but performance degrades with 100+ areas. Recommended: 10-50 areas per image.

**Q: Can I duplicate an Image Map?**  
A: Use course backup/restore or manually recreate. Direct duplication not yet supported.

**Q: Are there size limits?**  
A: Image: ~5MB recommended. CSS: unlimited (after upgrade).

---

## Tips & Tricks

💡 **Tip 1:** Create template images with grids to help align areas precisely

💡 **Tip 2:** Use subtle CSS transitions with color changes for better UX

💡 **Tip 3:** Test conditional display by completing/uncompleting required activities

💡 **Tip 4:** Use transparency in CSS to show overlap between active areas

💡 **Tip 5:** Combine with other activities: Image Map → Lesson → Quiz chain

💡 **Tip 6:** Name areas clearly in edit form for better student UX

💡 **Tip 7:** Use section-type links for broad navigation, module links for specific content

---

## Support

For issues or feature requests:
1. Check [CHANGELOG.md](CHANGELOG.md) for known issues
2. Review [CSS_TESTING.md](CSS_TESTING.md) for CSS validation help
3. See [UPGRADE_INSTRUCTIONS.md](UPGRADE_INSTRUCTIONS.md) for database updates
4. Check plugin repository for updates

---

**Version:** 1.0.1  
**Last Updated:** January 30, 2026  
**Language:** English
