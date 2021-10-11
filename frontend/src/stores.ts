import { writable } from "svelte/store";

export const cachedProducts = writable([]);
export const loginToken = writable("");