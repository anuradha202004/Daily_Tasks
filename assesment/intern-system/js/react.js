import React, { useState } from 'react';
import { ShoppingCart, Search, User, Menu, X, Heart, Star, ChevronRight, Package, CreditCard, Truck, Shield } from 'lucide-react';

const EasyCart = () => {
  const [cartItems, setCartItems] = useState([]);
  const [wishlist, setWishlist] = useState([]);
  const [currentPage, setCurrentPage] = useState('home');
  const [selectedCategory, setSelectedCategory] = useState('all');
  const [searchQuery, setSearchQuery] = useState('');
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const [showCheckout, setShowCheckout] = useState(false);

  const categories = [
    { id: 'all', name: 'All Products' },
    { id: 'electronics', name: 'Electronics' },
    { id: 'fashion', name: 'Fashion' },
    { id: 'home', name: 'Home & Living' },
    { id: 'sports', name: 'Sports & Outdoors' },
    { id: 'books', name: 'Books & Media' }
  ];

  const products = [
    { id: 1, name: 'Wireless Headphones', category: 'electronics', price: 89.99, rating: 4.5, reviews: 234, image: '🎧', stock: 45, description: 'Premium wireless headphones with noise cancellation' },
    { id: 2, name: 'Smart Watch', category: 'electronics', price: 199.99, rating: 4.7, reviews: 189, image: '⌚', stock: 30, description: 'Feature-rich smartwatch with health tracking' },
    { id: 3, name: 'Running Shoes', category: 'sports', price: 79.99, rating: 4.3, reviews: 456, image: '👟', stock: 120, description: 'Comfortable running shoes for all terrains' },
    { id: 4, name: 'Designer Handbag', category: 'fashion', price: 149.99, rating: 4.6, reviews: 98, image: '👜', stock: 25, description: 'Elegant designer handbag with premium materials' },
    { id: 5, name: 'Coffee Maker', category: 'home', price: 69.99, rating: 4.4, reviews: 312, image: '☕', stock: 55, description: 'Automatic coffee maker with programmable features' },
    { id: 6, name: 'Yoga Mat', category: 'sports', price: 29.99, rating: 4.5, reviews: 521, image: '🧘', stock: 200, description: 'Non-slip yoga mat with extra cushioning' },
    { id: 7, name: 'Bluetooth Speaker', category: 'electronics', price: 59.99, rating: 4.4, reviews: 267, image: '🔊', stock: 80, description: 'Portable Bluetooth speaker with rich sound' },
    { id: 8, name: 'Novel Collection', category: 'books', price: 24.99, rating: 4.8, reviews: 445, image: '📚', stock: 150, description: 'Bestselling novel collection bundle' },
    { id: 9, name: 'Desk Lamp', category: 'home', price: 39.99, rating: 4.2, reviews: 178, image: '💡', stock: 90, description: 'LED desk lamp with adjustable brightness' },
    { id: 10, name: 'Backpack', category: 'fashion', price: 54.99, rating: 4.5, reviews: 334, image: '🎒', stock: 65, description: 'Durable backpack with laptop compartment' },
    { id: 11, name: 'Fitness Tracker', category: 'sports', price: 49.99, rating: 4.3, reviews: 289, image: '⌚', stock: 110, description: 'Water-resistant fitness tracker with GPS' },
    { id: 12, name: 'Table Clock', category: 'home', price: 19.99, rating: 4.1, reviews: 156, image: '⏰', stock: 75, description: 'Modern table clock with alarm features' }
  ];

  const addToCart = (product) => {
    const existing = cartItems.find(item => item.id === product.id);
    if (existing) {
      setCartItems(cartItems.map(item =>
        item.id === product.id ? { ...item, quantity: item.quantity + 1 } : item
      ));
    } else {
      setCartItems([...cartItems, { ...product, quantity: 1 }]);
    }
  };

  const removeFromCart = (productId) => {
    setCartItems(cartItems.filter(item => item.id !== productId));
  };

  const updateQuantity = (productId, change) => {
    setCartItems(cartItems.map(item => {
      if (item.id === productId) {
        const newQty = item.quantity + change;
        return newQty > 0 ? { ...item, quantity: newQty } : item;
      }
      return item;
    }).filter(item => item.quantity > 0));
  };

  const toggleWishlist = (product) => {
    if (wishlist.find(item => item.id === product.id)) {
      setWishlist(wishlist.filter(item => item.id !== product.id));
    } else {
      setWishlist([...wishlist, product]);
    }
  };

  const filteredProducts = products.filter(product => {
    const matchesCategory = selectedCategory === 'all' || product.category === selectedCategory;
    const matchesSearch = product.name.toLowerCase().includes(searchQuery.toLowerCase());
    return matchesCategory && matchesSearch;
  });

  const cartTotal = cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
  const cartCount = cartItems.reduce((sum, item) => sum + item.quantity, 0);

  const ProductCard = ({ product }) => {
    const isInWishlist = wishlist.find(item => item.id === product.id);
    
    return (
      <div className="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow p-4">
        <div className="relative mb-4">
          <div className="text-6xl text-center mb-2">{product.image}</div>
          <button
            onClick={() => toggleWishlist(product)}
            className="absolute top-0 right-0 p-2 text-gray-400 hover:text-red-500"
          >
            <Heart className="h-5 w-5" fill={isInWishlist ? 'currentColor' : 'none'} />
          </button>
        </div>
        <h3 className="font-semibold text-lg mb-2 truncate">{product.name}</h3>
        <div className="flex items-center mb-2">
          <div className="flex items-center text-yellow-400">
            <Star className="h-4 w-4 fill-current" />
            <span className="ml-1 text-sm text-gray-600">{product.rating}</span>
          </div>
          <span className="ml-2 text-xs text-gray-500">({product.reviews} reviews)</span>
        </div>
        <p className="text-sm text-gray-600 mb-3">{product.description}</p>
        <div className="flex items-center justify-between">
          <span className="text-2xl font-bold text-blue-600">${product.price}</span>
          <button
            onClick={() => addToCart(product)}
            className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium"
          >
            Add to Cart
          </button>
        </div>
        <p className="text-xs text-gray-500 mt-2">Stock: {product.stock} units</p>
      </div>
    );
  };

  return (
    <div className="min-h-screen bg-gray-50">
      <header className="bg-white shadow-md sticky top-0 z-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex items-center justify-between h-16">
            <div className="flex items-center space-x-8">
              <h1 
                className="text-2xl font-bold text-blue-600 cursor-pointer"
                onClick={() => setCurrentPage('home')}
              >
                EasyCart
              </h1>
              <nav className="hidden md:flex space-x-6">
                <button onClick={() => setCurrentPage('home')} className="text-gray-700 hover:text-blue-600 font-medium">Home</button>
                <button onClick={() => setCurrentPage('products')} className="text-gray-700 hover:text-blue-600 font-medium">Products</button>
                <button onClick={() => setCurrentPage('about')} className="text-gray-700 hover:text-blue-600 font-medium">About</button>
                <button onClick={() => setCurrentPage('contact')} className="text-gray-700 hover:text-blue-600 font-medium">Contact</button>
              </nav>
            </div>

            <div className="flex items-center space-x-4">
              <div className="relative hidden md:block">
                <input
                  type="text"
                  placeholder="Search products..."
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  className="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-64"
                />
                <Search className="absolute left-3 top-2.5 h-5 w-5 text-gray-400" />
              </div>

              <button 
                onClick={() => setCurrentPage('wishlist')}
                className="relative p-2 text-gray-700 hover:text-red-500"
              >
                <Heart className="h-6 w-6" fill={wishlist.length > 0 ? 'currentColor' : 'none'} />
                {wishlist.length > 0 && (
                  <span className="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                    {wishlist.length}
                  </span>
                )}
              </button>

              <button 
                onClick={() => setCurrentPage('cart')}
                className="relative p-2 text-gray-700 hover:text-blue-600"
              >
                <ShoppingCart className="h-6 w-6" />
                {cartCount > 0 && (
                  <span className="absolute -top-1 -right-1 bg-blue-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                    {cartCount}
                  </span>
                )}
              </button>

              <button className="hidden md:flex items-center space-x-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <User className="h-5 w-5" />
                <span>Account</span>
              </button>

              <button 
                onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
                className="md:hidden p-2"
              >
                {mobileMenuOpen ? <X className="h-6 w-6" /> : <Menu className="h-6 w-6" />}
              </button>
            </div>
          </div>
        </div>

        {mobileMenuOpen && (
          <div className="md:hidden bg-white border-t">
            <nav className="px-4 py-2 space-y-2">
              <button onClick={() => { setCurrentPage('home'); setMobileMenuOpen(false); }} className="block w-full text-left py-2 text-gray-700">Home</button>
              <button onClick={() => { setCurrentPage('products'); setMobileMenuOpen(false); }} className="block w-full text-left py-2 text-gray-700">Products</button>
              <button onClick={() => { setCurrentPage('about'); setMobileMenuOpen(false); }} className="block w-full text-left py-2 text-gray-700">About</button>
              <button onClick={() => { setCurrentPage('contact'); setMobileMenuOpen(false); }} className="block w-full text-left py-2 text-gray-700">Contact</button>
            </nav>
          </div>
        )}
      </header>

      {currentPage === 'home' && (
        <div>
          <div className="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
              <div className="text-center">
                <h2 className="text-5xl font-bold mb-4">Welcome to EasyCart</h2>
                <p className="text-xl mb-8">Your Professional E-Commerce Solution</p>
                <button 
                  onClick={() => setCurrentPage('products')}
                  className="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 inline-flex items-center"
                >
                  Shop Now <ChevronRight className="ml-2 h-5 w-5" />
                </button>
              </div>
            </div>
          </div>

          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div className="grid md:grid-cols-4 gap-8 mb-16">
              <div className="text-center p-6 bg-white rounded-lg shadow-sm">
                <Truck className="h-12 w-12 text-blue-600 mx-auto mb-4" />
                <h3 className="font-semibold mb-2">Free Shipping</h3>
                <p className="text-gray-600 text-sm">On orders over $50</p>
              </div>
              <div className="text-center p-6 bg-white rounded-lg shadow-sm">
                <Shield className="h-12 w-12 text-blue-600 mx-auto mb-4" />
                <h3 className="font-semibold mb-2">Secure Payment</h3>
                <p className="text-gray-600 text-sm">100% secure transactions</p>
              </div>
              <div className="text-center p-6 bg-white rounded-lg shadow-sm">
                <Package className="h-12 w-12 text-blue-600 mx-auto mb-4" />
                <h3 className="font-semibold mb-2">Easy Returns</h3>
                <p className="text-gray-600 text-sm">30-day return policy</p>
              </div>
              <div className="text-center p-6 bg-white rounded-lg shadow-sm">
                <CreditCard className="h-12 w-12 text-blue-600 mx-auto mb-4" />
                <h3 className="font-semibold mb-2">Best Prices</h3>
                <p className="text-gray-600 text-sm">Competitive pricing guaranteed</p>
              </div>
            </div>

            <h3 className="text-3xl font-bold mb-8">Featured Products</h3>
            <div className="grid md:grid-cols-4 gap-6">
              {products.slice(0, 4).map(product => (
                <ProductCard key={product.id} product={product} />
              ))}
            </div>
          </div>
        </div>
      )}

      {currentPage === 'products' && (
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <h2 className="text-3xl font-bold mb-6">Our Products</h2>
          
          <div className="flex flex-wrap gap-2 mb-8">
            {categories.map(category => (
              <button
                key={category.id}
                onClick={() => setSelectedCategory(category.id)}
                className={`px-4 py-2 rounded-lg font-medium ${
                  selectedCategory === category.id
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                {category.name}
              </button>
            ))}
          </div>

          <div className="grid md:grid-cols-4 gap-6">
            {filteredProducts.map(product => (
              <ProductCard key={product.id} product={product} />
            ))}
          </div>

          {filteredProducts.length === 0 && (
            <div className="text-center py-12">
              <p className="text-gray-500 text-lg">No products found matching your criteria.</p>
            </div>
          )}
        </div>
      )}

      {currentPage === 'cart' && (
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <h2 className="text-3xl font-bold mb-6">Shopping Cart</h2>
          
          {cartItems.length === 0 ? (
            <div className="text-center py-12 bg-white rounded-lg shadow">
              <ShoppingCart className="h-24 w-24 text-gray-300 mx-auto mb-4" />
              <p className="text-gray-500 text-lg mb-4">Your cart is empty</p>
              <button
                onClick={() => setCurrentPage('products')}
                className="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700"
              >
                Continue Shopping
              </button>
            </div>
          ) : (
            <div className="grid lg:grid-cols-3 gap-8">
              <div className="lg:col-span-2 space-y-4">
                {cartItems.map(item => (
                  <div key={item.id} className="bg-white rounded-lg shadow p-6 flex items-center">
                    <div className="text-5xl mr-6">{item.image}</div>
                    <div className="flex-1">
                      <h3 className="font-semibold text-lg mb-1">{item.name}</h3>
                      <p className="text-gray-600 text-sm mb-2">{item.description}</p>
                      <p className="text-blue-600 font-bold text-xl">${item.price}</p>
                    </div>
                    <div className="flex items-center space-x-4">
                      <div className="flex items-center border rounded-lg">
                        <button
                          onClick={() => updateQuantity(item.id, -1)}
                          className="px-3 py-1 hover:bg-gray-100"
                        >
                          -
                        </button>
                        <span className="px-4 py-1 border-x">{item.quantity}</span>
                        <button
                          onClick={() => updateQuantity(item.id, 1)}
                          className="px-3 py-1 hover:bg-gray-100"
                        >
                          +
                        </button>
                      </div>
                      <button
                        onClick={() => removeFromCart(item.id)}
                        className="text-red-500 hover:text-red-700 font-medium"
                      >
                        Remove
                      </button>
                    </div>
                  </div>
                ))}
              </div>

              <div className="lg:col-span-1">
                <div className="bg-white rounded-lg shadow p-6 sticky top-20">
                  <h3 className="font-bold text-xl mb-4">Order Summary</h3>
                  <div className="space-y-2 mb-4 pb-4 border-b">
                    <div className="flex justify-between">
                      <span className="text-gray-600">Subtotal</span>
                      <span className="font-semibold">${cartTotal.toFixed(2)}</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-gray-600">Shipping</span>
                      <span className="font-semibold">{cartTotal > 50 ? 'Free' : '$5.99'}</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-gray-600">Tax</span>
                      <span className="font-semibold">${(cartTotal * 0.1).toFixed(2)}</span>
                    </div>
                  </div>
                  <div className="flex justify-between mb-6">
                    <span className="font-bold text-lg">Total</span>
                    <span className="font-bold text-xl text-blue-600">
                      ${(cartTotal + (cartTotal > 50 ? 0 : 5.99) + (cartTotal * 0.1)).toFixed(2)}
                    </span>
                  </div>
                  <button
                    onClick={() => setShowCheckout(true)}
                    className="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-semibold"
                  >
                    Proceed to Checkout
                  </button>
                </div>
              </div>
            </div>
          )}

          {showCheckout && (
            <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
              <div className="bg-white rounded-lg p-8 max-w-md w-full">
                <h3 className="text-2xl font-bold mb-4">Checkout</h3>
                <p className="text-gray-600 mb-6">
                  Your order has been placed successfully! Thank you for shopping with EasyCart.
                </p>
                <button
                  onClick={() => {
                    setShowCheckout(false);
                    setCartItems([]);
                    setCurrentPage('home');
                  }}
                  className="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-semibold"
                >
                  Continue Shopping
                </button>
              </div>
            </div>
          )}
        </div>
      )}

      {currentPage === 'wishlist' && (
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <h2 className="text-3xl font-bold mb-6">My Wishlist</h2>
          
          {wishlist.length === 0 ? (
            <div className="text-center py-12 bg-white rounded-lg shadow">
              <Heart className="h-24 w-24 text-gray-300 mx-auto mb-4" />
              <p className="text-gray-500 text-lg mb-4">Your wishlist is empty</p>
              <button
                onClick={() => setCurrentPage('products')}
                className="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700"
              >
                Browse Products
              </button>
            </div>
          ) : (
            <div className="grid md:grid-cols-4 gap-6">
              {wishlist.map(product => (
                <ProductCard key={product.id} product={product} />
              ))}
            </div>
          )}
        </div>
      )}

      {currentPage === 'about' && (
        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
          <h2 className="text-4xl font-bold mb-8 text-center">About EasyCart</h2>
          <div className="bg-white rounded-lg shadow-lg p-8">
            <p className="text-lg text-gray-700 mb-6">
              EasyCart is a professional e-commerce platform designed to provide seamless online shopping experiences for businesses and customers alike.
            </p>
            <h3 className="text-2xl font-semibold mb-4">Our Mission</h3>
            <p className="text-gray-700 mb-6">
              To empower businesses with cutting-edge e-commerce tools while delivering exceptional shopping experiences to customers.
            </p>
          </div>
        </div>
      )}

      {currentPage === 'contact' && (
        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
          <h2 className="text-4xl font-bold mb-8 text-center">Contact Us</h2>
          <div className="bg-white rounded-lg shadow-lg p-8">
            <h3 className="text-2xl font-semibold mb-6">Get in Touch</h3>
            <div className="space-y-4">
              <div>
                <p className="font-semibold">Email:</p>
                <p className="text-gray-700">support@easycart.com</p>
              </div>
              <div>
                <p className="font-semibold">Phone:</p>
                <p className="text-gray-700">+1 (555) 123-4567</p>
              </div>
            </div>
          </div>
        </div>
      )}

      <footer className="bg-gray-800 text-white py-8 mt-12">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <p className="text-gray-400">© 2026 EasyCart. All rights reserved.</p>
        </div>
      </footer>
    </div>
  );
};

export default EasyCart;