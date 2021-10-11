import { writable } from "svelte/store";

export const cartProduct = writable(new Map());
export const loginToken = writable("");