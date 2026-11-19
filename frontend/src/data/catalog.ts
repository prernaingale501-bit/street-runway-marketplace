import type { Brand, Category, Testimonial, Vendor, Coupon } from "@/types";
import { categoryImages, img } from "./images";

export const categories: Category[] = [
  { slug: "women", name: "Women", tagline: "Dresses, co-ords & ethnic edits", image: categoryImages.women, subcategories: ["Dresses", "Tops", "Blazers", "Ethnic Wear", "Trousers"] },
  { slug: "men", name: "Men", tagline: "Shirts, outerwear & streetwear", image: categoryImages.men, subcategories: ["Shirts", "T-Shirts", "Jackets", "Trousers"] },
  { slug: "kids", name: "Kids", tagline: "Playful, soft, made to move", image: categoryImages.kids, subcategories: ["Co-ord Sets", "Dresses"] },
  { slug: "footwear", name: "Footwear", tagline: "Sneakers, heels & loafers", image: categoryImages.footwear, subcategories: ["Sneakers", "Heels", "Loafers"] },
  { slug: "accessories", name: "Accessories", tagline: "Bags, eyewear & watches", image: categoryImages.accessories, subcategories: ["Bags", "Sunglasses", "Watches"] },
  { slug: "beauty", name: "Beauty", tagline: "Lip, fragrance & rituals", image: categoryImages.beauty, subcategories: ["Lipstick", "Fragrance"] },
];

export const brands: Brand[] = [
  { slug: "maison-kaya", name: "Maison Kaya", tagline: "Quiet luxury, made in Jaipur" },
  { slug: "noir-atelier", name: "Noir Atelier", tagline: "Sharp tailoring for every day" },
  { slug: "saffron-thread", name: "Saffron Thread", tagline: "Hand-embroidered heritage" },
  { slug: "urban-loom", name: "Urban Loom", tagline: "Linen-first menswear" },
  { slug: "kickstreet", name: "Kickstreet", tagline: "Sneaker culture, refined" },
  { slug: "little-lark", name: "Little Lark", tagline: "Organic cotton for kids" },
  { slug: "aurum-co", name: "Aurum & Co", tagline: "Accessories with heirloom intent" },
  { slug: "bloom-beauty", name: "Bloom Beauty", tagline: "Clean beauty, bold colour" },
];

export const vendors: Vendor[] = [
  { id: "v1", slug: "the-linen-edit", storeName: "The Linen Edit", ownerName: "Aanya Mehra", email: "aanya@linenedit.in", city: "Jaipur", state: "Rajasthan", description: "Breathable, hand-finished linen essentials designed for Indian summers. Every piece is cut in small batches in our Jaipur studio.", rating: 4.7, reviewCount: 1840, followers: 12400, categories: ["women", "men"], joinedAt: "2023-02-14", cover: img.linenDress, status: "active" },
  { id: "v2", slug: "noir-atelier", storeName: "Noir Atelier", ownerName: "Kabir Malhotra", email: "kabir@noiratelier.in", city: "Mumbai", state: "Maharashtra", description: "Modern tailoring and monochrome wardrobe staples — structured blazers, relaxed trousers and statement outerwear.", rating: 4.6, reviewCount: 960, followers: 8900, categories: ["women", "men"], joinedAt: "2023-06-02", cover: img.blackBlazer, status: "active" },
  { id: "v3", slug: "saffron-thread-house", storeName: "Saffron Thread House", ownerName: "Meher Siddiqui", email: "meher@saffronthread.in", city: "Lucknow", state: "Uttar Pradesh", description: "Chikankari and zari craft reimagined for the modern wardrobe, working with 120+ artisan families across Awadh.", rating: 4.8, reviewCount: 2310, followers: 21000, categories: ["women"], joinedAt: "2022-11-20", cover: img.ivoryKurta, status: "active" },
  { id: "v4", slug: "kickstreet", storeName: "Kickstreet", ownerName: "Rohan Iyer", email: "rohan@kickstreet.in", city: "Bengaluru", state: "Karnataka", description: "Sneakers, loafers and streetwear for people who live on their feet. Designed in Bengaluru, built to last.", rating: 4.5, reviewCount: 3120, followers: 18700, categories: ["footwear", "men"], joinedAt: "2023-01-09", cover: img.sneakers, status: "active" },
  { id: "v5", slug: "little-lark-kids", storeName: "Little Lark Kids", ownerName: "Priya Nair", email: "priya@littlelark.in", city: "Pune", state: "Maharashtra", description: "GOTS-certified organic cotton clothing for little ones, in soft colours and easy, play-ready cuts.", rating: 4.9, reviewCount: 740, followers: 5600, categories: ["kids"], joinedAt: "2024-03-18", cover: img.kidsDress, status: "active" },
  { id: "v6", slug: "aurum-accessories", storeName: "Aurum Accessories", ownerName: "Ishaan Kapoor", email: "ishaan@aurum.in", city: "New Delhi", state: "Delhi", description: "Full-grain leather bags, acetate eyewear and minimalist watches — finishing touches made to be passed down.", rating: 4.6, reviewCount: 1280, followers: 9800, categories: ["accessories", "footwear"], joinedAt: "2023-08-25", cover: img.leatherTote, status: "active" },
  { id: "v7", slug: "bloom-beauty-lab", storeName: "Bloom Beauty Lab", ownerName: "Tara D'Souza", email: "tara@bloombeauty.in", city: "Panaji", state: "Goa", description: "Clean, cruelty-free colour cosmetics and fragrances, formulated in Goa with botanical extracts.", rating: 4.7, reviewCount: 1560, followers: 14300, categories: ["beauty"], joinedAt: "2023-10-11", cover: img.perfume, status: "active" },
];

export const testimonials: Testimonial[] = [
  { name: "Riya Sharma", city: "Delhi", quote: "The linen dress fits like it was made for me. Packaging felt like opening a gift — Street Runway is now my first stop.", rating: 5 },
  { name: "Arjun Menon", city: "Kochi", quote: "Love discovering independent labels I would never find elsewhere. Delivery was a day early, and returns were effortless.", rating: 5 },
  { name: "Sneha Kulkarni", city: "Pune", quote: "Finally a marketplace that feels curated, not cluttered. The kurta set quality is exceptional for the price.", rating: 4 },
  { name: "Vikram Rao", city: "Hyderabad", quote: "Ordered sneakers and a tote from two different stores — one checkout, one delivery update thread. Seamless.", rating: 5 },
];

export const coupons: Coupon[] = [
  { code: "WELCOME10", description: "10% off your first order (up to ₹500)", type: "percent", value: 10, minOrder: 0, maxDiscount: 500, active: true, expiresAt: "2027-03-31" },
  { code: "RUNWAY20", description: "20% off on orders above ₹2,999 (up to ₹1,500)", type: "percent", value: 20, minOrder: 2999, maxDiscount: 1500, active: true, expiresAt: "2027-01-31" },
  { code: "FLAT300", description: "Flat ₹300 off on orders above ₹1,499", type: "flat", value: 300, minOrder: 1499, active: true, expiresAt: "2026-12-31" },
  { code: "FESTIVE15", description: "15% off festive edit (expired)", type: "percent", value: 15, minOrder: 999, maxDiscount: 800, active: false, expiresAt: "2026-10-31" },
];

export const announcements = [
  "Free shipping on orders above ₹999",
  "Use code WELCOME10 for 10% off your first order",
  "Easy 15-day returns on all fashion",
  "200+ independent labels, one checkout",
];

export const navLinks = [
  { label: "Women", search: { gender: "women" as const } },
  { label: "Men", search: { gender: "men" as const } },
  { label: "Kids", search: { gender: "kids" as const } },
  { label: "Footwear", search: { category: "footwear" } },
  { label: "Accessories", search: { category: "accessories" } },
  { label: "Beauty", search: { category: "beauty" } },
  { label: "New Arrivals", search: { tag: "new" as const } },
  { label: "Sale", search: { tag: "sale" as const }, highlight: true },
];
