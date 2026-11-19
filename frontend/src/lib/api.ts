/** Small Laravel JSON API client. Demo catalog remains the fallback until an API is configured. */
const baseUrl = (import.meta.env.VITE_API_BASE_URL as string | undefined)?.replace(/\/$/, "");
const API_TOKEN_KEY = "street-runway-api-token";

export function getApiToken() {
  return typeof window === "undefined" ? null : window.localStorage.getItem(API_TOKEN_KEY);
}

export function setApiToken(token: string) {
  window.localStorage.setItem(API_TOKEN_KEY, token);
}

export function clearApiToken() {
  window.localStorage.removeItem(API_TOKEN_KEY);
}

export class ApiError extends Error {
  constructor(message: string, public status: number, public payload?: unknown) {
    super(message);
    this.name = "ApiError";
  }
}

export function isApiConfigured() {
  return Boolean(baseUrl);
}

export async function apiRequest<T>(path: string, options: RequestInit = {}): Promise<T> {
  if (!baseUrl) throw new Error("VITE_API_BASE_URL is not configured.");
  const headers = new Headers(options.headers);
  headers.set("Accept", "application/json");
  const token = getApiToken();
  if (token) headers.set("Authorization", `Bearer ${token}`);
  if (options.body && !(options.body instanceof FormData)) headers.set("Content-Type", "application/json");
  const response = await fetch(`${baseUrl}/${path.replace(/^\//, "")}`, {
    ...options,
    headers,
    credentials: "omit",
  });
  const contentType = response.headers.get("content-type") ?? "";
  const payload: unknown = contentType.includes("application/json") ? await response.json() : await response.text();
  if (!response.ok) {
    const message = typeof payload === "object" && payload !== null && "message" in payload && typeof payload.message === "string"
      ? payload.message
      : `API request failed (${response.status})`;
    throw new ApiError(message, response.status, payload);
  }
  return payload as T;
}

export const api = {
  get: <T>(path: string, signal?: AbortSignal) => apiRequest<T>(path, { signal }),
  post: <T>(path: string, body: unknown) => apiRequest<T>(path, { method: "POST", body: JSON.stringify(body) }),
  put: <T>(path: string, body: unknown) => apiRequest<T>(path, { method: "PUT", body: JSON.stringify(body) }),
  patch: <T>(path: string, body: unknown) => apiRequest<T>(path, { method: "PATCH", body: JSON.stringify(body) }),
  delete: <T>(path: string) => apiRequest<T>(path, { method: "DELETE" }),
};

/** Endpoint groups mirror the planned Laravel resource areas. Override paths here if the API differs. */
export const services = {
  auth: { login: <T>(credentials: unknown) => api.post<T>("auth/login", credentials), register: <T>(details: unknown) => api.post<T>("auth/register", details), logout: () => api.post("auth/logout", {}) },
  products: { list: <T>(query = "") => api.get<T>(`products${query ? `?${query}` : ""}`), show: <T>(id: string) => api.get<T>(`products/${id}`) },
  categories: { list: <T>() => api.get<T>("categories") },
  vendors: { apply: <T>(details: FormData) => apiRequest<T>("vendor-applications", { method: "POST", body: details }), status: <T>() => api.get<T>("vendor-applications/me"), store: <T>(slug: string) => api.get<T>(`stores/${slug}`) },
  cart: { get: <T>() => api.get<T>("cart"), update: <T>(items: unknown) => api.put<T>("cart", { items }) },
  orders: { list: <T>() => api.get<T>("orders"), create: <T>(order: unknown) => api.post<T>("orders", order) },
  wishlist: { get: <T>() => api.get<T>("wishlist"), add: <T>(productId: string) => api.post<T>("wishlist", { product_id: productId }), remove: <T>(productId: string) => api.delete<T>(`wishlist/${productId}`) },
  payments: { initiate: <T>(orderId: string, method: string) => api.post<T>("payments", { order_id: orderId, method }) },
  admin: { applications: <T>() => api.get<T>("admin/vendor-applications"), reviewApplication: <T>(id: number | string, status: string, rejectionReason?: string) => api.patch<T>(`admin/vendor-applications/${id}`, { status, rejection_reason: rejectionReason }) },
};
