# CSS Validation Testing Guide

## Test Cases for CSS Input Validation

### 1. Valid Filter Examples

Test these in the textarea - they should show green border and preview:

```css
filter: brightness(1.2);
```

```css
filter: grayscale(1) opacity(0.5);
```

```css
filter: blur(5px) saturate(2);
```

### 2. Valid CSS Examples

```css
border: 3px solid #00ff00; background: rgba(0,255,0,0.2);
```

```css
box-shadow: 0 0 20px rgba(255,215,0,0.8);
```

```css
background: linear-gradient(45deg, rgba(255,0,0,0.3), rgba(0,0,255,0.3));
```

```css
border: 2px dashed #ff0000; background: repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,0,0,0.1) 10px, rgba(255,0,0,0.1) 20px);
```

```css
transform: scale(1.1); box-shadow: 0 0 15px rgba(0,255,0,0.6);
```

### 3. Invalid CSS Examples

These should show red border and no preview:

```css
border: invalid value here;
```

```css
color blue; (missing colon)
```

```css
filter: notafilter(123);
```

```css
{broken css}
```

### 4. Edge Cases

**Empty or "none":**
- Should remove all validation classes
- Should hide preview

**Mixed valid and invalid:**
```css
border: 3px solid #00ff00; invalid: property;
```
- Behavior depends on browser CSS parser
- May show as valid if browser ignores invalid parts

### 5. Visual Checks

#### Preview Box Features:
- ✅ Checkerboard background (shows transparency)
- ✅ 50x50 pixel size
- ✅ Updates in real-time as you type
- ✅ Hidden when empty or invalid

#### Textarea Features:
- ✅ Monospace font (code-like)
- ✅ Gray background (#f5f5f5)
- ✅ Green border when valid (.is-valid)
- ✅ Red border + pink background when invalid (.is-invalid)
- ✅ 3 rows height
- ✅ Placeholder examples visible when empty

### 6. Functional Testing

1. **Initial State:**
   - Open area edit form
   - Check if existing CSS is validated on load
   - Preview should appear for valid existing values

2. **Type New CSS:**
   - Start typing in textarea
   - Validation should trigger on each keystroke
   - Preview should update in real-time

3. **Copy/Paste:**
   - Copy complex CSS from examples
   - Paste into textarea
   - Should validate immediately

4. **Clear Field:**
   - Delete all content
   - Validation classes should be removed
   - Preview should disappear

5. **Form Submission:**
   - Enter valid CSS
   - Click "Save changes"
   - Area should be saved with CSS
   - Check view.php to see CSS applied to canvas overlays

### 7. Browser Testing

Test in:
- ✅ Chrome/Chromium
- ✅ Firefox
- ✅ Safari
- ✅ Edge

Different browsers may handle CSS parsing slightly differently.

### 8. CSS Properties to Test

**Filters:**
- `filter: blur(px)`
- `filter: brightness(number)`
- `filter: contrast(number)`
- `filter: grayscale(number)`
- `filter: hue-rotate(deg)`
- `filter: invert(number)`
- `filter: opacity(number)`
- `filter: saturate(number)`
- `filter: sepia(number)`

**Visual Effects:**
- `border: width style color`
- `box-shadow: x y blur color`
- `background: color or gradient`
- `opacity: number`
- `transform: scale/rotate/translate`

**Advanced:**
- Linear gradients
- Radial gradients
- Multiple backgrounds
- Box shadows with multiple layers

### 9. Expected Behavior

| Input | Border | Preview | Notes |
|-------|--------|---------|-------|
| Empty | None | Hidden | Default state |
| "none" | None | Hidden | Explicit none |
| Valid filter | Green | Visible | Filter applied |
| Valid CSS | Green | Visible | CSS applied |
| Invalid | Red | Hidden | Parse error |
| Partial valid | May vary | May show | Browser dependent |

### 10. Debugging

If validation doesn't work:

1. **Check console for errors:**
   - Open DevTools (F12)
   - Look for JavaScript errors

2. **Verify element IDs:**
   - activefilter, activefilter-preview, activefilter-preview-box
   - inactivefilter, inactivefilter-preview, inactivefilter-preview-box

3. **Check CSS classes:**
   - .css-input should be applied
   - .is-valid or .is-invalid should toggle

4. **Verify editor.js loaded:**
   - Check if ImageMapEditor.init() ran
   - Look for validation functions in console

5. **Database upgrade:**
   - If saving fails with "value too long"
   - Run Site Admin → Notifications → Upgrade database

---

**Testing Priority:**
1. HIGH: Valid filters show green + preview
2. HIGH: Invalid CSS shows red, no preview
3. MEDIUM: Real-time updates as you type
4. LOW: Edge cases and browser differences
