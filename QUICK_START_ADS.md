# 🎯 Ad Carousel Quick Start

## ✅ What's Done
Your landing page now has a professional advertisement carousel instead of the illustration! 

## 🚀 See It Live
1. Open your browser
2. Go to: `http://localhost/MulyaSuchi/public/index.php`
3. You'll see the ad carousel on the right side of the hero section

## 🎨 Current State
- ✅ 3 placeholder ads (orange, blue, green SVG graphics)
- ✅ Auto-rotation every 5 seconds
- ✅ Navigation arrows (hover to see)
- ✅ Dot indicators at bottom
- ✅ "Sponsored" badge on each ad
- ✅ Fully responsive

## 📝 To Replace Ads (3 Easy Steps)

### Step 1: Prepare Your Images
- Size: 550px wide × 450px tall
- Format: JPG or PNG
- Keep under 500KB
- High quality

### Step 2: Upload Images
Place images here:
```
assets/uploads/ads/ad-1.jpg   (your first ad)
assets/uploads/ads/ad-2.jpg   (your second ad)  
assets/uploads/ads/ad-3.jpg   (your third ad)
```

### Step 3: Update Links
Open: `public/index.php` (line ~113)

Change:
```php
<a href="#" target="_blank" ...>
```

To:
```php
<a href="https://advertiser-website.com" target="_blank" ...>
```

That's it! Refresh your page and the real ads will appear.

## 💰 Start Making Money

### Pricing Suggestion
- Position 1: NPR 8,000/month
- Position 2: NPR 6,000/month  
- Position 3: NPR 5,000/month

**Total Potential: NPR 19,000/month (~$140 USD)**

### Who to Contact
- Local retailers
- E-commerce websites
- Delivery services
- Banks/Financial services
- Mobile/Electronics shops
- Food brands
- Agricultural suppliers

### Email Template (Copy & Send)
```
Subject: Advertise to 10,000+ Daily Shoppers

Hi [Business Name],

Would you like to reach 10,000+ people daily who are actively 
searching for products and prices?

SastoMahango offers premium homepage advertisement slots:
✓ Prominent carousel placement
✓ Auto-rotating for maximum visibility
✓ Mobile-optimized
✓ Starting at NPR 5,000/month

Interested? Let's talk!

[Your Name]
SastoMahango Team
```

## 🎛️ Features

### Auto Features
- ✨ Rotates automatically (5 seconds per ad)
- ⏸️ Pauses when you hover
- 📱 Adapts to all screen sizes
- ⌨️ Arrow keys work for navigation

### Analytics
- 📊 Tracks when ads are shown (impressions)
- 🖱️ Tracks when ads are clicked
- 📈 Check browser console to see logs

## 🔧 Customization

### Change Speed
File: `assets/js/components/ad-carousel.js` (line 11)
```javascript
autoPlayInterval: 5000,  // Change to 7000 for 7 seconds
```

### Add 4th Ad
1. Add slide in `index.php`:
```php
<div class="ad-slide">
    <a href="https://example.com" target="_blank" rel="noopener noreferrer" class="ad-link">
        <img src="<?php echo rtrim(SITE_URL, '/'); ?>/assets/uploads/ads/ad-4.jpg" 
             alt="Advertisement 4" 
             class="ad-image">
        <div class="ad-badge">
            <i class="bi bi-badge-ad"></i> Sponsored
        </div>
    </a>
</div>
```

2. Add dot:
```php
<button class="ad-dot" data-slide="3" aria-label="Go to ad 4"></button>
```

## 📚 Full Documentation
- **Complete Guide**: `docs/AD_CAROUSEL_GUIDE.md`
- **Implementation Summary**: `docs/AD_CAROUSEL_IMPLEMENTATION.md`
- **Image Guidelines**: `assets/uploads/ads/README.md`

## ✨ Test Checklist
- [ ] Visit homepage - see carousel?
- [ ] Wait 5 seconds - does it auto-rotate?
- [ ] Hover over carousel - does it pause?
- [ ] Click arrows - does it navigate?
- [ ] Click dots - does it jump to that ad?
- [ ] Check on mobile device
- [ ] Test ad links work

## 🆘 Problems?

**Carousel not showing?**
- Clear browser cache (Ctrl+F5)
- Check browser console for errors (F12)

**Images not loading?**
- Check file paths are correct
- Verify images exist in ads folder

**Not auto-rotating?**
- Check JavaScript console for errors
- Ensure ad-carousel.js is loading

## 📞 Questions?
Check the full documentation in `docs/AD_CAROUSEL_GUIDE.md`

---

**You're all set! Start monetizing your platform! 🎉**

Replace the placeholder images, contact advertisers, and start earning revenue!
