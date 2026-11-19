// Shared domain types. Shapes mirror the planned Laravel REST API resources.

export type Gender = "women" | "men" | "kids" | "unisex";
export type CategorySlug = "women" | "men" | "kids" | "footwear" | "accessories" | "beauty";
export type ProductTag = "new" | "trending" | "bestseller" | "sale";

export interface ProductColor {
  name: string;
  hex: string;
}

export interface Product {
  id: string;
  slug: string;
  name: string;
  brand: string;
  vendorId: string;
  category: CategorySlug;
  subcategory: string;
  gender: Gender;
  /** Selling price in INR */
  price: number;
  /** Maximum retail price in INR (before discount) */
  mrp: number;
  rating: number;
  reviewCount: number;
  images: string[];
  colors: ProductColor[];
  sizes: string[];
  description: string;
  highlights: string[];
  specs: Record<string, string>;
  tags: ProductTag[];
  createdAt: string;
  popularity: number;
  stock: number;
}

export interface Category {
  slug: CategorySlug;
  name: string;
  tagline: string;
  image: string;
  subcategories: string[];
}

export interface Brand {
  slug: string;
  name: string;
  tagline: string;
}

export interface Vendor {
  id: string;
  slug: string;
  storeName: string;
  ownerName: string;
  email: string;
  city: string;
  state: string;
  description: string;
  rating: number;
  reviewCount: number;
  followers: number;
  categories: CategorySlug[];
  joinedAt: string;
  cover: string;
  status: "active" | "suspended";
}

export interface Review {
  id: string;
  productId: string;
  author: string;
  rating: number;
  title: string;
  body: string;
  date: string;
  verified: boolean;
  size?: string;
}

export interface Testimonial {
  name: string;
  city: string;
  quote: string;
  rating: number;
}

export interface CartItem {
  key: string;
  productId: string;
  slug: string;
  name: string;
  brand: string;
  image: string;
  price: number;
  mrp: number;
  size: string;
  color: string;
  quantity: number;
  vendorId: string;
}

export interface Address {
  id: string;
  label: string;
  fullName: string;
  phone: string;
  line1: string;
  line2?: string;
  city: string;
  state: string;
  pincode: string;
  isDefault: boolean;
}

export type OrderStatus = "placed" | "confirmed" | "shipped" | "out_for_delivery" | "delivered" | "cancelled" | "returned";
export type PaymentMethod = "upi" | "card" | "netbanking" | "cod";

export interface OrderItem {
  productId: string;
  name: string;
  image: string;
  size: string;
  color: string;
  quantity: number;
  price: number;
  vendorId: string;
}

export interface Order {
  id: string;
  customerName: string;
  customerEmail: string;
  items: OrderItem[];
  subtotal: number;
  discount: number;
  shipping: number;
  total: number;
  couponCode?: string;
  paymentMethod: PaymentMethod;
  status: OrderStatus;
  address: Omit<Address, "id" | "isDefault" | "label">;
  createdAt: string;
}

export interface Coupon {
  code: string;
  description: string;
  type: "percent" | "flat";
  value: number;
  minOrder: number;
  maxDiscount?: number;
  active: boolean;
  expiresAt: string;
}

export type UserRole = "customer" | "vendor" | "admin";

export interface User {
  id: string;
  name: string;
  email: string;
  phone?: string;
  role: UserRole;
  /** For vendor users: their application id */
  vendorApplicationId?: string;
}

export type VendorApplicationStatus = "pending" | "approved" | "rejected" | "suspended";

export interface VendorApplication {
  id: string;
  fullName: string;
  storeName: string;
  email: string;
  mobile: string;
  businessType: string;
  businessAddress: string;
  city: string;
  state: string;
  country: string;
  pincode: string;
  gstNumber?: string;
  panNumber: string;
  storeDescription: string;
  categories: CategorySlug[];
  documents: { logo?: string; gstCertificate?: string; identityProof?: string };
  status: VendorApplicationStatus;
  submittedAt: string;
  reviewedAt?: string;
  rejectionReason?: string;
  /** Set once approved: links to the public vendor profile */
  vendorId?: string;
}
