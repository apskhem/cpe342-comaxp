import { writable } from "svelte/store";

export const checkOutData = writable({ coupon: "", customer: "" });
export const cartProduct = writable(new Map());
export const loginToken = writable("");