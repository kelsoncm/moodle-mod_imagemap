# Image Map - Quick Start Guide

Get started with Image Map in 5 minutes!

---

## For Teachers: Create Your First Image Map

### Step 1: Add Activity (1 min)
```
1. Go to your course
2. Click "Turn editing on"
3. Click "Add an activity or resource"
4. Select "Image Map"
5. Enter name: "Course Navigation Map"
6. Click "Save and display"
```

### Step 2: Upload Image (1 min)
```
Back in course → Click your new Image Map → Click "Edit areas"
→ Image loads (this is the editor)
```

### Step 3: Draw Your First Area (2 min)

**Rectangle Example:**
```
1. Click [Rectangle] button
2. Click and drag on the image
3. Right-click the shape → Edit
4. Under "Link type" select "Module"
5. Pick a module from dropdown
6. Click "Save changes"
```

**Circle Example:**
```
1. Click [Circle] button
2. Click once for center
3. Drag out to set radius
4. Right-click to configure link
```

### Step 4: View as Student (1 min)
```
1. Log out or use another browser tab
2. View your Image Map as student
3. Click on the areas you created
4. It links to the selected module!
```

---

## Common Tasks

### Add Conditional Display
```
Right-click area → Edit
→ Under "Condition for visibility"
→ Select a module
→ Areas only show ACTIVE after completing that module
```

### Make Areas Look Different When Complete
```
Right-click area → Edit
→ "CSS when active": filter: brightness(1.2);
→ "CSS when inactive": filter: grayscale(1) opacity(0.5);
```

### Link to External Website
```
Right-click area → Edit
→ "Link type" select "URL"
→ Paste: https://example.com
→ Save
```

### Change Area Shape
```
Right-click shape → Edit → Change coordinates
→ Or delete and draw new shape
```

---

## Quick CSS Examples

Copy & paste these into "CSS when active" or "CSS when inactive":

### Green Highlight
```css
border: 3px solid #00ff00;
background: rgba(0, 255, 0, 0.2);
```

### Gold Glow
```css
filter: brightness(1.3);
box-shadow: 0 0 20px rgba(255, 215, 0, 0.8);
```

### Gray Out (Inactive)
```css
filter: grayscale(1) opacity(0.5);
```

### Blue Border
```css
border: 2px solid #0066ff;
background: rgba(0, 102, 255, 0.15);
```

### Gradient
```css
background: linear-gradient(45deg, rgba(255, 0, 0, 0.3), rgba(0, 0, 255, 0.3));
```

---

## Keyboard Shortcuts (Editor)

| Key | Action |
|-----|--------|
| `R` | Rectangle tool |
| `C` | Circle tool |
| `P` | Polygon tool |
| `Delete` | Delete selected shape |
| `Esc` | Close edit form |
| `Enter` | Save changes |

---

## Troubleshooting

### Areas Don't Appear
→ Click on image to activate canvas  
→ Check "Manage areas" shows shapes in list

### Image Too Big
→ Upload smaller image (<2MB)  
→ Use image compression tool first

### CSS Not Working
→ Check for **red border** (error)  
→ Try simple CSS first: `border: 1px solid red;`

### Links Don't Work
→ Check dropdown shows selected module  
→ Make sure target module exists  
→ For URLs: include `https://` or `http://`

---

## Next Steps

📖 Read [USER_GUIDE.md](USER_GUIDE.md) for detailed instructions  
🎨 See [CSS_TESTING.md](CSS_TESTING.md) for CSS examples  
💻 Check [IMPLEMENTATION.md](IMPLEMENTATION.md) for technical details

---

**That's it! You now have a working Image Map activity.** 🎉

For more advanced features and options, see the full User Guide.
