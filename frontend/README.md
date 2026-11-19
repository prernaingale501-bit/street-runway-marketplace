# Street Runway Marketplace

Build a modern, premium, fully responsive multi-vendor fashion e-commerce marketplace website inspired by https://samluvcart.com.

PROJECT NAME:
Street Runway
PROJECT OBJECTIVE:
Create a complete frontend for a multi-vendor fashion marketplace where customers can shop from multiple vendors, vendors can register and sell their products, and administrators can manage the marketplace.

TECHNOLOGY:
- React.js with Vite
- TypeScript
- Tailwind CSS
- Shadcn UI
- Lucide React icons
- React Router
- Context API or Zustand for state management
- Use reusable components and a clean folder structure.

DESIGN REQUIREMENTS:
Create an elegant, modern, premium fashion shopping experience.
Take design inspiration from SamLuvCart, Myntra, Ajio, and modern fashion marketplaces.

Design style:
- Minimal and premium UI.
- White and soft neutral backgrounds.
- Black typography with elegant accent colors.
- Beautiful fashion product photography.
- Modern typography.
- Smooth hover effects and transitions.
- Attractive product cards.
- Responsive layouts for desktop, tablet, and mobile.
- Professional navigation and footer.

IMPORTANT: Do not create a generic dashboard template. This should look like a real fashion shopping marketplace.

PAGES AND FEATURES:

1. HOMEPAGE
- Top announcement bar for offers.
- Header with logo, search bar, account, wishlist, and shopping cart.
- Navigation menu:
  Women, Men, Kids, Footwear, Accessories, Beauty, New Arrivals, Sale.
- Large promotional hero banner.
- Shop by category section.
- Trending products.
- New arrivals.
- Featured brands.
- Best-selling products.
- Featured vendors.
- Promotional banners.
- Customer testimonials.
- Newsletter subscription.
- Professional footer.

2. PRODUCT LISTING PAGE
- Product grid with product images.
- Product name, price, discounted price, and discount percentage.
- Product ratings.
- Filters:
  Category, Brand, Size, Color, Price, Gender, Discount.
- Sorting:
  Popularity, Newest, Price Low to High, Price High to Low.
- Pagination or load more.
- Wishlist button.

3. PRODUCT DETAILS PAGE
- Multiple product images.
- Product name and description.
- Price and discount.
- Size selection.
- Color selection.
- Quantity selector.
- Add to Cart.
- Buy Now.
- Add to Wishlist.
- Product specifications.
- Return policy.
- Delivery availability / PIN code.
- Vendor name and vendor profile link.
- Related products.
- Customer reviews.

4. CUSTOMER AUTHENTICATION
- Customer registration.
- Login.
- Forgot password.
- Profile management.
- Address management.
- Order history.
- Wishlist.
- Logout.

5. SHOPPING CART
- Cart items.
- Product image.
- Size and color.
- Quantity update.
- Remove product.
- Price summary.
- Coupon code.
- Shipping charges.
- Total amount.
- Proceed to checkout.

6. CHECKOUT
- Customer address.
- Shipping information.
- Order summary.
- Coupon application.
- Payment method selection.
- Place order button.
- Order success page.

7. VENDOR REGISTRATION PAGE (VERY IMPORTANT)
Create a dedicated vendor registration page.

Registration form fields:
- Full Name
- Store Name
- Email Address
- Mobile Number
- Password
- Confirm Password
- Business Type
- Business Address
- City
- State
- Country
- PIN Code
- GST Number (optional)
- PAN Number
- Bank Account Details (collect securely through the backend workflow, not as plain frontend data)
- Store Description
- Product Categories
- Upload Business Logo
- Upload GST Certificate (optional)
- Upload Identity Proof
- Accept Terms and Conditions.

After submission, display:
"Your vendor application has been submitted successfully. Our team will review your application. You will receive an email once your account is approved."

Vendor application status UI:
- Pending Approval
- Approved
- Rejected

Do not allow vendors to access selling features before approval.

8. VENDOR DASHBOARD UI
Create a separate vendor dashboard with:
- Overview and sales statistics.
- Total Orders.
- Total Revenue.
- Total Products.
- Pending Orders.
- Product Management.
- Add New Product.
- Edit Product.
- Delete Product.
- Product Images.
- Product Variants (size, color).
- Inventory Management.
- Order Management.
- Shipping Status.
- Sales Reports.
- Store Profile.
- Vendor Profile Settings.
- Earnings.
- Payout History.

Show an application-pending screen if the vendor has not been approved.

9. VENDOR STORE PAGE
- Vendor logo.
- Store name.
- Store description.
- Vendor product listings.
- Ratings.
- Available categories.
- Follow/store information UI.

10. CUSTOMER ACCOUNT DASHBOARD
- Profile.
- My Orders.
- Wishlist.
- Addresses.
- Account settings.
- Return requests.

11. STATIC INFORMATION PAGES
- About Us.
- Contact Us.
- Privacy Policy.
- Terms and Conditions.
- Shipping Policy.
- Return and Refund Policy.
- FAQ.

12. ADMIN DASHBOARD UI
Create a professional admin dashboard interface with:
- Total Sales.
- Total Orders.
- Total Customers.
- Total Vendors.
- Pending Vendor Applications.
- Total Products.
- Revenue Analytics.
- Recent Orders.
- Recent Vendor Registrations.

Admin management screens:
- Vendor Applications.
- View Vendor Details.
- Approve Vendor.
- Reject Vendor.
- Suspend Vendor.
- Customer Management.
- Product Management.
- Category Management.
- Brand Management.
- Order Management.
- Coupon Management.
- Banner Management.
- Commission Settings.
- Payment and Payout Management.

IMPORTANT VENDOR APPROVAL WORKFLOW:
A vendor must register first.
The application status must initially be PENDING.
The admin must be able to review the vendor application and approve or reject it.
Only APPROVED vendors can access product creation, inventory, orders, and sales features.

FRONTEND DEVELOPMENT REQUIREMENTS:
- Use realistic fashion product demo data.
- Use high-quality fashion images.
- Every navigation link should work.
- All buttons should have meaningful interactions.
- Search should work with demo product data.
- Filters should work.
- Cart should work using frontend state.
- Wishlist should work.
- Registration forms should have validation.
- Use reusable components.
- Use proper loading, empty, and error states.
- Use toast notifications.
- Avoid dead buttons and placeholder interactions.

API INTEGRATION PREPARATION:
The backend will be developed separately in Laravel.

Create a centralized API service layer.
Use environment variables for the API base URL.
Create modular services for:
- Authentication
- Products
- Categories
- Vendors
- Cart
- Orders
- Wishlist
- Payments
- Admin

For now, use mock data where the backend is not connected.

Create an .env.example file with:
VITE_API_BASE_URL=http://localhost:8000/api

Do not hardcode API URLs throughout components.

DELIVERABLE:
Generate the complete frontend application with all pages, routing, reusable components, dashboards, responsive layouts, and demo interactions.

Prioritize the homepage, product browsing, product details, vendor registration, vendor dashboard, and admin dashboard.

Make this look like a production-quality fashion marketplace rather than a simple demo website.

Keep the frontend architecture ready for integration with a Laravel REST API.

This project was built with [Lovable](https://lovable.dev).

## Build with Lovable

Continue developing this project in the [Lovable editor](https://lovable.dev/projects/836483c7-3c0b-4f59-847e-6cc80de25946).

- **Ship faster**: describe what you want to build and Lovable handles the code.
- **Stay in sync**: every change made in Lovable is committed straight to this repository.
- **Full ownership**: this code is yours. Push to `main` on GitHub and your changes sync back into Lovable, ready for your next prompt.

## Development

Prefer working locally? You need Node.js and npm — [install with nvm](https://github.com/nvm-sh/nvm#installing-and-updating).

```sh
git clone <this-repository-url>
cd <repository-name>
npm i
npm run dev
```
