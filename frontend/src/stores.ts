import { writable } from "svelte/store";

export const checkoutData = writable({ coupon: "", customer: "" });
export const cartProduct = writable(new Map());
export const loginToken = writable("");