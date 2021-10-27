import { writable } from "svelte/store";

export const checkoutData = writable({ coupon: "", customer: "", preorder: false });
export const cartProduct = writable(new Map());
export const loginToken = writable("");
export const currentUser = writable({});