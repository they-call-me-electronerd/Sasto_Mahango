# Contributor Dashboard - Visual Structure

## Layout Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                      NAVIGATION BAR                              │
│  [SastoMahango Logo]  Home  Products  About    [Theme] [Logout]  │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                                                                   │
│  👤  Welcome back, [Username]! 👋                                │
│      Ready to contribute to Nepal's market intelligence          │
│                                                                   │
│  Home > Dashboard                                                │
│                                                                   │
└─────────────────────────────────────────────────────────────────┘

┌──────────────────┬──────────────────┬──────────────────┬─────────────────┐
│ 📄 TOTAL         │ 🕐 PENDING       │ ✅ APPROVED      │ ⭐ POINTS       │
│ SUBMISSIONS      │ REVIEW           │                  │                 │
│                  │                  │                  │                 │
│ 42               │ 5                │ 35               │ 350             │
│ All-time         │ Awaiting         │ Successfully     │ Reputation      │
│ contributions    │ validation       │ validated        │ score           │
│                  │                  │                  │                 │
│ ↗ Active         │ ⏳ Review        │ ↗ 83%           │ 🏆 Level 1      │
└──────────────────┴──────────────────┴──────────────────┴─────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│  ⚡ Quick Actions                                                │
│                                                                   │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐ │
│  │ ➕ Add New Item │  │ 💰 Update Price │  │ 📊 View Analytics│ │
│  │                 │  │                 │  │                   │ │
│  │ Add a new       │  │ Submit current  │  │ Track your        │ │
│  │ product or      │  │ market prices   │  │ contribution      │ │
│  │ commodity...    │  │ for items       │  │ statistics        │ │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘ │
└─────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────┬──────────────────────┐
│  🕐 Recent Activity                      │  🔔 Notifications    │
│                                           │                      │
│  ┌────────────────────────────────────┐  │  ┌────────────────┐ │
│  │ Tomato (Local)              [PENDING]│  │  │ ⏳ Under Review│ │
│  │ 🏷️ Update Price  📅 Nov 24, 2025 │  │  │ 5 submissions  │ │
│  └────────────────────────────────────┘  │  │ Just now       │ │
│                                           │  └────────────────┘ │
│  ┌────────────────────────────────────┐  │                      │
│  │ Potato                    [APPROVED]│  │  ┌────────────────┐ │
│  │ 🏷️ Add Item     📅 Nov 23, 2025 │  │  │ ✅ Approved    │ │
│  └────────────────────────────────────┘  │  │ 35 submissions │ │
│                                           │  │ Recent         │ │
│  ┌────────────────────────────────────┐  │  └────────────────┘ │
│  │ Onion                     [REJECTED]│  │                      │
│  │ 🏷️ Update Price  📅 Nov 22, 2025 │  │  ┌────────────────┐ │
│  │ ⚠️ Reason: Price too high compared │  │  │ ℹ️ Welcome     │ │
│  │    to market average                │  │  │ Thank you for  │ │
│  └────────────────────────────────────┘  │  │ joining!       │ │
│                                           │  └────────────────┘ │
└──────────────────────────────────────────┴──────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                      PROFESSIONAL FOOTER                          │
│  [Same as main website - dark gradient with orange accents]     │
└─────────────────────────────────────────────────────────────────┘
```

## Color Scheme

### Primary Colors
- **Orange**: `#f97316` - Primary actions, icons, accents
- **Blue**: `#3b82f6` - Information, secondary actions
- **Green**: `#10b981` - Success states, approvals
- **Purple**: `#8b5cf6` - Gamification elements

### Background Colors (Light Mode)
- **Main BG**: Linear gradient `#fef3f0` → `#fff5ed` → `#f9fafb`
- **Card BG**: `#ffffff` (white)
- **Secondary BG**: `#f9fafb` (light gray)

### Background Colors (Dark Mode)
- **Main BG**: Linear gradient `#0f172a` → `#1e293b` → `#1f2937`
- **Card BG**: `rgba(30, 41, 59, 0.6)` (semi-transparent)
- **Secondary BG**: `#1f2937` (dark gray)

### Status Colors
- **Pending**: Yellow/Amber `#fef3c7` background, `#92400e` text
- **Approved**: Green `#d1fae5` background, `#065f46` text
- **Rejected**: Red `#fee2e2` background, `#991b1b` text

## Typography

### Fonts
- **Headings**: Manrope (bold, extrabold)
- **Body**: Inter (regular, medium, semibold)
- **Nepali**: Noto Sans Devanagari

### Font Sizes
- **Hero Title**: 30px (1.875rem)
- **Section Titles**: 24px (1.5rem)
- **Card Titles**: 18px (1.125rem)
- **Body Text**: 16px (1rem)
- **Small Text**: 14px (0.875rem)
- **Tiny Text**: 12px (0.75rem)

## Spacing System
- **XS**: 4px
- **SM**: 8px
- **MD**: 16px
- **LG**: 24px
- **XL**: 32px
- **2XL**: 48px

## Border Radius
- **Small**: 4px - Tags, badges
- **Medium**: 8px - Inputs
- **Large**: 12px - Buttons, small cards
- **XL**: 16px - Main cards
- **2XL**: 24px - Large sections
- **Full**: 9999px - Circular elements

## Animations

### Entry Animations
1. **Dashboard Header**: Slide down (0.6s)
2. **Stat Cards**: Staggered slide up (0.1s delay each)
3. **Quick Actions**: Slide up (0.5s delay)
4. **Activity Feed**: Slide up (0.6s delay)

### Hover Effects
- **Stat Cards**: Lift up 8px, orange shadow
- **Action Cards**: Lift up 4px, rotate icon
- **Activity Items**: Slide right 8px
- **Notifications**: Slight scale effect

### Transitions
- **Fast**: 150ms - Hover states
- **Base**: 300ms - Most interactions
- **Slow**: 500ms - Complex animations

## Responsive Breakpoints

### Desktop (1280px+)
- Full 4-column stats grid
- 3-column action cards
- 2-column dashboard grid (activity + notifications)

### Tablet (768px - 1279px)
- 2-column stats grid
- 2-column action cards
- Single column dashboard sections

### Mobile (< 768px)
- Single column stats
- Single column actions
- Stacked notifications
- Reduced padding and spacing

## Interactive Elements

### Stat Cards
- **Default**: White background, subtle shadow
- **Hover**: Lift up, show orange top border, enhanced shadow
- **Active**: Counter animation on first view

### Action Cards
- **Default**: Gradient background, border
- **Hover**: Lift up, orange border, icon rotates
- **Click**: Ripple effect

### Activity Items
- **Default**: Light background, left border hidden
- **Hover**: Slide right, show orange left border

### Notifications
- **Unread**: Orange left border, highlighted
- **Read**: Standard appearance
- **Click**: Mark as read, scale effect

## Accessibility Features
- ✅ Keyboard navigation (Tab, Enter)
- ✅ Focus indicators on all interactive elements
- ✅ ARIA labels for screen readers
- ✅ Color contrast ratios meet WCAG AA
- ✅ Reduced motion support
- ✅ Semantic HTML structure

## Performance Optimizations
- ✅ GPU-accelerated animations (transform, opacity)
- ✅ Intersection Observer for scroll animations
- ✅ CSS containment for reflow optimization
- ✅ Minimal JavaScript dependencies
- ✅ Lazy-loaded images (if applicable)

## Browser Compatibility
- ✅ Chrome 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Edge 90+
- ✅ Mobile browsers (iOS Safari, Chrome Mobile)

---

**Design System Version**: 1.0
**Last Updated**: November 24, 2025
**Designer**: SastoMahango Team
