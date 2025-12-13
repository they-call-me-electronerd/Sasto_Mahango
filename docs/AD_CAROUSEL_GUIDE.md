# Advertisement Carousel Guide

## Overview
The advertisement carousel has been successfully implemented on your landing page to help monetize your platform. The carousel automatically rotates through ads and provides a professional way to showcase sponsor content.

## Features
✅ **Automatic Rotation**: Ads rotate every 5 seconds
✅ **Manual Navigation**: Users can click arrows or dots to navigate
✅ **Hover Pause**: Carousel pauses when users hover over it
✅ **Keyboard Support**: Arrow keys for navigation
✅ **Responsive Design**: Works on all screen sizes
✅ **Analytics Ready**: Built-in click and impression tracking
✅ **Smooth Animations**: Professional transitions and effects

## File Structure
```
assets/
├── css/components/
│   └── ad-carousel.css          # Carousel styles
├── js/components/
│   └── ad-carousel.js           # Carousel functionality
└── uploads/ads/
    ├── ad-1.jpg                 # Advertisement image 1
    ├── ad-2.jpg                 # Advertisement image 2
    └── ad-3.jpg                 # Advertisement image 3
```

## How to Add/Replace Ads

### 1. Prepare Your Ad Images
- **Recommended Size**: 550x450 pixels (or 11:9 aspect ratio)
- **Format**: JPG, PNG, or SVG
- **File Size**: Keep under 500KB for fast loading
- **Quality**: High-quality images for professional appearance

### 2. Upload Ad Images
Place your ad images in: `assets/uploads/ads/`

Example:
- `ad-1.jpg` - Your first advertiser
- `ad-2.jpg` - Your second advertiser
- `ad-3.jpg` - Your third advertiser

### 3. Update Ad Links
Edit `public/index.php` (around line 108) to update the ad links:

```php
<div class="ad-slide active">
    <a href="https://advertiser-website.com" target="_blank" rel="noopener noreferrer" class="ad-link">
        <img src="<?php echo rtrim(SITE_URL, '/'); ?>/assets/uploads/ads/ad-1.jpg" 
             alt="Advertiser Name - Product/Service" 
             class="ad-image">
        <div class="ad-badge">
            <i class="bi bi-badge-ad"></i> Sponsored
        </div>
    </a>
</div>
```

### 4. Add More Ad Slides
To add a 4th, 5th, etc. ad:

1. Add the HTML slide in `index.php`:
```php
<div class="ad-slide">
    <a href="https://new-advertiser.com" target="_blank" rel="noopener noreferrer" class="ad-link">
        <img src="<?php echo rtrim(SITE_URL, '/'); ?>/assets/uploads/ads/ad-4.jpg" 
             alt="New Advertiser" 
             class="ad-image">
        <div class="ad-badge">
            <i class="bi bi-badge-ad"></i> Sponsored
        </div>
    </a>
</div>
```

2. Add a corresponding dot indicator:
```php
<button class="ad-dot" data-slide="3" aria-label="Go to ad 4"></button>
```

## Monetization Strategy

### Pricing Models

1. **CPM (Cost Per Mille)**
   - Charge per 1,000 impressions
   - Example: NPR 500 per 1,000 views

2. **Flat Monthly Rate**
   - Fixed price per month per ad slot
   - Example: NPR 5,000/month per position

3. **CPC (Cost Per Click)**
   - Charge per click on the ad
   - Example: NPR 10 per click

4. **Sponsored Placement**
   - Premium placement for featured products
   - Example: NPR 10,000/month for top slot

### Recommended Rates (Nepal Market)
Based on your 10K+ daily views:
- **Position 1 (First Slide)**: NPR 8,000-10,000/month
- **Position 2 (Second Slide)**: NPR 6,000-8,000/month
- **Position 3 (Third Slide)**: NPR 5,000-6,000/month

## Analytics & Tracking

The carousel includes built-in tracking functions:

### Impression Tracking
Every time an ad is displayed, it's logged:
```javascript
trackImpression(index)
```

### Click Tracking
Every time an ad is clicked, it's logged:
```javascript
trackClick(index)
```

### Google Analytics Integration
The carousel automatically sends events to Google Analytics if gtag is available:
- Event: `ad_impression`
- Event: `ad_click`

To enable, add Google Analytics to your site:
```html
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=YOUR-GA-ID"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'YOUR-GA-ID');
</script>
```

## Customization Options

### Change Rotation Speed
Edit `assets/js/components/ad-carousel.js`:
```javascript
const CONFIG = {
    autoPlayInterval: 5000, // Change to 7000 for 7 seconds
    transitionDuration: 600,
    pauseOnHover: true
};
```

### Disable Auto-Rotation
Set `autoPlayInterval` to `0` or remove the `startAutoPlay()` call.

### Change Colors
Edit `assets/css/components/ad-carousel.css`:
```css
.ad-badge {
    background: rgba(0, 0, 0, 0.7); /* Change badge background */
}

.ad-nav-btn {
    background: rgba(255, 255, 255, 0.9); /* Change arrow background */
}
```

## Best Practices

### For Advertisers
1. **Clear Value Proposition**: Make the ad message clear
2. **Strong CTA**: Include "Shop Now", "Learn More", etc.
3. **High-Quality Images**: Professional photography
4. **Mobile-Friendly**: Ensure text is readable on mobile

### For Your Platform
1. **Relevant Ads**: Only show ads relevant to your audience
2. **Quality Control**: Review all ads before publishing
3. **Performance Tracking**: Monitor which ads perform best
4. **A/B Testing**: Test different ad positions and designs
5. **Transparency**: Clearly label sponsored content

## Advertiser Onboarding Process

1. **Initial Contact**
   - Email: ads@yourdomain.com
   - Contact form on website

2. **Requirements from Advertiser**
   - Ad image (550x450px)
   - Landing page URL
   - Campaign duration
   - Business information

3. **Contract & Payment**
   - Send advertising agreement
   - Collect payment (advance)
   - Issue invoice

4. **Implementation**
   - Upload ad image
   - Update index.php with ad link
   - Test functionality
   - Notify advertiser

5. **Reporting**
   - Weekly/Monthly reports
   - Impressions and clicks
   - Performance metrics

## Sample Advertiser Pitch

### Email Template:
```
Subject: Advertise on SastoMahango - Reach 10K+ Daily Shoppers

Dear [Business Name],

SastoMahango is Nepal's leading price comparison platform with over 10,000 daily visitors actively searching for products and services.

Our Advertisement Carousel Opportunity:
✓ Premium homepage placement
✓ 10,000+ daily impressions
✓ Targeted audience (price-conscious shoppers)
✓ Auto-rotating carousel for maximum visibility
✓ Mobile-optimized for on-the-go shoppers

Pricing:
- NPR 8,000/month (Position 1 - First Slide)
- NPR 6,000/month (Position 2 - Second Slide)
- NPR 5,000/month (Position 3 - Third Slide)

Special Launch Offer: 20% off first month!

Interested? Reply to schedule a call.

Best regards,
SastoMahango Advertising Team
```

## Troubleshooting

### Carousel Not Auto-Rotating
- Check browser console for JavaScript errors
- Ensure ad-carousel.js is loaded correctly
- Verify at least 2 slides exist

### Images Not Displaying
- Check file paths are correct
- Verify images exist in `assets/uploads/ads/`
- Check file permissions (readable)

### Navigation Not Working
- Ensure dots and arrows have correct event listeners
- Check for JavaScript conflicts
- Verify slide indices match dot data attributes

## Future Enhancements

Consider adding:
- Admin panel for ad management
- Automatic ad scheduling
- Performance dashboard
- A/B testing capabilities
- Video ad support
- Animated GIF support
- Geographic targeting
- Time-based ad rotation

## Support

For technical questions or assistance:
- Review this documentation
- Check browser console for errors
- Test on different devices
- Contact your developer

---

**Created**: December 2025
**Last Updated**: December 2025
**Version**: 1.0
