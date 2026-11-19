import type { Product, Review } from "@/types";
import { img } from "./images";

const C = {
  black: { name: "Black", hex: "#1c1a18" },
  ivory: { name: "Ivory", hex: "#f1ece1" },
  beige: { name: "Beige", hex: "#d8c7ad" },
  white: { name: "White", hex: "#fafafa" },
  olive: { name: "Olive", hex: "#5b5e3a" },
  charcoal: { name: "Charcoal", hex: "#4a4a4a" },
  sage: { name: "Sage", hex: "#9aab8e" },
  rose: { name: "Rose", hex: "#c98a96" },
  tan: { name: "Tan", hex: "#a8764b" },
  navy: { name: "Navy", hex: "#22304a" },
  terracotta: { name: "Terracotta", hex: "#b25d3c" },
  champagne: { name: "Champagne", hex: "#e3cfb0" },
  gold: { name: "Gold", hex: "#c9a35a" },
  red: { name: "Red", hex: "#a3202b" },
};

const APPAREL = ["XS", "S", "M", "L", "XL"];
const MEN = ["S", "M", "L", "XL", "XXL"];
const KIDS = ["2-3Y", "4-5Y", "6-7Y", "8-9Y"];
const SHOES = ["UK 5", "UK 6", "UK 7", "UK 8", "UK 9", "UK 10"];

type Seed = Omit<Product, "id" | "slug" | "specs" | "highlights"> & {
  specs?: Record<string, string>;
  highlights?: string[];
};

const slugify = (s: string) =>
  s.toLowerCase().replace(/&/g, "and").replace(/[^a-z0-9]+/g, "-").replace(/(^-|-$)/g, "");

const seeds: Seed[] = [
  // Women
  { name: "Linen Wrap Midi Dress", brand: "Maison Kaya", vendorId: "v1", category: "women", subcategory: "Dresses", gender: "women", price: 2499, mrp: 3999, rating: 4.6, reviewCount: 412, images: [img.linenDress, img.satinCami, img.blackBlazer], colors: [C.beige, C.ivory, C.terracotta], sizes: APPAREL, description: "A softly structured wrap dress in pure European linen with flutter sleeves and a self-tie waist. Breathable, relaxed and endlessly wearable from brunch to sundown.", tags: ["trending", "bestseller", "sale"], createdAt: "2026-10-02", popularity: 98, stock: 64, specs: { Fabric: "100% Linen", Fit: "Relaxed", Length: "Midi", Neck: "V-Neck", Care: "Gentle machine wash" } },
  { name: "Oversized Tailored Blazer", brand: "Noir Atelier", vendorId: "v2", category: "women", subcategory: "Blazers", gender: "women", price: 4299, mrp: 5999, rating: 4.7, reviewCount: 268, images: [img.blackBlazer, img.satinCami, img.linenDress], colors: [C.black, C.charcoal, C.beige], sizes: APPAREL, description: "A boxy single-breasted blazer with sharp padded shoulders and a fluid drape. Wear it buttoned over a camisole or open with denim.", tags: ["trending", "new"], createdAt: "2026-11-08", popularity: 91, stock: 32, specs: { Fabric: "Poly-viscose suiting", Fit: "Oversized", Lining: "Half-lined", Closure: "Single button", Care: "Dry clean only" } },
  { name: "Chikankari Kurta Set with Dupatta", brand: "Saffron Thread", vendorId: "v3", category: "women", subcategory: "Ethnic Wear", gender: "women", price: 3899, mrp: 6499, rating: 4.8, reviewCount: 821, images: [img.ivoryKurta, img.linenDress, img.satinCami], colors: [C.ivory, C.rose, C.sage], sizes: APPAREL, description: "Hand-embroidered Lucknowi chikankari on soft cotton-chanderi, paired with straight trousers and an organza dupatta finished with zari borders.", tags: ["bestseller", "sale", "trending"], createdAt: "2026-09-14", popularity: 99, stock: 48, specs: { Fabric: "Cotton chanderi", Work: "Hand chikankari", Set: "Kurta, trousers & dupatta", Fit: "Straight", Care: "Hand wash cold" } },
  { name: "Silk Satin Camisole", brand: "Maison Kaya", vendorId: "v1", category: "women", subcategory: "Tops", gender: "women", price: 1599, mrp: 2299, rating: 4.4, reviewCount: 190, images: [img.satinCami, img.linenDress, img.blackBlazer], colors: [C.champagne, C.black, C.rose], sizes: APPAREL, description: "A bias-cut camisole in liquid satin with adjustable spaghetti straps and a cowl-inspired V neckline. Layer under tailoring or wear solo.", tags: ["new"], createdAt: "2026-11-12", popularity: 76, stock: 90, specs: { Fabric: "Satin (viscose blend)", Fit: "Regular", Neck: "V-Neck", Care: "Hand wash" } },
  { name: "Wide-Leg Pleated Trousers", brand: "Noir Atelier", vendorId: "v2", category: "women", subcategory: "Trousers", gender: "women", price: 2799, mrp: 3499, rating: 4.5, reviewCount: 143, images: [img.blackBlazer, img.satinCami], colors: [C.black, C.ivory], sizes: APPAREL, description: "High-rise, floor-grazing trousers with double front pleats and pressed creases for a long, lean line.", tags: ["new"], createdAt: "2026-11-01", popularity: 70, stock: 41 },
  { name: "Linen Co-ord Shirt & Shorts", brand: "Maison Kaya", vendorId: "v1", category: "women", subcategory: "Tops", gender: "women", price: 2199, mrp: 3299, rating: 4.3, reviewCount: 98, images: [img.linenDress, img.satinCami], colors: [C.beige, C.white], sizes: APPAREL, description: "An easy two-piece in washed linen — a boxy resort shirt and elasticated shorts made for slow summer days.", tags: ["sale"], createdAt: "2026-08-20", popularity: 64, stock: 22 },
  { name: "Zari Border Anarkali Kurta", brand: "Saffron Thread", vendorId: "v3", category: "women", subcategory: "Ethnic Wear", gender: "women", price: 4599, mrp: 6999, rating: 4.7, reviewCount: 356, images: [img.ivoryKurta, img.satinCami], colors: [C.ivory, C.terracotta], sizes: APPAREL, description: "A flared anarkali with delicate gota and zari borders, crafted for festive evenings and intimate celebrations.", tags: ["trending", "sale"], createdAt: "2026-10-18", popularity: 88, stock: 18 },
  // Men
  { name: "Relaxed Pure Linen Shirt", brand: "Urban Loom", vendorId: "v1", category: "men", subcategory: "Shirts", gender: "men", price: 1899, mrp: 2799, rating: 4.6, reviewCount: 533, images: [img.menLinenShirt, img.menTrousers, img.menBomber], colors: [C.white, C.beige, C.sage], sizes: MEN, description: "A relaxed-fit shirt in garment-washed linen with a soft spread collar and mother-of-pearl buttons. Gets better with every wash.", tags: ["bestseller", "trending", "sale"], createdAt: "2026-09-05", popularity: 96, stock: 120, specs: { Fabric: "100% Linen", Fit: "Relaxed", Collar: "Spread", Sleeve: "Full", Care: "Machine wash" } },
  { name: "Nylon Flight Bomber Jacket", brand: "Noir Atelier", vendorId: "v2", category: "men", subcategory: "Jackets", gender: "men", price: 3999, mrp: 5499, rating: 4.5, reviewCount: 211, images: [img.menBomber, img.menGraphicTee, img.menLinenShirt], colors: [C.olive, C.black, C.navy], sizes: MEN, description: "A classic MA-1 silhouette in water-resistant nylon with ribbed trims, a utility sleeve pocket and a lightly padded body.", tags: ["trending", "new"], createdAt: "2026-11-10", popularity: 90, stock: 36, specs: { Fabric: "Nylon shell, poly fill", Fit: "Regular", Closure: "Zip", Pockets: "4", Care: "Machine wash cold" } },
  { name: "Oversized Washed Graphic Tee", brand: "Kickstreet", vendorId: "v4", category: "men", subcategory: "T-Shirts", gender: "men", price: 999, mrp: 1499, rating: 4.4, reviewCount: 892, images: [img.menGraphicTee, img.menBomber], colors: [C.black, C.charcoal, C.white], sizes: MEN, description: "Heavyweight 240 GSM cotton with an acid-washed finish, dropped shoulders and an abstract brushstroke print.", tags: ["bestseller", "trending"], createdAt: "2026-07-22", popularity: 97, stock: 200, specs: { Fabric: "100% Cotton, 240 GSM", Fit: "Oversized", Neck: "Crew", Care: "Machine wash inside out" } },
  { name: "Pleated Wide Wool-Blend Trousers", brand: "Urban Loom", vendorId: "v2", category: "men", subcategory: "Trousers", gender: "men", price: 2999, mrp: 3999, rating: 4.5, reviewCount: 164, images: [img.menTrousers, img.menLinenShirt], colors: [C.charcoal, C.black, C.beige], sizes: MEN, description: "Double-pleated trousers with a high rise and generous leg in a soft, drapey wool blend. A modern take on vintage tailoring.", tags: ["new"], createdAt: "2026-11-05", popularity: 74, stock: 52 },
  { name: "Cargo Utility Pants", brand: "Kickstreet", vendorId: "v4", category: "men", subcategory: "Trousers", gender: "men", price: 1799, mrp: 2599, rating: 4.3, reviewCount: 305, images: [img.menGraphicTee, img.menTrousers], colors: [C.charcoal, C.olive], sizes: MEN, description: "Garment-dyed cotton twill cargos with bellowed pockets and a relaxed straight leg.", tags: ["sale"], createdAt: "2026-08-02", popularity: 80, stock: 75 },
  { name: "Knit Polo Shirt", brand: "Urban Loom", vendorId: "v1", category: "men", subcategory: "Shirts", gender: "men", price: 1499, mrp: 1999, rating: 4.2, reviewCount: 88, images: [img.menTrousers, img.menLinenShirt], colors: [C.black, C.ivory], sizes: MEN, description: "A fine-gauge knit polo with a clean open collar and ribbed cuffs. Smart enough for dinner, easy enough for weekends.", tags: ["new"], createdAt: "2026-11-14", popularity: 60, stock: 44 },
  // Kids
  { name: "Organic Cotton Camp Co-ord", brand: "Little Lark", vendorId: "v5", category: "kids", subcategory: "Co-ord Sets", gender: "kids", price: 1299, mrp: 1799, rating: 4.8, reviewCount: 174, images: [img.kidsCoord, img.kidsDress], colors: [C.sage, C.beige], sizes: KIDS, description: "A breezy camp-collar shirt and pull-on shorts in GOTS-certified organic cotton slub. Soft on skin, tough on play.", tags: ["bestseller", "new"], createdAt: "2026-10-28", popularity: 85, stock: 60 },
  { name: "Tiered Puff-Sleeve Dress", brand: "Little Lark", vendorId: "v5", category: "kids", subcategory: "Dresses", gender: "kids", price: 1199, mrp: 1699, rating: 4.9, reviewCount: 221, images: [img.kidsDress, img.kidsCoord], colors: [C.rose, C.ivory], sizes: KIDS, description: "A twirl-ready tiered dress with puff sleeves and a square neckline in organic cotton linen.", tags: ["trending", "sale"], createdAt: "2026-09-30", popularity: 87, stock: 38 },
  { name: "Mini Linen Party Shirt", brand: "Little Lark", vendorId: "v5", category: "kids", subcategory: "Co-ord Sets", gender: "kids", price: 899, mrp: 1199, rating: 4.6, reviewCount: 64, images: [img.kidsCoord], colors: [C.white, C.sage], sizes: KIDS, description: "A mini version of our best-selling linen shirt, made for weddings, birthdays and everything in between.", tags: ["new"], createdAt: "2026-11-11", popularity: 55, stock: 30 },
  // Footwear
  { name: "Chunky Leather Court Sneakers", brand: "Kickstreet", vendorId: "v4", category: "footwear", subcategory: "Sneakers", gender: "unisex", price: 3499, mrp: 4999, rating: 4.6, reviewCount: 1204, images: [img.sneakers, img.loafers], colors: [C.white, C.ivory], sizes: SHOES, description: "Premium leather uppers with suede accents on a sculpted, cushioned platform sole. Built for 20,000 steps a day.", tags: ["bestseller", "trending", "sale"], createdAt: "2026-08-15", popularity: 100, stock: 140, specs: { Upper: "Leather & suede", Sole: "Rubber, cushioned EVA", Closure: "Lace-up", Warranty: "6 months", Care: "Wipe clean" } },
  { name: "Sculpted Block Heel Mules", brand: "Aurum & Co", vendorId: "v6", category: "footwear", subcategory: "Heels", gender: "women", price: 2799, mrp: 3999, rating: 4.4, reviewCount: 287, images: [img.blockHeels, img.leatherTote], colors: [C.tan, C.black], sizes: SHOES.slice(0, 5), description: "Square-toe mules with a sculpted 6cm block heel and a padded leather footbed for all-day comfort.", tags: ["new", "trending"], createdAt: "2026-11-03", popularity: 82, stock: 26 },
  { name: "Penny Leather Loafers", brand: "Aurum & Co", vendorId: "v6", category: "footwear", subcategory: "Loafers", gender: "men", price: 3999, mrp: 5499, rating: 4.7, reviewCount: 402, images: [img.loafers, img.sneakers], colors: [C.tan, C.black], sizes: SHOES.slice(2), description: "Hand-stitched penny loafers in polished full-grain leather with a leather sole and cushioned insole.", tags: ["bestseller"], createdAt: "2026-07-10", popularity: 89, stock: 34 },
  { name: "Minimal Slip-On Sneakers", brand: "Kickstreet", vendorId: "v4", category: "footwear", subcategory: "Sneakers", gender: "unisex", price: 1999, mrp: 2999, rating: 4.2, reviewCount: 516, images: [img.sneakers], colors: [C.white, C.black], sizes: SHOES, description: "Elastic-gusset slip-ons with a padded collar and memory foam insole. Effortless on, effortless off.", tags: ["sale"], createdAt: "2026-06-28", popularity: 73, stock: 88 },
  // Accessories
  { name: "Structured Leather Tote", brand: "Aurum & Co", vendorId: "v6", category: "accessories", subcategory: "Bags", gender: "women", price: 5499, mrp: 7999, rating: 4.8, reviewCount: 633, images: [img.leatherTote, img.sunglasses], colors: [C.tan, C.black, C.ivory], sizes: ["One Size"], description: "A roomy, structured tote in full-grain vegetable-tanned leather with a laptop sleeve and magnetic closure.", tags: ["bestseller", "trending"], createdAt: "2026-09-08", popularity: 94, stock: 40, specs: { Material: "Full-grain leather", Dimensions: "38 x 28 x 14 cm", Closure: "Magnetic", Compartments: "3 + laptop sleeve", Warranty: "1 year" } },
  { name: "Acetate Cat-Eye Sunglasses", brand: "Aurum & Co", vendorId: "v6", category: "accessories", subcategory: "Sunglasses", gender: "unisex", price: 1899, mrp: 2999, rating: 4.5, reviewCount: 247, images: [img.sunglasses, img.leatherTote], colors: [C.black, C.tan], sizes: ["One Size"], description: "Hand-polished acetate frames with polarised, UV400 lenses and a subtly lifted cat-eye shape.", tags: ["new", "sale"], createdAt: "2026-10-30", popularity: 78, stock: 66 },
  { name: "Minimal Gold-Tone Watch", brand: "Aurum & Co", vendorId: "v6", category: "accessories", subcategory: "Watches", gender: "unisex", price: 4999, mrp: 6999, rating: 4.6, reviewCount: 189, images: [img.watch, img.sunglasses], colors: [C.gold, C.black], sizes: ["One Size"], description: "A slim 36mm case in brushed gold-tone steel on a soft Italian leather strap, powered by Japanese quartz.", tags: ["trending"], createdAt: "2026-10-05", popularity: 84, stock: 28 },
  { name: "Mini Crossbody Bag", brand: "Aurum & Co", vendorId: "v6", category: "accessories", subcategory: "Bags", gender: "women", price: 2499, mrp: 3499, rating: 4.4, reviewCount: 158, images: [img.leatherTote], colors: [C.black, C.terracotta], sizes: ["One Size"], description: "A compact crossbody with an adjustable strap and gold hardware — fits your phone, cards and a lipstick.", tags: ["new"], createdAt: "2026-11-15", popularity: 69, stock: 50 },
  // Beauty
  { name: "Velvet Matte Lipstick", brand: "Bloom Beauty", vendorId: "v7", category: "beauty", subcategory: "Lipstick", gender: "women", price: 699, mrp: 999, rating: 4.5, reviewCount: 1408, images: [img.lipstick, img.perfume], colors: [C.red, C.terracotta, C.rose], sizes: ["3.5 g"], description: "A creamy, transfer-resistant matte enriched with jojoba and shea for 12-hour comfortable colour.", tags: ["bestseller", "sale"], createdAt: "2026-07-01", popularity: 93, stock: 300 },
  { name: "Amber Oud Eau de Parfum", brand: "Bloom Beauty", vendorId: "v7", category: "beauty", subcategory: "Fragrance", gender: "unisex", price: 2899, mrp: 3999, rating: 4.7, reviewCount: 512, images: [img.perfume, img.lipstick], colors: [C.gold], sizes: ["50 ml", "100 ml"], description: "Warm amber, smoked oud and a whisper of saffron. Long-lasting, sophisticated and made for evenings.", tags: ["trending", "new"], createdAt: "2026-11-06", popularity: 86, stock: 80 },
  { name: "Lip & Cheek Tint Duo", brand: "Bloom Beauty", vendorId: "v7", category: "beauty", subcategory: "Lipstick", gender: "women", price: 899, mrp: 1299, rating: 4.3, reviewCount: 233, images: [img.lipstick], colors: [C.rose, C.terracotta], sizes: ["2 x 5 ml"], description: "A buildable, dewy tint that works on lips and cheeks for a fresh, flushed look in seconds.", tags: ["new"], createdAt: "2026-11-13", popularity: 62, stock: 120 },
  { name: "Neroli Bloom Body Mist", brand: "Bloom Beauty", vendorId: "v7", category: "beauty", subcategory: "Fragrance", gender: "women", price: 799, mrp: 1199, rating: 4.4, reviewCount: 301, images: [img.perfume], colors: [C.champagne], sizes: ["150 ml"], description: "A light, sparkling mist of neroli, white tea and musk — your everyday fresh-air moment.", tags: ["sale"], createdAt: "2026-08-25", popularity: 71, stock: 95 },
];

const defaultHighlights = (s: Seed) => [
  `Sold by an independent Street Runway label`,
  s.category === "beauty" ? "Dermatologically tested & cruelty-free" : "Quality checked before dispatch",
  "Easy 15-day returns & exchanges",
];

export const products: Product[] = seeds.map((s, i) => ({
  ...s,
  id: `p${i + 1}`,
  slug: slugify(s.name),
  highlights: s.highlights ?? defaultHighlights(s),
  specs: s.specs ?? { Brand: s.brand, Category: s.subcategory, Fit: "Regular", Care: "As per label" },
}));

const reviewers = ["Ananya", "Rahul", "Ishita", "Karan", "Neha", "Siddharth", "Pooja", "Aditya", "Zara", "Dev"];
const reviewBodies = [
  { title: "Exactly as pictured", body: "Colour and fabric are exactly like the photos. Fits true to size and feels premium." },
  { title: "Great quality", body: "Stitching is neat and the material feels lovely. Got lots of compliments the first time I wore it." },
  { title: "Good, runs slightly large", body: "Beautiful piece but I would size down. Exchange was quick and painless." },
  { title: "Worth every rupee", body: "Delivered in two days in beautiful packaging. Will definitely order again from this store." },
  { title: "Nice but delivery was slow", body: "Product is lovely, delivery took a day longer than promised. Overall happy." },
];

export function reviewsFor(productId: string): Review[] {
  const n = Number(productId.replace(/\D/g, "")) || 1;
  return Array.from({ length: 5 }, (_, i) => {
    const b = reviewBodies[(n + i) % reviewBodies.length] ?? reviewBodies[0]!;
    return {
      id: `${productId}-r${i}`,
      productId,
      author: reviewers[(n * 3 + i) % reviewers.length] ?? "Verified buyer",
      rating: [5, 5, 4, 5, 3][(n + i) % 5] ?? 5,
      title: b.title,
      body: b.body,
      date: `2026-${String(((n + i) % 10) + 1).padStart(2, "0")}-${String(((n * 7 + i) % 27) + 1).padStart(2, "0")}`,
      verified: i !== 3,
      size: ["S", "M", "L", "M", "XL"][i] ?? "M",
    };
  });
}
