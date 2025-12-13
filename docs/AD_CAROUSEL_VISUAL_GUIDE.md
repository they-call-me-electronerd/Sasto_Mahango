# Ad Carousel Visual Preview

## 🖼️ What It Looks Like

```
┌─────────────────────────────────────────────────────────────────┐
│  What is the price of                    ╔═══════════════════╗  │
│  anything today?                          ║                   ║  │
│                                           ║    [Ad Image]     ║  │
│  Get instant, verified prices...         ║                   ║  │
│                                           ║   550 x 450px     ║  │
│  [Search Box]                             ║                   ║  │
│                                           ║  ┌─────────────┐  ║  │
│  558+     15+      Daily                  ║  │ 💼 Sponsored│  ║  │
│  Products Markets  Updates                ║  └─────────────┘  ║  │
│                                           ║                   ║  │
│                                           ║   ◀          ▶   ║  │
│                                           ║                   ║  │
│                                           ║   ⚫ ⚪ ⚪        ║  │
│                                           ╚═══════════════════╝  │
└─────────────────────────────────────────────────────────────────┘
```

## 🎨 Design Elements

### 1. Ad Container
- **Size**: 550×450px
- **Border Radius**: 24px rounded corners
- **Shadow**: Soft 3D shadow effect
- **Background**: Subtle gradient overlay
- **Animation**: Gentle floating motion

### 2. Sponsored Badge
- **Position**: Top-right corner
- **Style**: Dark semi-transparent with icon
- **Text**: "💼 Sponsored"
- **Animation**: Subtle pulse effect

### 3. Navigation Arrows
- **Position**: Left and right middle
- **Style**: White circular buttons
- **Visibility**: Appear on hover (desktop)
- **Size**: 48px circles
- **Icons**: Chevron left/right

### 4. Dot Indicators
- **Position**: Bottom center
- **Style**: Dots inside dark capsule
- **Active State**: Extended pill shape
- **Interaction**: Click to jump to slide
- **Count**: One per ad slide

## 📱 Responsive Behavior

### Desktop (1200px+)
```
┌─────────────────────────────────────────┐
│                                         │
│  [Content]      [Ad Carousel 550x450]  │
│                                         │
└─────────────────────────────────────────┘
```

### Tablet (768px - 1199px)
```
┌────────────────────────────────────┐
│                                    │
│  [Content]   [Ad Carousel 480x400] │
│                                    │
└────────────────────────────────────┘
```

### Mobile (< 768px)
```
┌──────────────────┐
│                  │
│    [Content]     │
│                  │
│                  │
│ [Ad Carousel]    │
│   100% width     │
│   350px height   │
│                  │
└──────────────────┘
```

## 🎬 Animation Flow

### Auto-Rotation (5 seconds)
```
Ad 1 (Active) → Wait 5s → Fade Out
                          ↓
Ad 2 (Fade In) → Wait 5s → Fade Out  
                          ↓
Ad 3 (Fade In) → Wait 5s → Fade Out
                          ↓
Ad 1 (Fade In) → Loop continues...
```

### User Interaction
```
User Hovers
    ↓
Pause Auto-Rotation
    ↓
User Leaves
    ↓
Resume Auto-Rotation
```

## 🎨 Color Scheme

### Current Placeholders

**Ad 1 - Orange Theme**
- Primary: #f97316 (Orange)
- Secondary: #ea580c (Dark Orange)
- Accent: #fbbf24 (Yellow)

**Ad 2 - Blue Theme**
- Primary: #3b82f6 (Blue)
- Secondary: #2563eb (Dark Blue)
- Accent: #60a5fa (Light Blue)

**Ad 3 - Green Theme**
- Primary: #10b981 (Green)
- Secondary: #059669 (Dark Green)
- Accent: rgba(255,255,255,0.3) (White overlay)

## 📊 Layout Breakdown

```css
.ad-carousel-container {
    width: 550px;
    height: 450px;
    border-radius: 24px;
    box-shadow: 0 25px 60px rgba(0,0,0,0.15);
}

.ad-slide {
    position: absolute;
    transition: opacity 0.6s ease;
}

.ad-badge {
    position: absolute;
    top: 16px;
    right: 16px;
    background: rgba(0,0,0,0.7);
    padding: 8px 16px;
    border-radius: 20px;
}

.ad-nav-btn {
    position: absolute;
    top: 50%;
    width: 48px;
    height: 48px;
    border-radius: 50%;
}

.ad-indicators {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
}
```

## 🎯 Interactive States

### Normal State
- Ad displayed with gentle float animation
- Sponsored badge visible
- Navigation hidden (desktop)
- Dots visible

### Hover State
- Float animation continues
- Navigation arrows appear
- Image scales up slightly (2%)
- Cursor becomes pointer

### Active/Transition
- Current ad fades out
- Next ad fades in
- Active dot extends
- Smooth 600ms transition

### Click State
- Arrow/dot pressed effect
- Immediate slide change
- Auto-rotation resets
- Smooth navigation

## 📐 Spacing & Layout

```
Hero Section
├── Left Content (50%)
│   ├── Badge
│   ├── Heading
│   ├── Search Box
│   └── Stats
│
└── Right Content (50%)
    └── Ad Carousel
        ├── Ad Slides (stacked)
        ├── Navigation Arrows
        └── Dot Indicators
```

## 🎨 Visual Hierarchy

1. **Primary**: Ad Image (largest, center focus)
2. **Secondary**: Sponsored Badge (notification)
3. **Tertiary**: Navigation Controls (subtle, on-demand)
4. **Quaternary**: Dot Indicators (passive navigation)

## 💡 Design Principles

### Clarity
- Clear "Sponsored" label
- High contrast navigation
- Obvious interactive elements

### Usability
- Large clickable areas
- Smooth transitions
- Pause on hover
- Multiple navigation methods

### Performance
- Optimized images
- Hardware-accelerated CSS
- Efficient JavaScript
- Lazy loading ready

### Accessibility
- Keyboard navigation
- ARIA labels
- Focus indicators
- Semantic HTML

---

## 🎬 Live Preview

To see the actual carousel:
1. Open: `http://localhost/MulyaSuchi/public/index.php`
2. Look at the hero section (top right side)
3. Watch it auto-rotate every 5 seconds
4. Hover to see navigation arrows
5. Click dots to jump between ads

**The carousel seamlessly replaces the orange character illustration!**
