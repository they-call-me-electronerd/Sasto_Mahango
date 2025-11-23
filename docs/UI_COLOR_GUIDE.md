# MulyaSuchi UI Color System Guide

## Brand Identity - Orange Gradient

The core brand color is an **orange gradient** that should be used consistently across all UI elements.

### Primary Orange Gradient

```css
/* Light Mode */
background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);

/* Dark Mode */
background: linear-gradient(135deg, #fb923c 0%, #f97316 100%);
```

**Using CSS Variables** (Recommended):
```css
background: var(--primary-gradient);
```

### Orange Color Variants

| Color | Light Mode | Dark Mode | Usage |
|-------|------------|-----------|-------|
| Primary | `#f97316` | `#fb923c` | Main buttons, CTAs, badges |
| Dark | `#ea580c` | `#f97316` | Hover states, emphasis |
| Light | `#fb923c` | `#fdba74` | Backgrounds, subtle accents |

**CSS Variables**:
```css
color: var(--primary-orange);        /* Main orange */
color: var(--primary-orange-dark);   /* Darker shade */
color: var(--primary-orange-light);  /* Lighter shade */
```

## Accent Colors

### Blue (Information, Links)
- Light: `#3b82f6` → Dark: `#60a5fa`
- Variable: `var(--accent-blue)`
- Usage: Links, info badges, secondary CTAs

### Green (Success, Positive)
- Light: `#10b981` → Dark: `#34d399`
- Variable: `var(--accent-green)` or `var(--success)`
- Usage: Success messages, positive metrics, "in stock"

### Red (Error, Negative)
- Light: `#ef4444` → Dark: `#f87171`
- Variable: `var(--accent-red)` or `var(--error)`
- Usage: Error messages, negative metrics, "out of stock"

### Yellow (Warning)
- Light: `#f59e0b` → Dark: `#fbbf24`
- Variable: `var(--accent-yellow)` or `var(--warning)`
- Usage: Warning messages, pending states

## Background Colors

| Layer | Light Mode | Dark Mode | CSS Variable |
|-------|------------|-----------|--------------|
| Primary (Body) | `#ffffff` | `#111827` | `var(--bg-primary)` |
| Secondary (Cards) | `#f9fafb` | `#1f2937` | `var(--bg-secondary)` |
| Tertiary (Inputs) | `#f3f4f6` | `#374151` | `var(--bg-tertiary)` |

## Text Colors

| Type | Light Mode | Dark Mode | CSS Variable |
|------|------------|-----------|--------------|
| Primary (Headings) | `#111827` | `#f1f5f9` | `var(--text-primary)` |
| Secondary (Body) | `#6b7280` | `#e2e8f0` | `var(--text-secondary)` |
| Tertiary (Meta) | `#9ca3af` | `#cbd5e1` | `var(--text-tertiary)` |

## Shadows

```css
box-shadow: var(--shadow-sm);  /* Subtle depth */
box-shadow: var(--shadow-md);  /* Medium elevation */
box-shadow: var(--shadow-lg);  /* High elevation */
```

## Common UI Patterns

### Buttons

#### Primary Button (Orange Gradient)
```css
.btn-primary {
    background: var(--primary-gradient);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(249, 115, 22, 0.3);
}
```

#### Secondary Button
```css
.btn-secondary {
    background: transparent;
    color: var(--primary-orange);
    border: 2px solid var(--primary-orange);
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 600;
}

.btn-secondary:hover {
    background: var(--primary-orange);
    color: white;
}
```

### Cards

```css
.card {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: var(--shadow-md);
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.card-title {
    color: var(--text-primary);
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.card-description {
    color: var(--text-secondary);
    font-size: 0.9rem;
}
```

### Inputs

```css
.form-input {
    background: var(--bg-tertiary);
    color: var(--text-primary);
    border: 1px solid var(--border-color);
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    width: 100%;
}

.form-input:focus {
    outline: none;
    border-color: var(--primary-orange);
    box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
}

.form-input::placeholder {
    color: var(--text-tertiary);
}
```

### Badges

```css
/* Primary Badge (Orange) */
.badge-primary {
    background: var(--primary-gradient);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
}

/* Success Badge */
.badge-success {
    background: var(--success);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
}

/* Info Badge */
.badge-info {
    background: var(--accent-blue);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
}
```

### Hero Sections

```css
.hero {
    background: var(--primary-gradient);
    color: white;
    padding: 4rem 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}

/* Alternative: Gradient with pattern */
.hero-pattern {
    background: 
        linear-gradient(135deg, rgba(249, 115, 22, 0.95), rgba(234, 88, 12, 0.95)),
        url('/assets/images/pattern.svg');
    background-size: cover;
    background-position: center;
}
```

### Dark Mode Specific Styles

```css
/* Make elements darker in dark mode */
[data-theme="dark"] .section-bg {
    background: var(--bg-secondary);
}

[data-theme="dark"] .card-elevated {
    background: #1e293b; /* Slightly lighter than bg-secondary */
    border-color: #374151;
}

/* Ensure text contrast */
[data-theme="dark"] .text-muted {
    color: var(--text-tertiary);
}

/* Override inline styles if needed */
[data-theme="dark"] .forced-dark {
    background: #111827 !important;
    color: #f1f5f9 !important;
}
```

## Typography

### Font Families

```css
/* Body text */
font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;

/* Headings */
font-family: 'Manrope', 'Inter', sans-serif;

/* Nepali text */
font-family: 'Noto Sans Devanagari', 'Inter', sans-serif;
```

### Font Sizes

```css
/* Headings */
h1 { font-size: 2.5rem; font-weight: 800; }
h2 { font-size: 2rem; font-weight: 700; }
h3 { font-size: 1.5rem; font-weight: 600; }
h4 { font-size: 1.25rem; font-weight: 600; }
h5 { font-size: 1.125rem; font-weight: 500; }

/* Body */
body { font-size: 1rem; line-height: 1.6; }
.text-sm { font-size: 0.875rem; }
.text-xs { font-size: 0.75rem; }
.text-lg { font-size: 1.125rem; }
```

## Transitions & Animations

### Standard Transitions

```css
/* Default */
transition: all 0.3s ease;

/* Theme transition */
transition: background-color var(--theme-transition), 
            color var(--theme-transition);

/* Hover elevation */
.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}
```

### Loading States

```css
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.loading {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
```

## Responsive Design

### Breakpoints

```css
/* Mobile-first approach */
/* Extra small: < 480px (default) */
/* Small: 480px - 768px */
@media (min-width: 480px) { }

/* Medium: 768px - 992px */
@media (min-width: 768px) { }

/* Large: 992px - 1200px */
@media (min-width: 992px) { }

/* Extra large: > 1200px */
@media (min-width: 1200px) { }
```

## Best Practices

1. **Always use CSS variables** instead of hardcoded colors
2. **Test in both light and dark modes** before deploying
3. **Maintain orange gradient** as primary brand color
4. **Use semantic colors** (success=green, error=red, warning=yellow)
5. **Ensure text contrast** meets WCAG AA standards (4.5:1 ratio)
6. **Avoid pure black** (#000000) - use `var(--bg-primary)` instead
7. **Smooth transitions** - all theme changes should be animated
8. **Mobile-first** - design for small screens first, enhance for larger screens

## Quick Reference

### Most Used Color Combinations

```css
/* Orange CTA Button */
background: var(--primary-gradient);
color: white;

/* Card */
background: var(--bg-primary);
color: var(--text-primary);
border: 1px solid var(--border-color);

/* Input Field */
background: var(--bg-tertiary);
color: var(--text-primary);

/* Success Message */
background: var(--success);
color: white;

/* Link */
color: var(--accent-blue);
```
