# 🛒 SastoMahango - Smart Grocery Price Comparison Platform

![HTML5](https://img.shields.io/badge/html5-%23E34F26.svg?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/css3-%231572B6.svg?style=for-the-badge&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/javascript-%23323330.svg?style=for-the-badge&logo=javascript&logoColor=%23F7DF1E)
![Firebase](https://img.shields.io/badge/firebase-%23039BE5.svg?style=for-the-badge&logo=firebase)
![Node.js](https://img.shields.io/badge/node.js-6DA55F?style=for-the-badge&logo=node.js&logoColor=white)
![Express.js](https://img.shields.io/badge/express.js-%23404d59.svg?style=for-the-badge&logo=express&logoColor=%2361DAFB)
![Git](https://img.shields.io/badge/git-%23F05033.svg?style=for-the-badge&logo=git&logoColor=white)

## 🌐 Live Demo

**[Visit SastoMahango Live →](https://sastomahango.gt.tc)**   *[sastomahango.com](https://sastomahango.gt.tc)**   

Experience the platform in action with real-time price data and full functionality.

---

> *"Sasto Mahango"* translates to **"Cheap & Expensive"** in Nepali - your ultimate tool for finding the best grocery deals in Kathmandu!

## 📋 Table of Contents
- [Overview](#-overview)
- [Key Features](#-key-features)
- [Technology Stack](#-technology-stack)
- [Project Structure](#-project-structure)
- [Getting Started](#-getting-started)
- [Usage Guide](#-usage-guide)
- [API Documentation](#-api-documentation)
- [Contributing](#-contributing)
- [License](#-license)
- [Contact](#-contact)

---

## 🎯 Overview

**SastoMahango** is a comprehensive web-based platform designed to help consumers in Kathmandu make informed grocery shopping decisions by comparing prices across multiple supermarkets. The platform features real-time price tracking, intelligent search capabilities, and detailed product information.

### 🌟 Why SastoMahango?

- 💰 **Save Money**: Compare prices across 6 major supermarkets in one place
- ⚡ **Save Time**: No need to visit multiple stores or websites
- 📊 **Smart Insights**: View price trends and get the best deals
- 🔍 **Easy Search**: Find products quickly with advanced filtering
- 📱 **Responsive Design**: Works seamlessly on all devices

---

## ✨ Key Features

### Core Functionality
- 🔍 **Advanced Product Search**
  - Real-time search with instant results
  - Smart filtering by category, price range, and availability
  - Autocomplete suggestions
  - Search history tracking

- 💵 **Price Comparison**
  - Side-by-side price comparison across 6 supermarkets
  - Visual indicators for best deals
  - Real-time price updates
  - Historical price tracking

- 🏪 **Multi-Store Coverage**
  - Bhatbhateni Supermarket
  - Salesberry
  - BigMart
  - NepMart
  - Local Organic Market
  - QuickMart Express

### User Experience
- 📱 **Responsive Design**
  - Mobile-first approach
  - Adaptive layouts for all screen sizes
  - Touch-friendly interfaces
  - Fast loading times

- 🎨 **Modern UI/UX**
  - Clean, intuitive interface
  - Smooth animations and transitions
  - Dark mode support
  - Accessibility features

- 📊 **Data Visualization**
  - Interactive price charts
  - Trend indicators
  - Comparative analytics
  - Visual price history

### Technical Features
- ⚡ **Performance Optimized**
  - Efficient data caching
  - Lazy loading
  - Optimized database queries
  - CDN integration

- 🔒 **Secure & Reliable**
  - Firebase Authentication
  - Secure data transmission
  - Regular backups
  - Error handling & logging

---

## 🛠 Technology Stack

### Frontend
- **HTML5**: Semantic markup and structure
- **CSS3**: Modern styling with animations
  - Flexbox & Grid layouts
  - Custom properties (CSS variables)
  - Media queries for responsiveness
- **JavaScript (ES6+)**: Interactive functionality
  - Async/await for API calls
  - DOM manipulation
  - Event handling
  - Local storage management

### Backend
- **Node.js**: Server-side JavaScript runtime
- **Express.js**: Web application framework
  - RESTful API endpoints
  - Middleware integration
  - Route handling
  - Error management

### Database & Services
- **Firebase**:
  - Firestore: NoSQL database
  - Authentication: User management
  - Hosting: Web deployment
  - Functions: Serverless backend

### Development Tools
- **Git**: Version control
- **npm**: Package management
- **Firebase CLI**: Deployment & management
- **VS Code**: Development environment

---

## 📁 Project Structure

```
Sasto_Mahango/
│
├── public/                  # Public assets
│   ├── css/
│   │   ├── styles.css      # Main stylesheet
│   │   ├── compare.css     # Comparison page styles
│   │   └── product.css     # Product detail styles
│   │
│   ├── js/
│   │   ├── app.js          # Main application logic
│   │   ├── compare.js      # Comparison functionality
│   │   ├── product.js      # Product detail handling
│   │   └── firebase-config.js  # Firebase configuration
│   │
│   ├── images/             # Image assets
│   │   ├── logos/          # Store logos
│   │   └── products/       # Product images
│   │
│   └── index.html          # Main landing page
│
├── server/                 # Backend server
│   ├── server.js           # Express server setup
│   ├── routes/             # API routes
│   │   ├── products.js     # Product endpoints
│   │   └── stores.js       # Store endpoints
│   │
│   └── middleware/         # Custom middleware
│       ├── auth.js         # Authentication
│       └── validator.js    # Input validation
│
├── firebase/               # Firebase configuration
│   ├── firestore.rules     # Security rules
│   └── functions/          # Cloud functions
│
├── docs/                   # Documentation
│   ├── API.md              # API documentation
│   ├── SETUP.md            # Setup instructions
│   └── CONTRIBUTING.md     # Contribution guidelines
│
├── .gitignore              # Git ignore rules
├── package.json            # Project dependencies
├── firebase.json           # Firebase configuration
└── README.md               # This file
```

---

## 🚀 Getting Started

### Prerequisites
- Node.js (v14 or higher)
- npm or yarn
- Firebase account
- Git

### Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/they-call-me-electronerd/Sasto_Mahango.git
   cd Sasto_Mahango
   ```

2. **Install Dependencies**
   ```bash
   npm install
   ```

3. **Firebase Setup**
   ```bash
   # Install Firebase CLI
   npm install -g firebase-tools
   
   # Login to Firebase
   firebase login
   
   # Initialize Firebase
   firebase init
   ```

4. **Environment Configuration**
   Create a `firebase-config.js` file in `public/js/`:
   ```javascript
   const firebaseConfig = {
     apiKey: "YOUR_API_KEY",
     authDomain: "YOUR_AUTH_DOMAIN",
     projectId: "YOUR_PROJECT_ID",
     storageBucket: "YOUR_STORAGE_BUCKET",
     messagingSenderId: "YOUR_MESSAGING_SENDER_ID",
     appId: "YOUR_APP_ID"
   };
   
   firebase.initializeApp(firebaseConfig);
   ```

5. **Start Development Server**
   ```bash
   npm start
   ```

6. **Access the Application**
   Open your browser and navigate to:
   ```
   http://localhost:3000
   ```

### Deployment

1. **Build for Production**
   ```bash
   npm run build
   ```

2. **Deploy to Firebase**
   ```bash
   firebase deploy
   ```

---

## 📖 Usage Guide

### For Users

#### Searching for Products
1. Enter product name in the search bar
2. Use filters to narrow down results:
   - Category selection
   - Price range
   - Store availability
3. View results in grid or list view
4. Click on products for detailed information

#### Comparing Prices
1. Select products you want to compare
2. Click "Compare" button
3. View side-by-side price comparison
4. See which store offers the best deal
5. Check price history and trends

#### Product Details
- View high-resolution product images
- Check availability across stores
- See detailed specifications
- Read product descriptions
- View price history charts

### For Developers

#### Adding New Products
```javascript
// Example: Adding a product via API
const newProduct = {
  name: "Product Name",
  category: "Category",
  prices: {
    bhatbhateni: 150,
    salesberry: 145,
    bigmart: 155
  },
  image: "image-url",
  description: "Product description"
};

fetch('/api/products', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify(newProduct)
});
```

#### Updating Prices
```javascript
// Update prices for a product
updateProductPrices(productId, {
  storeName: "bhatbhateni",
  newPrice: 145,
  timestamp: Date.now()
});
```

---

## 📡 API Documentation

### Endpoints

#### Products

**GET /api/products**
- Description: Get all products
- Query Parameters:
  - `category`: Filter by category
  - `minPrice`: Minimum price
  - `maxPrice`: Maximum price
  - `store`: Filter by store
- Response: Array of product objects

**GET /api/products/:id**
- Description: Get specific product
- Parameters: Product ID
- Response: Product object

**POST /api/products**
- Description: Create new product
- Body: Product object
- Response: Created product

**PUT /api/products/:id**
- Description: Update product
- Parameters: Product ID
- Body: Updated fields
- Response: Updated product

**DELETE /api/products/:id**
- Description: Delete product
- Parameters: Product ID
- Response: Success message

#### Stores

**GET /api/stores**
- Description: Get all stores
- Response: Array of store objects

**GET /api/stores/:id/products**
- Description: Get products from specific store
- Parameters: Store ID
- Response: Array of products

### Response Format

```json
{
  "success": true,
  "data": {
    "id": "product-id",
    "name": "Product Name",
    "category": "Category",
    "prices": {
      "bhatbhateni": 150,
      "salesberry": 145
    },
    "availability": ["bhatbhateni", "salesberry"],
    "lastUpdated": "2024-01-15T10:30:00Z"
  }
}
```

---

## 🤝 Contributing

We welcome contributions! Please follow these guidelines:

### How to Contribute

1. **Fork the Repository**
   ```bash
   git fork https://github.com/they-call-me-electronerd/Sasto_Mahango.git
   ```

2. **Create a Feature Branch**
   ```bash
   git checkout -b feature/AmazingFeature
   ```

3. **Make Your Changes**
   - Write clean, documented code
   - Follow existing code style
   - Add tests if applicable
   - Update documentation

4. **Commit Your Changes**
   ```bash
   git commit -m 'Add some AmazingFeature'
   ```

5. **Push to Branch**
   ```bash
   git push origin feature/AmazingFeature
   ```

6. **Open a Pull Request**
   - Provide clear description
   - Reference any related issues
   - Wait for review

### Code Style Guidelines
- Use meaningful variable names
- Comment complex logic
- Follow ES6+ standards
- Maintain consistent indentation
- Write descriptive commit messages

### Reporting Issues
- Use GitHub Issues
- Provide detailed description
- Include steps to reproduce
- Add screenshots if relevant
- Specify your environment

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

### MIT License Summary
- ✅ Commercial use
- ✅ Modification
- ✅ Distribution
- ✅ Private use
- ⚠️ Liability
- ⚠️ Warranty

---

## 📞 Contact

**Project Maintainer**: they-call-me-electronerd

- 🐙 GitHub: [@they-call-me-electronerd](https://github.com/they-call-me-electronerd)
- 📧 Email: [Your Email]
- 🌐 Website: [Your Website]

**Project Link**: [https://github.com/they-call-me-electronerd/Sasto_Mahango](https://github.com/they-call-me-electronerd/Sasto_Mahango)

---

## 🙏 Acknowledgments

- Firebase team for excellent backend services
- Open source community for valuable resources
- All contributors who help improve this project
- Users who provide feedback and suggestions

---

## 🗺 Roadmap

### Current Focus (v1.x)
- [x] Core price comparison functionality
- [x] Multi-store integration
- [x] Responsive design
- [x] Basic search and filtering

### Coming Soon (v2.0)
- [ ] User accounts and wishlists
- [ ] Price alerts and notifications
- [ ] Mobile application (React Native)
- [ ] Advanced analytics dashboard
- [ ] Store location mapping
- [ ] Shopping list features
- [ ] Recipe suggestions based on prices
- [ ] Social sharing capabilities

### Future Plans (v3.0+)
- [ ] AI-powered price predictions
- [ ] Barcode scanning
- [ ] Voice search
- [ ] Multilingual support
- [ ] Store partnerships
- [ ] Coupon and deal integration

---

<div align="center">

**Made with ❤️ in Nepal**

⭐ Star this repo if you find it helpful!

[Report Bug](https://github.com/they-call-me-electronerd/Sasto_Mahango/issues) · [Request Feature](https://github.com/they-call-me-electronerd/Sasto_Mahango/issues) · [Documentation](https://github.com/they-call-me-electronerd/Sasto_Mahango/wiki)

</div>
